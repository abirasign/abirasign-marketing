@extends('layouts.app')

@section('title', 'Thank You — AbiraSign')

@push('styles')
<style>
    .thankyou-wrap { min-height: calc(100vh - 60px); background: var(--bg-alt); display: flex; align-items: center; justify-content: center; padding: 64px 20px; }
    .thankyou-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 56px 48px; max-width: 520px; width: 100%; text-align: center; }
    .thankyou-icon { width: 56px; height: 56px; border-radius: 50%; background: #DCFCE7; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
    .thankyou-card h1 { font-size: 26px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px; }
    .thankyou-card p { font-size: 15px; color: var(--text-secondary); line-height: 1.7; margin-bottom: 10px; }
    .thankyou-details { background: var(--bg-alt); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 16px 20px; margin: 24px 0; text-align: left; }
    .thankyou-detail-row { display: flex; justify-content: space-between; align-items: center; font-size: 14px; padding: 5px 0; }
    .thankyou-detail-row .detail-label { color: var(--text-secondary); }
    .thankyou-detail-row .detail-val { font-weight: 600; color: var(--text-primary); }
    .hipaa-badge { display: inline-flex; align-items: center; gap: 5px; background: #DCFCE7; color: #166534; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: var(--radius-pill); margin-top: 10px; }
    .thankyou-steps { text-align: left; margin: 24px 0; display: flex; flex-direction: column; gap: 12px; }
    .thankyou-step { display: flex; gap: 12px; align-items: flex-start; font-size: 14px; color: var(--text-secondary); line-height: 1.5; }
    .thankyou-step-num { width: 22px; height: 22px; border-radius: 50%; background: var(--teal-light); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: var(--teal-dark); flex-shrink: 0; margin-top: 1px; }
    .thankyou-card .btn { display: inline-block; margin-top: 8px; }
    @media (max-width: 480px) {
        .thankyou-card { padding: 36px 24px; }
    }
</style>
@endpush

@section('content')

@php
    $plan    = session('signup_plan', 'payg');
    $billing = session('signup_billing', 'monthly');
    $name    = session('signup_name');
    $email   = session('signup_email');

    $planLabel = match($plan) {
        'payg'         => 'Pay as you go',
        'starter'      => 'Starter',
        'professional' => 'Professional',
        'enterprise'   => 'Enterprise',
        default        => ucfirst($plan),
    };
    $billingLabel = $billing === 'annual' ? 'Annual (10% discount)' : 'Monthly';
    $hipaa = session('signup_hipaa', false) && in_array($plan, ['professional', 'enterprise']);
    $isPaid = in_array($plan, ['starter', 'professional', 'enterprise']);
@endphp

<div class="thankyou-wrap">
    <div class="thankyou-card">

        <div class="thankyou-icon">
            <svg width="26" height="26" viewBox="0 0 26 26" fill="none"><path d="M5 13l5.5 5.5L21 8" stroke="#16A34A" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>

        @if($isPaid)
            <h1>You're all set!</h1>
            <p>Thanks{{ $name ? ', ' . $name : '' }}. Your payment was received and your account is being set up.</p>
        @else
            <h1>You're on the list</h1>
            <p>Thanks{{ $name ? ', ' . $name : '' }}. We've received your signup request and will be in touch shortly.</p>
        @endif

        <p>Keep an eye on <strong>{{ $email ?? 'your inbox' }}</strong> — we'll send next steps within one business day.</p>

        <div class="thankyou-details">
            <div class="thankyou-detail-row">
                <span class="detail-label">Selected plan</span>
                <span class="detail-val">{{ $planLabel }}</span>
            </div>
            @if($plan !== 'payg')
            <div class="thankyou-detail-row">
                <span class="detail-label">Billing</span>
                <span class="detail-val">{{ $billingLabel }}</span>
            </div>
            @endif
            @if($hipaa)
            <div class="thankyou-detail-row">
                <span class="detail-label">HIPAA + BAA</span>
                <span class="detail-val" style="color: #16A34A;">✓ Included</span>
            </div>
            @endif
        </div>

        <div class="thankyou-steps">
            <div class="thankyou-step">
                <div class="thankyou-step-num">1</div>
                We'll review your signup and reach out to confirm your account details.
            </div>
            <div class="thankyou-step">
                <div class="thankyou-step-num">2</div>
                @if($hipaa)
                    We'll send your Business Associate Agreement for signature before activating your account.
                @else
                    You'll receive a welcome email with a link to set up your password and access your dashboard.
                @endif
            </div>
            <div class="thankyou-step">
                <div class="thankyou-step-num">3</div>
                Your account will be active and ready to send documents — usually within one business day.
            </div>
        </div>

        <a href="{{ env('APP_LOGIN_URL', 'https://dev.abirasign.com/login') }}" class="btn btn-primary">Log in to your account →</a>

    </div>
</div>

@endsection
