@extends('layouts.app')

@section('title', 'Submit a Support Request — AbiraSign')
@section('meta_description', 'Submit a support request to the AbiraSign team. We respond within one business day.')

@push('styles')
<style>
    .request-wrap { min-height: calc(100vh - 60px); background: var(--bg-alt); padding: 64px 20px; }
    .request-inner { max-width: 680px; margin: 0 auto; }
    .request-back { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; color: var(--teal); margin-bottom: 28px; }
    .request-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 40px; }
    .request-card h1 { font-size: 24px; font-weight: 700; color: var(--text-primary); margin-bottom: 6px; }
    .request-card-sub { font-size: 14px; color: var(--text-secondary); margin-bottom: 28px; line-height: 1.6; }
    .form-group { margin-bottom: 18px; }
    .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-primary); margin-bottom: 6px; }
    .form-label .optional { color: var(--text-muted); font-weight: 400; font-size: 12px; }
    .form-input { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 14px; color: var(--text-primary); background: #fff; transition: border-color .15s; outline: none; box-sizing: border-box; }
    .form-input:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(14,116,144,.08); }
    .form-input.error { border-color: #EF4444; }
    .form-select { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 14px; color: var(--text-primary); background: #fff; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath d='M2 4l4 4 4-4' stroke='%236B7280' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; cursor: pointer; outline: none; transition: border-color .15s; box-sizing: border-box; }
    .form-select:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(14,116,144,.08); }
    .form-select.error { border-color: #EF4444; }
    .form-textarea { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 14px; color: var(--text-primary); background: #fff; transition: border-color .15s; outline: none; resize: vertical; min-height: 140px; font-family: inherit; line-height: 1.6; box-sizing: border-box; }
    .form-textarea:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(14,116,144,.08); }
    .form-textarea.error { border-color: #EF4444; }
    .form-error { font-size: 12px; color: #EF4444; margin-top: 4px; display: block; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .form-file { width: 100%; padding: 10px 14px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 13px; color: var(--text-secondary); background: #fff; cursor: pointer; box-sizing: border-box; }
    .form-divider { height: 1px; background: var(--border); margin: 24px 0; }
    .phi-warning { background: #FEF3C7; border: 1px solid #FCD34D; border-radius: var(--radius-md); padding: 12px 16px; margin-bottom: 18px; font-size: 13px; color: #92400E; line-height: 1.6; }
    .submit-btn { width: 100%; padding: 13px; background: var(--teal); color: #fff; border: none; border-radius: var(--radius-md); font-size: 15px; font-weight: 600; cursor: pointer; transition: opacity .15s; }
    .submit-btn:hover { opacity: .9; }
    .alert-error { background: #FEF2F2; border: 1px solid #FECACA; border-radius: var(--radius-md); padding: 12px 16px; margin-bottom: 20px; font-size: 14px; color: #B91C1C; }
    @media (max-width: 600px) {
        .request-wrap { padding: 32px 16px; }
        .request-card { padding: 24px 20px; }
        .form-row { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="request-wrap">
    <div class="request-inner">
        <a href="{{ route('support') }}" class="request-back">← Back to support</a>

        <div class="request-card">
            <h1>Submit a support request</h1>
            <p class="request-card-sub">We respond within one business day. For urgent issues, please describe the impact in your message.</p>

            @if($errors->any())
                <div class="alert-error">Please correct the errors below and try again.</div>
            @endif

            <form method="POST" action="{{ route('support.submit') }}" enctype="multipart/form-data">
                @csrf

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="name">Your name</label>
                        <input type="text" id="name" name="name" class="form-input {{ $errors->has('name') ? 'error' : '' }}" value="{{ old('name') }}" placeholder="Jane Smith" autocomplete="name">
                        @error('name')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">Email address</label>
                        <input type="email" id="email" name="email" class="form-input {{ $errors->has('email') ? 'error' : '' }}" value="{{ old('email') }}" placeholder="jane@yourcompany.com" autocomplete="email">
                        @error('email')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="organization">Organization <span class="optional">(optional)</span></label>
                        <input type="text" id="organization" name="organization" class="form-input {{ $errors->has('organization') ? 'error' : '' }}" value="{{ old('organization') }}" placeholder="Acme Medical" autocomplete="organization">
                        @error('organization')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="plan">Your plan</label>
                        <select id="plan" name="plan" class="form-select {{ $errors->has('plan') ? 'error' : '' }}">
                            <option value="" disabled {{ old('plan') ? '' : 'selected' }}>Select plan</option>
                            <option value="payg"         {{ old('plan') == 'payg'         ? 'selected' : '' }}>Pay As You Go</option>
                            <option value="starter"      {{ old('plan') == 'starter'      ? 'selected' : '' }}>Starter</option>
                            <option value="professional" {{ old('plan') == 'professional' ? 'selected' : '' }}>Professional</option>
                            <option value="enterprise"   {{ old('plan') == 'enterprise'   ? 'selected' : '' }}>Enterprise</option>
                            <option value="not_customer" {{ old('plan') == 'not_customer' ? 'selected' : '' }}>Not yet a customer</option>
                        </select>
                        @error('plan')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="topic">Topic</label>
                        <select id="topic" name="topic" class="form-select {{ $errors->has('topic') ? 'error' : '' }}">
                            <option value="" disabled {{ old('topic') ? '' : 'selected' }}>Select topic</option>
                            <option value="billing"           {{ old('topic') == 'billing'           ? 'selected' : '' }}>Billing &amp; subscription</option>
                            <option value="account_users"     {{ old('topic') == 'account_users'     ? 'selected' : '' }}>Account &amp; users</option>
                            <option value="sending_documents" {{ old('topic') == 'sending_documents' ? 'selected' : '' }}>Sending documents</option>
                            <option value="forms_templates"   {{ old('topic') == 'forms_templates'   ? 'selected' : '' }}>Forms &amp; templates</option>
                            <option value="kiosk"             {{ old('topic') == 'kiosk'             ? 'selected' : '' }}>Kiosk mode</option>
                            <option value="hipaa_baa"         {{ old('topic') == 'hipaa_baa'         ? 'selected' : '' }}>HIPAA &amp; BAA</option>
                            <option value="technical_bug"     {{ old('topic') == 'technical_bug'     ? 'selected' : '' }}>Technical issue / bug</option>
                            <option value="other"             {{ old('topic') == 'other'             ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('topic')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="browser">Browser &amp; OS <span class="optional">(optional)</span></label>
                        <input type="text" id="browser" name="browser" class="form-input {{ $errors->has('browser') ? 'error' : '' }}" value="{{ old('browser') }}" placeholder="e.g. Chrome on Windows 11">
                        @error('browser')<span class="form-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" class="form-input {{ $errors->has('subject') ? 'error' : '' }}" value="{{ old('subject') }}" placeholder="Brief summary of your issue">
                    @error('subject')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="message">Description</label>
                    <textarea id="message" name="message" class="form-textarea {{ $errors->has('message') ? 'error' : '' }}" placeholder="Please describe your issue in as much detail as possible. Include any error messages, steps to reproduce, and what you expected to happen.">{{ old('message') }}</textarea>
                    @error('message')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="screenshot">Screenshot <span class="optional">(optional — JPG, PNG, GIF, WebP, PDF · max 5MB)</span></label>
                    <input type="file" id="screenshot" name="screenshot" class="form-file {{ $errors->has('screenshot') ? 'error' : '' }}" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf">
                    @error('screenshot')<span class="form-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-divider"></div>

                <div class="phi-warning">
                    ⚠ <strong>Do not include sensitive or confidential information in this form.</strong>
                    This includes protected health information (PHI), passwords, payment card numbers, Social Security numbers, and any other sensitive personal or business data. Describe issues in general terms only — our team will never ask you to provide this information through a support form.
                </div>

                <div class="form-group">
                    <label style="display:flex; align-items:flex-start; gap:12px; cursor:pointer;">
                        <input type="checkbox" name="no_phi" id="no_phi" value="1"
                               style="width:16px; height:16px; margin-top:2px; flex-shrink:0; accent-color:var(--teal); cursor:pointer;"
                               {{ old('no_phi') ? 'checked' : '' }}>
                        <span style="font-size:13px; color:var(--text-secondary); line-height:1.55;">
                            I confirm this submission does not contain any sensitive or confidential information, including PHI, passwords, or payment data.
                        </span>
                    </label>
                    @error('no_phi')<span class="form-error" style="margin-left:28px;">You must confirm this submission contains no PHI.</span>@enderror
                </div>

                <button type="submit" class="submit-btn">Submit support request →</button>

                <p style="font-size: 12px; color: var(--text-muted); text-align: center; margin-top: 14px; line-height: 1.6;">
                    Your information is never sold or shared with third parties.
                    View our <a href="{{ route('privacy') }}" style="color: var(--teal);">Privacy Policy</a>.
                </p>
            </form>
        </div>
    </div>
</div>
@endsection
