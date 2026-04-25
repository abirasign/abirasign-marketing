@extends('layouts.app')

@section('title', 'Pricing — AbiraSign')
@section('meta_description', 'Simple, transparent pricing. No per-envelope fees on paid plans. HIPAA compliance included on Professional and Enterprise. No contracts.')

@push('styles')
<style>
    /* ── Hero ── */
    .pricing-hero { padding: 72px 32px 56px; text-align: center; background: #fff; border-bottom: 1px solid var(--border); }
    .pricing-hero h1 { font-size: 40px; font-weight: 700; color: var(--text-primary); margin-bottom: 14px; line-height: 1.2; }
    .pricing-hero p { font-size: 17px; color: var(--text-secondary); max-width: 520px; margin: 0 auto 0; line-height: 1.65; }

    /* ── Billing toggle ── */
    .billing-toggle-wrap { display: flex; align-items: center; justify-content: center; gap: 16px; margin-top: 28px; }
    .billing-toggle-label { font-size: 15px; color: var(--text-secondary); font-weight: 500; transition: color .2s; }
    .billing-toggle-label.active { color: var(--text-primary); font-weight: 600; }
    .toggle { position: relative; width: 48px; height: 28px; cursor: pointer; flex-shrink: 0; }
    .toggle input { opacity: 0; width: 0; height: 0; position: absolute; }
    .toggle-track { position: absolute; inset: 0; background: var(--teal); border-radius: 99px; transition: background .2s; }
    .toggle-thumb { position: absolute; top: 4px; left: 4px; width: 20px; height: 20px; background: #fff; border-radius: 50%; transition: transform .2s; pointer-events: none; box-shadow: 0 1px 3px rgba(0,0,0,.15); }
    .toggle input:checked ~ .toggle-thumb { transform: translateX(20px); }
    .annual-badge { background: #DCFCE7; color: #166534; font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: var(--radius-pill); }

    /* ── Plans ── */
    .plans { padding: 72px 0; background: var(--bg-alt); border-bottom: 1px solid var(--border); }
    .plans-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
    .plan-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 32px 26px; display: flex; flex-direction: column; }
    .plan-card.featured { border: 2px solid var(--teal); position: relative; }
    .plan-popular { position: absolute; top: -13px; left: 50%; transform: translateX(-50%); background: var(--teal); color: #fff; font-size: 11px; font-weight: 600; padding: 3px 14px; border-radius: var(--radius-pill); white-space: nowrap; }
    .plan-tag { font-size: 11px; font-weight: 600; color: var(--teal); text-transform: uppercase; letter-spacing: .07em; margin-bottom: 12px; }
    .plan-name { font-size: 22px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; }
    .plan-desc { font-size: 13px; color: var(--text-secondary); margin-bottom: 20px; line-height: 1.55; min-height: 40px; }
    .plan-price { font-size: 36px; font-weight: 700; color: var(--text-primary); line-height: 1; margin-bottom: 4px; }
    .plan-price sup { font-size: 16px; vertical-align: super; font-weight: 600; }
    .plan-price sub { font-size: 13px; font-weight: 400; color: var(--text-secondary); }
    .plan-price-note { font-size: 12px; color: var(--text-secondary); margin-bottom: 24px; min-height: 18px; }
    .plan-divider { height: 1px; background: var(--border); margin: 20px 0; }
    .plan-features { list-style: none; display: flex; flex-direction: column; gap: 10px; flex: 1; margin-bottom: 28px; }
    .plan-features li { font-size: 13px; color: var(--text-secondary); display: flex; gap: 8px; align-items: flex-start; line-height: 1.45; }
    .plan-features li .ck { color: var(--teal); font-weight: 700; flex-shrink: 0; }
    .plan-features li .nx { color: var(--text-muted); flex-shrink: 0; }
    .plan-hipaa-badge { display: inline-flex; align-items: center; gap: 6px; background: #DCFCE7; color: #166534; font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: var(--radius-pill); margin-bottom: 20px; }
    .plan-hipaa-none { display: inline-flex; align-items: center; gap: 6px; background: #FEF9C3; color: #854D0E; font-size: 11px; font-weight: 500; padding: 4px 10px; border-radius: var(--radius-pill); margin-bottom: 20px; }
    .plan-btn { width: 100%; padding: 12px; border-radius: var(--radius-md); font-size: 14px; font-weight: 600; cursor: pointer; border: none; text-align: center; display: block; transition: opacity .15s; text-decoration: none; }
    .plan-btn-primary { background: var(--teal); color: #fff; }
    .plan-btn-primary:hover { opacity: .9; }
    .plan-btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text-secondary); }
    .plan-btn-outline:hover { border-color: #9CA3AF; color: var(--text-primary); }

    /* ── Comparison table ── */
    .comparison { padding: 80px 0; background: #fff; border-bottom: 1px solid var(--border); }
    .comparison h2 { font-size: 28px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; }
    .comparison-sub { font-size: 16px; color: var(--text-secondary); margin-bottom: 40px; line-height: 1.6; }
    .comp-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .comp-table th { padding: 12px 16px; font-size: 12px; font-weight: 600; border-bottom: 2px solid var(--border); background: var(--bg-alt); text-align: center; }
    .comp-table th:first-child { text-align: left; width: 30%; }
    .comp-table th.col-featured { background: #F0FDFA; color: var(--teal); border-top: 2px solid var(--teal); }
    .comp-table td { padding: 11px 16px; border-bottom: 1px solid var(--border); color: var(--text-secondary); text-align: center; vertical-align: middle; }
    .comp-table td:first-child { text-align: left; font-size: 13px; color: var(--text-primary); }
    .comp-table td.col-featured { background: #F0FDFA; }
    .comp-table tr:last-child td { border-bottom: none; }
    .comp-table tr:hover td { background: var(--bg-surface); }
    .comp-table tr:hover td.col-featured { background: #E6FBF7; }
    .comp-section-header td { background: var(--bg-alt) !important; font-weight: 600; font-size: 12px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: .06em; padding: 10px 16px; }
    .comp-section-header td.col-featured { background: #E6FBF7 !important; }
    .yes { color: var(--teal); font-size: 15px; font-weight: 700; }
    .no { color: var(--text-muted); font-size: 15px; }
    .val { color: var(--text-primary); font-weight: 500; font-size: 13px; }
    .val-muted { color: var(--text-secondary); font-size: 12px; }

    /* ── Overage ── */
    .overage { padding: 56px 0; background: var(--bg-alt); border-bottom: 1px solid var(--border); }
    .overage-box { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px 28px; display: flex; gap: 16px; align-items: flex-start; }
    .overage-icon { width: 38px; height: 38px; border-radius: var(--radius-md); background: var(--teal-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0; margin-top: 2px; }
    .overage-box h3 { font-size: 15px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px; }
    .overage-box p { font-size: 14px; color: var(--text-secondary); line-height: 1.6; }

    /* ── FAQ ── */
    .faq { padding: 72px 0; background: #fff; border-bottom: 1px solid var(--border); }
    .faq-list { display: flex; flex-direction: column; border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; }
    .faq-item { border-bottom: 1px solid var(--border); }
    .faq-item:last-child { border-bottom: none; }
    .faq-q { width: 100%; background: none; border: none; padding: 18px 20px; text-align: left; font-size: 15px; font-weight: 600; color: var(--text-primary); cursor: pointer; display: flex; justify-content: space-between; align-items: center; gap: 16px; }
    .faq-q:hover { background: var(--bg-surface); }
    .faq-icon { font-size: 20px; color: var(--text-muted); flex-shrink: 0; transition: transform .2s; line-height: 1; }
    .faq-a { display: none; padding: 0 20px 18px; font-size: 14px; color: var(--text-secondary); line-height: 1.7; }
    .faq-item.open .faq-a { display: block; }
    .faq-item.open .faq-icon { transform: rotate(45deg); }

    /* ── CTA ── */
    .pricing-cta { padding: 80px 32px; text-align: center; background: var(--bg-alt); }
    .pricing-cta h2 { font-size: 28px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px; }
    .pricing-cta p { font-size: 16px; color: var(--text-secondary); margin-bottom: 28px; max-width: 420px; margin-left: auto; margin-right: auto; line-height: 1.65; }
    .cta-btns { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

    /* ── Mobile ── */
    @media (max-width: 900px) {
        .plans-grid { grid-template-columns: 1fr 1fr; }
    }
    @media (max-width: 600px) {
        .pricing-hero { padding: 52px 20px 44px; }
        .pricing-hero h1 { font-size: 30px; }
        .plans-grid { grid-template-columns: 1fr; }
        .comp-table th:nth-child(3), .comp-table td:nth-child(3) { display: none; }
        .pricing-cta { padding: 64px 20px; }
    }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="pricing-hero">
    <div class="section-label">Pricing</div>
    <h1>Transparent pricing. No surprises.</h1>
    <p>No per-envelope fees on paid plans. No contracts. HIPAA compliance included on Professional and Enterprise — not a locked-away enterprise feature.</p>
    <div class="billing-toggle-wrap">
        <span class="billing-toggle-label" id="label-monthly">Monthly</span>
        <label class="toggle">
            <input type="checkbox" id="billingToggle" checked onchange="toggleBilling(this.checked)">
            <div class="toggle-track"></div>
            <div class="toggle-thumb"></div>
        </label>
        <span class="billing-toggle-label active" id="label-annual">Annual <span class="annual-badge">Save 10%</span></span>
    </div>
</section>

{{-- Plan cards --}}
<section class="plans">
    <div class="wrap" style="max-width: 1100px;">
        <div class="plans-grid">

            {{-- PAYG --}}
            <div class="plan-card">
                <div class="plan-tag">Pay as you go</div>
                <div class="plan-name">PAYG</div>
                <div class="plan-desc">No commitment. Send envelopes only when you need them.</div>
                <div class="plan-price"><sup>$</sup>10<sub>/envelope</sub></div>
                <div class="plan-price-note">No monthly fee</div>
                <span class="plan-hipaa-none">No PHI permitted — see TOS</span>
                <div class="plan-divider"></div>
                <ul class="plan-features">
                    <li><span class="ck">✓</span> E-signature collection</li>
                    <li><span class="ck">✓</span> Audit trail per envelope</li>
                    <li><span class="ck">✓</span> Document upload + field placement</li>
                    <li><span class="ck">✓</span> Email delivery</li>
                    <li><span class="ck">✓</span> Mobile responsive signing</li>
                    <li><span class="ck">✓</span> Reporting</li>
                    <li><span class="nx">✕</span> <span style="opacity:.6">No HIPAA compliance</span></li>
                    <li><span class="nx">✕</span> <span style="opacity:.6">No form builder</span></li>
                </ul>
                <a href="{{ route('signup') }}?plan=payg" class="plan-btn plan-btn-outline">Get started free</a>
            </div>

            {{-- Starter --}}
            <div class="plan-card">
                <div class="plan-tag">Subscription</div>
                <div class="plan-name">Starter</div>
                <div class="plan-desc">For small teams with regular signing needs. General business use.</div>
                <div class="plan-price"><sup>$</sup><span id="price-starter">40.50</span><sub>/user/mo</sub></div>
                <div class="plan-price-note" id="note-starter">Billed annually · no contract</div>
                <span class="plan-hipaa-none">No HIPAA — general use only</span>
                <div class="plan-divider"></div>
                <ul class="plan-features">
                    <li><span class="ck">✓</span> Unlimited sends</li>
                    <li><span class="ck">✓</span> E-signature collection</li>
                    <li><span class="ck">✓</span> Document upload + field placement</li>
                    <li><span class="ck">✓</span> Pre-made form templates</li>
                    <li><span class="ck">✓</span> Form packets</li>
                    <li><span class="ck">✓</span> User management</li>
                    <li><span class="ck">✓</span> Audit trail</li>
                    <li><span class="ck">✓</span> Email delivery</li>
                    <li><span class="ck">✓</span> Reporting</li>
                </ul>
                <a href="{{ route('signup') }}?plan=starter" class="plan-btn plan-btn-outline">Get started</a>
            </div>

            {{-- Professional --}}
            <div class="plan-card featured">
                <div class="plan-popular">Most popular</div>
                <div class="plan-tag">Subscription</div>
                <div class="plan-name">Professional</div>
                <div class="plan-desc">For growing teams and healthcare practices. HIPAA included.</div>
                <div class="plan-price"><sup>$</sup><span id="price-professional">67.50</span><sub>/user/mo</sub></div>
                <div class="plan-price-note" id="note-professional">Billed annually · no contract</div>
                <span class="plan-hipaa-badge">✓ HIPAA compliance included</span>
                <div class="plan-divider"></div>
                <ul class="plan-features">
                    <li><span class="ck">✓</span> Everything in Starter</li>
                    <li><span class="ck">✓</span> HIPAA compliance + BAA</li>
                    <li><span class="ck">✓</span> Dedicated tenant database</li>
                    <li><span class="ck">✓</span> Custom form builder</li>
                    <li><span class="ck">✓</span> SMS notifications</li>
                    <li><span class="ck">✓</span> Advanced reporting</li>
                    <li><span class="ck">✓</span> Custom branding + logo</li>
                    <li><span class="ck">✓</span> Kiosk mode for in-person signing</li>
                </ul>
                <a href="{{ route('signup') }}?plan=professional" class="plan-btn plan-btn-primary">Get started</a>
            </div>

            {{-- Enterprise --}}
            <div class="plan-card">
                <div class="plan-tag">Enterprise</div>
                <div class="plan-name">Enterprise</div>
                <div class="plan-desc">For multi-location organizations and high-volume businesses.</div>
                <div class="plan-price" style="font-size: 24px; padding-top: 6px;">Custom pricing</div>
                <div class="plan-price-note">Volume-based · contact us</div>
                <span class="plan-hipaa-badge">✓ HIPAA compliance included</span>
                <div class="plan-divider"></div>
                <ul class="plan-features">
                    <li><span class="ck">✓</span> Everything in Professional</li>
                    <li><span class="ck">✓</span> Multiple locations</li>
                    <li><span class="ck">✓</span> API access</li>
                    <li><span class="ck">✓</span> Bulk send</li>
                    <li><span class="ck">✓</span> Custom subdomain</li>
                    <li><span class="ck">✓</span> White-label option</li>
                    <li><span class="ck">✓</span> Dedicated onboarding</li>
                    <li><span class="ck">✓</span> Priority support SLA</li>
                </ul>
                <a href="/contact?reason=sales" class="plan-btn plan-btn-outline">Contact sales</a>
            </div>

        </div>
    </div>
</section>

{{-- Comparison table --}}
<section class="comparison">
    <div class="wrap">
        <div class="section-label">Compare plans</div>
        <h2>Everything side by side</h2>
        <p class="comparison-sub">See exactly what's included at each tier — no hidden footnotes.</p>
        <table class="comp-table">
            <thead>
                <tr>
                    <th>Feature</th>
                    <th>PAYG</th>
                    <th>Starter<br><span id="th-starter" style="font-weight:400;color:var(--text-secondary)">$40.50/user/mo</span></th>
                    <th class="col-featured">Professional<br><span id="th-professional" style="font-weight:400;">$67.50/user/mo</span></th>
                    <th>Enterprise<br><span style="font-weight:400;color:var(--text-secondary)">Custom</span></th>
                </tr>
            </thead>
            <tbody>

                <tr class="comp-section-header">
                    <td colspan="5">Core e-signature</td>
                </tr>
                <tr>
                    <td>E-signature collection</td>
                    <td><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Document upload + field placement</td>
                    <td><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Mobile responsive signing</td>
                    <td><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Audit trail</td>
                    <td><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Email delivery</td>
                    <td><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Reporting</td>
                    <td><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Envelope sends</td>
                    <td><span class="val">$10/envelope</span></td>
                    <td><span class="val">Unlimited</span></td>
                    <td class="col-featured"><span class="val">Unlimited</span></td>
                    <td><span class="val">Unlimited</span></td>
                </tr>

                <tr class="comp-section-header">
                    <td colspan="5">Forms &amp; templates</td>
                </tr>
                <tr>
                    <td>Pre-made form templates</td>
                    <td><span class="no">—</span></td>
                    <td><span class="yes">✓</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Custom form builder</td>
                    <td><span class="no">—</span></td>
                    <td><span class="no">—</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Form packets</td>
                    <td><span class="no">—</span></td>
                    <td><span class="yes">✓</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Kiosk mode (in-person signing)</td>
                    <td><span class="no">—</span></td>
                    <td><span class="no">—</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Bulk send</td>
                    <td><span class="no">—</span></td>
                    <td><span class="no">—</span></td>
                    <td class="col-featured"><span class="no">—</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>

                <tr class="comp-section-header">
                    <td colspan="5">Team &amp; organization</td>
                </tr>
                <tr>
                    <td>User management</td>
                    <td><span class="no">—</span></td>
                    <td><span class="yes">✓</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Multiple locations</td>
                    <td><span class="no">—</span></td>
                    <td><span class="no">—</span></td>
                    <td class="col-featured"><span class="no">—</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>SMS notifications</td>
                    <td><span class="no">—</span></td>
                    <td><span class="no">—</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Advanced reporting</td>
                    <td><span class="no">—</span></td>
                    <td><span class="no">—</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>API access</td>
                    <td><span class="no">—</span></td>
                    <td><span class="no">—</span></td>
                    <td class="col-featured"><span class="no">—</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>

                <tr class="comp-section-header">
                    <td colspan="5">Branding &amp; customization</td>
                </tr>
                <tr>
                    <td>Custom branding + logo</td>
                    <td><span class="no">—</span></td>
                    <td><span class="no">—</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Custom subdomain</td>
                    <td><span class="no">—</span></td>
                    <td><span class="no">—</span></td>
                    <td class="col-featured"><span class="no">—</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>White-label option</td>
                    <td><span class="no">—</span></td>
                    <td><span class="no">—</span></td>
                    <td class="col-featured"><span class="no">—</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>

                <tr class="comp-section-header">
                    <td colspan="5">HIPAA &amp; compliance</td>
                </tr>
                <tr>
                    <td>PHI permitted</td>
                    <td><span class="no">—</span></td>
                    <td><span class="no">—</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>HIPAA compliance</td>
                    <td><span class="no">—</span></td>
                    <td><span class="no">—</span></td>
                    <td class="col-featured"><span class="yes">✓ Included</span></td>
                    <td><span class="yes">✓ Included</span></td>
                </tr>
                <tr>
                    <td>Business Associate Agreement (BAA)</td>
                    <td><span class="no">—</span></td>
                    <td><span class="no">—</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Dedicated tenant database</td>
                    <td><span class="no">—</span></td>
                    <td><span class="no">—</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Document retention lock</td>
                    <td><span class="no">—</span></td>
                    <td><span class="no">—</span></td>
                    <td class="col-featured"><span class="val">10yr adult / 30yr minor</span></td>
                    <td><span class="val">Custom</span></td>
                </tr>
                <tr>
                    <td>E-SIGN Act + UETA compliant</td>
                    <td><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>

                <tr class="comp-section-header">
                    <td colspan="5">Support &amp; onboarding</td>
                </tr>
                <tr>
                    <td>Email support</td>
                    <td><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                    <td class="col-featured"><span class="yes">✓</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Dedicated onboarding</td>
                    <td><span class="no">—</span></td>
                    <td><span class="no">—</span></td>
                    <td class="col-featured"><span class="no">—</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>
                <tr>
                    <td>Priority support SLA</td>
                    <td><span class="no">—</span></td>
                    <td><span class="no">—</span></td>
                    <td class="col-featured"><span class="no">—</span></td>
                    <td><span class="yes">✓</span></td>
                </tr>

            </tbody>
        </table>
    </div>
</section>

{{-- PAYG note --}}
<section class="overage">
    <div class="wrap">
        <div class="overage-box">
            <div class="overage-icon">
                <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7" stroke="#0E7490" stroke-width="1.5"/><path d="M9 5v4l2.5 2.5" stroke="#0E7490" stroke-width="1.5" stroke-linecap="round"/></svg>
            </div>
            <div>
                <h3>How does Pay as you go work?</h3>
                <p>PAYG envelopes are billed at $10 each with no monthly commitment. There are no expiry dates and no minimum purchase. PAYG is intended for general business use only — PHI is not permitted per our <a href="{{ route('terms') }}" style="color: var(--teal);">Terms of Service</a>. If your use case involves patient data, you need Professional or Enterprise.</p>
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
                <button class="faq-q" onclick="toggleFaq(this)">Why is HIPAA compliance not available on Starter?<span class="faq-icon">+</span></button>
                <div class="faq-a">HIPAA compliance requires a dedicated database, a signed Business Associate Agreement, encrypted storage with retention locking, and comprehensive audit logging. These are included in Professional and Enterprise. The Starter tier uses shared infrastructure and is designed for general business use — it must not be used to process Protected Health Information.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" onclick="toggleFaq(this)">What counts as a user?<span class="faq-icon">+</span></button>
                <div class="faq-a">A user is any staff member who logs into your AbiraSign dashboard to send forms, manage documents, or view submissions. The people signing your documents (your patients or clients) are not counted as users and are never charged.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" onclick="toggleFaq(this)">What is a Business Associate Agreement (BAA)?<span class="faq-icon">+</span></button>
                <div class="faq-a">A BAA is a legally required contract between a healthcare provider and any vendor that handles Protected Health Information on their behalf. Under HIPAA, you cannot share patient data with a software vendor without a BAA in place. AbiraSign signs a BAA with every Professional and Enterprise customer before their account is activated for healthcare use.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" onclick="toggleFaq(this)">Can I upgrade or downgrade plans?<span class="faq-icon">+</span></button>
                <div class="faq-a">Yes. You can upgrade at any time and the change takes effect immediately. Downgrades take effect at the end of your current billing period. Note that downgrading from Professional to Starter removes HIPAA compliance — you must ensure no PHI remains in your account before downgrading.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" onclick="toggleFaq(this)">Are there contracts or cancellation fees?<span class="faq-icon">+</span></button>
                <div class="faq-a">No contracts and no cancellation fees on monthly plans. Cancel any time from your account settings. Enterprise customers may have custom contract terms negotiated at signup.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" onclick="toggleFaq(this)">How long are signed documents stored?<span class="faq-icon">+</span></button>
                <div class="faq-a">All completed documents are stored for the life of your account. Professional and Enterprise healthcare customers have a minimum 10-year retention lock applied to adult patient documents and 30-year retention for minor patients, in compliance with HIPAA record retention requirements. Enterprise customers can negotiate custom retention policies.</div>
            </div>
            <div class="faq-item">
                <button class="faq-q" onclick="toggleFaq(this)">What is kiosk mode?<span class="faq-icon">+</span></button>
                <div class="faq-a">Kiosk mode allows patients or clients to complete and sign documents directly on a tablet or computer in your office — without receiving an email link. The form resets after each submission so the next person can start fresh. Ideal for waiting rooms and front-desk check-in workflows.</div>
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
        <a href="{{ route('contact') }}" class="btn btn-ghost">Talk to sales</a>
    </div>
</section>

@endsection

@push('scripts')
<script>
function toggleBilling(isAnnual) {
    document.getElementById('price-starter').textContent      = isAnnual ? '40.50' : '45.00';
    document.getElementById('note-starter').textContent       = isAnnual ? 'Billed annually · no contract' : 'Billed monthly · no contract';
    document.getElementById('price-professional').textContent = isAnnual ? '67.50' : '75.00';
    document.getElementById('note-professional').textContent  = isAnnual ? 'Billed annually · no contract' : 'Billed monthly · no contract';
    document.getElementById('th-starter').textContent         = isAnnual ? '$40.50/user/mo' : '$45.00/user/mo';
    document.getElementById('th-professional').textContent    = isAnnual ? '$67.50/user/mo' : '$75.00/user/mo';
    document.getElementById('label-monthly').classList.toggle('active', !isAnnual);
    document.getElementById('label-annual').classList.toggle('active', isAnnual);
}

function toggleFaq(btn) {
    const item = btn.closest('.faq-item');
    const isOpen = item.classList.contains('open');
    document.querySelectorAll('.faq-item').forEach(i => i.classList.remove('open'));
    if (!isOpen) item.classList.add('open');
}
</script>
@endpush
