@extends('layouts.app')

@section('title', 'Payment Confirmed — AbiraSign')

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
    .thankyou-steps { text-align: left; margin: 24px 0; display: flex; flex-direction: column; gap: 12px; }
    .thankyou-step { display: flex; gap: 12px; align-items: flex-start; font-size: 14px; color: var(--text-secondary); line-height: 1.5; }
    .thankyou-step-num { width: 22px; height: 22px; border-radius: 50%; background: var(--teal-light); display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 700; color: var(--teal-dark); flex-shrink: 0; margin-top: 1px; }
    .hipaa-notice { background: #ede9ff; border: 1px solid #c4b5fd; border-radius: var(--radius-md); padding: 14px 18px; margin: 16px 0; font-size: 14px; color: #5b21b6; text-align: left; }
    .thankyou-card .btn { display: inline-block; margin-top: 8px; }
    @media (max-width: 480px) {
        .thankyou-card { padding: 36px 24px; }
    }
</style>
@endpush

@section('content')

<div class="thankyou-wrap">
    <div class="thankyou-card">

        <div class="thankyou-icon">
            <svg width="26" height="26" viewBox="0 0 26 26" fill="none">
                <path d="M5 13l5.5 5.5L21 8" stroke="#16A34A" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>

        <h1>Payment confirmed!</h1>
        <p>
            @if($quote)
                Thank you, {{ $quote->contact_name }}. Your payment has been received and your AbiraSign Enterprise account is being set up.
            @else
                Your payment has been received and your account is being set up.
            @endif
        </p>
        <p>Keep an eye on <strong>{{ $quote->contact_email ?? 'your inbox' }}</strong> — you'll receive a welcome email with next steps shortly.</p>

        @if($quote)
            <div class="thankyou-details">
                <div class="thankyou-detail-row">
                    <span class="detail-label">Client</span>
                    <span class="detail-val">{{ $quote->client_name }}</span>
                </div>
                <div class="thankyou-detail-row">
                    <span class="detail-label">Quote ID</span>
                    <span class="detail-val">{{ $quote->quote_id }}</span>
                </div>
                <div class="thankyou-detail-row">
                    <span class="detail-label">Plan</span>
                    <span class="detail-val">Enterprise</span>
                </div>
                <div class="thankyou-detail-row">
                    <span class="detail-label">Billing term</span>
                    <span class="detail-val">{{ $quote->billing_term === 'triennial' ? 'Triennial (3 years)' : 'Annual (1 year)' }}</span>
                </div>
                <div class="thankyou-detail-row">
                    <span class="detail-label">Annual total</span>
                    <span class="detail-val">${{ number_format($quote->annual_total, 2) }}/yr</span>
                </div>
                @if($quote->hipaa_required)
                    <div class="thankyou-detail-row">
                        <span class="detail-label">HIPAA + BAA</span>
                        <span class="detail-val" style="color:#16A34A;">✓ Required</span>
                    </div>
                @endif
            </div>

            @if($quote->hipaa_required)
                <div class="hipaa-notice">
                    🔒 <strong>BAA required</strong> — A Business Associate Agreement will be sent to {{ $quote->contact_email }} for signature before your account goes live.
                </div>
            @endif
        @endif

        <div class="thankyou-steps">
            <div class="thankyou-step">
                <div class="thankyou-step-num">1</div>
                Your payment receipt will be emailed to you from Stripe shortly.
            </div>
            <div class="thankyou-step">
                <div class="thankyou-step-num">2</div>
                @if($quote?->hipaa_required)
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

        <p style="font-size:13px;color:var(--text-secondary);margin-top:8px;">
            Questions? Contact us at <a href="mailto:support@abirasign.com" style="color:var(--teal-dark);">support@abirasign.com</a>
        </p>

    </div>
</div>

@endsection
