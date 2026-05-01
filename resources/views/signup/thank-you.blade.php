@extends('layouts.app')

@section('title', 'Thank You — AbiraSign')

@push('styles')
<style>
    .thankyou-wrap { min-height: calc(100vh - 60px); background: var(--bg-alt); display: flex; align-items: center; justify-content: center; padding: 64px 20px; }
    .thankyou-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 56px 48px; max-width: 540px; width: 100%; text-align: center; }
    .thankyou-icon { width: 56px; height: 56px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
    .thankyou-icon.green  { background: #DCFCE7; }
    .thankyou-icon.purple { background: #ede9fe; }
    .thankyou-icon.blue   { background: #e0f2fe; }
    .thankyou-card h1 { font-size: 26px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px; }
    .thankyou-card p { font-size: 15px; color: var(--text-secondary); line-height: 1.7; margin-bottom: 10px; }
    .thankyou-details { background: var(--bg-alt); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 16px 20px; margin: 24px 0; text-align: left; }
    .thankyou-detail-row { display: flex; justify-content: space-between; align-items: center; font-size: 14px; padding: 5px 0; border-bottom: 0.5px solid var(--border); }
    .thankyou-detail-row:last-child { border-bottom: none; }
    .thankyou-detail-row .detail-label { color: var(--text-secondary); }
    .thankyou-detail-row .detail-val { font-weight: 600; color: var(--text-primary); }
    .notice-box { border-radius: var(--radius-md); padding: 12px 16px; margin: 16px 0; font-size: 13px; text-align: left; line-height: 1.6; }
    .notice-box.purple { background: #ede9fe; border: 1px solid #c4b5fd; color: #5b21b6; }
    .notice-box.blue   { background: #e0f2fe; border: 1px solid #bae6fd; color: #0c4a6e; }
    .notice-box.amber  { background: #fef3c7; border: 1px solid #fcd34d; color: #92400e; }
    .notice-box.green  { background: #dcfce7; border: 1px solid #86efac; color: #166534; }
    .thankyou-steps { text-align: left; margin: 24px 0; display: flex; flex-direction: column; gap: 12px; }
    .thankyou-step { display: flex; gap: 12px; align-items: flex-start; font-size: 14px; color: var(--text-secondary); line-height: 1.5; }
    .thankyou-step-num { width: 22px; height: 22px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; flex-shrink: 0; margin-top: 1px; }
    .step-num-purple { background: #ede9fe; color: #5b21b6; }
    .step-num-green  { background: #dcfce7; color: #166534; }
    .step-num-blue   { background: #e0f2fe; color: #0c4a6e; }
    .thankyou-card .btn { display: inline-block; margin-top: 8px; }
    @media (max-width: 480px) { .thankyou-card { padding: 36px 24px; } }
</style>
@endpush

@section('content')

@php
    $plan      = session('signup_plan', 'payg');
    $billing   = session('signup_billing', 'monthly');
    $name      = session('signup_name');
    $email     = session('signup_email');
    $numUsers  = session('signup_num_users', 1);
    $hipaa     = session('signup_hipaa', false) && in_array($plan, ['professional', 'enterprise']);
    $trialEnd  = session('signup_trial_end');
    $isTrial   = in_array($plan, ['starter', 'professional']);
    $isPayg    = $plan === 'payg';

    $planLabel = match($plan) {
        'payg'         => 'Pay As You Go',
        'starter'      => 'Starter',
        'professional' => 'Professional',
        'enterprise'   => 'Enterprise',
        default        => ucfirst($plan),
    };

    $billingLabel = $billing === 'annual' ? 'Annual (10% discount)' : 'Monthly';

    $rates = [
        'starter'      => ['monthly' => 45.00, 'annual' => 40.50],
        'professional' => ['monthly' => 75.00, 'annual' => 67.50],
    ];
    $monthlyRate  = $rates[$plan][$billing] ?? null;
    $totalMonthly = $monthlyRate ? number_format($monthlyRate * $numUsers, 2) : null;
    $trialEndFmt  = $trialEnd ? \Carbon\Carbon::createFromTimestamp($trialEnd)->format('F j, Y') : now()->addDays(14)->format('F j, Y');
@endphp

<div class="thankyou-wrap">
    <div class="thankyou-card">

        {{-- Icon --}}
        @if($isTrial)
            <div class="thankyou-icon purple">
                <svg width="26" height="26" viewBox="0 0 26 26" fill="none"><path d="M13 3L15.5 9.5H22.5L17 13.5L19 20L13 16L7 20L9 13.5L3.5 9.5H10.5L13 3Z" stroke="#7c3aed" stroke-width="2" stroke-linejoin="round"/></svg>
            </div>
            <h1>Your free trial has started!</h1>
            <p>Welcome{{ $name ? ', ' . $name : '' }}! Your 14-day free trial of AbiraSign <strong>{{ $planLabel }}</strong> is now active.</p>
        @elseif($isPayg)
            <div class="thankyou-icon blue">
                <svg width="26" height="26" viewBox="0 0 26 26" fill="none"><rect x="3" y="7" width="20" height="14" rx="2" stroke="#0284c7" stroke-width="2"/><path d="M3 11h20" stroke="#0284c7" stroke-width="2"/></svg>
            </div>
            <h1>Your account is ready!</h1>
            <p>Welcome{{ $name ? ', ' . $name : '' }}! Your Pay As You Go account has been created and your card is saved.</p>
        @else
            <div class="thankyou-icon green">
                <svg width="26" height="26" viewBox="0 0 26 26" fill="none"><path d="M5 13l5.5 5.5L21 8" stroke="#16A34A" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <h1>You're all set!</h1>
            <p>Thanks{{ $name ? ', ' . $name : '' }}. Your account is being set up now.</p>
        @endif

        <p>Check <strong>{{ $email ?? 'your inbox' }}</strong> for your account setup link.</p>

        {{-- Plan details box --}}
        <div class="thankyou-details">
            <div class="thankyou-detail-row">
                <span class="detail-label">Plan</span>
                <span class="detail-val">{{ $planLabel }}</span>
            </div>
            @if($isPayg)
                <div class="thankyou-detail-row">
                    <span class="detail-label">Rate</span>
                    <span class="detail-val">$10.00 per envelope sent</span>
                </div>
                <div class="thankyou-detail-row">
                    <span class="detail-label">Monthly fee</span>
                    <span class="detail-val">None</span>
                </div>
            @elseif($isTrial)
                <div class="thankyou-detail-row">
                    <span class="detail-label">Trial ends</span>
                    <span class="detail-val" style="color:#7c3aed;">{{ $trialEndFmt }}</span>
                </div>
                <div class="thankyou-detail-row">
                    <span class="detail-label">Billing term</span>
                    <span class="detail-val">{{ $billingLabel }}</span>
                </div>
                @if($totalMonthly)
                <div class="thankyou-detail-row">
                    <span class="detail-label">Rate after trial</span>
                    <span class="detail-val">${{ $totalMonthly }}/mo ({{ $numUsers }} seat{{ $numUsers != 1 ? 's' : '' }})</span>
                </div>
                @endif
            @else
                <div class="thankyou-detail-row">
                    <span class="detail-label">Billing</span>
                    <span class="detail-val">{{ $billingLabel }}</span>
                </div>
            @endif
            @if($hipaa)
                <div class="thankyou-detail-row">
                    <span class="detail-label">HIPAA + BAA</span>
                    <span class="detail-val" style="color:#16A34A;">✓ Included</span>
                </div>
            @endif
        </div>

        {{-- Contextual notices --}}
        @if($isTrial)
            <div class="notice-box purple">
                🎉 <strong>14-day free trial active.</strong> You won't be charged until {{ $trialEndFmt }}.
                Cancel anytime before then — no charge.
            </div>
            <div class="notice-box amber">
                ⚠ <strong>HIPAA compliance and BAA are not available during your trial.</strong>
                These features activate automatically when your paid subscription begins.
                Do not use this account to process protected health information (PHI) during the trial period.
            </div>
        @elseif($isPayg)
            <div class="notice-box blue">
                💳 Your card will be charged <strong>$10.00</strong> each time you send an envelope.
                No monthly fee — you only pay when you send.
            </div>
            <div class="notice-box amber">
                ⚠ <strong>Pay As You Go accounts cannot be used to process protected health information (PHI) — ever.</strong>
                HIPAA compliance is not available on this plan. See our Terms of Service for details.
            </div>
        @elseif($hipaa)
            <div class="notice-box amber">
                ⚠ <strong>Action required:</strong> A Business Associate Agreement has been sent to {{ $email ?? 'your email' }}.
                Your account must remain inactive for PHI until the BAA is fully executed.
            </div>
        @endif

        {{-- Next steps --}}
        <div class="thankyou-steps">
            @if($isTrial)
                <div class="thankyou-step">
                    <div class="thankyou-step-num step-num-purple">1</div>
                    Check your email for your account setup link. Click it to set your password and log in.
                </div>
                <div class="thankyou-step">
                    <div class="thankyou-step-num step-num-purple">2</div>
                    Explore AbiraSign free for 14 days. Upload documents, build forms, and send to patients or clients.
                </div>
                <div class="thankyou-step">
                    <div class="thankyou-step-num step-num-purple">3</div>
                    On {{ $trialEndFmt }}, your card will be charged ${{ $totalMonthly ?? '—' }}/mo automatically. Cancel anytime before then.
                </div>
            @elseif($isPayg)
                <div class="thankyou-step">
                    <div class="thankyou-step-num step-num-blue">1</div>
                    Check your email for your account setup link. Click it to set your password and log in.
                </div>
                <div class="thankyou-step">
                    <div class="thankyou-step-num step-num-blue">2</div>
                    Upload your documents or build forms in your dashboard.
                </div>
                <div class="thankyou-step">
                    <div class="thankyou-step-num step-num-blue">3</div>
                    Send your first envelope — your card on file will be charged $10.00 at send time.
                </div>
            @else
                <div class="thankyou-step">
                    <div class="thankyou-step-num step-num-green">1</div>
                    Check your email for your account setup link to set your password.
                </div>
                <div class="thankyou-step">
                    <div class="thankyou-step-num step-num-green">2</div>
                    @if($hipaa)
                        Sign your Business Associate Agreement sent to your email before sending PHI.
                    @else
                        Log in and start sending documents right away.
                    @endif
                </div>
                <div class="thankyou-step">
                    <div class="thankyou-step-num step-num-green">3</div>
                    Need help? Email <a href="mailto:support@abirasign.com" style="color:var(--teal);">support@abirasign.com</a> — we typically respond within one business day.
                </div>
            @endif
        </div>

        <a href="{{ env('APP_LOGIN_URL', 'https://dev.abirasign.com/login') }}" class="btn btn-primary">
            Log in to your account →
        </a>

    </div>
</div>

@endsection
