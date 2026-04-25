<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class SignupController extends Controller
{
    public function show(Request $request)
    {
        $plan    = in_array($request->query('plan'), ['payg','starter','professional','enterprise'])
                   ? $request->query('plan') : 'payg';
        $billing = $request->query('billing') === 'monthly' ? 'monthly' : 'annual';
        return view('signup.index', compact('plan', 'billing'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'practice_name' => 'required|string|max:255',
            'contact_name'  => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:20',
            'plan'          => 'required|in:payg,starter,professional,enterprise',
            'billing'       => 'required|in:monthly,annual',
            'practice_type' => 'required|in:healthcare,legal,real_estate,hr,fitness,general',
            'num_users' => 'required|integer|min:1|max:9999',
            'hipaa_required' => 'sometimes|boolean',
        ]);

        // Store lead data in session for use after Stripe redirect
        session([
            'signup_name'         => $request->contact_name,
            'signup_email'        => $request->email,
            'signup_plan'         => $request->plan,
            'signup_billing'      => $request->billing,
            'signup_practice'     => $request->practice_name,
            'signup_phone'        => $request->phone,
            'signup_practice_type'=> $request->practice_type,
            'signup_num_users' => $request->num_users,
            'signup_hipaa' => $request->boolean('hipaa_required'),
        ]);

        // PAYG — no Stripe checkout, go straight to thank-you
        if ($request->plan === 'payg') {
            $this->sendLeadNotification(
            $request->contact_name,
            $request->email,
            $request->plan,
            $request->billing,
            $request->practice_name,
            $request->phone,
            $request->practice_type,
            $request->num_users
        );
    return redirect()->route('signup.thankyou');
}

        // Starter / Professional — redirect to Stripe Checkout
        $priceId = $this->getPriceId($request->plan, $request->billing);

        if (!$priceId) {
            return back()->withErrors(['plan' => 'Unable to find pricing for the selected plan. Please try again.']);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $meta = [
            'plan'          => $request->plan,
            'billing'       => $request->billing,
            'practice_name' => $request->practice_name,
            'contact_name'  => $request->contact_name,
            'phone'         => $request->phone,
            'practice_type' => $request->practice_type,
            'num_users' => $request->num_users,
            'hipaa_required' => $request->boolean('hipaa_required') ? '1' : '0',
];

$checkoutSession = StripeSession::create([
    'mode'                 => 'subscription',
    'payment_method_types' => ['card', 'us_bank_account'],
    'customer_email'       => $request->email,
    'metadata'             => $meta,
    'line_items' => [[
    'price'    => $priceId,
    'quantity' => (int) $request->num_users,
]],
    'subscription_data'    => [
        'metadata' => $meta,
    ],
            'success_url'          => route('signup.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'           => route('signup') . '?plan=' . $request->plan . '&billing=' . $request->billing,
            'allow_promotion_codes'=> true,
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
            $stripeSession = StripeSession::retrieve($sessionId);
            session(['signup_stripe_session' => $sessionId]);
        } catch (\Exception $e) {
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
        <div style='font-family: sans-serif; max-width: 600px; color: #111827;'>
            <h2 style='color: #0E7490; margin-bottom: 4px;'>New signup — {$planLabel}</h2>
            <p style='color: #6B7280; font-size: 13px; margin-top: 0;'>Submitted via abirasign.com/signup</p>
            <table style='width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 14px;'>
                <tr><td style='padding: 8px 0; color: #6B7280; width: 140px;'>Name</td><td style='padding: 8px 0; font-weight: 600;'>" . e($name) . "</td></tr>
                <tr><td style='padding: 8px 0; color: #6B7280;'>Email</td><td style='padding: 8px 0;'><a href='mailto:" . e($email) . "' style='color: #0E7490;'>" . e($email) . "</a></td></tr>
                <tr><td style='padding: 8px 0; color: #6B7280;'>Phone</td><td style='padding: 8px 0;'>" . e($phone) . "</td></tr>
                <tr><td style='padding: 8px 0; color: #6B7280;'>Business</td><td style='padding: 8px 0;'>" . e($practice) . "</td></tr>
                <tr><td style='padding: 8px 0; color: #6B7280;'>Industry</td><td style='padding: 8px 0;'>" . e(ucfirst(str_replace('_', ' ', $practiceType))) . "</td></tr>
                <tr><td style='padding: 8px 0; color: #6B7280;'>Staff users</td><td style='padding: 8px 0;'>" . (int)$numUsers . "</td></tr>
                <tr><td style='padding: 8px 0; color: #6B7280;'>Plan</td><td style='padding: 8px 0; font-weight: 600; color: #0E7490;'>" . e($planLabel) . "</td></tr>
                <tr><td style='padding: 8px 0; color: #6B7280;'>Billing</td><td style='padding: 8px 0;'>" . e($billingLabel) . "</td></tr>
            </table>
            <p style='font-size: 12px; color: #9CA3AF; margin-top: 24px;'>Reply directly to this email to contact " . e($name) . ".</p>
        </div>
    ";

    try {
        Mail::html($html, function ($mail) use ($to, $name, $email, $planLabel) {
            $mail->to($to)
                 ->replyTo($email, $name)
                 ->subject('[AbiraSign Signup] ' . $planLabel . ' — ' . $name);
        });
        \Log::info('Signup lead notification sent', ['email' => $email, 'plan' => $planLabel]);
    } catch (\Exception $e) {
        \Log::error('Signup lead notification failed', ['error' => $e->getMessage()]);
    }
}
}
