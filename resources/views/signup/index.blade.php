@extends('layouts.app')

@section('title', 'Get Started — AbiraSign')
@section('meta_description', 'Sign up for AbiraSign. E-signatures and digital intake forms for any business. HIPAA compliance add-on available.')

@push('styles')
<style>
    .signup-wrap { min-height: calc(100vh - 60px); background: var(--bg-alt); display: flex; align-items: flex-start; justify-content: center; padding: 64px 20px; }
    .signup-grid { display: grid; grid-template-columns: 1fr 420px; gap: 56px; max-width: 960px; width: 100%; align-items: start; }
    .signup-left { padding-top: 8px; }
    .signup-left h1 { font-size: 32px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px; line-height: 1.25; }
    .signup-left p { font-size: 16px; color: var(--text-secondary); line-height: 1.65; margin-bottom: 32px; }
    .signup-points { display: flex; flex-direction: column; gap: 14px; margin-bottom: 36px; }
    .signup-point { display: flex; gap: 12px; align-items: flex-start; }
    .signup-point-icon { width: 28px; height: 28px; border-radius: 50%; background: var(--teal-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 1px; }
    .signup-point-icon svg { width: 13px; height: 13px; }
    .signup-point-text { font-size: 14px; color: var(--text-secondary); line-height: 1.55; }
    .signup-point-text strong { color: var(--text-primary); font-weight: 600; }
    .signup-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 36px; }
    .signup-card h2 { font-size: 20px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px; }
    .signup-card-sub { font-size: 14px; color: var(--text-secondary); margin-bottom: 28px; }
    .form-group { margin-bottom: 18px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px; }
    .form-label span { color: var(--text-muted); font-weight: 400; }
    .form-input { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 14px; color: var(--text-primary); background: #fff; transition: border-color .15s; outline: none; }
    .form-input:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(14,116,144,.08); }
    .form-input.error { border-color: #EF4444; }
    .form-select { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 14px; color: var(--text-primary); background: #fff; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath d='M2 4l4 4 4-4' stroke='%236B7280' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; cursor: pointer; outline: none; transition: border-color .15s; }
    .form-select:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(14,116,144,.08); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .form-error { font-size: 12px; color: #EF4444; margin-top: 4px; display: block; }
    .plan-options { display: flex; flex-direction: column; gap: 10px; }
    .plan-option { border: 1px solid var(--border); border-radius: var(--radius-md); padding: 12px 14px; cursor: pointer; display: flex; align-items: center; gap: 12px; transition: border-color .15s; }
    .plan-option:hover { border-color: #9CA3AF; }
    .plan-option.selected { border-color: var(--teal); background: #F0FDFA; }
    .plan-option input[type=radio] { accent-color: var(--teal); flex-shrink: 0; }
    .plan-option-info { flex: 1; }
    .plan-option-name { font-size: 14px; font-weight: 600; color: var(--text-primary); }
    .plan-option-desc { font-size: 12px; color: var(--text-secondary); margin-top: 2px; }
    .plan-option-price { font-size: 14px; font-weight: 700; color: var(--teal); flex-shrink: 0; }
    .hipaa-option { margin-top: 18px; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 14px; background: var(--bg-surface); }
    .hipaa-option-header { display: flex; align-items: center; gap: 10px; cursor: pointer; }
    .hipaa-option-header input[type=checkbox] { accent-color: var(--teal); flex-shrink: 0; }
    .hipaa-option-title { font-size: 14px; font-weight: 600; color: var(--text-primary); }
    .hipaa-option-subtitle { font-size: 12px; color: var(--text-secondary); margin-top: 2px; }
    .hipaa-unavail { font-size: 12px; color: var(--text-muted); margin-top: 8px; padding: 8px 10px; background: #FEF9C3; border-radius: var(--radius-sm); }
    .form-divider { height: 1px; background: var(--border); margin: 24px 0; }
    .submit-btn { width: 100%; padding: 13px; background: var(--teal); color: #fff; border: none; border-radius: var(--radius-md); font-size: 15px; font-weight: 600; cursor: pointer; transition: opacity .15s; margin-top: 4px; }
    .submit-btn:hover { opacity: .9; }
    .submit-btn:disabled { opacity: .6; cursor: not-allowed; }
    .form-footer { font-size: 12px; color: var(--text-muted); text-align: center; margin-top: 14px; line-height: 1.6; }
    .form-footer a { color: var(--teal); }
    .alert-error { background: #FEF2F2; border: 1px solid #FECACA; border-radius: var(--radius-md); padding: 12px 16px; margin-bottom: 20px; font-size: 14px; color: #B91C1C; }
    @media (max-width: 860px) {
        .signup-grid { grid-template-columns: 1fr; gap: 32px; }
        .signup-left { padding-top: 0; }
    }
    @media (max-width: 480px) {
        .signup-wrap { padding: 32px 16px; }
        .signup-card { padding: 24px 20px; }
        .form-row { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

<div class="signup-wrap">
    <div class="signup-grid">

        {{-- Left: value prop --}}
        <div class="signup-left">
            <div class="section-label">Get started</div>
            <h1>Start sending documents for signature today</h1>
            <p>Tell us about your business and we'll get your account set up. No credit card required to get started on Pay as you go.</p>
            <div class="signup-points">
                <div class="signup-point">
                    <div class="signup-point-icon">
                        <svg viewBox="0 0 13 13" fill="none"><path d="M2 7l3 3 6-6" stroke="#0E7490" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div class="signup-point-text"><strong>No monthly fees on bundles.</strong> Buy envelopes when you need them — they never expire.</div>
                </div>
                <div class="signup-point">
                    <div class="signup-point-icon">
                        <svg viewBox="0 0 13 13" fill="none"><path d="M2 7l3 3 6-6" stroke="#0E7490" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div class="signup-point-text"><strong>HIPAA compliance available.</strong> Add a dedicated database and BAA on any paid bundle.</div>
                </div>
                <div class="signup-point">
                    <div class="signup-point-icon">
                        <svg viewBox="0 0 13 13" fill="none"><path d="M2 7l3 3 6-6" stroke="#0E7490" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div class="signup-point-text"><strong>Up and running fast.</strong> Most accounts are active within one business day.</div>
                </div>
                <div class="signup-point">
                    <div class="signup-point-icon">
                        <svg viewBox="0 0 13 13" fill="none"><path d="M2 7l3 3 6-6" stroke="#0E7490" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div class="signup-point-text"><strong>Full audit trail on every document.</strong> E-SIGN Act and UETA compliant out of the box.</div>
                </div>
            </div>
            <p style="font-size: 13px; color: var(--text-muted); line-height: 1.6;">Already have an account? <a href="{{ env('APP_LOGIN_URL', 'https://dev.abirasign.com/login') }}" style="color: var(--teal);">Log in here →</a></p>
        </div>

        {{-- Right: form --}}
        <div class="signup-card">
            <h2>Create your account</h2>
            <p class="signup-card-sub">Takes less than 2 minutes.</p>

            @if($errors->any())
                <div class="alert-error">
                    Please correct the errors below and try again.
                </div>
            @endif

            <form method="POST" action="{{ route('signup.submit') }}" id="signupForm">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="contact_name">Your name</label>
                        <input type="text" id="contact_name" name="contact_name" class="form-input {{ $errors->has('contact_name') ? 'error' : '' }}" value="{{ old('contact_name') }}" placeholder="Jane Smith" autocomplete="name">
                        @error('contact_name')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="practice_name">Business name</label>
                        <input type="text" id="practice_name" name="practice_name" class="form-input {{ $errors->has('practice_name') ? 'error' : '' }}" value="{{ old('practice_name') }}" placeholder="Acme LLC" autocomplete="organization">
                        @error('practice_name')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Work email</label>
                    <input type="email" id="email" name="email" class="form-input {{ $errors->has('email') ? 'error' : '' }}" value="{{ old('email') }}" placeholder="jane@yourcompany.com" autocomplete="email">
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Phone number</label>
                    <input type="tel" id="phone" name="phone" class="form-input {{ $errors->has('phone') ? 'error' : '' }}" value="{{ old('phone') }}" placeholder="(555) 000-0000" autocomplete="tel">
                    @error('phone')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="practice_type">Industry</label>
                    <select id="practice_type" name="practice_type" class="form-select {{ $errors->has('practice_type') ? 'error' : '' }}">
                        <option value="" disabled {{ old('practice_type') ? '' : 'selected' }}>Select your industry</option>
                        <option value="healthcare" {{ old('practice_type') == 'healthcare' ? 'selected' : '' }}>Healthcare / Medical practice</option>
                        <option value="legal" {{ old('practice_type') == 'legal' ? 'selected' : '' }}>Legal / Professional services</option>
                        <option value="real_estate" {{ old('practice_type') == 'real_estate' ? 'selected' : '' }}>Real estate</option>
                        <option value="hr" {{ old('practice_type') == 'hr' ? 'selected' : '' }}>HR / Onboarding</option>
                        <option value="fitness" {{ old('practice_type') == 'fitness' ? 'selected' : '' }}>Fitness / Wellness</option>
                        <option value="general" {{ old('practice_type') == 'general' ? 'selected' : '' }}>Other / General business</option>
                    </select>
                    @error('practice_type')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-divider"></div>

                <div class="form-group">
                    <label class="form-label">Select a plan</label>
                    <div class="plan-options" id="planOptions">
                        <label class="plan-option {{ old('plan') == 'payg' || !old('plan') ? 'selected' : '' }}">
                            <input type="radio" name="plan" value="payg" {{ old('plan') == 'payg' || !old('plan') ? 'checked' : '' }} onchange="onPlanChange()">
                            <div class="plan-option-info">
                                <div class="plan-option-name">Pay as you go</div>
                                <div class="plan-option-desc">No commitment — pay per envelope</div>
                            </div>
                            <div class="plan-option-price">$10/env</div>
                        </label>
                        <label class="plan-option {{ old('plan') == 'starter' ? 'selected' : '' }}">
                            <input type="radio" name="plan" value="starter" {{ old('plan') == 'starter' ? 'checked' : '' }} onchange="onPlanChange()">
                            <div class="plan-option-info">
                                <div class="plan-option-name">Starter bundle</div>
                                <div class="plan-option-desc">50 envelopes, never expire</div>
                            </div>
                            <div class="plan-option-price">$XX</div>
                        </label>
                        <label class="plan-option {{ old('plan') == 'standard' ? 'selected' : '' }}">
                            <input type="radio" name="plan" value="standard" {{ old('plan') == 'standard' ? 'checked' : '' }} onchange="onPlanChange()">
                            <div class="plan-option-info">
                                <div class="plan-option-name">Standard bundle</div>
                                <div class="plan-option-desc">250 envelopes, never expire</div>
                            </div>
                            <div class="plan-option-price">$XX</div>
                        </label>
                        <label class="plan-option {{ old('plan') == 'enterprise' ? 'selected' : '' }}">
                            <input type="radio" name="plan" value="enterprise" {{ old('plan') == 'enterprise' ? 'checked' : '' }} onchange="onPlanChange()">
                            <div class="plan-option-info">
                                <div class="plan-option-name">Enterprise</div>
                                <div class="plan-option-desc">Custom volume — we'll contact you</div>
                            </div>
                            <div class="plan-option-price">Custom</div>
                        </label>
                    </div>
                    @error('plan')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="hipaa-option" id="hipaaOption">
                    <label class="hipaa-option-header">
                        <input type="checkbox" name="hipaa_addon" value="1" id="hipaaCheckbox" {{ old('hipaa_addon') ? 'checked' : '' }}>
                        <div>
                            <div class="hipaa-option-title">Add HIPAA compliance <span style="color: var(--teal); font-weight: 700;">+$49</span></div>
                            <div class="hipaa-option-subtitle">Dedicated database + Business Associate Agreement</div>
                        </div>
                    </label>
                    <div id="hipaaUnavail" class="hipaa-unavail" style="display: none;">
                        HIPAA compliance is not available on Pay as you go. Select a bundle to enable this option.
                    </div>
                </div>

                <div class="form-divider"></div>

                <button type="submit" class="submit-btn" id="submitBtn">
                    Continue →
                </button>

                <p class="form-footer">
                    By continuing you agree to our <a href="{{ route('terms') }}">Terms of Service</a> and <a href="{{ route('privacy') }}">Privacy Policy</a>.<br>
                    Your information is never sold or shared with third parties.
                </p>
            </form>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
function onPlanChange() {
    const selected = document.querySelector('input[name="plan"]:checked')?.value;
    const hipaaOption = document.getElementById('hipaaOption');
    const hipaaCheckbox = document.getElementById('hipaaCheckbox');
    const hipaaUnavail = document.getElementById('hipaaUnavail');

    document.querySelectorAll('.plan-option').forEach(el => el.classList.remove('selected'));
    document.querySelector(`input[name="plan"]:checked`)?.closest('.plan-option')?.classList.add('selected');

    if (selected === 'payg') {
        hipaaCheckbox.checked = false;
        hipaaCheckbox.disabled = true;
        hipaaUnavail.style.display = 'block';
        hipaaOption.style.opacity = '.6';
    } else {
        hipaaCheckbox.disabled = false;
        hipaaUnavail.style.display = 'none';
        hipaaOption.style.opacity = '1';
    }
}

document.addEventListener('DOMContentLoaded', () => onPlanChange());
</script>
@endpush
