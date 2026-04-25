<?php
namespace App\Http\Controllers;

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
        ]);

        // PAYG — no Stripe checkout, go straight to thank-you
        if ($request->plan === 'payg') {
            return redirect()->route('signup.thankyou');
        }

        // Starter / Professional — redirect to Stripe Checkout
        $priceId = $this->getPriceId($request->plan, $request->billing);

        if (!$priceId) {
            return back()->withErrors(['plan' => 'Unable to find pricing for the selected plan. Please try again.']);
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $checkoutSession = StripeSession::create([
            'mode'                 => 'subscription',
            'payment_method_types' => ['card', 'us_bank_account'],
            'customer_email'       => $request->email,
            'line_items'           => [[
                'price'    => $priceId,
                'quantity' => 1,
            ]],
            'subscription_data'    => [
                'metadata' => [
                    'plan'          => $request->plan,
                    'billing'       => $request->billing,
                    'practice_name' => $request->practice_name,
                    'contact_name'  => $request->contact_name,
                    'phone'         => $request->phone,
                    'practice_type' => $request->practice_type,
                ],
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
}
