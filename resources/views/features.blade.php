@extends('layouts.app')

@section('title', 'Features — AbiraSign')
@section('meta_description', 'Explore AbiraSign features — e-signatures, digital intake forms, form packets, kiosk mode, HIPAA compliance, and more. Built for any business, purpose-hardened for healthcare.')

@push('styles')
<style>
    /* ── Hero ── */
    .feat-hero { padding: 80px 32px 72px; text-align: center; background: #fff; border-bottom: 1px solid var(--border); }
    .feat-hero h1 { font-size: 40px; font-weight: 700; color: var(--text-primary); margin-bottom: 14px; line-height: 1.2; max-width: 640px; margin-left: auto; margin-right: auto; }
    .feat-hero h1 em { font-style: normal; color: var(--teal); }
    .feat-hero p { font-size: 17px; color: var(--text-secondary); max-width: 520px; margin: 0 auto 32px; line-height: 1.65; }
    .feat-hero-btns { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

    /* ── Section shared ── */
    .feat-section { padding: 80px 0; border-bottom: 1px solid var(--border); }
    .feat-section.alt { background: var(--bg-alt); }
    .feat-section.white { background: #fff; }
    .feat-section.teal { background: #F0FDFA; border-top: 1px solid #99F6E4; border-bottom: 1px solid #99F6E4; }
    .feat-inner { display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: center; }
    .feat-inner.reverse { direction: rtl; }
    .feat-inner.reverse > * { direction: ltr; }
    .feat-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: var(--teal); margin-bottom: 10px; }
    .feat-inner h2 { font-size: 28px; font-weight: 700; color: var(--text-primary); line-height: 1.3; margin-bottom: 14px; }
    .feat-inner p { font-size: 15px; color: var(--text-secondary); line-height: 1.7; margin-bottom: 10px; }
    .feat-checks { display: flex; flex-direction: column; gap: 12px; margin-top: 20px; }
    .feat-check { display: flex; gap: 10px; align-items: flex-start; font-size: 14px; color: var(--text-secondary); line-height: 1.5; }
    .feat-check .ck { color: var(--teal); font-weight: 700; flex-shrink: 0; margin-top: 1px; }
    .feat-check strong { color: var(--text-primary); }

    /* ── Visual panels ── */
    .feat-visual { background: var(--bg-surface); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 32px; display: flex; flex-direction: column; gap: 12px; }
    .feat-visual.teal-bg { background: #E6FBF7; border-color: #99F6E4; }
    .feat-visual.dark-bg { background: #0F172A; border-color: #1E293B; }
    .feat-visual-label { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .07em; color: var(--text-muted); margin-bottom: 4px; }
    .feat-visual-label.light { color: #64748B; }
    .feat-mock-bar { height: 10px; border-radius: 99px; background: var(--teal-light); }
    .feat-mock-bar.full { width: 100%; }
    .feat-mock-bar.three-q { width: 75%; }
    .feat-mock-bar.half { width: 50%; }
    .feat-mock-bar.q { width: 25%; }
    .feat-mock-bar.dark { background: #1E3A5F; }
    .feat-mock-row { display: flex; align-items: center; gap: 10px; padding: 10px 12px; background: #fff; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 12px; color: var(--text-secondary); }
    .feat-mock-row .dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .feat-mock-row .dot.green { background: #22C55E; }
    .feat-mock-row .dot.amber { background: #F59E0B; }
    .feat-mock-row .dot.teal { background: var(--teal); }
    .feat-mock-row .dot.gray { background: #9CA3AF; }
    .feat-mock-badge { display: inline-flex; align-items: center; gap: 5px; font-size: 11px; font-weight: 600; padding: 3px 9px; border-radius: var(--radius-pill); }
    .feat-mock-badge.green { background: #DCFCE7; color: #166534; }
    .feat-mock-badge.teal { background: var(--teal-light); color: var(--teal-dark); }
    .feat-mock-badge.amber { background: #FEF3C7; color: #92400E; }
    .feat-field-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .feat-field { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 10px 12px; }
    .feat-field-label { font-size: 10px; font-weight: 600; color: var(--text-muted); margin-bottom: 4px; text-transform: uppercase; letter-spacing: .05em; }
    .feat-field-val { font-size: 12px; color: var(--text-primary); }
    .feat-field-sig { font-family: cursive; font-size: 18px; color: var(--teal-dark); border-bottom: 1.5px solid var(--teal); padding-bottom: 2px; }
    .kiosk-screen { background: #0F172A; border-radius: var(--radius-lg); padding: 28px 24px; display: flex; flex-direction: column; gap: 16px; }
    .kiosk-header { display: flex; align-items: center; gap: 10px; }
    .kiosk-dot { width: 10px; height: 10px; border-radius: 50%; }
    .kiosk-title { font-size: 14px; font-weight: 600; color: #F1F5F9; }
    .kiosk-sub { font-size: 11px; color: #64748B; }
    .kiosk-field { background: #1E293B; border-radius: var(--radius-md); padding: 12px 14px; }
    .kiosk-field-label { font-size: 10px; color: #64748B; margin-bottom: 4px; text-transform: uppercase; letter-spacing: .05em; }
    .kiosk-field-val { font-size: 13px; color: #F1F5F9; }
    .kiosk-btn { background: var(--teal); color: #fff; border-radius: var(--radius-md); padding: 11px; text-align: center; font-size: 13px; font-weight: 600; }

    /* ── Plan badge grid ── */
    .plan-badge-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-top: 40px; }
    .plan-badge-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px 20px; }
    .plan-badge-card.featured { border: 2px solid var(--teal); }
    .plan-badge-name { font-size: 13px; font-weight: 700; color: var(--text-primary); margin-bottom: 14px; padding-bottom: 10px; border-bottom: 1px solid var(--border); }
    .plan-badge-list { display: flex; flex-direction: column; gap: 8px; }
    .plan-badge-item { font-size: 12px; color: var(--text-secondary); display: flex; gap: 7px; align-items: flex-start; line-height: 1.4; }
    .plan-badge-item .ck { color: var(--teal); font-weight: 700; flex-shrink: 0; }
    .plan-badge-item .nx { color: var(--text-muted); flex-shrink: 0; }

    /* ── Mobile ── */
    @media (max-width: 860px) {
        .feat-inner { grid-template-columns: 1fr; gap: 36px; }
        .feat-inner.reverse { direction: ltr; }
        .plan-badge-grid { grid-template-columns: 1fr 1fr; }
        .feat-hero { padding: 56px 20px 52px; }
        .feat-hero h1 { font-size: 30px; }
    }
    @media (max-width: 480px) {
        .plan-badge-grid { grid-template-columns: 1fr; }
        .feat-field-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- Hero --}}
<section class="feat-hero">
    <div class="section-label">Features</div>
    <h1>Everything you need to collect signatures <em>and</em> run intake</h1>
    <p>AbiraSign goes beyond basic e-signatures — it's a complete document and intake platform built for the way real businesses operate, with HIPAA compliance built in for healthcare.</p>
    <div class="feat-hero-btns">
        <a href="{{ route('signup') }}" class="btn btn-primary">Start free trial</a>
        <a href="{{ route('pricing') }}" class="btn btn-ghost">See pricing</a>
    </div>
</section>

{{-- 1. Core e-signatures --}}
<section class="feat-section white">
    <div class="wrap">
        <div class="feat-inner">
            <div>
                <div class="feat-label">E-signatures</div>
                <h2>Legally binding signatures on any document</h2>
                <p>Upload any PDF and place signature, date, initials, and text fields exactly where you need them. Recipients sign on any device — no account or app download required.</p>
                <p>Every signature is timestamped, IP-logged, and stored with a complete audit trail. Fully compliant with the E-SIGN Act and UETA.</p>
                <div class="feat-checks">
                    <div class="feat-check"><span class="ck">✓</span><span><strong>Draw or type signatures</strong> — touchscreen, mouse, or keyboard</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>Multi-field placement</strong> — signature, date, initials, printed name, text, checkbox</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>Full audit trail</strong> — every view, open, and submission logged with timestamp + IP</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>E-SIGN Act + UETA compliant</strong> — legally binding in all 50 states</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>SHA-256 document integrity</strong> — tamper-evident storage on every completed document</span></div>
                </div>
            </div>
            <div class="feat-visual">
                <div class="feat-visual-label">Signature request</div>
                <div class="feat-field-grid">
                    <div class="feat-field">
                        <div class="feat-field-label">Patient name</div>
                        <div class="feat-field-val">Jane Smith</div>
                    </div>
                    <div class="feat-field">
                        <div class="feat-field-label">Date</div>
                        <div class="feat-field-val">{{ now()->format('m/d/Y') }}</div>
                    </div>
                    <div class="feat-field" style="grid-column: span 2;">
                        <div class="feat-field-label">Signature</div>
                        <div class="feat-field-sig">Jane Smith</div>
                    </div>
                </div>
                <div class="feat-mock-row">
                    <div class="dot green"></div>
                    Completed · {{ now()->format('M j, Y g:i A') }}
                </div>
                <div class="feat-mock-row">
                    <div class="dot teal"></div>
                    SHA-256 integrity verified
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 2. Form builder & intake --}}
<section class="feat-section alt">
    <div class="wrap">
        <div class="feat-inner reverse">
            <div>
                <div class="feat-label">Forms &amp; intake</div>
                <h2>Build intake forms from scratch — no coding required</h2>
                <p>AbiraSign's form builder lets you create custom intake forms, questionnaires, and consent documents without uploading a PDF. Build once, send to anyone.</p>
                <p>Bundle multiple documents into a single packet — patients or clients complete everything in one seamless session from a single link.</p>
                <div class="feat-checks">
                    <div class="feat-check"><span class="ck">✓</span><span><strong>Rich field types</strong> — text, yes/no, multi-choice, checkbox, date, paragraph, signature, and disclaimer blocks</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>WYSIWYG disclaimer editor</strong> — rich text with inline signature capture for consent acknowledgements</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>Multi-page forms</strong> — organize long intake flows across multiple pages</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>Form packets</strong> — bundle PDFs and forms into a single link, completed in order</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>Pre-made templates</strong> — start from a library of common business and healthcare forms</span></div>
                </div>
            </div>
            <div class="feat-visual">
                <div class="feat-visual-label">Form builder</div>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <div class="feat-mock-row"><div class="dot teal"></div> Full name <span style="margin-left:auto;"><span class="feat-mock-badge teal">Text</span></span></div>
                    <div class="feat-mock-row"><div class="dot teal"></div> Date of birth <span style="margin-left:auto;"><span class="feat-mock-badge teal">Date</span></span></div>
                    <div class="feat-mock-row" style="flex-wrap:nowrap;"><div class="dot teal"></div> <span style="flex:1; min-width:0; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">Have you had surgery in the last 6 months?</span> <span style="flex-shrink:0; margin-left:8px;"><span class="feat-mock-badge teal">Yes / No</span></span></div>
                    <div class="feat-mock-row"><div class="dot teal"></div> Current medications <span style="margin-left:auto;"><span class="feat-mock-badge teal">Paragraph</span></span></div>
                    <div class="feat-mock-row"><div class="dot teal"></div> Patient consent <span style="margin-left:auto;"><span class="feat-mock-badge green">Disclaimer + Sig</span></span></div>
                </div>
                <div style="display:flex; gap:8px; margin-top:4px;">
                    <span class="feat-mock-badge teal">3 documents</span>
                    <span class="feat-mock-badge green">1 packet</span>
                    <span class="feat-mock-badge teal">1 secure link</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 3. Delivery & workflow --}}
<section class="feat-section white">
    <div class="wrap">
        <div class="feat-inner">
            <div>
                <div class="feat-label">Delivery &amp; workflow</div>
                <h2>Send via email, SMS, or let patients sign in your office</h2>
                <p>AbiraSign fits any workflow — send a secure link ahead of an appointment, follow up with a reminder, or have clients sign on a tablet in your waiting room.</p>
                <p>No app downloads, no accounts, no friction. Recipients open a link, complete the form, and you're notified instantly.</p>
                <div class="feat-checks">
                    <div class="feat-check"><span class="ck">✓</span><span><strong>Email &amp; SMS delivery</strong> — send secure signing links via email, text, or both</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>Manual reminders</strong> — resend a secure link to anyone with a pending document in one click</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>No account required</strong> — recipients sign from any device without creating a login</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>Instant notifications</strong> — get notified the moment a document is completed</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>Location-scoped sending</strong> — multi-location teams send and manage documents per location</span></div>
                </div>
            </div>
            <div class="feat-visual">
                <div class="feat-visual-label">Recent activity</div>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <div class="feat-mock-row"><div class="dot green"></div> Sarah Johnson — New Patient Intake <span style="margin-left:auto;"><span class="feat-mock-badge green">Completed</span></span></div>
                    <div class="feat-mock-row"><div class="dot amber"></div> Michael Torres — Consent Form <span style="margin-left:auto;"><span class="feat-mock-badge amber">Pending</span></span></div>
                    <div class="feat-mock-row"><div class="dot teal"></div> Lisa Chen — HIPAA Authorization <span style="margin-left:auto;"><span class="feat-mock-badge teal">Opened</span></span></div>
                    <div class="feat-mock-row"><div class="dot gray"></div> James Park — Service Agreement <span style="margin-left:auto;"><span class="feat-mock-badge amber">Pending</span></span></div>
                </div>
                <div style="display:flex; gap:8px; margin-top:4px;">
                    <span class="feat-mock-badge green">Email ✓</span>
                    <span class="feat-mock-badge teal">SMS ✓</span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 4. Kiosk mode --}}
<section class="feat-section alt">
    <div class="wrap">
        <div class="feat-inner reverse">
            <div>
                <div class="feat-label">Kiosk mode</div>
                <h2>In-person signing — right in your office</h2>
                <p>Set up a tablet or computer at your front desk and let patients or clients complete their forms on-site — no email, no link, no staff involvement required.</p>
                <p>Kiosk mode is PIN-protected, auto-resets after each session, and wipes the screen if the tab loses focus — keeping every patient's information private.</p>
                <div class="feat-checks">
                    <div class="feat-check"><span class="ck">✓</span><span><strong>PIN-protected launch</strong> — staff enter a PIN to start each session, patients can't access the dashboard</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>Auto-reset after completion</strong> — screen clears automatically so the next patient can start fresh</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>Privacy protection</strong> — session wipes instantly if the browser tab loses focus</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>Location-scoped</strong> — each location has its own kiosk config, packet, and PIN</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>Works on any device</strong> — iPad, Android tablet, or any browser-enabled computer</span></div>
                </div>
                <p style="margin-top: 20px; font-size: 13px; color: var(--text-muted);">Available on Professional and Enterprise plans.</p>
            </div>
            <div class="kiosk-screen">
                <div class="kiosk-header">
                    <div class="kiosk-dot" style="background:#22C55E;"></div>
                    <div>
                        <div class="kiosk-title">Patient Check-In</div>
                        <div class="kiosk-sub">Westside Medical · Front Desk</div>
                    </div>
                </div>
                <div class="kiosk-field">
                    <div class="kiosk-field-label">Full name</div>
                    <div class="kiosk-field-val">Jane Smith</div>
                </div>
                <div class="kiosk-field">
                    <div class="kiosk-field-label">Date of birth</div>
                    <div class="kiosk-field-val">04 / 12 / 1985</div>
                </div>
                <div class="kiosk-field" style="background:#0F3460;">
                    <div class="kiosk-field-label">Signature</div>
                    <div style="font-family:cursive; font-size:20px; color:#7DD3FC; border-bottom:1px solid #334155; padding-bottom:4px;">Jane Smith</div>
                </div>
                <div class="kiosk-btn">Submit &amp; complete →</div>
            </div>
        </div>
    </div>
</section>

{{-- 5. HIPAA & compliance --}}
<section class="feat-section teal">
    <div class="wrap">
        <div class="feat-inner">
            <div>
                <div class="feat-label" style="color:#0E7490;">HIPAA &amp; compliance</div>
                <h2 style="color:#134E4A;">Built for healthcare from the ground up</h2>
                <p style="color:#1F6360;">Most e-signature platforms treat HIPAA as an afterthought. AbiraSign builds compliance in — dedicated infrastructure, a signed BAA, and retention locking are all part of the plan, not paid extras.</p>
                <p style="color:#1F6360;">Required on Professional and Enterprise. A BAA is executed before your account is activated for healthcare use.</p>
                <div class="feat-checks">
                    <div class="feat-check"><span class="ck">✓</span><span style="color:#1F6360;"><strong style="color:#134E4A;">Dedicated tenant database</strong> — your PHI is never co-mingled with other clients' data</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span style="color:#1F6360;"><strong style="color:#134E4A;">Business Associate Agreement</strong> — signed at onboarding, required by HIPAA</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span style="color:#1F6360;"><strong style="color:#134E4A;">Retention lock</strong> — 10 years for adult records, 30 years for minor records, per applicable law</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span style="color:#1F6360;"><strong style="color:#134E4A;">SHA-256 document integrity</strong> — tamper-evident hashing on every stored document</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span style="color:#1F6360;"><strong style="color:#134E4A;">Comprehensive audit log</strong> — every patient interaction logged with timestamp, IP, and user agent</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span style="color:#1F6360;"><strong style="color:#134E4A;">Encrypted storage</strong> — all PHI stored encrypted at rest on HIPAA-compliant infrastructure</span></div>
                </div>
            </div>
            <div class="feat-visual teal-bg">
                <div class="feat-visual-label">HIPAA compliance status</div>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <div class="feat-mock-row"><div class="dot green"></div> Dedicated database provisioned</div>
                    <div class="feat-mock-row"><div class="dot green"></div> BAA executed · {{ now()->format('M j, Y') }}</div>
                    <div class="feat-mock-row"><div class="dot green"></div> Retention lock active — 10yr / 30yr</div>
                    <div class="feat-mock-row"><div class="dot green"></div> Encrypted storage — GCS HIPAA bucket</div>
                    <div class="feat-mock-row"><div class="dot green"></div> SHA-256 integrity on all documents</div>
                    <div class="feat-mock-row"><div class="dot green"></div> Audit log — all patient interactions</div>
                </div>
                <span class="feat-mock-badge green" style="margin-top:4px; font-size:12px; padding: 5px 12px;">✓ HIPAA compliant · Professional &amp; Enterprise</span>
            </div>
        </div>
    </div>
</section>

{{-- 6. Security & storage --}}
<section class="feat-section white">
    <div class="wrap">
        <div class="feat-inner reverse">
            <div>
                <div class="feat-label">Security &amp; storage</div>
                <h2>Your documents are safe — and provably so</h2>
                <p>Every completed document is stored with SHA-256 integrity verification, making any tampering detectable. Documents are available to retrieve, view, and download at any time for the life of your account.</p>
                <div class="feat-checks">
                    <div class="feat-check"><span class="ck">✓</span><span><strong>SHA-256 hashing</strong> — computed from final stored PDF bytes, stored in the audit log and document metadata</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>Dedicated database per account</strong> — every plan, including PAYG, gets an isolated tenant database</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>Google Cloud Storage</strong> — enterprise-grade object storage with redundancy</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>Audit log on every action</strong> — who sent it, who opened it, who signed it, when, and from where</span></div>
                    <div class="feat-check"><span class="ck">✓</span><span><strong>No hard deletes</strong> — documents are never permanently destroyed from your account history</span></div>
                </div>
            </div>
            <div class="feat-visual">
                <div class="feat-visual-label">Document audit trail</div>
                <div style="display:flex; flex-direction:column; gap:8px;">
                    <div class="feat-mock-row"><div class="dot teal"></div> Sent via email · {{ now()->subMinutes(32)->format('g:i A') }}</div>
                    <div class="feat-mock-row"><div class="dot amber"></div> Opened · {{ now()->subMinutes(28)->format('g:i A') }} · 192.168.x.x</div>
                    <div class="feat-mock-row"><div class="dot green"></div> Signed · {{ now()->subMinutes(25)->format('g:i A') }}</div>
                    <div class="feat-mock-row"><div class="dot green"></div> PDF stored · SHA-256 verified</div>
                </div>
                <div style="background: var(--bg-alt); border-radius: var(--radius-md); padding: 10px 12px; font-size: 11px; color: var(--text-muted); word-break: break-all; font-family: monospace;">
                    a3f2c1e8b4d...9f7a2c4e1b SHA-256
                </div>
            </div>
        </div>
    </div>
</section>

{{-- 7. Plan summary --}}
<section class="feat-section alt">
    <div class="wrap">
        <div class="section-label">Plans</div>
        <h2 class="section-title">Features by plan</h2>
        <p class="section-sub">Every account gets a dedicated database. HIPAA compliance and advanced features are included on Professional and Enterprise.</p>
        <div class="plan-badge-grid">

            <div class="plan-badge-card">
                <div class="plan-badge-name">PAYG — $10/envelope</div>
                <div class="plan-badge-list">
                    <div class="plan-badge-item"><span class="ck">✓</span> E-signature collection</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> Document upload + field placement</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> Audit trail</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> Email delivery</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> Dedicated database</div>
                    <div class="plan-badge-item"><span class="nx">✕</span> No HIPAA / PHI</div>
                </div>
            </div>

            <div class="plan-badge-card">
                <div class="plan-badge-name">Starter — $45/user/mo</div>
                <div class="plan-badge-list">
                    <div class="plan-badge-item"><span class="ck">✓</span> Everything in PAYG</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> Unlimited sends</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> Pre-made templates</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> Form packets</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> User management</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> 14-day free trial</div>
                    <div class="plan-badge-item"><span class="nx">✕</span> No HIPAA / PHI</div>
                </div>
            </div>

            <div class="plan-badge-card featured">
                <div class="plan-badge-name" style="color:var(--teal);">Professional — $75/user/mo</div>
                <div class="plan-badge-list">
                    <div class="plan-badge-item"><span class="ck">✓</span> Everything in Starter</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> HIPAA compliance + BAA</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> Custom form builder</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> Kiosk mode</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> SMS delivery</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> Custom branding</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> 14-day free trial</div>
                </div>
            </div>

            <div class="plan-badge-card">
                <div class="plan-badge-name">Enterprise — Custom</div>
                <div class="plan-badge-list">
                    <div class="plan-badge-item"><span class="ck">✓</span> Everything in Professional</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> Multiple locations</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> API access</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> Bulk send</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> Custom subdomain</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> White-label</div>
                    <div class="plan-badge-item"><span class="ck">✓</span> Dedicated onboarding</div>
                </div>
            </div>

        </div>
        <div style="text-align:center; margin-top:32px;">
            <a href="{{ route('pricing') }}" class="btn btn-ghost">See full pricing details →</a>
        </div>
    </div>
</section>

{{-- Final CTA --}}
<section style="padding: 96px 32px; text-align: center; background: #fff; border-top: 1px solid var(--border);">
    <div class="section-label">Get started</div>
    <h2 class="section-title">Ready to see it in action?</h2>
    <p class="section-sub" style="max-width: 420px; margin-left: auto; margin-right: auto;">Starter and Professional include a 14-day free trial. No IT required. HIPAA-ready when your industry needs it.</p>
    <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap; margin-top:28px;">
        <a href="{{ route('signup') }}" class="btn btn-primary">Start free trial</a>
        <a href="/contact?reason=sales" class="btn btn-ghost">Talk to sales</a>
    </div>
</section>

@endsection
