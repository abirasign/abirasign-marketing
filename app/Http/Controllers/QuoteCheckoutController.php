<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class QuoteCheckoutController extends Controller
{
    public function checkout(string $token)
    {
        $quote = DB::table('quotes')->where('token', $token)->first();

        if (!$quote) {
            abort(404);
        }

        if ($quote->status !== 'accepted') {
            abort(403, 'This quote is not available for payment.');
        }

        Stripe::setApiKey(env('STRIPE_SECRET'));

        // Calculate charge amount
        $annualTotal    = (float) $quote->annual_total;
        $billingTerm    = $quote->billing_term;
        $chargeAmount   = $billingTerm === 'triennial'
            ? $annualTotal * 3
            : $annualTotal;
        $amountInCents  = (int) round($chargeAmount * 100);
        $termLabel      = $billingTerm === 'triennial' ? '3-year' : 'Annual';
        $description    = "AbiraSign Enterprise ({$termLabel}) — {$quote->client_name}";

        $successUrl = url('/quote-checkout/success') . '?session_id={CHECKOUT_SESSION_ID}&token=' . $token;
        $cancelUrl  = env('ADMIN_URL', 'https://admin-dev.abirasign.com') . '/quote/' . $token;

        $session = StripeSession::create([
            'payment_method_types' => ['card', 'us_bank_account'],
            'mode'                 => 'payment',
            'customer_email'       => $quote->contact_email,
            'line_items'           => [[
                'price_data' => [
                    'currency'     => 'usd',
                    'unit_amount'  => $amountInCents,
                    'product_data' => [
                        'name'        => $description,
                        'description' => "Quote ID: {$quote->quote_id}",
                    ],
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'type'         => 'quote',
                'quote_token'  => $token,
                'quote_id'     => $quote->quote_id,
                'client_name'  => $quote->client_name,
                'contact_name' => $quote->contact_name,
                'billing_term' => $billingTerm,
                'hipaa'        => $quote->hipaa_required ? '1' : '0',
            ],
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
        ]);

        return redirect($session->url);
    }

    public function success(Request $request)
    {
        $token     = $request->query('token');
        $sessionId = $request->query('session_id');

        $quote = $token ? DB::table('quotes')->where('token', $token)->first() : null;

        return view('quote-checkout-success', compact('quote', 'sessionId'));
    }
}
