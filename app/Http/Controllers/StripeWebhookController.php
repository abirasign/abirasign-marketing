<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
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
            'checkout.session.completed'   => $this->handleCheckoutCompleted($event->data->object),
            'customer.subscription.created'=> $this->handleSubscriptionCreated($event->data->object),
            'invoice.payment_failed'       => $this->handlePaymentFailed($event->data->object),
            default                        => null,
        };

        return response('OK', 200);
    }

    private function handleCheckoutCompleted($session)
{
    $metadata = $session->metadata;
    $email    = $session->customer_email;
    $name     = $metadata->contact_name ?? 'Unknown';
    $plan     = $metadata->plan         ?? 'unknown';
    $billing  = $metadata->billing      ?? 'monthly';
    $practice = $metadata->practice_name ?? '—';
    $phone    = $metadata->phone         ?? '—';
    $type     = $metadata->practice_type ?? '—';

    \Log::info('Stripe checkout.session.completed', [
        'email'        => $email,
        'subscription' => $session->subscription,
        'plan'         => $plan,
    ]);

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
            <p style='color: #6B7280; font-size: 13px; margin-top: 0;'>Stripe Checkout completed · account needs provisioning</p>
            <table style='width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px;'>
                <tr><td style='padding: 8px 0; color: #6B7280; width: 140px;'>Name</td><td style='padding: 8px 0; font-weight: 600;'>" . e($name) . "</td></tr>
                <tr><td style='padding: 8px 0; color: #6B7280;'>Email</td><td style='padding: 8px 0;'><a href='mailto:" . e($email) . "' style='color: #0E7490;'>" . e($email) . "</a></td></tr>
                <tr><td style='padding: 8px 0; color: #6B7280;'>Phone</td><td style='padding: 8px 0;'>" . e($phone) . "</td></tr>
                <tr><td style='padding: 8px 0; color: #6B7280;'>Business</td><td style='padding: 8px 0;'>" . e($practice) . "</td></tr>
                <tr><td style='padding: 8px 0; color: #6B7280;'>Industry</td><td style='padding: 8px 0;'>" . e(ucfirst(str_replace('_', ' ', $type))) . "</td></tr>
                <tr><td style='padding: 8px 0; color: #6B7280;'>Plan</td><td style='padding: 8px 0; font-weight: 600; color: #0E7490;'>" . e($planLabel) . "</td></tr>
                <tr><td style='padding: 8px 0; color: #6B7280;'>Billing</td><td style='padding: 8px 0;'>" . e($billingLabel) . "</td></tr>
                <tr><td style='padding: 8px 0; color: #6B7280;'>Subscription</td><td style='padding: 8px 0; font-size: 12px; color: #6B7280;'>" . e($session->subscription ?? '—') . "</td></tr>
            </table>
            <div style='background: #FEF9C3; border: 1px solid #FDE047; border-radius: 8px; padding: 16px; font-size: 14px; color: #854D0E;'>
                <strong>Action required:</strong> Provision this account in the AbiraSign admin portal.
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
        // Triggered when subscription is created after checkout
        // TODO: Store subscription ID against tenant record once provisioning is wired in
        \Log::info('Stripe customer.subscription.created', [
            'customer'     => $subscription->customer,
            'subscription' => $subscription->id,
            'status'       => $subscription->status,
        ]);
    }

    private function handlePaymentFailed($invoice)
    {
        // Triggered when a subscription renewal payment fails
        // TODO: Send payment failure notification email to customer
        \Log::warning('Stripe invoice.payment_failed', [
            'customer'      => $invoice->customer,
            'amount_due'    => $invoice->amount_due,
            'attempt_count' => $invoice->attempt_count,
        ]);
    }
}
