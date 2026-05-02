<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
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
            'checkout.session.completed'           => $this->handleCheckoutCompleted($event->data->object),
            'customer.subscription.created'        => $this->handleSubscriptionCreated($event->data->object),
            'customer.subscription.updated'        => $this->handleSubscriptionUpdated($event->data->object),
            'customer.subscription.deleted'        => $this->handleSubscriptionDeleted($event->data->object),
            'customer.subscription.trial_will_end' => $this->handleTrialWillEnd($event->data->object),
            'invoice.payment_succeeded'            => $this->handlePaymentSucceeded($event->data->object),
            'invoice.payment_failed'               => $this->handlePaymentFailed($event->data->object),
            'setup_intent.succeeded'               => $this->handleSetupIntentSucceeded($event->data->object),
            'payment_intent.succeeded'             => $this->handlePaymentIntentSucceeded($event->data->object),
            default                                => null,
        };

        return response('OK', 200);
    }

    // ── PAYG: card saved successfully ─────────────────────────────────────────
    private function handleSetupIntentSucceeded($setupIntent)
    {
        $metadata = $setupIntent->metadata;
        $plan     = $metadata->plan ?? '';

        if ($metadata->plan !== 'payg') return;

        $email     = $metadata->email         ?? null;
        $name      = $metadata->contact_name  ?? 'Unknown';
        $practice  = $metadata->practice_name ?? '—';
        $phone     = $metadata->phone         ?? '—';
        $type      = $metadata->practice_type ?? '—';
        $numUsers  = (int) ($metadata->num_users ?? 1);

        \Log::info('Stripe setup_intent.succeeded (PAYG)', [
            'email'             => $email,
            'payment_method'    => $setupIntent->payment_method,
            'customer'          => $setupIntent->customer,
        ]);

        if (!$email) {
            \Log::error('PAYG setup_intent: no email in metadata');
            return;
        }

        // Trigger PAYG provisioning
        $this->triggerProvisioning([
            'client_name'             => $practice,
            'contact_name'            => $name,
            'email'                   => $email,
            'phone'                   => $phone,
            'plan'                    => 'payg',
            'num_users'               => $numUsers,
            'stripe_customer_id'      => $setupIntent->customer,
            'stripe_payment_method_id'=> $setupIntent->payment_method,
            'stripe_setup_intent_id'  => $setupIntent->id,
        ]);

        $this->sendLeadNotification($name, $email, 'payg', 'payg', $practice, $phone, $type, $numUsers);
    }

    // ── Checkout completed (Starter/Pro subscription or Enterprise quote) ──────
    private function handleCheckoutCompleted($session)
    {
        $metadata = $session->metadata;

        if (($metadata->type ?? '') === 'quote') {
            $this->handleQuotePayment($session);
            return;
        }

        $plan = $metadata->plan ?? 'unknown';

        // PAYG is handled via setup_intent.succeeded — skip here
        if ($plan === 'payg') return;

        $email    = $session->customer_email;
        $name     = $metadata->contact_name  ?? 'Unknown';
        $billing  = $metadata->billing       ?? 'monthly';
        $practice = $metadata->practice_name ?? '—';
        $phone    = $metadata->phone         ?? '—';
        $type     = $metadata->practice_type ?? '—';
        $numUsers = (int) ($metadata->num_users ?? 1);
        $hipaa    = ($metadata->hipaa_required ?? '0') === '1';

        \Log::info('Stripe checkout.session.completed', [
            'email'        => $email,
            'subscription' => $session->subscription,
            'plan'         => $plan,
        ]);

        $this->sendPaymentNotification(
            $name, $email, $plan, $billing, $practice, $phone, $type, $numUsers,
            $session->customer, $session->subscription
        );

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

    // ── Subscription created ───────────────────────────────────────────────────
    private function handleSubscriptionCreated($subscription)
    {
        \Log::info('Stripe customer.subscription.created', [
            'customer'     => $subscription->customer,
            'subscription' => $subscription->id,
            'status'       => $subscription->status,
        ]);
        // Provisioning triggered via checkout.session.completed
    }

    // ── Subscription updated (plan change, cancel, reactivate, trial→active) ──
    private function handleSubscriptionUpdated($subscription)
    {
        \Log::info('Stripe customer.subscription.updated', [
            'subscription' => $subscription->id,
            'status'       => $subscription->status,
        ]);

        $row = DB::table('subscriptions')
            ->where('stripe_sub_id', $subscription->id)
            ->first();

        if (!$row) {
            \Log::warning('subscription.updated — no matching subscription in DB', [
                'stripe_sub_id' => $subscription->id,
            ]);
            return;
        }

        $isCancelled = $subscription->cancel_at_period_end
            || (!is_null($subscription->cancel_at) && $subscription->cancel_at > time());

        $status = match(true) {
            $isCancelled                          => 'cancelled',
            $subscription->status === 'trialing'  => 'trialing',
            $subscription->status === 'active'    => 'active',
            $subscription->status === 'past_due'  => 'past_due',
            $subscription->status === 'canceled'  => 'cancelled',
            default                               => $subscription->status,
        };

        $periodEnd = $subscription->current_period_end
            ?? $subscription->items->data[0]->current_period_end
            ?? null;

        $nextBilling = null;
        if ($isCancelled && $subscription->cancel_at) {
            $nextBilling = date('Y-m-d', (int) $subscription->cancel_at);
        } elseif ($periodEnd) {
            $nextBilling = date('Y-m-d', (int) $periodEnd);
        }

        $trialEnd = $subscription->trial_end
            ? date('Y-m-d', (int) $subscription->trial_end)
            : null;

        // Detect plan change from Stripe item
        $item        = $subscription->items->data[0] ?? null;
        $stripePriceId = $item?->price?->id;
        $newPlanType = $this->planFromPriceId($stripePriceId);
        $newNumUsers = $item?->quantity ?? $row->num_users ?? null;

        $updates = [
            'status'       => $status,
            'next_billing' => $nextBilling,
            'trial_end_date' => $trialEnd,
            'updated_at'   => now(),
        ];

        if ($newPlanType && $newPlanType !== $row->plan_type) {
            $updates['plan_type']    = $newPlanType;
            $updates['monthly_rate'] = $this->monthlyRateForPlan($newPlanType, $row->billing_term);
        }

        DB::table('subscriptions')
            ->where('stripe_sub_id', $subscription->id)
            ->update($updates);

        // If trial just converted to active, send conversion email
        if ($row->status === 'trialing' && $status === 'active') {
            $this->sendTrialConvertedEmail($row->tenant_id);
        }

        \Log::info('Subscription updated in DB', [
            'stripe_sub_id' => $subscription->id,
            'status'        => $status,
            'next_billing'  => $nextBilling,
        ]);
    }

    // ── Subscription deleted ───────────────────────────────────────────────────
    private function handleSubscriptionDeleted($subscription)
    {
        \Log::info('Stripe customer.subscription.deleted', [
            'subscription' => $subscription->id,
        ]);

        $row = DB::table('subscriptions')
            ->where('stripe_sub_id', $subscription->id)
            ->first();

        if (!$row) return;

        $wasTrial      = $row->status === 'trialing';
        $pendingPlan   = $row->pending_plan ?? null;
        $isPaygSwitch  = $pendingPlan === 'payg';
        $isPlanSwitch  = in_array($pendingPlan, ['starter', 'payg']);

        // Starter downgrade — reactivate with new plan
        if ($pendingPlan === 'starter' && !$isPaygSwitch) {
            DB::table('subscriptions')
                ->where('stripe_sub_id', $subscription->id)
                ->update([
                    'plan_type'    => 'starter',
                    'monthly_rate' => 45.00,
                    'status'       => 'active',
                    'pending_plan' => null,
                    'next_billing' => null,
                    'updated_at'   => now(),
                ]);

            // Create new Starter subscription in Stripe
            // (handled by upgradePayg flow if needed — for now just update DB)
            DB::table('tenants')
                ->where('tenant_id', $row->tenant_id)
                ->update(['status' => 'active', 'updated_at' => now()]);

            $tenant = DB::table('tenants')->where('tenant_id', $row->tenant_id)->first();
            if ($tenant) {
                $name = $tenant->primary_contact ?? $tenant->client_name;
                $html = "
                    <div style='font-family:sans-serif;max-width:600px;color:#111827;'>
                        <div style='margin-bottom:24px;'>
                            <span style='font-size:20px;font-weight:700;'>Abira<span style='color:#0E7490;'>Sign</span></span>
                        </div>
                        <h2 style='font-size:18px;font-weight:700;color:#111827;margin-bottom:12px;'>Your plan has switched to Starter</h2>
                        <p style='font-size:15px;color:#374151;line-height:1.7;'>Hi " . e($name) . ", your plan has been switched to <strong>Starter</strong>.</p>
                        <p style='font-size:14px;color:#374151;'>You now have unlimited envelope sends at \$45.00/user/mo. HIPAA compliance and BAA are not available on this plan.</p>
                        <p style='margin-top:20px;'>
                            <a href='" . env('ABIRASIGN_APP_URL', 'https://dev.abirasign.com') . "/billing'
                               style='background:#534AB7;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;'>
                                View billing →
                            </a>
                        </p>
                        <hr style='border:none;border-top:1px solid #E5E7EB;margin:24px 0;'>
                        <p style='font-size:12px;color:#9CA3AF;'>© " . date('Y') . " BrightNet Technologies LLC, DBA AbiraSign</p>
                    </div>
                ";
                try {
                    Mail::html($html, function ($mail) use ($tenant, $name) {
                        $mail->to($tenant->primary_email, $name)
                             ->subject('[AbiraSign] Your plan has switched to Starter');
                    });
                } catch (\Exception $e) {
                    \Log::error('Starter switch email failed', ['error' => $e->getMessage()]);
                }
            }

            \Log::info('Subscription switched to Starter', ['tenant_id' => $row->tenant_id]);
            return;
        }
        
        if ($isPaygSwitch) {
            // Switching to PAYG — retrieve default payment method from Stripe Customer
            $paymentMethodId = null;
            try {
                Stripe::setApiKey(env('STRIPE_SECRET'));
                $customer        = \Stripe\Customer::retrieve($row->stripe_customer_id);
                $paymentMethodId = $customer->invoice_settings->default_payment_method
                    ?? $row->stripe_payment_method_id;
            } catch (\Exception $e) {
                \Log::error('PAYG switch: could not retrieve payment method', [
                    'error' => $e->getMessage(),
                ]);
            }

            // Switch to PAYG — keep tenant active
            DB::table('subscriptions')
                ->where('stripe_sub_id', $subscription->id)
                ->update([
                    'plan_type'                => 'payg',
                    'monthly_rate'             => 0.00,
                    'billing_term'             => 'monthly',
                    'status'                   => 'active',
                    'stripe_sub_id'            => null,
                    'next_billing'             => null,
                    'pending_plan'             => null,
                    'stripe_payment_method_id' => $paymentMethodId,
                    'updated_at'               => now(),
                ]);

            DB::table('tenants')
                ->where('tenant_id', $row->tenant_id)
                ->update(['status' => 'active', 'updated_at' => now()]);

            // Send plan change email
            $tenant = DB::table('tenants')->where('tenant_id', $row->tenant_id)->first();
            if ($tenant) {
                $name = $tenant->primary_contact ?? $tenant->client_name;
                $html = "
                    <div style='font-family:sans-serif;max-width:600px;color:#111827;'>
                        <div style='margin-bottom:24px;'>
                            <span style='font-size:20px;font-weight:700;'>Abira<span style='color:#0E7490;'>Sign</span></span>
                        </div>
                        <h2 style='font-size:18px;font-weight:700;color:#111827;margin-bottom:12px;'>Your plan has switched to Pay As You Go</h2>
                        <p style='font-size:15px;color:#374151;line-height:1.7;'>Hi " . e($name) . ", your plan has been switched to <strong>Pay As You Go</strong>.</p>
                        <p style='font-size:14px;color:#374151;'>Going forward, your card on file will be charged <strong>\$10.00</strong> each time you send an envelope. There is no monthly fee.</p>
                        <div style='background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:12px 14px;margin:16px 0;font-size:13px;color:#92400e;'>
                            ⚠ <strong>PAYG accounts cannot process protected health information (PHI).</strong>
                            If you need HIPAA compliance, upgrade to Professional from your billing page.
                        </div>
                        <p style='margin-top:20px;'>
                            <a href='" . env('ABIRASIGN_APP_URL', 'https://dev.abirasign.com') . "/billing'
                               style='background:#534AB7;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;'>
                                View billing →
                            </a>
                        </p>
                        <hr style='border:none;border-top:1px solid #E5E7EB;margin:24px 0;'>
                        <p style='font-size:12px;color:#9CA3AF;'>© " . date('Y') . " BrightNet Technologies LLC, DBA AbiraSign</p>
                    </div>
                ";
                try {
                    Mail::html($html, function ($mail) use ($tenant, $name) {
                        $mail->to($tenant->primary_email, $name)
                             ->subject('[AbiraSign] Your plan has switched to Pay As You Go');
                    });
                } catch (\Exception $e) {
                    \Log::error('PAYG switch email failed', ['error' => $e->getMessage()]);
                }
            }

            \Log::info('Subscription switched to PAYG', ['tenant_id' => $row->tenant_id]);
            return;
        }

        // Normal cancellation — deactivate tenant
        $wasTrial = $row->status === 'trialing';

        DB::table('subscriptions')
            ->where('stripe_sub_id', $subscription->id)
            ->update(['status' => 'cancelled', 'updated_at' => now()]);

        DB::table('tenants')
            ->where('tenant_id', $row->tenant_id)
            ->update(['status' => 'inactive', 'updated_at' => now()]);

        $tenant = DB::table('tenants')->where('tenant_id', $row->tenant_id)->first();
        if ($tenant) {
            if ($wasTrial) {
                $this->sendTrialCancelledEmail($tenant, $row);
            } else {
                $this->sendSubscriptionCancelledEmail($tenant, $row);
            }
        }

        \Log::info('Subscription cancelled and tenant deactivated', [
            'tenant_id' => $row->tenant_id,
        ]);
    }

    // ── Trial ending in 3 days ─────────────────────────────────────────────────
    private function handleTrialWillEnd($subscription)
    {
        \Log::info('Stripe customer.subscription.trial_will_end', [
            'subscription' => $subscription->id,
            'trial_end'    => $subscription->trial_end,
        ]);

        $row = DB::table('subscriptions')
            ->where('stripe_sub_id', $subscription->id)
            ->first();

        if (!$row) return;

        $tenant = DB::table('tenants')->where('tenant_id', $row->tenant_id)->first();
        if (!$tenant) return;

        $trialEndDate = date('F j, Y', (int) $subscription->trial_end);
        $planLabel    = ucfirst($row->plan_type);
        $rate         = '$' . number_format($row->monthly_rate, 2);
        $numUsers     = $tenant->num_users ?? 1;
        $totalRate    = '$' . number_format($row->monthly_rate * $numUsers, 2);
        $name         = $tenant->primary_contact ?? $tenant->client_name;
        $billingPortalUrl = env('ABIRASIGN_APP_URL', 'https://dev.abirasign.com') . '/billing';

        $html = "
            <div style='font-family:sans-serif;max-width:600px;color:#111827;'>
                <div style='margin-bottom:24px;'>
                    <span style='font-size:20px;font-weight:700;'>Abira<span style='color:#0E7490;'>Sign</span></span>
                </div>
                <h2 style='font-size:20px;font-weight:700;color:#111827;margin-bottom:10px;'>Your free trial ends in 3 days</h2>
                <p style='font-size:15px;color:#374151;line-height:1.7;'>Hi " . e($name) . ", your 14-day free trial of AbiraSign <strong>{$planLabel}</strong> ends on <strong>{$trialEndDate}</strong>.</p>
                <p style='font-size:15px;color:#374151;line-height:1.7;'>On that date, your card on file will automatically be charged <strong>{$totalRate}/month</strong> ({$numUsers} user seat" . ($numUsers > 1 ? 's' : '') . " × {$rate}/mo).</p>
                <div style='background:#f0f9ff;border:1px solid #bae6fd;border-radius:8px;padding:16px;margin:20px 0;font-size:14px;color:#0c4a6e;'>
                    <strong>Note:</strong> HIPAA compliance and Business Associate Agreements are not available on trial accounts. Upgrade to Professional after your trial to enable HIPAA features.
                </div>
                <p style='font-size:14px;color:#374151;'>If you'd like to cancel before being charged, you can do so from your billing page.</p>
                <div style='margin:28px 0;'>
                    <a href='{$billingPortalUrl}' style='background:#534AB7;color:#fff;padding:14px 28px;border-radius:8px;text-decoration:none;font-weight:600;font-size:15px;'>Manage your subscription →</a>
                </div>
                <hr style='border:none;border-top:1px solid #E5E7EB;margin:24px 0;'>
                <p style='font-size:12px;color:#9CA3AF;'>© " . date('Y') . " BrightNet Technologies LLC, DBA AbiraSign</p>
            </div>
        ";

        try {
            Mail::html($html, function ($mail) use ($tenant, $name) {
                $mail->to($tenant->primary_email, $name)
                     ->subject('[AbiraSign] Your free trial ends in 3 days');
            });
            \Log::info('Trial ending reminder sent', ['email' => $tenant->primary_email]);
        } catch (\Exception $e) {
            \Log::error('Trial ending reminder failed', ['error' => $e->getMessage()]);
        }
    }

    // ── Payment succeeded — send receipt ──────────────────────────────────────
    private function handlePaymentSucceeded($invoice)
    {
        // Skip $0 invoices (trial start, etc.)
        if ($invoice->amount_paid === 0) return;

        $row = DB::table('subscriptions')
            ->where('stripe_customer_id', $invoice->customer)
            ->whereIn('status', ['active', 'trialing', 'past_due'])
            ->first();

        if (!$row) return;

        $tenant = DB::table('tenants')->where('tenant_id', $row->tenant_id)->first();
        if (!$tenant) return;

        // If previously suspended, reactivate
        if ($row->status === 'past_due') {
            DB::table('subscriptions')
                ->where('id', $row->id)
                ->update(['status' => 'active', 'updated_at' => now()]);

            DB::table('tenants')
                ->where('tenant_id', $row->tenant_id)
                ->update(['status' => 'active', 'updated_at' => now()]);

            \Log::info('Subscription reactivated after successful payment', [
                'tenant_id' => $row->tenant_id,
            ]);
        }

        $amount      = '$' . number_format($invoice->amount_paid / 100, 2);
        $name        = $tenant->primary_contact ?? $tenant->client_name;
        $invoiceUrl  = $invoice->hosted_invoice_url ?? null;
        $invoicePdf  = $invoice->invoice_pdf ?? null;
        $periodStart = date('F j, Y', $invoice->period_start);
        $periodEnd   = date('F j, Y', $invoice->period_end);

        $html = "
            <div style='font-family:sans-serif;max-width:600px;color:#111827;'>
                <div style='margin-bottom:24px;'>
                    <span style='font-size:20px;font-weight:700;'>Abira<span style='color:#0E7490;'>Sign</span></span>
                </div>
                <h2 style='font-size:20px;font-weight:700;color:#111827;margin-bottom:10px;'>Payment receipt</h2>
                <p style='font-size:15px;color:#374151;line-height:1.7;'>Hi " . e($name) . ", thank you — your payment of <strong>{$amount}</strong> has been received.</p>
                <table style='width:100%;border-collapse:collapse;margin:20px 0;font-size:14px;'>
                    <tr><td style='padding:8px 0;color:#6B7280;width:140px;'>Amount</td><td style='padding:8px 0;font-weight:600;'>{$amount}</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Period</td><td style='padding:8px 0;'>{$periodStart} – {$periodEnd}</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Plan</td><td style='padding:8px 0;'>" . ucfirst($row->plan_type) . "</td></tr>
                </table>
                " . ($invoiceUrl ? "<p style='margin-top:20px;'><a href='{$invoiceUrl}' style='background:#534AB7;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;'>View invoice →</a>" . ($invoicePdf ? " &nbsp;<a href='{$invoicePdf}' style='color:#534AB7;font-size:14px;'>Download PDF</a>" : '') . "</p>" : '') . "
                <hr style='border:none;border-top:1px solid #E5E7EB;margin:24px 0;'>
                <p style='font-size:12px;color:#9CA3AF;'>© " . date('Y') . " BrightNet Technologies LLC, DBA AbiraSign</p>
            </div>
        ";

        try {
            Mail::html($html, function ($mail) use ($tenant, $name) {
                $mail->to($tenant->primary_email, $name)
                     ->subject('[AbiraSign] Payment receipt — thank you');
            });
        } catch (\Exception $e) {
            \Log::error('Receipt email failed', ['error' => $e->getMessage()]);
        }
    }

    // ── Payment failed — suspend immediately ──────────────────────────────────
    private function handlePaymentFailed($invoice)
    {
        \Log::warning('Stripe invoice.payment_failed', [
            'customer'      => $invoice->customer,
            'amount_due'    => $invoice->amount_due,
            'attempt_count' => $invoice->attempt_count,
        ]);

        $subscription = DB::table('subscriptions')
            ->where('stripe_customer_id', $invoice->customer)
            ->whereIn('status', ['active', 'trialing', 'past_due'])
            ->first();

        if (!$subscription) return;

        // Suspend subscription and tenant immediately
        DB::table('subscriptions')
            ->where('id', $subscription->id)
            ->update(['status' => 'past_due', 'updated_at' => now()]);

        DB::table('tenants')
            ->where('tenant_id', $subscription->tenant_id)
            ->update(['status' => 'suspended', 'updated_at' => now()]);

        $tenant = DB::table('tenants')
            ->where('tenant_id', $subscription->tenant_id)
            ->first();

        if (!$tenant) return;

        $amount       = '$' . number_format($invoice->amount_due / 100, 2);
        $attemptCount = $invoice->attempt_count ?? 1;
        $name         = $tenant->primary_contact ?? $tenant->client_name;
        $billingUrl   = env('ABIRASIGN_APP_URL', 'https://dev.abirasign.com') . '/billing';

        $html = "
            <div style='font-family:sans-serif;max-width:600px;color:#111827;'>
                <div style='margin-bottom:24px;'>
                    <span style='font-size:20px;font-weight:700;'>Abira<span style='color:#0E7490;'>Sign</span></span>
                </div>
                <h2 style='color:#dc2626;margin-bottom:4px;'>Payment failed — account suspended</h2>
                <p style='color:#6B7280;font-size:13px;margin-top:0;'>Attempt {$attemptCount}</p>
                <p style='font-size:14px;color:#374151;'>Hi " . e($name) . ", we were unable to process your payment of <strong>{$amount}</strong>.</p>
                <p style='font-size:14px;color:#374151;'>Your account has been <strong>suspended</strong> until payment is resolved. Please update your payment method to restore access.</p>
                <div style='background:#fee2e2;border:1px solid #fca5a5;border-radius:8px;padding:12px 16px;margin:16px 0;font-size:13px;color:#991b1b;'>
                    ⚠ Access to your AbiraSign account is currently restricted. Update your payment method to restore full access immediately.
                </div>
                <p style='margin-top:20px;'>
                    <a href='{$billingUrl}' style='background:#dc2626;color:#fff;padding:12px 24px;border-radius:6px;text-decoration:none;font-size:14px;font-weight:600;'>Update payment method →</a>
                </p>
                <hr style='border:none;border-top:1px solid #E5E7EB;margin:24px 0;'>
                <p style='font-size:12px;color:#9CA3AF;'>© " . date('Y') . " BrightNet Technologies LLC, DBA AbiraSign</p>
            </div>
        ";

        try {
            Mail::html($html, function ($mail) use ($tenant, $name) {
                $mail->to($tenant->primary_email, $name)
                     ->subject('[AbiraSign] Payment failed — account suspended');
            });
            \Log::info('Payment failed + suspension notification sent', [
                'tenant_id' => $subscription->tenant_id,
            ]);
        } catch (\Exception $e) {
            \Log::error('Payment failed notification error', ['error' => $e->getMessage()]);
        }
    }

    // ── Trial converted to paid ────────────────────────────────────────────────
    private function sendTrialConvertedEmail(string $tenantId): void
    {
        $tenant = DB::table('tenants')->where('tenant_id', $tenantId)->first();
        if (!$tenant) return;

        $sub  = DB::table('subscriptions')->where('tenant_id', $tenantId)->first();
        $name = $tenant->primary_contact ?? $tenant->client_name;

        $html = "
            <div style='font-family:sans-serif;max-width:600px;color:#111827;'>
                <div style='margin-bottom:24px;'>
                    <span style='font-size:20px;font-weight:700;'>Abira<span style='color:#0E7490;'>Sign</span></span>
                </div>
                <h2 style='font-size:20px;font-weight:700;color:#111827;'>Welcome aboard!</h2>
                <p style='font-size:15px;color:#374151;line-height:1.7;'>Hi " . e($name) . ", your AbiraSign trial has ended and your paid subscription is now active. Thank you for choosing AbiraSign!</p>
                <p style='font-size:14px;color:#374151;'>Your plan: <strong>" . ucfirst($sub->plan_type ?? '—') . "</strong></p>
                <hr style='border:none;border-top:1px solid #E5E7EB;margin:24px 0;'>
                <p style='font-size:12px;color:#9CA3AF;'>© " . date('Y') . " BrightNet Technologies LLC, DBA AbiraSign</p>
            </div>
        ";

        try {
            Mail::html($html, function ($mail) use ($tenant, $name) {
                $mail->to($tenant->primary_email, $name)
                     ->subject('[AbiraSign] Your trial has ended — subscription active');
            });
        } catch (\Exception $e) {
            \Log::error('Trial converted email failed', ['error' => $e->getMessage()]);
        }
    }

    // ── Trial cancelled ────────────────────────────────────────────────────────
    private function sendTrialCancelledEmail($tenant, $subscription): void
    {
        $name = $tenant->primary_contact ?? $tenant->client_name;

        $html = "
            <div style='font-family:sans-serif;max-width:600px;color:#111827;'>
                <div style='margin-bottom:24px;'>
                    <span style='font-size:20px;font-weight:700;'>Abira<span style='color:#0E7490;'>Sign</span></span>
                </div>
                <h2 style='font-size:20px;font-weight:700;color:#111827;'>Thanks for trying AbiraSign</h2>
                <p style='font-size:15px;color:#374151;line-height:1.7;'>Hi " . e($name) . ", your trial has been cancelled and your card will not be charged. We're sorry to see you go!</p>
                <p style='font-size:14px;color:#374151;'><strong>Important:</strong> You have <strong>30 days</strong> to retrieve any documents you sent during your trial. After 30 days, we reserve the right to permanently remove them.</p>
                <p style='font-size:14px;color:#374151;'>We'd love to hear your feedback — reply to this email to let us know how we could improve.</p>
                <p style='font-size:14px;color:#374151;'>You can resubscribe at any time at <a href='" . env('APP_URL', 'https://abirasign.com') . "/signup' style='color:#534AB7;'>abirasign.com/signup</a>.</p>
                <hr style='border:none;border-top:1px solid #E5E7EB;margin:24px 0;'>
                <p style='font-size:12px;color:#9CA3AF;'>© " . date('Y') . " BrightNet Technologies LLC, DBA AbiraSign</p>
            </div>
        ";

        try {
            Mail::html($html, function ($mail) use ($tenant, $name) {
                $mail->to($tenant->primary_email, $name)
                     ->replyTo('hello@abirasign.com', 'AbiraSign')
                     ->subject('[AbiraSign] Your trial has been cancelled');
            });
        } catch (\Exception $e) {
            \Log::error('Trial cancelled email failed', ['error' => $e->getMessage()]);
        }
    }

    // ── Subscription cancelled (paid) ─────────────────────────────────────────
    private function sendSubscriptionCancelledEmail($tenant, $subscription): void
    {
        $name    = $tenant->primary_contact ?? $tenant->client_name;
        $endDate = $subscription->next_billing
            ? \Carbon\Carbon::parse($subscription->next_billing)->format('F j, Y')
            : 'immediately';

        $html = "
            <div style='font-family:sans-serif;max-width:600px;color:#111827;'>
                <div style='margin-bottom:24px;'>
                    <span style='font-size:20px;font-weight:700;'>Abira<span style='color:#0E7490;'>Sign</span></span>
                </div>
                <h2 style='font-size:20px;font-weight:700;color:#111827;'>Subscription cancelled</h2>
                <p style='font-size:15px;color:#374151;line-height:1.7;'>Hi " . e($name) . ", your AbiraSign subscription has been cancelled. You retain full access until <strong>{$endDate}</strong>.</p>
                <p style='font-size:14px;color:#374151;'><strong>Document retrieval:</strong> You have <strong>30 days from your access end date</strong> to retrieve any sent documents. After 30 days, we reserve the right to permanently remove them.</p>
                <p style='font-size:14px;color:#374151;'>We'd love to hear your feedback — reply to this email to let us know how we could improve.</p>
                <p style='font-size:14px;color:#374151;'>You can resubscribe at any time at <a href='" . env('APP_URL', 'https://abirasign.com') . "/signup' style='color:#534AB7;'>abirasign.com/signup</a>.</p>
                <hr style='border:none;border-top:1px solid #E5E7EB;margin:24px 0;'>
                <p style='font-size:12px;color:#9CA3AF;'>© " . date('Y') . " BrightNet Technologies LLC, DBA AbiraSign</p>
            </div>
        ";

        try {
            Mail::html($html, function ($mail) use ($tenant, $name) {
                $mail->to($tenant->primary_email, $name)
                     ->replyTo('hello@abirasign.com', 'AbiraSign')
                     ->subject('[AbiraSign] Subscription cancelled — access ends ' . ($endDate ?? 'soon'));
            });
        } catch (\Exception $e) {
            \Log::error('Cancellation email failed', ['error' => $e->getMessage()]);
        }
    }

    // ── Quote payment ──────────────────────────────────────────────────────────
    private function handleQuotePayment($session): void
    {
        $metadata    = $session->metadata;
        $quoteToken  = $metadata->quote_token  ?? null;
        $quoteId     = $metadata->quote_id     ?? '—';
        $clientName  = $metadata->client_name  ?? '—';
        $contactName = $metadata->contact_name ?? '—';
        $billingTerm = $metadata->billing_term ?? '—';
        $hipaa       = ($metadata->hipaa ?? '0') === '1';

        if ($quoteToken) {
            DB::table('quotes')
                ->where('token', $quoteToken)
                ->update(['payment_method' => 'stripe']);
        }

        $this->sendQuotePaymentNotification(
            $quoteId, $clientName, $contactName, $billingTerm,
            $session->amount_total, $hipaa, $session->customer
        );
    }
    // ── PAYG envelope charge receipt ──────────────────────────────────────────
    private function handlePaymentIntentSucceeded($intent)
    {
        // Only handle PAYG envelope charges — skip subscription-related intents
        $metadata = $intent->metadata;
        if (empty($metadata->tenant_id) || empty($metadata->user_email)) return;

        \Log::info('PAYG payment_intent.succeeded', [
            'intent_id' => $intent->id,
            'tenant_id' => $metadata->tenant_id,
            'amount'    => $intent->amount,
        ]);

        $tenant = DB::table('tenants')
            ->where('tenant_id', $metadata->tenant_id)
            ->first();

        if (!$tenant) return;

        $name   = $tenant->primary_contact ?? $tenant->client_name;
        $amount = '$' . number_format($intent->amount / 100, 2);
        $date   = date('F j, Y');

        $html = "
            <div style='font-family:sans-serif;max-width:600px;color:#111827;'>
                <div style='margin-bottom:24px;'>
                    <span style='font-size:20px;font-weight:700;'>Abira<span style='color:#0E7490;'>Sign</span></span>
                </div>
                <h2 style='font-size:20px;font-weight:700;color:#111827;margin-bottom:10px;'>Envelope sent — receipt</h2>
                <p style='font-size:15px;color:#374151;line-height:1.7;'>Hi " . e($name) . ", your envelope has been sent and your card has been charged.</p>
                <table style='width:100%;border-collapse:collapse;margin:20px 0;font-size:14px;'>
                    <tr><td style='padding:8px 0;color:#6B7280;width:140px;'>Amount</td><td style='padding:8px 0;font-weight:600;'>{$amount}</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Date</td><td style='padding:8px 0;'>{$date}</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Description</td><td style='padding:8px 0;'>1 envelope sent via AbiraSign</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Sent by</td><td style='padding:8px 0;'>" . e($metadata->user_email ?? '—') . "</td></tr>
                </table>
                <p style='font-size:13px;color:#6B7280;'>Questions? Reply to this email or contact <a href='mailto:support@abirasign.com' style='color:#534AB7;'>support@abirasign.com</a>.</p>
                <hr style='border:none;border-top:1px solid #E5E7EB;margin:24px 0;'>
                <p style='font-size:12px;color:#9CA3AF;'>© " . date('Y') . " BrightNet Technologies LLC, DBA AbiraSign</p>
            </div>
        ";

        try {
            Mail::html($html, function ($mail) use ($tenant, $name, $metadata) {
                $mail->to($tenant->primary_email, $name)
                     ->subject('[AbiraSign] Receipt — $10.00 envelope sent');
            });
            \Log::info('PAYG envelope receipt sent', ['email' => $tenant->primary_email]);
        } catch (\Exception $e) {
            \Log::error('PAYG receipt email failed', ['error' => $e->getMessage()]);
        }
    }
    // ── Helpers ───────────────────────────────────────────────────────────────
    private function planFromPriceId(?string $priceId): ?string
    {
        if (!$priceId) return null;

        $map = [
            env('STRIPE_PRICE_STARTER_MONTHLY') => 'starter',
            env('STRIPE_PRICE_STARTER_ANNUAL')  => 'starter',
            env('STRIPE_PRICE_PRO_MONTHLY')     => 'professional',
            env('STRIPE_PRICE_PRO_ANNUAL')      => 'professional',
        ];

        return $map[$priceId] ?? null;
    }

    private function monthlyRateForPlan(string $plan, string $billingTerm): float
    {
        return match([$plan, $billingTerm]) {
            ['starter',      'monthly'] => 45.00,
            ['starter',      'annual']  => 40.50,
            ['professional', 'monthly'] => 75.00,
            ['professional', 'annual']  => 67.50,
            default                     => 0.00,
        };
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
                CURLOPT_TIMEOUT => 30,
            ]);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            $decoded = json_decode($response, true);

            if ($httpCode === 201) {
                \Log::info('Auto-provisioning triggered successfully', [
                    'email'     => $data['email'],
                    'tenant_id' => $decoded['tenant_id'] ?? null,
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

    private function sendLeadNotification(string $name, string $email, string $plan, string $billing, string $practice, string $phone, string $practiceType, int $numUsers = 1): void
    {
        $to        = config('mail.contact_to', 'hello@abirasign.com');
        $planLabel = match($plan) {
            'payg'         => 'Pay as you go',
            'starter'      => 'Starter',
            'professional' => 'Professional',
            default        => ucfirst($plan),
        };

        $html = "
            <div style='font-family:sans-serif;max-width:600px;color:#111827;'>
                <h2 style='color:#0E7490;margin-bottom:4px;'>New signup — {$planLabel}</h2>
                <table style='width:100%;border-collapse:collapse;margin:20px 0;font-size:14px;'>
                    <tr><td style='padding:8px 0;color:#6B7280;width:140px;'>Name</td><td style='padding:8px 0;font-weight:600;'>" . e($name) . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Email</td><td style='padding:8px 0;'>" . e($email) . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Phone</td><td style='padding:8px 0;'>" . e($phone) . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Business</td><td style='padding:8px 0;'>" . e($practice) . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Plan</td><td style='padding:8px 0;font-weight:600;'>" . e($planLabel) . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Users</td><td style='padding:8px 0;'>{$numUsers}</td></tr>
                </table>
            </div>
        ";

        try {
            Mail::html($html, function ($mail) use ($to, $name, $email, $planLabel) {
                $mail->to($to)
                     ->replyTo($email, $name)
                     ->subject('[AbiraSign Signup] ' . $planLabel . ' — ' . $name);
            });
        } catch (\Exception $e) {
            \Log::error('Lead notification failed', ['error' => $e->getMessage()]);
        }
    }

    private function sendPaymentNotification(string $name, string $email, string $plan, string $billing, string $practice, string $phone, string $type, int $numUsers, ?string $customerId, ?string $subscriptionId): void
    {
        $to        = config('mail.contact_to', 'hello@abirasign.com');
        $planLabel = ucfirst($plan);
        $billingLabel = $billing === 'annual' ? 'Annual (10% discount)' : 'Monthly';

        $html = "
            <div style='font-family:sans-serif;max-width:600px;color:#111827;'>
                <h2 style='color:#0E7490;'>💳 Trial started — {$planLabel}</h2>
                <p style='color:#6B7280;font-size:13px;'>14-day trial · auto-provisioning triggered</p>
                <table style='width:100%;border-collapse:collapse;margin:20px 0;font-size:14px;'>
                    <tr><td style='padding:8px 0;color:#6B7280;width:140px;'>Name</td><td style='padding:8px 0;font-weight:600;'>" . e($name) . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Email</td><td style='padding:8px 0;'>" . e($email) . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Business</td><td style='padding:8px 0;'>" . e($practice) . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Plan</td><td style='padding:8px 0;font-weight:600;'>" . e($planLabel) . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Billing</td><td style='padding:8px 0;'>" . e($billingLabel) . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Users</td><td style='padding:8px 0;'>{$numUsers}</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Stripe Customer</td><td style='padding:8px 0;font-size:12px;'>" . e($customerId ?? '—') . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Subscription</td><td style='padding:8px 0;font-size:12px;'>" . e($subscriptionId ?? '—') . "</td></tr>
                </table>
            </div>
        ";

        try {
            Mail::html($html, function ($mail) use ($to, $name, $email, $planLabel) {
                $mail->to($to)
                     ->replyTo($email, $name)
                     ->subject('[AbiraSign] Trial started — ' . $planLabel . ' · ' . $name);
            });
        } catch (\Exception $e) {
            \Log::error('Payment notification failed', ['error' => $e->getMessage()]);
        }
    }

    private function sendQuotePaymentNotification(string $quoteId, string $clientName, string $contactName, string $billingTerm, int $amountTotal, bool $hipaa, ?string $customerId): void
    {
        $to     = config('mail.contact_to', 'hello@abirasign.com');
        $amount = '$' . number_format($amountTotal / 100, 2);
        $term   = $billingTerm === 'triennial' ? 'Triennial (3 years)' : 'Annual (1 year)';
        $hipaaNote = $hipaa
            ? "<div style='background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;padding:12px 16px;margin-top:16px;font-size:13px;color:#92400e;'><strong>⚠ HIPAA required</strong> — BAA must be executed before provisioning.</div>"
            : "<div style='background:#dcfce7;border:1px solid #86efac;border-radius:8px;padding:12px 16px;margin-top:16px;font-size:13px;color:#166534;'><strong>✓ No HIPAA</strong> — account can be provisioned immediately.</div>";

        $html = "
            <div style='font-family:sans-serif;max-width:600px;color:#111827;'>
                <h2 style='color:#534AB7;'>💳 Enterprise quote payment received</h2>
                <p style='color:#6B7280;font-size:13px;'>Quote ID: {$quoteId}</p>
                <table style='width:100%;border-collapse:collapse;margin:20px 0;font-size:14px;'>
                    <tr><td style='padding:8px 0;color:#6B7280;width:140px;'>Client</td><td style='padding:8px 0;font-weight:600;'>" . e($clientName) . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Contact</td><td style='padding:8px 0;'>" . e($contactName) . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Billing term</td><td style='padding:8px 0;'>" . e($term) . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Amount paid</td><td style='padding:8px 0;font-weight:600;color:#534AB7;'>{$amount}</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Stripe Customer</td><td style='padding:8px 0;font-size:12px;'>" . e($customerId ?? '—') . "</td></tr>
                </table>
                {$hipaaNote}
                <p style='margin-top:20px;font-size:14px;'>Log in to the <a href='https://admin-dev.abirasign.com/quotes' style='color:#534AB7;'>admin portal</a> to provision their account.</p>
                <p style='font-size:12px;color:#9CA3AF;'>© " . date('Y') . " BrightNet Technologies LLC, DBA AbiraSign</p>
            </div>
        ";

        try {
            Mail::html($html, function ($mail) use ($to, $quoteId, $clientName) {
                $mail->to($to)
                     ->subject('[AbiraSign] Enterprise payment received — ' . $quoteId . ' · ' . $clientName);
            });
        } catch (\Exception $e) {
            \Log::error('Quote payment notification failed', ['error' => $e->getMessage()]);
        }
    }
}
