@extends('layouts.app')

@section('title', 'Contact Us — AbiraSign')
@section('meta_description', 'Get in touch with the AbiraSign team. Questions about pricing, HIPAA compliance, or enterprise plans — we\'re here to help.')

@push('styles')
<style>
    .contact-wrap { min-height: calc(100vh - 60px); background: var(--bg-alt); display: flex; align-items: flex-start; justify-content: center; padding: 64px 20px; }
    .contact-grid { display: grid; grid-template-columns: 1fr 480px; gap: 56px; max-width: 980px; width: 100%; align-items: start; }
    .contact-left { padding-top: 8px; }
    .contact-left h1 { font-size: 32px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px; line-height: 1.25; }
    .contact-left p { font-size: 16px; color: var(--text-secondary); line-height: 1.65; margin-bottom: 32px; }
    .contact-methods { display: flex; flex-direction: column; gap: 16px; margin-bottom: 36px; }
    .contact-method { display: flex; gap: 14px; align-items: flex-start; }
    .contact-method-icon { width: 38px; height: 38px; border-radius: var(--radius-md); background: var(--teal-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .contact-method-title { font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 2px; }
    .contact-method-detail { font-size: 14px; color: var(--text-secondary); line-height: 1.5; }
    .contact-method-detail a { color: var(--teal); }
    .contact-topics { margin-top: 36px; }
    .contact-topics h3 { font-size: 15px; font-weight: 600; color: var(--text-primary); margin-bottom: 14px; }
    .topic-list { display: flex; flex-direction: column; gap: 8px; }
    .topic-item { display: flex; gap: 10px; align-items: center; font-size: 14px; color: var(--text-secondary); }
    .topic-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--teal); flex-shrink: 0; }
    .contact-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 36px; }
    .contact-card h2 { font-size: 20px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px; }
    .contact-card-sub { font-size: 14px; color: var(--text-secondary); margin-bottom: 28px; }
    .form-group { margin-bottom: 18px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px; }
    .form-label .optional { color: var(--text-muted); font-weight: 400; font-size: 12px; }
    .form-input { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 14px; color: var(--text-primary); background: #fff; transition: border-color .15s; outline: none; }
    .form-input:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(14,116,144,.08); }
    .form-input.error { border-color: #EF4444; }
    .form-select { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 14px; color: var(--text-primary); background: #fff; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath d='M2 4l4 4 4-4' stroke='%236B7280' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; cursor: pointer; outline: none; transition: border-color .15s; }
    .form-select:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(14,116,144,.08); }
    .form-textarea { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 14px; color: var(--text-primary); background: #fff; transition: border-color .15s; outline: none; resize: vertical; min-height: 120px; font-family: inherit; line-height: 1.6; }
    .form-textarea:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(14,116,144,.08); }
    .form-textarea.error { border-color: #EF4444; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .form-error { font-size: 12px; color: #EF4444; margin-top: 4px; display: block; }
    .submit-btn { width: 100%; padding: 13px; background: var(--teal); color: #fff; border: none; border-radius: var(--radius-md); font-size: 15px; font-weight: 600; cursor: pointer; transition: opacity .15s; margin-top: 4px; }
    .submit-btn:hover { opacity: .9; }
    .form-footer { font-size: 12px; color: var(--text-muted); text-align: center; margin-top: 14px; line-height: 1.6; }
    .alert-error { background: #FEF2F2; border: 1px solid #FECACA; border-radius: var(--radius-md); padding: 12px 16px; margin-bottom: 20px; font-size: 14px; color: #B91C1C; }
    @media (max-width: 860px) {
        .contact-grid { grid-template-columns: 1fr; gap: 32px; }
        .contact-left { padding-top: 0; }
    }
    @media (max-width: 480px) {
        .contact-wrap { padding: 32px 16px; }
        .contact-card { padding: 24px 20px; }
        .form-row { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

<div class="contact-wrap">
    <div class="contact-grid">

        {{-- Left --}}
        <div class="contact-left">
            <div class="section-label">Contact</div>
            <h1>We'd love to hear from you</h1>
            <p>Whether you have questions about pricing, need help evaluating HIPAA compliance for your practice, or want to discuss an enterprise plan — reach out and we'll get back to you within one business day.</p>

            <div class="contact-methods">
                <div class="contact-method">
                    <div class="contact-method-icon">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M3 3.5A1.5 1.5 0 014.5 2h.879a1 1 0 01.943.667l.812 2.167a1 1 0 01-.293 1.09L5.5 7c.95 1.9 2.6 3.55 4.5 4.5l1.076-1.341a1 1 0 011.09-.293l2.167.812A1 1 0 0115 11.621V12.5A1.5 1.5 0 0113.5 14C7.701 14 2 8.299 2 2.5A.5.5 0 013 2v1.5z" stroke="#0E7490" stroke-width="1.4" stroke-linejoin="round"/></svg>
                    </div>
                    <div>
                        <div class="contact-method-title">Sales inquiries</div>
                        <div class="contact-method-detail">For enterprise and HIPAA questions,<br>use the form and select the relevant topic.</div>
                    </div>
                </div>
                <div class="contact-method">
                    <div class="contact-method-icon">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="8" cy="8" r="6" stroke="#0E7490" stroke-width="1.4"/><path d="M8 5v3l2 2" stroke="#0E7490" stroke-width="1.4" stroke-linecap="round"/></svg>
                    </div>
                    <div>
                        <div class="contact-method-title">Response time</div>
                        <div class="contact-method-detail">Within one business day for all inquiries.</div>
                    </div>
                </div>
            </div>

            <div class="contact-topics">
                <h3>Common reasons people reach out</h3>
                <div class="topic-list">
                    <div class="topic-item"><div class="topic-dot"></div> Questions about HIPAA compliance and the BAA process</div>
                    <div class="topic-item"><div class="topic-dot"></div> Enterprise pricing and custom volume quotes</div>
                    <div class="topic-item"><div class="topic-dot"></div> Evaluating AbiraSign for a specific use case</div>
                    <div class="topic-item"><div class="topic-dot"></div> Billing and account questions</div>
                </div>
            </div>
        </div>

        {{-- Right: form --}}
        <div class="contact-card">
            <h2>Send us a message</h2>
            <p class="contact-card-sub">We'll get back to you within one business day.</p>
            <div style="background:#f0f9ff;border:1px solid #bae6fd;border-radius:var(--radius-md);padding:11px 14px;margin-bottom:20px;font-size:13px;color:#0c4a6e;line-height:1.6;">
                🛠 <strong>Existing customer looking for support?</strong>
                <a href="{{ route('support.request') }}" style="color:var(--teal);font-weight:600;">Submit a support request here →</a>
            </div>

            @if($errors->any())
                <div class="alert-error">
                    Please correct the errors below and try again.
                </div>
            @endif

            <form method="POST" action="{{ route('contact.submit') }}">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="name">Your name</label>
                        <input type="text" id="name" name="name" class="form-input {{ $errors->has('name') ? 'error' : '' }}" value="{{ old('name') }}" placeholder="Jane Smith" autocomplete="name">
                        @error('name')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="company">Company <span class="optional">(optional)</span></label>
                        <input type="text" id="company" name="company" class="form-input {{ $errors->has('company') ? 'error' : '' }}" value="{{ old('company') }}" placeholder="Acme LLC" autocomplete="organization">
                        @error('company')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email address</label>
                    <input type="email" id="email" name="email" class="form-input {{ $errors->has('email') ? 'error' : '' }}" value="{{ old('email') }}" placeholder="jane@yourcompany.com" autocomplete="email">
                    @error('email')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="phone">Phone <span class="optional">(optional)</span></label>
                    <input type="tel" id="phone" name="phone" class="form-input {{ $errors->has('phone') ? 'error' : '' }}" value="{{ old('phone') }}" placeholder="(555) 000-0000" autocomplete="tel">
                    @error('phone')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="subject">What can we help with?</label>
                    <select id="subject" name="subject" class="form-select {{ $errors->has('subject') ? 'error' : '' }}">
                        <option value="" disabled {{ old('subject') ? '' : 'selected' }}>Select a topic</option>
                        <option value="sales" {{ old('subject') == 'sales' ? 'selected' : '' }}>Sales / Pricing</option>
                        <option value="hipaa" {{ old('subject') == 'hipaa' ? 'selected' : '' }}>HIPAA compliance</option>
                        <option value="general" {{ old('subject') == 'general' ? 'selected' : '' }}>General question</option>
                        <option value="billing" {{ old('subject') == 'billing' ? 'selected' : '' }}>Billing</option>
                        <option value="other" {{ old('subject') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('subject')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="message">Message</label>
                    <textarea id="message" name="message" class="form-textarea {{ $errors->has('message') ? 'error' : '' }}" placeholder="Tell us a bit about your business and what you're looking for...">{{ old('message') }}</textarea>
                    @error('message')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <button type="submit" class="submit-btn">Send message →</button>

                <p class="form-footer">
                    Your information is never sold or shared with third parties.<br>
                    View our <a href="{{ route('privacy') }}" style="color: var(--teal);">Privacy Policy</a>.
                </p>

            </form>
        </div>

    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const reason = new URLSearchParams(window.location.search).get('reason');
    const validReasons = ['sales','hipaa','general','billing','other'];
    if (reason && validReasons.includes(reason)) {
        const select = document.getElementById('subject');
        if (select) select.value = reason;
    }
});
</script>
@endpush

@endsection
