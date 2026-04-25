<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload   = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret    = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        match ($event->type) {
            'checkout.session.completed'    => $this->handleCheckoutCompleted($event->data->object),
            'customer.subscription.created' => $this->handleSubscriptionCreated($event->data->object),
            'invoice.payment_failed'        => $this->handlePaymentFailed($event->data->object),
            default                         => null,
        };

        return response('OK', 200);
    }

    private function handleCheckoutCompleted($session)
    {
        $metadata = $session->metadata;
        $email    = $session->customer_email;
        $name     = $metadata->contact_name  ?? 'Unknown';
        $plan     = $metadata->plan          ?? 'unknown';
        $billing  = $metadata->billing       ?? 'monthly';
        $practice = $metadata->practice_name ?? '—';
        $phone    = $metadata->phone         ?? '—';
        $type     = $metadata->practice_type ?? '—';
        $numUsers = (int) ($metadata->num_users ?? 1);
        $hipaa = ($metadata->hipaa_required ?? '0') === '1';

        \Log::info('Stripe checkout.session.completed', [
            'email'        => $email,
            'subscription' => $session->subscription,
            'plan'         => $plan,
        ]);

        // Send internal lead notification email
        $this->sendPaymentNotification(
            $name, $email, $plan, $billing, $practice, $phone, $type, $numUsers,
            $session->customer, $session->subscription
        );

        // Trigger auto-provisioning for Starter and Professional
        if (in_array($plan, ['starter', 'professional'])) {
    $this->triggerProvisioning([
        'client_name'            => $practice,
        'contact_name'           => $name,
        'email'                  => $email,
        'phone'                  => $phone,
        'plan'                   => $plan,
        'billing'                => $billing,
        'num_users'              => $numUsers,
        'hipaa_required'         => $hipaa,
        'stripe_customer_id'     => $session->customer,
        'stripe_subscription_id' => $session->subscription,
    ]);
}
    }

    private function triggerProvisioning(array $data): void
    {
        $url   = env('ABIRASIGN_APP_URL', 'https://dev.abirasign.com') . '/api/internal/provision';
        $token = env('INTERNAL_API_TOKEN');

        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($data),
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                    'X-Internal-Token: ' . $token,
                    'Accept: application/json',
                ],
                CURLOPT_TIMEOUT        => 30,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $decoded = json_decode($response, true);

            if ($httpCode === 201) {
                \Log::info('Auto-provisioning triggered successfully', [
                    'email'     => $data['email'],
                    'tenant_id' => $decoded['tenant_id'] ?? null,
                    'setup_url' => $decoded['setup_url'] ?? null,
                ]);
            } else {
                \Log::error('Auto-provisioning failed', [
                    'email'     => $data['email'],
                    'http_code' => $httpCode,
                    'response'  => $response,
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Auto-provisioning exception', [
                'email' => $data['email'],
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function sendPaymentNotification(
        string $name, string $email, string $plan, string $billing,
        string $practice, string $phone, string $type, int $numUsers,
        ?string $customerId, ?string $subscriptionId
    ): void {
        $to        = config('mail.contact_to', 'hello@abirasign.com');
        $planLabel = match($plan) {
            'starter'      => 'Starter',
            'professional' => 'Professional',
            default        => ucfirst($plan),
        };
        $billingLabel = $billing === 'annual' ? 'Annual (10% discount)' : 'Monthly';

        $html = "
            <div style='font-family: sans-serif; max-width: 600px; color: #111827;'>
                <h2 style='color: #0E7490; margin-bottom: 4px;'>💳 Payment confirmed — {$planLabel}</h2>
                <p style='color: #6B7280; font-size: 13px; margin-top: 0;'>Stripe Checkout completed · auto-provisioning triggered</p>
                <table style='width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px;'>
                    <tr><td style='padding: 8px 0; color: #6B7280; width: 140px;'>Name</td><td style='padding: 8px 0; font-weight: 600;'>" . e($name) . "</td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Email</td><td style='padding: 8px 0;'><a href='mailto:" . e($email) . "' style='color: #0E7490;'>" . e($email) . "</a></td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Phone</td><td style='padding: 8px 0;'>" . e($phone) . "</td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Business</td><td style='padding: 8px 0;'>" . e($practice) . "</td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Industry</td><td style='padding: 8px 0;'>" . e(ucfirst(str_replace('_', ' ', $type))) . "</td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Staff users</td><td style='padding: 8px 0;'>" . $numUsers . "</td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Plan</td><td style='padding: 8px 0; font-weight: 600; color: #0E7490;'>" . e($planLabel) . "</td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Billing</td><td style='padding: 8px 0;'>" . e($billingLabel) . "</td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Stripe Customer</td><td style='padding: 8px 0; font-size: 12px;'>" . e($customerId ?? '—') . "</td></tr>
                    <tr><td style='padding: 8px 0; color: #6B7280;'>Subscription</td><td style='padding: 8px 0; font-size: 12px;'>" . e($subscriptionId ?? '—') . "</td></tr>
                </table>
                <div style='background: #DCFCE7; border: 1px solid #86EFAC; border-radius: 8px; padding: 16px; font-size: 14px; color: #166534;'>
                    <strong>✓ Auto-provisioning triggered.</strong> Account setup email will be sent to the client automatically.
                </div>
                <p style='font-size: 12px; color: #9CA3AF; margin-top: 24px;'>© " . date('Y') . " BrightNet Technologies LLC, DBA AbiraSign</p>
            </div>
        ";

        try {
            Mail::html($html, function ($mail) use ($to, $name, $email, $planLabel) {
                $mail->to($to)
                     ->replyTo($email, $name)
                     ->subject('[AbiraSign] Payment confirmed — ' . $planLabel . ' · ' . $name);
            });
            \Log::info('Signup payment notification sent', ['email' => $email, 'plan' => $plan]);
        } catch (\Exception $e) {
            \Log::error('Signup payment notification failed', ['error' => $e->getMessage()]);
        }
    }

    private function handleSubscriptionCreated($subscription)
    {
        \Log::info('Stripe customer.subscription.created', [
            'customer'     => $subscription->customer,
            'subscription' => $subscription->id,
            'status'       => $subscription->status,
        ]);
    }

    private function handlePaymentFailed($invoice)
    {
        \Log::warning('Stripe invoice.payment_failed', [
            'customer'      => $invoice->customer,
            'amount_due'    => $invoice->amount_due,
            'attempt_count' => $invoice->attempt_count,
        ]);
        // TODO: Send payment failure notification email to customer
    }
}
