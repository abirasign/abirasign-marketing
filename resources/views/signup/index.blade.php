@extends('layouts.app')

@section('title', 'Get Started — AbiraSign')
@section('meta_description', 'Sign up for AbiraSign. E-signatures and digital patient intake forms. HIPAA compliance included on Professional and Enterprise.')

@push('styles')
<style>
    .signup-wrap { min-height: calc(100vh - 60px); background: var(--bg-alt); display: flex; align-items: flex-start; justify-content: center; padding: 64px 20px; }
    .signup-grid { display: grid; grid-template-columns: 1fr 440px; gap: 56px; max-width: 980px; width: 100%; align-items: start; }
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
    .signup-card-sub { font-size: 14px; color: var(--text-secondary); margin-bottom: 24px; }
    .form-group { margin-bottom: 18px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px; }
    .form-input { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 14px; color: var(--text-primary); background: #fff; transition: border-color .15s; outline: none; box-sizing: border-box; }
    .form-input:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(14,116,144,.08); }
    .form-input.error { border-color: #EF4444; }
    .form-select { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 14px; color: var(--text-primary); background: #fff; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath d='M2 4l4 4 4-4' stroke='%236B7280' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; cursor: pointer; outline: none; transition: border-color .15s; box-sizing: border-box; }
    .form-select:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(14,116,144,.08); }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .form-error { font-size: 12px; color: #EF4444; margin-top: 4px; display: block; }

    /* Billing toggle */
    .billing-toggle-wrap { display: flex; align-items: center; gap: 12px; margin-bottom: 14px; }
    .billing-toggle-label { font-size: 13px; color: var(--text-secondary); font-weight: 500; transition: color .2s; }
    .billing-toggle-label.active { color: var(--text-primary); font-weight: 600; }
    .toggle { position: relative; width: 42px; height: 24px; cursor: pointer; flex-shrink: 0; }
    .toggle input { opacity: 0; width: 0; height: 0; position: absolute; }
    .toggle-track { position: absolute; inset: 0; background: var(--teal); border-radius: 99px; transition: background .2s; }
    .toggle-thumb { position: absolute; top: 3px; left: 3px; width: 18px; height: 18px; background: #fff; border-radius: 50%; transition: transform .2s; pointer-events: none; box-shadow: 0 1px 3px rgba(0,0,0,.15); }
    .toggle input:checked ~ .toggle-thumb { transform: translateX(18px); }
    .annual-badge { background: #DCFCE7; color: #166534; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: var(--radius-pill); }

    /* Plan options */
    .plan-options { display: flex; flex-direction: column; gap: 10px; }
    .plan-option { border: 1px solid var(--border); border-radius: var(--radius-md); padding: 12px 14px; cursor: pointer; display: flex; align-items: center; gap: 12px; transition: border-color .15s; }
    .plan-option:hover { border-color: #9CA3AF; }
    .plan-option.selected { border-color: var(--teal); background: #F0FDFA; }
    .plan-option input[type=radio] { accent-color: var(--teal); flex-shrink: 0; }
    .plan-option-info { flex: 1; }
    .plan-option-name { font-size: 14px; font-weight: 600; color: var(--text-primary); }
    .plan-option-desc { font-size: 12px; color: var(--text-secondary); margin-top: 2px; }
    .plan-option-price { font-size: 13px; font-weight: 700; color: var(--teal); flex-shrink: 0; text-align: right; }

    /* HIPAA status badge */
    .hipaa-status { margin-top: 12px; padding: 10px 14px; border-radius: var(--radius-md); font-size: 13px; font-weight: 500; display: flex; align-items: center; gap: 8px; }
    .hipaa-status.included { background: #DCFCE7; color: #166534; }
    .hipaa-status.excluded { background: #FEF9C3; color: #854D0E; }

    .form-divider { height: 1px; background: var(--border); margin: 24px 0; }
    .submit-btn { width: 100%; padding: 13px; background: var(--teal); color: #fff; border: none; border-radius: var(--radius-md); font-size: 15px; font-weight: 600; cursor: pointer; transition: opacity .15s; margin-top: 4px; }
    .submit-btn:hover { opacity: .9; }
    .submit-btn.enterprise-btn { background: var(--text-primary); }
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
            <p>Set up your account in minutes. Starter and Professional include a 14-day free trial — no commitment required.</p>
            <div class="signup-points">
                <div class="signup-point">
                    <div class="signup-point-icon">
                        <svg viewBox="0 0 13 13" fill="none"><path d="M2 7l3 3 6-6" stroke="#0E7490" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div class="signup-point-text"><strong>Unlimited sends on paid plans.</strong> Starter, Professional, and Enterprise all include unlimited envelope sends — no per-document fees.</div>
                </div>
                <div class="signup-point">
                    <div class="signup-point-icon">
                        <svg viewBox="0 0 13 13" fill="none"><path d="M2 7l3 3 6-6" stroke="#0E7490" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div class="signup-point-text"><strong>Dedicated database on every plan.</strong> Your data is never co-mingled. HIPAA compliance and a signed BAA are available on Professional and Enterprise.</div>
                </div>
                <div class="signup-point">
                    <div class="signup-point-icon">
                        <svg viewBox="0 0 13 13" fill="none"><path d="M2 7l3 3 6-6" stroke="#0E7490" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </div>
                    <div class="signup-point-text"><strong>Self-service setup.</strong> PAYG and subscription accounts are provisioned automatically — you'll receive your login within minutes of signing up.</div>
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
                <div class="alert-error">Please correct the errors below and try again.</div>
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

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="practice_type">Industry</label>
                        <select id="practice_type" name="practice_type" class="form-select {{ $errors->has('practice_type') ? 'error' : '' }}">
                            <option value="" disabled {{ old('practice_type') ? '' : 'selected' }}>Select industry</option>
                            <option value="healthcare" {{ old('practice_type') == 'healthcare' ? 'selected' : '' }}>Healthcare / Medical</option>
                            <option value="legal" {{ old('practice_type') == 'legal' ? 'selected' : '' }}>Legal / Professional</option>
                            <option value="real_estate" {{ old('practice_type') == 'real_estate' ? 'selected' : '' }}>Real estate</option>
                            <option value="hr" {{ old('practice_type') == 'hr' ? 'selected' : '' }}>HR / Onboarding</option>
                            <option value="fitness" {{ old('practice_type') == 'fitness' ? 'selected' : '' }}>Fitness / Wellness</option>
                            <option value="general" {{ old('practice_type') == 'general' ? 'selected' : '' }}>Other / General</option>
                        </select>
                        @error('practice_type')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="num_users">Number of staff</label>
                        <input type="number" id="num_users" name="num_users" class="form-input {{ $errors->has('num_users') ? 'error' : '' }}" value="{{ old('num_users', 1) }}" min="1" max="999" placeholder="1" oninput="updatePrices()">
                        @error('num_users')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-divider"></div>

                {{-- Billing toggle --}}
                <div class="form-group">
                    <label class="form-label">Billing term</label>
                    <div class="billing-toggle-wrap">
                        <span class="billing-toggle-label" id="label-monthly">Monthly</span>
                        <label class="toggle">
                            <input type="checkbox" id="billingToggle" onchange="onBillingChange(this.checked)">
                            <div class="toggle-track"></div>
                            <div class="toggle-thumb"></div>
                        </label>
                        <span class="billing-toggle-label" id="label-annual">Annual <span class="annual-badge">Save 10%</span></span>
                    </div>
                </div>

                {{-- Hidden billing input submitted with form --}}
                <input type="hidden" name="billing" id="billingInput" value="annual">

                {{-- Plan selector --}}
                <div class="form-group">
                    <label class="form-label">Select a plan</label>
                    <div class="plan-options" id="planOptions">
                        <label class="plan-option" data-plan="payg">
                            <input type="radio" name="plan" value="payg" onchange="onPlanChange()">
                            <div class="plan-option-info">
                                <div class="plan-option-name">Pay as you go</div>
                                <div class="plan-option-desc">No monthly fee — pay per envelope sent</div>
                            </div>
                            <div class="plan-option-price">$10/env</div>
                        </label>
                        <label class="plan-option" data-plan="starter">
                            <input type="radio" name="plan" value="starter" onchange="onPlanChange()">
                            <div class="plan-option-info">
                                <div class="plan-option-name">Starter</div>
                                <div class="plan-option-desc">Unlimited sends · general use</div>
                            </div>
                            <div class="plan-option-price" id="price-starter"><span id="price-starter-strike" style="display:none;text-decoration:line-through;color:var(--text-muted);font-weight:400;margin-right:4px;"></span><span id="price-starter-val">$45.00/user/mo</span></div>
                        </label>
                        <label class="plan-option" data-plan="professional">
                            <input type="radio" name="plan" value="professional" onchange="onPlanChange()">
                            <div class="plan-option-info">
                                <div class="plan-option-name">Professional</div>
                                <div class="plan-option-desc">Unlimited sends · HIPAA + BAA available</div>
                            </div>
                            <div class="plan-option-price" id="price-professional"><span id="price-professional-strike" style="display:none;text-decoration:line-through;color:var(--text-muted);font-weight:400;margin-right:4px;"></span><span id="price-professional-val">$75.00/user/mo</span></div>
                        </label>
                        <label class="plan-option" data-plan="enterprise">
                            <input type="radio" name="plan" value="enterprise" onchange="onPlanChange()">
                            <div class="plan-option-info">
                                <div class="plan-option-name">Enterprise</div>
                                <div class="plan-option-desc">Multi-location · API · white-label · priority SLA</div>
                            </div>
                            <div class="plan-option-price">Custom</div>
                        </label>
                    </div>
                    @error('plan')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                {{-- HIPAA section --}}
                <div id="hipaaSection">
                    <div id="hipaaStatus" class="hipaa-status excluded">
                        <span>⚠</span> No HIPAA — not for protected health information
                    </div>
                    <div id="hipaaToggleSection" style="display:none; margin-top:12px; border:1px solid var(--border); border-radius:var(--radius-md); padding:14px 16px;">
    <div style="display:flex; align-items:flex-start; gap:14px;">
        <div style="flex-shrink:0; padding-top:2px;">
            <input type="checkbox" id="hipaaToggle" name="hipaa_required" value="1" onchange="onHipaaChange(this.checked)" style="width:18px; height:18px; accent-color:var(--teal); cursor:pointer; margin:0;">
        </div>
        <div style="flex:1;">
            <div style="font-size:14px; font-weight:600; color:var(--text-primary); margin-bottom:4px;">Enable HIPAA compliance + BAA</div>
            <div style="font-size:12px; color:var(--text-secondary); line-height:1.6;">Includes a dedicated database and Business Associate Agreement. Required if you handle protected health information (PHI). A fully executed BAA is required for some features.</div>
        </div>
    </div>
    <div id="hipaaOnBadge" style="display:none; margin-top:10px; padding:8px 12px; background:#DCFCE7; border-radius:var(--radius-md); font-size:12px; font-weight:600; color:#166534;">
        ✓ Executed BAA required to send PHI.
    </div>
</div>
                </div>

                {{-- Trial notice (shown for Starter/Pro) --}}
                <div id="trialNotice" style="display:none;margin-top:12px;padding:12px 14px;background:#ede9fe;border:1px solid #c4b5fd;border-radius:var(--radius-md);font-size:13px;color:#5b21b6;">
                    🎉 <strong>14-day free trial included.</strong> No charge until your trial ends. Card required to start — cancel anytime before trial ends and you won't be billed.
                    HIPAA compliance and BAA are not available during the trial period.
                    <div style="margin-top:8px;padding-top:8px;border-top:1px solid #c4b5fd;font-size:12px;">
                        📅 Trials start on <strong>monthly billing</strong>. You can switch to annual (and save 10%) after your trial ends from your billing page.
                    </div>
                </div>

                {{-- Skip trial toggle (Professional only) --}}
                <div id="skipTrialWrap" style="display:none;margin-top:10px;">
                    <label style="display:flex;align-items:flex-start;gap:10px;cursor:pointer;padding:10px 12px;border:1px solid var(--border);border-radius:var(--radius-md);background:var(--bg-alt);">
                        <input type="checkbox" id="skipTrialToggle" onchange="onSkipTrialChange()"
                               style="width:15px;height:15px;margin-top:2px;flex-shrink:0;accent-color:var(--teal);cursor:pointer;">
                        <span style="font-size:13px;color:var(--text-secondary);line-height:1.5;">
                            <strong style="color:var(--text-primary);">Skip the trial — I need HIPAA access right away.</strong>
                            Your card will be charged immediately and HIPAA compliance + BAA will be available from day one.
                        </span>
                    </label>
                </div>
                <input type="hidden" name="skip_trial" id="skipTrialInput" value="0">

                {{-- PAYG notice --}}
                <div id="paygNotice" style="display:none;margin-top:12px;padding:12px 14px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:var(--radius-md);font-size:13px;color:#0c4a6e;">
                    💳 <strong>Pay As You Go:</strong> $10.00 charged per envelope sent. No monthly fee. A card is required at signup to enable sending.
                    <strong>PAYG accounts cannot be used to process protected health information (PHI) — ever.</strong>
                    HIPAA compliance is not available on this plan.
                </div>

                <div class="form-divider"></div>

                <button type="submit" class="submit-btn" id="submitBtn">Continue →</button>

                {{-- Explicit TOS + PP acceptance checkbox --}}
                <div style="margin-top:16px;padding:14px 16px;border:1px solid var(--border);border-radius:var(--radius-md);background:var(--bg-alt);">
                    <label style="display:flex;align-items:flex-start;gap:12px;cursor:pointer;">
                        <input type="checkbox" name="accept_policies" id="accept_policies" value="1"
                               style="width:16px;height:16px;margin-top:2px;flex-shrink:0;accent-color:var(--teal);cursor:pointer;"
                               {{ old('accept_policies') ? 'checked' : '' }}>
                        <span style="font-size:13px;color:var(--text-secondary);line-height:1.55;">
                            I have read and agree to the
                            <a href="{{ route('terms') }}" target="_blank" style="color:var(--teal);">Terms of Service</a>
                            @if($currentTos) <span style="color:var(--text-muted);">(v{{ $currentTos->version }})</span> @endif
                            and
                            <a href="{{ route('privacy') }}" target="_blank" style="color:var(--teal);">Privacy Policy</a>
                            @if($currentPp) <span style="color:var(--text-muted);">(v{{ $currentPp->version }})</span> @endif.
                            Your information is never sold or shared with third parties.
                        </span>
                    </label>
                    @error('accept_policies')
                        <p style="font-size:12px;color:#EF4444;margin:8px 0 0 28px;">You must accept the Terms of Service and Privacy Policy to continue.</p>
                    @enderror
                </div>
            </form>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
const PRICES = {
    starter:      { monthly: 45.00, annual: 40.50 },
    professional: { monthly: 75.00, annual: 67.50 },
};

const urlParams   = new URLSearchParams(window.location.search);
const initPlan    = urlParams.get('plan')    || '{{ old("plan", $plan) }}';
const initBilling = urlParams.get('billing') || '{{ old("billing", $billing) }}';

function formatPrice(perUser, users) {
    const total = (perUser * users).toFixed(2);
    return users > 1
        ? `$${total}/mo · $${perUser.toFixed(2)}/user`
        : `$${perUser.toFixed(2)}/user/mo`;
}

function updatePrices() {
    const isAnnual = document.getElementById('billingToggle').checked;
    const users    = Math.max(1, parseInt(document.getElementById('num_users').value) || 1);

    ['starter', 'professional'].forEach(plan => {
        const monthlyPrice = PRICES[plan].monthly;
        const annualPrice  = PRICES[plan].annual;
        const strikeEl     = document.getElementById(`price-${plan}-strike`);
        const valEl        = document.getElementById(`price-${plan}-val`);
        if (!strikeEl || !valEl) return;

        if (isAnnual) {
            strikeEl.textContent  = formatPrice(monthlyPrice, users);
            strikeEl.style.display = 'inline';
            valEl.textContent     = formatPrice(annualPrice, users);
        } else {
            strikeEl.style.display = 'none';
            valEl.textContent     = formatPrice(monthlyPrice, users);
        }
    });

    document.getElementById('label-monthly').classList.toggle('active', !isAnnual);
    document.getElementById('label-annual').classList.toggle('active', isAnnual);
}

function onBillingChange(isAnnual) {
    document.getElementById('billingInput').value = isAnnual ? 'annual' : 'monthly';
    updatePrices();
}

function onSkipTrialChange() {
    const checked = document.getElementById('skipTrialToggle').checked;
    document.getElementById('skipTrialInput').value = checked ? '1' : '0';
    const trialNotice        = document.getElementById('trialNotice');
    const hipaaToggleSection = document.getElementById('hipaaToggleSection');
    const hipaaToggle        = document.getElementById('hipaaToggle');
    trialNotice.style.display        = checked ? 'none' : 'block';
    hipaaToggleSection.style.display = checked ? 'block' : 'none';
    if (!checked) {
        hipaaToggle.checked = false;
        onHipaaChange(false);
    }
}

function onHipaaChange(isOn) {
    document.getElementById('hipaaOnBadge').style.display = isOn ? 'flex' : 'none';
    const status = document.getElementById('hipaaStatus');
    if (isOn) {
        status.className = 'hipaa-status included';
        status.innerHTML = '<span>✓</span> HIPAA + BAA included — BAA sent before activation';
    } else {
        status.className = 'hipaa-status excluded';
        status.innerHTML = '<span>⚠</span> No HIPAA — general use only';
    }
}

function onPlanChange() {
    const selected = document.querySelector('input[name="plan"]:checked')?.value;
    document.querySelectorAll('.plan-option').forEach(el => el.classList.remove('selected'));
    document.querySelector(`input[name="plan"]:checked`)?.closest('.plan-option')?.classList.add('selected');

    const hipaaSection       = document.getElementById('hipaaSection');
    const hipaaStatus        = document.getElementById('hipaaStatus');
    const hipaaToggleSection = document.getElementById('hipaaToggleSection');
    const hipaaToggle        = document.getElementById('hipaaToggle');
    const billingToggleWrap  = document.querySelector('.billing-toggle-wrap');
    const trialNotice        = document.getElementById('trialNotice');
    const paygNotice         = document.getElementById('paygNotice');

    // Reset notices
    trialNotice.style.display = 'none';
    paygNotice.style.display  = 'none';
    document.getElementById('skipTrialWrap').style.display = 'none';
    document.getElementById('skipTrialToggle').checked = false;
    document.getElementById('skipTrialInput').value = '0';

    if (selected === 'payg') {
        // PAYG: hide billing toggle, hide HIPAA entirely, show PAYG notice
        if (billingToggleWrap) billingToggleWrap.style.display = 'none';
        hipaaSection.style.display       = 'none';
        hipaaToggleSection.style.display = 'none';
        hipaaToggle.checked              = false;
        paygNotice.style.display         = 'block';
    } else if (selected === 'professional') {
        if (billingToggleWrap) billingToggleWrap.style.display = 'flex';
        hipaaSection.style.display       = 'block';
        hipaaToggleSection.style.display = 'none';
        hipaaToggle.checked              = false;
        onHipaaChange(false);
        hipaaStatus.style.display = 'none';
        trialNotice.style.display = 'block';
        document.getElementById('skipTrialWrap').style.display = 'block';
    } else if (selected === 'enterprise') {
        if (billingToggleWrap) billingToggleWrap.style.display = 'flex';
        hipaaSection.style.display       = 'block';
        hipaaToggleSection.style.display = 'none';
        hipaaStatus.style.display        = 'flex';
        hipaaStatus.className            = 'hipaa-status included';
        hipaaStatus.innerHTML            = '<span>✓</span> HIPAA compliance + BAA included';
    } else {
        // Starter
        if (billingToggleWrap) billingToggleWrap.style.display = 'flex';
        hipaaSection.style.display       = 'block';
        hipaaToggleSection.style.display = 'none';
        hipaaStatus.style.display        = 'flex';
        hipaaStatus.className            = 'hipaa-status excluded';
        hipaaStatus.innerHTML            = '<span>⚠</span> No HIPAA — general use only';
        hipaaToggle.checked              = false;
        trialNotice.style.display        = 'block';
    }

    const btn = document.getElementById('submitBtn');
    if (selected === 'enterprise') {
        btn.textContent = 'Contact sales →';
        btn.classList.add('enterprise-btn');
        btn.type = 'button';
        btn.onclick = () => window.location.href = '/contact?reason=sales';
    } else if (selected === 'payg') {
        btn.textContent = 'Save card & activate →';
        btn.classList.remove('enterprise-btn');
        btn.type = 'submit';
        btn.onclick = null;
    } else {
        btn.textContent = 'Start free trial →';
        btn.classList.remove('enterprise-btn');
        btn.type = 'submit';
        btn.onclick = null;
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const isAnnual = initBilling === 'annual';
    document.getElementById('billingToggle').checked = isAnnual;
    document.getElementById('billingInput').value = isAnnual ? 'annual' : 'monthly';
    updatePrices();

    const planRadio = document.querySelector(`input[name="plan"][value="${initPlan}"]`);
    if (planRadio) {
        planRadio.checked = true;
    } else {
        document.querySelector('input[name="plan"][value="payg"]').checked = true;
    }
    onPlanChange();
});
</script>
@endpush
