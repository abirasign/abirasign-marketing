@extends('layouts.app')

@section('title', 'Privacy Policy — AbiraSign')
@section('meta_description', 'AbiraSign Privacy Policy — how we collect, use, and protect your information.')

@push('styles')
<style>
    .legal-hero { padding: 64px 32px 48px; background: #fff; border-bottom: 1px solid var(--border); }
    .legal-hero h1 { font-size: 36px; font-weight: 700; color: var(--text-primary); margin-bottom: 10px; }
    .legal-hero p { font-size: 15px; color: var(--text-secondary); }
    .legal-body { padding: 64px 0; background: var(--bg-alt); border-bottom: 1px solid var(--border); }
    .legal-content { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 48px; }
    .legal-content h2 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin: 32px 0 10px; }
    .legal-content h2:first-child { margin-top: 0; }
    .legal-content p { font-size: 15px; color: var(--text-secondary); line-height: 1.75; margin-bottom: 14px; }
    .legal-content ul { margin: 0 0 14px 20px; }
    .legal-content ul li { font-size: 15px; color: var(--text-secondary); line-height: 1.75; margin-bottom: 6px; }
    .legal-content a { color: var(--teal); }
    .legal-content a:hover { text-decoration: underline; }
    .draft-notice { background: #FEF9C3; border: 1px solid #FDE68A; border-radius: var(--radius-md); padding: 14px 18px; margin-bottom: 32px; display: flex; gap: 10px; align-items: flex-start; }
    .draft-notice p { font-size: 13px; color: #92400E; margin: 0; line-height: 1.55; }
    @media (max-width: 768px) {
        .legal-hero { padding: 48px 20px 36px; }
        .legal-content { padding: 28px 20px; }
    }
</style>
@endpush

@section('content')

<section class="legal-hero">
    <div class="wrap">
        <div class="section-label">Legal</div>
        <h1>Privacy Policy</h1>
        <p>Last updated: {{ date('F j, Y') }} &mdash; BrightNet Technologies LLC, DBA AbiraSign</p>
    </div>
</section>

<section class="legal-body">
    <div class="wrap">
        <div class="legal-content">

            <div class="draft-notice">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="flex-shrink:0;margin-top:1px"><circle cx="8" cy="8" r="7" stroke="#D97706" stroke-width="1.5"/><path d="M8 5v3M8 10.5v.5" stroke="#D97706" stroke-width="1.5" stroke-linecap="round"/></svg>
                <p>This privacy policy is currently being finalized by legal counsel. Final policy will be published prior to general availability. Questions? Contact us at <a href="mailto:hello@abirasign.com">hello@abirasign.com</a>.</p>
            </div>

            <h2>1. Introduction</h2>
            <p>BrightNet Technologies LLC, operating as AbiraSign ("we," "us," or "our"), is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard information when you use our Service at abirasign.com and related applications.</p>
            <p>By using the Service, you consent to the data practices described in this policy. If you do not agree, please do not use the Service.</p>

            <h2>2. Information we collect</h2>
            <p>We collect information you provide directly to us, including:</p>
            <ul>
                <li>Account registration information (name, email address, phone number, business name)</li>
                <li>Billing and payment information (processed securely through our payment processor)</li>
                <li>Documents and form content you upload or create through the Service</li>
                <li>Signature data, IP addresses, and user agent strings collected at the time of signing</li>
                <li>Communications you send to us</li>
            </ul>
            <p>We also collect certain information automatically when you use the Service, including log data, device information, and usage data.</p>

            <h2>3. How we use your information</h2>
            <p>We use the information we collect to:</p>
            <ul>
                <li>Provide, operate, and maintain the Service</li>
                <li>Process transactions and send related information</li>
                <li>Maintain audit trails and document integrity records required for e-signature compliance</li>
                <li>Send administrative communications such as account confirmations and security alerts</li>
                <li>Respond to comments and questions and provide customer support</li>
                <li>Comply with applicable legal obligations</li>
            </ul>

            <h2>4. Protected health information (PHI)</h2>
            <p>Customers who use AbiraSign with the HIPAA compliance add-on may process Protected Health Information (PHI) through the Service. PHI processed on behalf of healthcare customers is governed by a Business Associate Agreement (BAA) and is handled in accordance with HIPAA requirements.</p>
            <p>PHI is stored in a dedicated, isolated database and is never used for any purpose other than providing the contracted Service to the applicable healthcare customer. We do not use PHI for analytics, marketing, or any secondary purpose.</p>
            <p>The Pay as you go tier is not covered by a BAA and must not be used to process PHI. See our Terms of Service for details.</p>

            <h2>5. Data sharing and disclosure</h2>
            <p>We do not sell, trade, or rent your personal information to third parties. We may share information in the following limited circumstances:</p>
            <ul>
                <li><strong>Service providers:</strong> We share information with vendors who assist in operating the Service (cloud infrastructure, email delivery, payment processing) under confidentiality obligations</li>
                <li><strong>Legal requirements:</strong> We may disclose information when required by law, court order, or governmental authority</li>
                <li><strong>Business transfers:</strong> In the event of a merger, acquisition, or sale of assets, your information may be transferred as part of that transaction</li>
            </ul>

            <h2>6. Data retention</h2>
            <p>We retain your account information for the duration of your account and for a reasonable period thereafter. Completed documents are retained for the life of your account. Healthcare customers with the HIPAA add-on have a minimum 10-year retention lock applied to all documents at upload.</p>
            <p>You may request deletion of your account and associated data by contacting us. Note that certain data may be retained as required by law or for legitimate business purposes such as fraud prevention.</p>

            <h2>7. Security</h2>
            <p>We implement industry-standard technical and organizational measures to protect your information, including encrypted data transmission, encrypted storage for sensitive fields, access controls, and comprehensive audit logging. However, no method of transmission over the internet or electronic storage is 100% secure.</p>

            <h2>8. Cookies and tracking</h2>
            <p>We use session cookies necessary for the operation of the Service. We do not use third-party advertising cookies or cross-site tracking technologies. You may disable cookies in your browser settings, but this may affect the functionality of the Service.</p>

            <h2>9. Your rights</h2>
            <p>Depending on your location, you may have certain rights regarding your personal information, including the right to access, correct, or delete your data. To exercise these rights, contact us at <a href="mailto:hello@abirasign.com">hello@abirasign.com</a>.</p>

            <h2>10. Children's privacy</h2>
            <p>The Service is not directed to children under the age of 13. We do not knowingly collect personal information from children under 13. If you believe we have inadvertently collected such information, please contact us immediately.</p>

            <h2>11. Changes to this policy</h2>
            <p>We may update this Privacy Policy from time to time. We will notify you of material changes by updating the "Last updated" date at the top of this page. Your continued use of the Service after changes constitutes acceptance of the updated policy.</p>

            <h2>12. Contact</h2>
            <p>If you have questions or concerns about this Privacy Policy, please contact us at <a href="mailto:hello@abirasign.com">hello@abirasign.com</a> or by mail at BrightNet Technologies LLC, Georgia, United States.</p>

        </div>
    </div>
</section>

@endsection
