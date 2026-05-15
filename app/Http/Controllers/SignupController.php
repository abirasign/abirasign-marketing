<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\SetupIntent;

class SignupController extends Controller
{
    public function show(Request $request)
    {
        $plan    = in_array($request->query('plan'), ['payg','starter','professional','enterprise'])
                   ? $request->query('plan') : 'payg';
        $billing = $request->query('billing') === 'monthly' ? 'monthly' : 'annual';

        $today      = now()->toDateString();
        $currentTos = DB::table('policy_versions')
            ->where('type', 'tos')
            ->where('effective_date', '<=', $today)
            ->orderByDesc('effective_date')->orderByDesc('id')
            ->first();
        $currentPp = DB::table('policy_versions')
            ->where('type', 'pp')
            ->where('effective_date', '<=', $today)
            ->orderByDesc('effective_date')->orderByDesc('id')
            ->first();

        return view('signup.index', compact('plan', 'billing', 'currentTos', 'currentPp'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'practice_name'   => 'required|string|max:255',
            'contact_name'    => 'required|string|max:255',
            'email'           => 'required|email|max:255',
            'phone'           => 'required|string|max:20',
            'plan'            => 'required|in:payg,starter,professional,enterprise',
            'billing'         => 'required|in:monthly,annual',
            'practice_type'   => 'required|in:healthcare,legal,real_estate,hr,fitness,general',
            'num_users'       => 'required|integer|min:1|max:9999',
            'hipaa_required'  => 'sometimes|boolean',
            'accept_policies' => 'accepted',
        ]);

        $email = strtolower(trim($request->email));

        $effectiveBilling = in_array($request->plan, ['starter', 'professional']) ? 'monthly' : $request->billing;

        session([
            'signup_name'          => $request->contact_name,
            'signup_email'         => $email,
            'signup_plan'          => $request->plan,
            'signup_billing'       => $effectiveBilling,
            'signup_practice'      => $request->practice_name,
            'signup_phone'         => $request->phone,
            'signup_practice_type' => $request->practice_type,
            'signup_num_users'     => $request->num_users,
            'signup_hipaa'         => $request->boolean('hipaa_required'),
        ]);

        $this->writePolicyAck($email, $request->ip());

        // Duplicate email check for all paid plans
        if ($request->plan !== 'payg') {
            $exists = DB::table('tenants')
                ->where('primary_email', $email)
                ->whereIn('status', ['active', 'suspended'])
                ->exists();

            if ($exists) {
                return back()->withErrors([
                    'email' => 'An account with this email address already exists. Please log in or use a different email.'
                ])->withInput();
            }
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $meta = [
            'plan'           => $request->plan,
            'billing'        => $request->billing,
            'practice_name'  => $request->practice_name,
            'contact_name'   => $request->contact_name,
            'phone'          => $request->phone,
            'practice_type'  => $request->practice_type,
            'num_users'      => $request->num_users,
            'hipaa_required' => $request->boolean('hipaa_required') ? '1' : '0',
            'skip_trial'     => ($request->plan === 'professional' && $request->input('skip_trial') === '1') ? '1' : '0',
        ];

        // PAYG — Setup Intent to save card, no immediate charge
        if ($request->plan === 'payg') {
            // Duplicate email check for PAYG too
            $exists = DB::table('tenants')
                ->where('primary_email', $email)
                ->whereIn('status', ['active', 'suspended'])
                ->exists();

            if ($exists) {
                return back()->withErrors([
                    'email' => 'An account with this email address already exists. Please log in or use a different email.'
                ])->withInput();
            }

            $checkoutSession = StripeSession::create([
                'mode'                 => 'setup',
                'payment_method_types' => ['card'],
                'customer_email'       => $email,
                'metadata'             => $meta,
                'success_url'          => route('signup.success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url'           => route('signup') . '?plan=payg',
            ]);

            return redirect($checkoutSession->url);
        }

        // Starter / Professional — subscription with 14-day trial
        // Trials always start on monthly billing to prevent accidental annual charges
        $trialBilling = 'monthly';
        $priceId = $this->getPriceId($request->plan, $trialBilling);

        if (!$priceId) {
            return back()->withErrors(['plan' => 'Unable to find pricing for the selected plan. Please try again.']);
        }

        $skipTrial = $request->plan === 'professional' && $request->input('skip_trial') === '1';

        $subscriptionData = $skipTrial
            ? ['metadata' => array_merge($meta, ['billing' => 'monthly'])]
            : ['trial_period_days' => 14, 'metadata' => array_merge($meta, ['billing' => 'monthly'])];

        $checkoutSession = StripeSession::create([
            'mode'                 => 'subscription',
            'payment_method_types' => ['card'],
            'customer_email'       => $email,
            'metadata'             => $meta,
            'line_items'           => [[
                'price'    => $priceId,
                'quantity' => (int) $request->num_users,
            ]],
            'subscription_data'     => $subscriptionData,
            'success_url'           => route('signup.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'            => route('signup') . '?plan=' . $request->plan . '&billing=' . $request->billing,
            'allow_promotion_codes' => true,
        ]);

        return redirect($checkoutSession->url);
    }

    public function success(Request $request)
    {
        $sessionId = $request->query('session_id');

        if (!$sessionId || !session('signup_name')) {
            return redirect()->route('signup');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $stripeSession = StripeSession::retrieve([
                'id'     => $sessionId,
                'expand' => ['setup_intent', 'subscription'],
            ]);

            session(['signup_stripe_session' => $sessionId]);

            // For PAYG: store setup intent details in session for webhook processing
            if ($stripeSession->mode === 'setup') {
                session([
                    'signup_stripe_customer'      => $stripeSession->customer,
                    'signup_stripe_setup_intent'  => $stripeSession->setup_intent?->id,
                    'signup_stripe_payment_method'=> $stripeSession->setup_intent?->payment_method,
                ]);
            }

            // For Starter/Pro: store subscription info
            if ($stripeSession->mode === 'subscription') {
                session([
                    'signup_stripe_customer'     => $stripeSession->customer,
                    'signup_stripe_subscription' => $stripeSession->subscription?->id,
                    'signup_trial_end'           => $stripeSession->subscription?->trial_end,
                    'signup_skip_trial'          => $stripeSession->subscription?->trial_end === null,
                ]);
            }

        } catch (\Exception $e) {
            \Log::error('Stripe session retrieval failed', ['error' => $e->getMessage()]);
            return redirect()->route('signup');
        }

        return redirect()->route('signup.thankyou');
    }

    public function thankYou()
    {
        if (!session('signup_name')) {
            return redirect()->route('signup');
        }
        return view('signup.thank-you');
    }

    private function getPriceId(string $plan, string $billing): ?string
    {
        $map = [
            'starter'      => [
                'monthly' => env('STRIPE_PRICE_STARTER_MONTHLY'),
                'annual'  => env('STRIPE_PRICE_STARTER_ANNUAL'),
            ],
            'professional' => [
                'monthly' => env('STRIPE_PRICE_PRO_MONTHLY'),
                'annual'  => env('STRIPE_PRICE_PRO_ANNUAL'),
            ],
        ];

        return $map[$plan][$billing] ?? null;
    }

    private function writePolicyAck(string $email, string $ip): void
    {
        $today = now()->toDateString();

        $currentTos = DB::table('policy_versions')
            ->where('type', 'tos')
            ->where('effective_date', '<=', $today)
            ->orderByDesc('effective_date')->orderByDesc('id')
            ->first();

        $currentPp = DB::table('policy_versions')
            ->where('type', 'pp')
            ->where('effective_date', '<=', $today)
            ->orderByDesc('effective_date')->orderByDesc('id')
            ->first();

        if (!$currentTos && !$currentPp) return;

        $exists = DB::table('policy_acknowledgements')
            ->where('email', $email)
            ->whereNull('tenant_id')
            ->where('tos_version_id', $currentTos->id ?? null)
            ->where('pp_version_id',  $currentPp->id  ?? null)
            ->exists();

        if ($exists) return;

        $now = now();
        DB::table('policy_acknowledgements')->insert([
            'email'           => $email,
            'tenant_id'       => null,
            'tos_version_id'  => $currentTos->id ?? null,
            'pp_version_id'   => $currentPp->id  ?? null,
            'ip_address'      => $ip,
            'acknowledged_at' => $now,
            'created_at'      => $now,
            'updated_at'      => $now,
        ]);
    }

    private function sendLeadNotification(string $name, string $email, string $plan, string $billing, string $practice, string $phone, string $practiceType, int $numUsers = 1): void
    {
        $to        = config('mail.contact_to', 'hello@abirasign.com');
        $planLabel = match($plan) {
            'payg'         => 'Pay as you go',
            'starter'      => 'Starter',
            'professional' => 'Professional',
            'enterprise'   => 'Enterprise',
            default        => ucfirst($plan),
        };
        $billingLabel = $billing === 'annual' ? 'Annual (10% discount)' : 'Monthly';

        $html = "
            <div style='font-family:sans-serif;max-width:600px;color:#111827;'>
                <h2 style='color:#0E7490;margin-bottom:4px;'>New signup — {$planLabel}</h2>
                <p style='color:#6B7280;font-size:13px;margin-top:0;'>Submitted via abirasign.com/signup</p>
                <table style='width:100%;border-collapse:collapse;margin:20px 0;font-size:14px;'>
                    <tr><td style='padding:8px 0;color:#6B7280;width:140px;'>Name</td><td style='padding:8px 0;font-weight:600;'>" . e($name) . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Email</td><td style='padding:8px 0;'><a href='mailto:" . e($email) . "' style='color:#0E7490;'>" . e($email) . "</a></td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Phone</td><td style='padding:8px 0;'>" . e($phone) . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Business</td><td style='padding:8px 0;'>" . e($practice) . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Industry</td><td style='padding:8px 0;'>" . e(ucfirst(str_replace('_', ' ', $practiceType))) . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Staff users</td><td style='padding:8px 0;'>" . (int)$numUsers . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Plan</td><td style='padding:8px 0;font-weight:600;color:#0E7490;'>" . e($planLabel) . "</td></tr>
                    <tr><td style='padding:8px 0;color:#6B7280;'>Billing</td><td style='padding:8px 0;'>" . e($billingLabel) . "</td></tr>
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
            \Log::error('Signup lead notification failed', ['error' => $e->getMessage()]);
        }
    }
}
