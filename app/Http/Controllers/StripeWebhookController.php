<?php
namespace App\Http\Controllers;

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
        // Triggered when customer completes Stripe Checkout
        // TODO: Trigger account provisioning once provisioning API is wired in
        // Available data:
        //   $session->customer_email
        //   $session->customer
        //   $session->subscription
        //   $session->metadata (plan, billing, practice_name, contact_name, phone, practice_type)
        \Log::info('Stripe checkout.session.completed', [
            'email'        => $session->customer_email,
            'subscription' => $session->subscription,
            'metadata'     => $session->metadata,
        ]);
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
