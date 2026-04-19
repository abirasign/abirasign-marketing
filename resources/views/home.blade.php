@extends('layouts.app')

@section('title', 'AbiraSign — E-Signatures for Any Business')
@section('meta_description', 'Legally binding e-signatures and digital intake forms for any business. HIPAA-compliant add-on available for healthcare practices. No monthly fees on bundles.')

@push('styles')
<style>
    /* ── Hero ── */
    .hero { padding: 80px 32px 72px; text-align: center; background: #fff; border-bottom: 1px solid var(--border); }
    .hero-eyebrow { display: inline-flex; align-items: center; gap: 8px; background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--radius-pill); padding: 5px 14px; font-size: 12px; color: var(--text-secondary); margin-bottom: 24px; }
    .hero-eyebrow-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--teal); flex-shrink: 0; }
    .hero h1 { font-size: 44px; font-weight: 700; color: var(--text-primary); line-height: 1.15; max-width: 660px; margin: 0 auto 18px; }
    .hero h1 em { font-style: normal; color: var(--teal); }
    .hero-sub { font-size: 18px; color: var(--text-secondary); max-width: 540px; margin: 0 auto 36px; line-height: 1.65; }
    .hero-btns { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-bottom: 48px; }
    .trust-bar { display: flex; gap: 28px; justify-content: center; flex-wrap: wrap; padding-top: 32px; border-top: 1px solid var(--border); }
    .trust-item { display: flex; align-items: center; gap: 7px; font-size: 13px; color: var(--text-secondary); }
    .trust-check { color: var(--teal); font-weight: 600; }

    /* ── Use cases ── */
    .use-cases { padding: 80px 0; background: var(--bg-alt); border-bottom: 1px solid var(--border); }
    .uc-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; }
    .uc-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 22px; }
    .uc-icon { font-size: 24px; margin-bottom: 12px; }
    .uc-card h3 { font-size: 15px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px; }
    .uc-card p { font-size: 13px; color: var(--text-secondary); line-height: 1.55; }
    .uc-tag { display: inline-block; margin-top: 10px; font-size: 11px; padding: 3px 9px; border-radius: var(--radius-pill); background: var(--teal-light); color: var(--teal-dark); font-weight: 500; }

    /* ── How it works ── */
    .how { padding: 80px 0; background: #fff; border-bottom: 1px solid var(--border); }
    .steps { display: grid; grid-template-columns: repeat(3, 1fr); gap: 32px; }
    .step-num { width: 36px; height: 36px; border-radius: 50%; background: var(--teal-light); display: flex; align-items: center; justify-content: center; font-size: 14px; font-weight: 600; color: var(--teal-dark); margin-bottom: 14px; }
    .step h3 { font-size: 16px; font-weight: 600; color: var(--text-primary); margin-bottom: 8px; }
    .step p { font-size: 14px; color: var(--text-secondary); line-height: 1.6; }

    /* ── Features ── */
    .features { padding: 80px 0; background: var(--bg-alt); border-bottom: 1px solid var(--border); }
    .feat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; }
    .feat-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px; }
    .feat-icon { width: 38px; height: 38px; border-radius: var(--radius-md); background: var(--teal-light); display: flex; align-items: center; justify-content: center; margin-bottom: 14px; }
    .feat-card h3 { font-size: 15px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px; }
    .feat-card p { font-size: 13px; color: var(--text-secondary); line-height: 1.55; }

    /* ── HIPAA band ── */
    .hipaa-band { padding: 72px 0; background: #F0FDFA; border-top: 1px solid #99F6E4; border-bottom: 1px solid #99F6E4; }
    .hipaa-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: center; }
    .hipaa-inner h2 { font-size: 26px; font-weight: 700; color: #134E4A; margin-bottom: 12px; line-height: 1.3; }
    .hipaa-inner p { font-size: 15px; color: #1F6360; line-height: 1.65; margin-bottom: 8px; }
    .hipaa-checks { display: flex; flex-direction: column; gap: 12px; }
    .hipaa-check { display: flex; gap: 10px; align-items: flex-start; font-size: 14px; color: #1F6360; line-height: 1.5; }
    .hipaa-check .ck { color: var(--teal); font-weight: 700; flex-shrink: 0; margin-top: 1px; }

    /* ── Compare ── */
    .compare { padding: 80px 0; background: #fff; border-bottom: 1px solid var(--border); }
    .compare-table { width: 100%; border-collapse: collapse; font-size: 14px; }
    .compare-table th { text-align: left; padding: 11px 16px; font-weight: 600; font-size: 12px; color: var(--text-secondary); border-bottom: 1px solid var(--border); background: var(--bg-surface); }
    .compare-table th.col-us { color: var(--teal); background: #F0FDFA; }
    .compare-table td { padding: 12px 16px; border-bottom: 1px solid var(--border); color: var(--text-secondary); }
    .compare-table td.col-us { background: #F0FDFA; }
    .compare-table tr:last-child td { border-bottom: none; }
    .compare-table th:not(:first-child), .compare-table td:not(:first-child) { text-align: center; }
    .yes { color: var(--teal); font-weight: 600; }
    .no { color: var(--text-muted); }

    /* ── Social proof ── */
    .social { padding: 80px 0; background: var(--bg-alt); border-bottom: 1px solid var(--border); }
    .testimonials { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 16px; margin-bottom: 40px; }
    .tcard { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px; }
    .tcard-quote { font-size: 14px; color: var(--text-primary); line-height: 1.65; margin-bottom: 16px; font-style: italic; }
    .tcard-author { font-size: 14px; font-weight: 600; color: var(--text-primary); }
    .tcard-role { font-size: 12px; color: var(--text-secondary); margin-top: 2px; }
    .logo-bar { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; padding-top: 32px; border-top: 1px solid var(--border); }
    .logo-pill { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 10px 22px; font-size: 13px; color: var(--text-muted); }

    /* ── Pricing tease ── */
    .pricing-tease { padding: 80px 0; background: #fff; border-bottom: 1px solid var(--border); text-align: center; }
    .price-pills { display: flex; gap: 14px; justify-content: center; flex-wrap: wrap; margin-bottom: 28px; }
    .price-pill { background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 18px 24px; text-align: center; min-width: 140px; }
    .price-pill-name { font-size: 12px; color: var(--text-secondary); margin-bottom: 4px; }
    .price-pill-val { font-size: 22px; font-weight: 700; color: var(--text-primary); }
    .price-pill-note { font-size: 11px; color: var(--text-muted); margin-top: 3px; }

    /* ── Final CTA ── */
    .final-cta { padding: 96px 32px; text-align: center; background: var(--bg-alt); border-bottom: 1px solid var(--border); }
    .final-cta h2 { font-size: 32px; font-weight: 700; color: var(--text-primary); margin-bottom: 14px; }
    .final-cta p { font-size: 16px; color: var(--text-secondary); margin-bottom: 32px; max-width: 420px; margin-left: auto; margin-right: auto; line-height: 1.65; }
    .final-btns { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

    /* ── Mobile ── */
    @media (max-width: 768px) {
        .hero { padding: 56px 20px 52px; }
        .hero h1 { font-size: 32px; }
        .hero-sub { font-size: 16px; }
        .trust-bar { gap: 14px; }
        .steps { grid-template-columns: 1fr; gap: 24px; }
        .hipaa-inner { grid-template-columns: 1fr; gap: 32px; }
        .uc-grid { grid-template-columns: 1fr 1fr; }
        .feat-grid { grid-template-columns: 1fr; }
        .testimonials { grid-template-columns: 1fr; }
        .price-pills { flex-direction: column; align-items: center; }
        .final-cta { padding: 64px 20px; }
        .compare-table th:nth-child(3), .compare-table td:nth-child(3) { display: none; }
    }
    @media (max-width: 480px) {
        .uc-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="hero">
    <div class="hero-eyebrow">
        <div class="hero-eyebrow-dot"></div>
        E-signatures that work for any business — including healthcare
    </div>
    <h1>Get documents signed <em>faster</em>, from anywhere</h1>
    <p class="hero-sub">AbiraSign makes it easy to collect legally binding e-signatures and digital intake forms — with HIPAA compliance built in for the practices that need it.</p>
    <div class="hero-btns">
        <a href="{{ route('signup') }}" class="btn btn-primary">Get started today</a>
        <a href="{{ route('pricing') }}" class="btn btn-ghost">See pricing</a>
    </div>
    <div class="trust-bar">
        <div class="trust-item"><span class="trust-check">✓</span> Legally binding e-signatures</div>
        <div class="trust-item"><span class="trust-check">✓</span> Works on any device</div>
        <div class="trust-item"><span class="trust-check">✓</span> HIPAA-ready when you need it</div>
        <div class="trust-item"><span class="trust-check">✓</span> No monthly fees on bundles</div>
    </div>
</section>

{{-- Use cases --}}
<section class="use-cases">
    <div class="wrap">
        <div class="section-label">Who uses AbiraSign</div>
        <h2 class="section-title">Built for any industry. Purpose-hardened for healthcare.</h2>
        <p class="section-sub">Whether you're collecting patient consents or contractor agreements, AbiraSign handles it — with the compliance layer only when your industry requires it.</p>
        <div class="uc-grid">
            <div class="uc-card">
                <div class="uc-icon">🏥</div>
                <h3>Medical practices</h3>
                <p>Patient intake, consent forms, and clinical documents with full HIPAA compliance and a signed BAA.</p>
                <span class="uc-tag">HIPAA add-on available</span>
            </div>
            <div class="uc-card">
                <div class="uc-icon">⚖️</div>
                <h3>Legal &amp; professional services</h3>
                <p>Engagement letters, NDAs, and retainer agreements signed and returned in minutes, not days.</p>
            </div>
            <div class="uc-card">
                <div class="uc-icon">🏠</div>
                <h3>Real estate</h3>
                <p>Lease agreements, disclosures, and offer letters — signed from any device with a full audit trail.</p>
            </div>
            <div class="uc-card">
                <div class="uc-icon">🏢</div>
                <h3>HR &amp; onboarding</h3>
                <p>Employee agreements, policy acknowledgments, and onboarding packets — collected before day one.</p>
            </div>
            <div class="uc-card">
                <div class="uc-icon">💼</div>
                <h3>Small business</h3>
                <p>Service agreements, proposals, and client contracts. No fax, no printing, no chasing signatures.</p>
            </div>
            <div class="uc-card">
                <div class="uc-icon">🏋️</div>
                <h3>Fitness &amp; wellness</h3>
                <p>Liability waivers, membership agreements, and health intake forms collected digitally.</p>
            </div>
        </div>
    </div>
</section>

{{-- How it works --}}
<section class="how">
    <div class="wrap">
        <div class="section-label">How it works</div>
        <h2 class="section-title">From setup to signed document in minutes</h2>
        <p class="section-sub">No IT team required. Upload your documents, send a link, get it back signed.</p>
        <div class="steps">
            <div class="step">
                <div class="step-num">1</div>
                <h3>Upload or build your documents</h3>
                <p>Upload existing PDFs and place signature fields, or use our form builder to create forms from scratch — checkboxes, yes/no, multi-choice, and more.</p>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <h3>Send a secure link</h3>
                <p>Send via email or SMS. Recipients open a secure link on any device — no account or app download required.</p>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <h3>Signed, stored, and audited</h3>
                <p>Completed documents are stored with SHA-256 integrity hashing and a full audit trail on every action taken.</p>
            </div>
        </div>
    </div>
</section>

{{-- Features --}}
<section class="features">
    <div class="wrap">
        <div class="section-label">Platform features</div>
        <h2 class="section-title">Everything you need. Nothing you don't.</h2>
        <p class="section-sub">Core e-signature features for everyone. HIPAA compliance available as an add-on for healthcare.</p>
        <div class="feat-grid">
            <div class="feat-card">
                <div class="feat-icon">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="2" y="2" width="14" height="14" rx="2" stroke="#0E7490" stroke-width="1.5"/><path d="M5 9h8M5 6h8M5 12h5" stroke="#0E7490" stroke-width="1.5" stroke-linecap="round"/></svg>
                </div>
                <h3>Document upload + field placement</h3>
                <p>Upload any PDF and place signature, date, initials, and text fields exactly where you need them.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M3 14C5 11 7 12 9 9s4-5 5-6" stroke="#0E7490" stroke-width="1.5" stroke-linecap="round"/><circle cx="14" cy="3" r="2" fill="#0E7490"/></svg>
                </div>
                <h3>Draw or type signatures</h3>
                <p>Recipients sign on any device — touchscreen, mouse, or keyboard. Every signature is timestamped and IP-logged.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><rect x="2" y="4" width="14" height="11" rx="2" stroke="#0E7490" stroke-width="1.5"/><path d="M6 8.5h6M6 11h4" stroke="#0E7490" stroke-width="1.5" stroke-linecap="round"/></svg>
                </div>
                <h3>Custom form builder</h3>
                <p>Build intake forms and questionnaires from scratch — text, checkboxes, yes/no, multi-choice, signatures, and more.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M9 2v7m0 0l-2.5-2.5M9 9l2.5-2.5" stroke="#0E7490" stroke-width="1.5" stroke-linecap="round"/><rect x="2" y="12" width="14" height="4" rx="1.5" stroke="#0E7490" stroke-width="1.5"/></svg>
                </div>
                <h3>Form packets</h3>
                <p>Bundle multiple documents into a single packet. One secure link — everything signed in one session.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="7" stroke="#0E7490" stroke-width="1.5"/><path d="M6 9l2.5 2.5L12 7" stroke="#0E7490" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3>Full audit trail</h3>
                <p>Every view, signature, and submission is logged with timestamp, IP address, and user agent — built for compliance.</p>
            </div>
            <div class="feat-card">
                <div class="feat-icon">
                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none"><path d="M9 2l2.5 2.5H15v10H3V4.5h3.5L9 2z" stroke="#0E7490" stroke-width="1.5" stroke-linejoin="round"/><path d="M7 10.5l2 2 3.5-3.5" stroke="#0E7490" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
                <h3>Secure document storage</h3>
                <p>Completed documents stored with SHA-256 integrity verification. Retrieve, view, and download at any time.</p>
            </div>
        </div>
    </div>
</section>

{{-- HIPAA band --}}
<section class="hipaa-band" id="hipaa">
    <div class="wrap">
        <div class="hipaa-inner">
            <div>
                <div class="section-label" style="color: #0E7490;">For healthcare practices</div>
                <h2>HIPAA compliance when your industry demands it</h2>
                <p>Most e-signature platforms treat HIPAA as an afterthought — or won't touch it at all. AbiraSign's HIPAA add-on gives healthcare practices a fully isolated, compliant environment without switching platforms.</p>
                <p>Available on all paid bundles. Includes a signed BAA and dedicated database at onboarding.</p>
            </div>
            <div class="hipaa-checks">
                <div class="hipaa-check"><span class="ck">✓</span> Dedicated tenant database — your data is never co-mingled</div>
                <div class="hipaa-check"><span class="ck">✓</span> Business Associate Agreement signed at onboarding</div>
                <div class="hipaa-check"><span class="ck">✓</span> Encrypted storage with 10-year retention lock</div>
                <div class="hipaa-check"><span class="ck">✓</span> Comprehensive audit log on every patient interaction</div>
                <div class="hipaa-check"><span class="ck">✓</span> SHA-256 document integrity verification</div>
                <div class="hipaa-check"><span class="ck">✓</span> E-SIGN Act + UETA compliant signatures</div>
            </div>
        </div>
    </div>
</section>

{{-- Comparison table --}}
<section class="compare">
    <div class="wrap">
        <div class="section-label">How we compare</div>
        <h2 class="section-title" style="margin-bottom: 28px;">AbiraSign vs. the alternatives</h2>
        <table class="compare-table">
            <thead>
                <tr>
                    <th style="width: 44%;">Feature</th>
                    <th class="col-us">AbiraSign</th>
                    <th>DocuSign / HelloSign</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>Legally binding e-signatures</td><td class="col-us yes">✓</td><td class="yes">✓</td></tr>
                <tr><td>Works on any device</td><td class="col-us yes">✓</td><td class="yes">✓</td></tr>
                <tr><td>Custom form builder</td><td class="col-us yes">✓</td><td class="no">Limited</td></tr>
                <tr><td>No per-user monthly fees</td><td class="col-us yes">✓</td><td class="no">✕</td></tr>
                <tr><td>HIPAA add-on available</td><td class="col-us yes">✓</td><td class="no">Enterprise only</td></tr>
                <tr><td>BAA included</td><td class="col-us yes">✓ on paid plans</td><td class="no">Paid add-on</td></tr>
                <tr><td>Dedicated database per client</td><td class="col-us yes">✓ with HIPAA add-on</td><td class="no">✕</td></tr>
                <tr><td>Buy envelopes as needed</td><td class="col-us yes">✓</td><td class="no">✕ subscription only</td></tr>
            </tbody>
        </table>
    </div>
</section>

{{-- Social proof --}}
<section class="social">
    <div class="wrap">
        <div class="section-label">Trusted by businesses and practices</div>
        <h2 class="section-title" style="margin-bottom: 32px;">What our customers say</h2>
        <div class="testimonials">
            <div class="tcard">
                <p class="tcard-quote">"We evaluated DocuSign but the per-user pricing didn't make sense for our team. AbiraSign's bundle model was exactly what we needed."</p>
                <div class="tcard-author">Operations Manager</div>
                <div class="tcard-role">Professional services firm</div>
            </div>
            <div class="tcard">
                <p class="tcard-quote">"The HIPAA add-on was the deciding factor. We needed a BAA and an isolated database — AbiraSign was the only platform that offered both without enterprise pricing."</p>
                <div class="tcard-author">Practice Manager</div>
                <div class="tcard-role">Multi-location medical clinic · Georgia</div>
            </div>
            <div class="tcard">
                <p class="tcard-quote">"Our clients sign lease agreements from their phones now. The audit trail has already saved us once when a tenant disputed a signature."</p>
                <div class="tcard-author">Principal Broker</div>
                <div class="tcard-role">Real estate brokerage</div>
            </div>
        </div>
        <div class="logo-bar">
            <div class="logo-pill">Client logo</div>
            <div class="logo-pill">Client logo</div>
            <div class="logo-pill">Client logo</div>
            <div class="logo-pill">Client logo</div>
        </div>
    </div>
</section>

{{-- Pricing tease --}}
<section class="pricing-tease">
    <div class="wrap">
        <div class="section-label">Pricing</div>
        <h2 class="section-title">Buy envelopes when you need them</h2>
        <p class="section-sub" style="max-width: 500px; margin-left: auto; margin-right: auto;">No subscriptions. No per-user fees. Buy a bundle or pay as you go — add HIPAA compliance on any paid plan.</p>
        <div class="price-pills">
            <div class="price-pill">
                <div class="price-pill-name">Pay as you go</div>
                <div class="price-pill-val">$10</div>
                <div class="price-pill-note">per envelope</div>
            </div>
            <div class="price-pill">
                <div class="price-pill-name">Starter bundle</div>
                <div class="price-pill-val">$XX</div>
                <div class="price-pill-note">50 envelopes</div>
            </div>
            <div class="price-pill">
                <div class="price-pill-name">Standard bundle</div>
                <div class="price-pill-val">$XX</div>
                <div class="price-pill-note">250 envelopes</div>
            </div>
            <div class="price-pill">
                <div class="price-pill-name">Enterprise</div>
                <div class="price-pill-val">Custom</div>
                <div class="price-pill-note">volume pricing</div>
            </div>
        </div>
        <a href="{{ route('pricing') }}" class="btn btn-ghost">See full pricing details →</a>
    </div>
</section>

{{-- Final CTA --}}
<section class="final-cta">
    <h2>Ready to get documents signed faster?</h2>
    <p>Set up in minutes. No IT required. HIPAA-ready when your industry needs it.</p>
    <div class="final-btns">
        <a href="{{ route('signup') }}" class="btn btn-primary">Get started today</a>
        <a href="mailto:hello@abirasign.com" class="btn btn-ghost">Contact sales</a>
    </div>
</section>

@endsection
