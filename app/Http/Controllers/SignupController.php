<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SignupController extends Controller
{
    public function show(Request $request)
    {
        $plan = $request->query('plan', 'payg');
        return view('signup.index', compact('plan'));
    }

    public function submit(Request $request)
    {
        $request->validate([
            'practice_name' => 'required|string|max:255',
            'contact_name'  => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:20',
            'plan'          => 'required|in:payg,starter,standard,enterprise',
            'practice_type' => 'required|in:healthcare,legal,real_estate,hr,fitness,general',
        ]);

        // HIPAA add-on not permitted on PAYG
        $hipaaAddon = $request->plan !== 'payg' && $request->boolean('hipaa_addon');

        // TODO: Replace with Stripe Checkout redirect when Stripe is provisioned
        // TODO: Store lead in DB or send internal notification email
        session([
            'signup_name'  => $request->contact_name,
            'signup_email' => $request->email,
            'signup_plan'  => $request->plan,
            'signup_hipaa' => $hipaaAddon,
        ]);

        return redirect()->route('signup.thankyou');
    }

    public function thankYou()
    {
        if (!session('signup_name')) {
            return redirect()->route('signup');
        }
        return view('signup.thank-you');
    }
}
