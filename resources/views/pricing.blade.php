@extends('layouts.app')

@section('title', 'Pricing — AbiraSign')
@section('meta_description', 'Simple, transparent pricing. Buy envelopes when you need them. No monthly fees. HIPAA compliance add-on available for healthcare practices.')

@push('styles')
<style>
    /* ── Hero ── */
    .pricing-hero { padding: 72px 32px 56px; text-align: center; background: #fff; border-bottom: 1px solid var(--border); }
    .pricing-hero h1 { font-size: 40px; font-weight: 700; color: var(--text-primary); margin-bottom: 14px; line-height: 1.2; }
    .pricing-hero p { font-size: 17px; color: var(--text-secondary); max-width: 500px; margin: 0 auto 32px; line-height: 1.65; }
    .hipaa-toggle-wrap { display: flex; align-items: center; justify-content: center; gap: 12px; margin-top: 8px; }
    .hipaa-toggle-label { font-size: 14px; color: var(--text-secondary); }
    .toggle { position: relative; width: 44px; height: 26px; cursor: pointer; flex-shrink: 0; }
    .toggle input { opacity: 0; width: 0; height: 0; position: absolute; }
    .toggle-track { position: absolute; inset: 0; background: var(--border); border-radius: 99px; border: 1px solid #D1D5DB; transition: background .2s; }
    .toggle input:checked + .toggle-track { background: var(--teal); border-color: var(--teal); }
    .toggle-thumb { position: absolute; top: 3px; left: 3px; width: 20px; height: 20px; background: #fff; border-radius: 50%; transition: transform .2s; pointer-events: none; box-shadow: 0 1px 3px rgba(0,0,0,.15); }
    .toggle input:checked ~ .toggle-thumb { transform: translateX(18px); }

    /* ── Cards ── */
    .plans { padding: 72px 0; background: var(--bg-alt); border-bottom: 1px solid var(--border); }
    .wrap-wide { max-width: 1300px; }
    .plans-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
    .plan-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 34px 28px; display: flex; flex-direction: column; }
    .plan-card.featured { border: 2px solid var(--teal); }
    .plan-tag { font-size: 12px; font-weight: 600; color: var(--teal); text-transform: uppercase; letter-spacing: .07em; margin-bottom: 16px; }
    .plan-name { font-size: 22px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; }
    .plan-desc { font-size: 14px; color: var(--text-secondary); margin-bottom: 24px; line-height: 1.55; }
    .plan-price { font-size: 36px; font-weight: 700; color: var(--text-primary); line-height: 1; margin-bottom: 6px; }
    .plan-price sup { font-size: 16px; vertical-align: super; font-weight: 600; }
    .plan-price sub { font-size: 14px; font-weight: 400; color: var(--text-secondary); }
    .plan-price-note { font-size: 13px; color: var(--text-secondary); margin-bottom: 24px; min-height: 18px; }
    .plan-divider { height: 1px; background: var(--border); margin: 20px 0; }
    .plan-features { list-style: none; display: flex; flex-direction: column; gap: 12px; flex: 1; margin-bottom: 28px; }
    .plan-features li { font-size: 14px; color: var(--text-secondary); display: flex; gap: 8px; align-items: flex-start; line-height: 1.55; }
    .plan-features li .ck { color: var(--teal); font-weight: 700; flex-shrink: 0; }
    .plan-features li .nx { color: var(--text-muted); flex-shrink: 0; }
    .plan-hipaa { display: flex; align-items: center; gap: 7px; margin-bottom: 20px; font-size: 12px; color: var(--text-secondary); min-height: 24px; }
    .hipaa-pill { background: var(--teal-light); color: var(--teal-dark); font-size: 11px; padding: 3px 10px; border-radius: var(--radius-pill); font-weight: 500; transition: background .2s, color .2s; }
    .hipaa-pill.active { background: #DCFCE7; color: #166534; }
    .hipaa-unavailable { background: #FEF9C3; color: #854D0E; font-size: 11px; padding: 3px 10px; border-radius: var(--radius-pill); font-weight: 500; }
    .plan-btn { width: 100%; padding: 13px; border-radius: var(--radius-md); font-size: 15px; font-weight: 500; cursor: pointer; border: none; text-align: center; display: block; transition: opacity .15s; }
    .plan-btn-primary { background: var(--teal); color: #fff; }
    .plan-btn-primary:hover { opacity: .9; }
    .plan-btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text-secondary); }
    .plan-btn-outline:hover { border-color: #9CA3AF; color: var(--text-primary); }

    /* ── Overage note ── */
    .overage { padding: 56px 0; background: #fff; border-bottom: 1px solid var(--border); }
    .overage-box { background: var(--bg-alt); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px 28px; display: flex; gap: 16px; align-items: flex-start; }
    .overage-icon { width: 36px; height: 36px; border-radius: var(--radius-md); background: var(--teal-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; }
    .overage-box h3 { font-size: 15px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px; }
    .overage-box p { font-size: 14px; color: var(--text-secondary); line-height: 1.6; }

    /* ── HIPAA explainer ── */
    .hipaa-section { padding: 72px 0; background: var(--bg-alt); border-bottom: 1px solid var(--border); }
    .hipaa-box { background: #F0FDFA; border: 1px solid #99F6E4; border-radius: var(--radius-lg); padding: 36px; }
    .hipaa-box h2 { font-size: 22px; font-weight: 700; color: #134E4A; margin-bottom: 10px; }
    .hipaa-box p { font-size: 14px; color: #1F6360; line-height: 1.65; margin-bottom: 20px; }
    .hipaa-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 12px; }
    .hipaa-item { display: flex; gap: 10px; align-items: flex-start; font-size: 13px; color: #1F6360; line-height: 1.5; }
    .hipaa-item .ck { color: var(--teal); font-weight: 700; flex-shrink: 0; margin-top: 1px; }

    /* ── FAQ ── */
    .faq { padding: 72px 0; background: #fff; border-bottom: 1px solid var(--border); }
    .faq-list { display: flex; flex-direction: column; gap: 0; border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; }
    .faq-item { border-bottom: 1px solid var(--border); }
    .faq-item:last-child { border-bottom: none; }
    .faq-q { width: 100%; background: none; border: none; padding: 18px 20px; text-align: left; font-size: 15px; font-weight: 600; color: var(--text-primary); cursor: pointer; display: flex; justify-content: space-between; align-items: center; gap: 16px; }
    .faq-q:hover { background: var(--bg-surface); }
    .faq-icon { font-size: 18px; color: var(--text-muted); flex-shrink: 0; transition: transform .2s; }
    .faq-a { display: none; padding: 0 20px 18px; font-size: 14px; color: var(--text-secondary); line-height: 1.7; }
    .faq-item.open .faq-a { display: block; }
    .faq-item.open .faq-icon { transform: rotate(45deg); }

    /* ── Final CTA ── */
    .pricing-cta { padding: 80px 32px; text-align: center; background: var(--bg-alt); }
    .pricing-cta h2 { font-size: 28px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px; }
    .pricing-cta p { font-size: 16px; color: var(--text-secondary); margin-bottom: 28px; max-width: 400px; margin-left: auto; margin-right: auto; line-height: 1.65; }
    .cta-btns { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

    /* ── Mobile ── */
    @media (max-width: 768px) {
        .pricing-hero { padding: 52px 20px 44px; }
        .pricing-hero h1 { font-size: 30px; }
        .plans-grid { grid-template-columns: 1fr 1fr; }
        .hipaa-grid { grid-template-columns: 1fr; }
        .hipaa-box { padding: 24px 20px; }
        .pricing-cta { padding: 64px 20px; }
    }
    @media (max-width: 480px) {
        .plans-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="pricing-hero">
    <div class="section-label">Pricing</div>
    <h1>Simple, transparent pricing</h1>
    <p>Buy envelopes when you need them. No monthly fees on bundles. No per-user charges. Add HIPAA compliance for healthcare workflows.</p>
    <div class="hipaa-toggle-wrap">
        <span class="hipaa-toggle-label">Without HIPAA add-on</span>
        <label class="toggle">
            <input type="checkbox" id="hipaaToggle" onchange="toggleHipaa(this.checked)">
            <div class="toggle-track"></div>
            <div class="toggle-thumb"></div>
        </label>
        <span class="hipaa-toggle-label">With HIPAA add-on</span>
    </div>
</section>

{{-- Plan cards --}}
<section class="plans">
    <div class="wrap wrap-wide">
        <div class="plans-grid">

            {{-- PAYG --}}
            <div class="plan-card">
                <div class="plan-tag">Pay as you go</div>
                <div class="plan-name">PAYG</div>
                <div class="plan-desc">No commitment. Send envelopes only when you need them.</div>
                <div class="plan-price"><sup>$</sup>10<sub>/envelope</sub></div>
                <div class="plan-price-note">No bundle required</div>
                <div class="plan-divider"></div>
                <ul class="plan-features">
                    <li><span class="ck">✓</span> E-signature collection</li>
                    <li><span class="ck">✓</span> Audit trail per envelope</li>
                    <li><span class="ck">✓</span> Document upload + field placement</li>
                    <li><span class="ck">✓</span> Email delivery</li>
                    <li><span class="nx">✕</span> <span style="opacity: .6;">HIPAA add-on not available</span></li>
                </ul>
                <div class="plan-hipaa">
                    <span class="hipaa-unavailable">No PHI permitted — see TOS</span>
                </div>
                <a href="{{ route('signup') }}?plan=payg" class="plan-btn plan-btn-outline">Get started free</a>
            </div>

            {{-- Starter --}}
            <div class="plan-card">
                <div class="plan-tag">Bundle</div>
                <div class="plan-name">Starter</div>
                <div class="plan-desc">Perfect for small teams or occasional document signing.</div>
                <div class="plan-price"><sup>$</sup>XX</div>
                <div class="plan-price-note">50 envelopes · $X.XX per envelope</div>
                <div class="plan-divider"></div>
                <ul class="plan-features">
                    <li><span class="ck">✓</span> 50 envelopes included</li>
                    <li><span class="ck">✓</span> E-signature + form builder</li>
                    <li><span class="ck">✓</span> Audit trail + document hash</li>
                    <li><span class="ck">✓</span> Email delivery</li>
                    <li><span class="ck">✓</span> Overage billed at $10/envelope</li>
                </ul>
                <div class="plan-hipaa">
                    <span class="hipaa-pill" id="hipaa-starter">HIPAA add-on $49</span>
                </div>
                <a href="{{ route('signup') }}?plan=starter" class="plan-btn plan-btn-outline">Buy Starter bundle</a>
            </div>

            {{-- Standard --}}
            <div class="plan-card featured">
                <div class="plan-tag">Bundle · Most popular</div>
                <div class="plan-name">Standard</div>
                <div class="plan-desc">For growing teams with regular document signing volume.</div>
                <div class="plan-price"><sup>$</sup>XX</div>
                <div class="plan-price-note">250 envelopes · $X.XX per envelope</div>
                <div class="plan-divider"></div>
                <ul class="plan-features">
                    <li><span class="ck">✓</span> 250 envelopes included</li>
                    <li><span class="ck">✓</span> E-signature + form builder</li>
                    <li><span class="ck">✓</span> Audit trail + document hash</li>
                    <li><span class="ck">✓</span> Email + SMS delivery</li>
                    <li><span class="ck">✓</span> Overage billed at $10/envelope</li>
                </ul>
                <div class="plan-hipaa">
                    <span class="hipaa-pill" id="hipaa-standard">HIPAA add-on $49</span>
                </div>
                <a href="{{ route('signup') }}?plan=standard" class="plan-btn plan-btn-primary">Buy Standard bundle</a>
            </div>

            {{-- Enterprise --}}
            <div class="plan-card">
                <div class="plan-tag">Enterprise</div>
                <div class="plan-name">Enterprise</div>
                <div class="plan-desc">High-volume organizations and multi-location businesses.</div>
                <div class="plan-price" style="font-size: 22px; padding-top: 5px;">Custom</div>
                <div class="plan-price-note">Volume-based · contact us</div>
                <div class="plan-divider"></div>
                <ul class="plan-features">
                    <li><span class="ck">✓</span> Custom envelope volume</li>
                    <li><span class="ck">✓</span> All Standard features</li>
                    <li><span class="ck">✓</span> Dedicated onboarding support</li>
                    <li><span class="ck">✓</span> Priority support SLA</li>
                    <li><span class="ck">✓</span> Multi-location management</li>
                </ul>
                <div class="plan-hipaa">
                    <span class="hipaa-pill" id="hipaa-enterprise">HIPAA add-on available</span>
                </div>
                <a href="mailto:hello@abirasign.com?subject=Enterprise inquiry" class="plan-btn plan-btn-outline">Contact sales</a>
            </div>

        </div>
    </div>
</section>

{{-- Overage note --}}
<section class="overage">
    <div class="wrap">
        <div class="overage-box">
            <div class="overage-icon">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7" stroke="#0E7490" stroke-width="1.5"/><path d="M9 5v4l2.5 2.5" stroke="#0E7490" stroke-width="1.5" stroke-linecap="round"/></svg>
            </div>
            <div>
                <h3>What happens when your bundle runs out?</h3>
                <p>Overages are billed automatically at $10 per envelope — the same rate as Pay as you go. You'll receive an email notification when your balance runs low. At any time you can purchase a new bundle and we'll credit your remaining envelope balance toward it.</p>
            </div>
        </div>
    </div>
</section>

{{-- HIPAA explainer --}}
<section class="hipaa-section" id="hipaa">
    <div class="wrap">
        <div class="hipaa-box">
            <div class="section-label" style="color: #0E7490;">HIPAA compliance add-on</div>
            <h2>For healthcare practices handling protected health information</h2>
            <p>Available on Starter, Standard, and Enterprise bundles. The HIPAA add-on is <strong>not available</strong> on Pay as you go — PAYG envelopes must not contain PHI per our Terms of Service. When you add HIPAA compliance, your account is provisioned with a completely isolated database and a signed BAA before any patient data is processed.</p>
            <div class="hipaa-grid">
                <div class="hipaa-item"><span class="ck">✓</span> Dedicated tenant database — your data is never co-mingled with other organizations</div>
                <div class="hipaa-item"><span class="ck">✓</span> Business Associate Agreement (BAA) signed at onboarding</div>
                <div class="hipaa-item"><span class="ck">✓</span> Encrypted storage with 10-year document retention lock</div>
                <div class="hipaa-item"><span class="ck">✓</span> Full audit log on every patient interaction</div>
                <div class="hipaa-item"><span class="ck">✓</span> SHA-256 document integrity hashing on every submission</div>
                <div class="hipaa-item"><span class="ck">✓</span> E-SIGN Act + UETA compliant electronic signatures</div>
            </div>
        </div>
    </div>
</section>

{{-- FAQ --}}
<section class="faq">
    <div class="wrap">
        <div class="section-label">FAQ</div>
        <h2 class="section-title" style="margin-bottom: 28px;">Common questions</h2>
        <div class="faq-list">
            <div class="faq-item">
                <button class="faq-q" onclick="toggleFaq(this)">
                    What counts as an envelope?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-a">An envelope is one completed document session — a patient or recipient receives a secure link, completes and signs the document or form packet, and submits. Each submission consumes one envelope from your bundle regardless of how many individual documents or fields are included in the packet.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" onclick="toggleFaq(this)">
                    Do bundle envelopes expire?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-a">No. Bundle envelopes do not expire. Use them at whatever pace works for your business — there's no monthly rollover or expiry date.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" onclick="toggleFaq(this)">
                    Can I use AbiraSign for healthcare without the HIPAA add-on?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-a">No. If your use case involves protected health information (PHI) — patient names, dates of birth, medical history, diagnoses, or any data covered under HIPAA — you must add the HIPAA compliance add-on. The Pay as you go tier explicitly prohibits PHI in our Terms of Service. Healthcare use without the HIPAA add-on is not permitted.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" onclick="toggleFaq(this)">
                    What is a Business Associate Agreement (BAA)?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-a">A BAA is a legally required contract between a healthcare provider and any vendor that handles PHI on their behalf. Under HIPAA, you cannot share patient data with a software vendor unless a BAA is in place. AbiraSign signs a BAA with every HIPAA add-on customer before their account is activated.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" onclick="toggleFaq(this)">
                    How long are signed documents stored?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-a">All completed documents are stored indefinitely for the life of your account. Healthcare customers with the HIPAA add-on have a minimum 10-year retention lock applied to all documents at the time of storage, in compliance with HIPAA record retention requirements.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" onclick="toggleFaq(this)">
                    Is there a free trial?
                    <span class="faq-icon">+</span>
                </button>
                <div class="faq-a">The Pay as you go tier requires no upfront commitment — you only pay when you send an envelope. This effectively serves as a try-before-you-buy option for general use. HIPAA-enabled accounts require onboarding and a signed BAA, so a self-service trial is not available for healthcare use.</div>
            </div>
        </div>
    </div>
</section>

{{-- Final CTA --}}
<section class="pricing-cta">
    <h2>Ready to get started?</h2>
    <p>Set up in minutes. No IT required. HIPAA-ready when your industry needs it.</p>
    <div class="cta-btns">
        <a href="{{ route('signup') }}" class="btn btn-primary">Get started today</a>
        <a href="mailto:hello@abirasign.com" class="btn btn-ghost">Contact sales</a>
    </div>
</section>

@endsection

@push('scripts')
<script>
function toggleHipaa(on) {
    const ids = ['hipaa-starter', 'hipaa-standard', 'hipaa-enterprise'];
    ids.forEach(id => {
        const el = document.getElementById(id);
        if (on) {
            el.textContent = 'HIPAA add-on included (+$49)';
            el.classList.add('active');
        } else {
            el.textContent = 'HIPAA add-on +$49';
            el.classList.remove('active');
        }
    });
}

function toggleFaq(btn) {
    const item = btn.closest('.faq-item');
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
}
</script>
@endpush
