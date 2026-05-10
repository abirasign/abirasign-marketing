@extends('layouts.app')

@section('title', 'Knowledge Base — AbiraSign')
@section('meta_description', 'Search the AbiraSign knowledge base for help with setup, billing, HIPAA compliance, sending documents, and more.')

@push('styles')
<style>
    .kb-wrap { min-height: calc(100vh - 60px); background: var(--bg-alt); padding: 64px 20px; }
    .kb-hero { text-align: center; margin-bottom: 48px; }
    .kb-hero h1 { font-size: 34px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px; }
    .kb-hero p { font-size: 16px; color: var(--text-secondary); max-width: 460px; margin: 0 auto 28px; line-height: 1.65; }
    .kb-search-wrap { position: relative; max-width: 520px; margin: 0 auto; }
    .kb-search-input { width: 100%; padding: 13px 16px 13px 44px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 15px; color: var(--text-primary); background: #fff; outline: none; transition: border-color .15s; box-sizing: border-box; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
    .kb-search-input:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(14,116,144,.08); }
    .kb-search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none; }
    .kb-inner { max-width: 760px; margin: 0 auto; }
    .kb-coming-soon { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 40px; text-align: center; margin-bottom: 32px; }
    .kb-coming-soon-icon { font-size: 40px; margin-bottom: 16px; }
    .kb-coming-soon h2 { font-size: 20px; font-weight: 700; color: var(--text-primary); margin-bottom: 10px; }
    .kb-coming-soon p { font-size: 14px; color: var(--text-secondary); line-height: 1.7; max-width: 400px; margin: 0 auto 20px; }
    .kb-categories { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .kb-cat { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 20px; display: flex; gap: 14px; align-items: flex-start; opacity: .6; }
    .kb-cat-icon { width: 36px; height: 36px; border-radius: var(--radius-md); background: var(--teal-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .kb-cat h3 { font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 3px; }
    .kb-cat p { font-size: 12px; color: var(--text-secondary); }
    .kb-back { display: inline-flex; align-items: center; gap: 6px; font-size: 14px; color: var(--teal); margin-bottom: 28px; }
    @media (max-width: 600px) {
        .kb-categories { grid-template-columns: 1fr; }
        .kb-hero h1 { font-size: 26px; }
    }
</style>
@endpush

@section('content')
<div class="kb-wrap">
    <div class="kb-inner">
        <a href="{{ route('support') }}" class="kb-back">← Back to support</a>

        <div class="kb-hero">
            <div class="section-label">Knowledge Base</div>
            <h1>Search for answers</h1>
            <p>Browse articles on setup, billing, HIPAA, sending documents, and more.</p>
            <div class="kb-search-wrap">
                <svg class="kb-search-icon" width="18" height="18" viewBox="0 0 18 18" fill="none"><circle cx="7.5" cy="7.5" r="5" stroke="#9CA3AF" stroke-width="1.5"/><path d="M12 12l3.5 3.5" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round"/></svg>
                <input type="text" class="kb-search-input" placeholder="Search articles... (coming soon)" disabled>
            </div>
        </div>

        <div class="kb-coming-soon">
            <div class="kb-coming-soon-icon">📚</div>
            <h2>Knowledge base coming soon</h2>
            <p>We're building out our article library. In the meantime, submit a support request and our team will get back to you within one business day.</p>
            <a href="{{ route('support.request') }}" class="btn btn-primary">Submit a support request</a>
        </div>

        <div class="kb-categories">
            <div class="kb-cat">
                <div class="kb-cat-icon"><svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 2l1.5 3.5H13l-2.5 2 1 3.5L8 9l-3.5 2 1-3.5L3 5.5h3.5L8 2z" stroke="#0E7490" stroke-width="1.3" stroke-linejoin="round"/></svg></div>
                <div><h3>Getting started</h3><p>Setup, account creation, and first steps</p></div>
            </div>
            <div class="kb-cat">
                <div class="kb-cat-icon"><svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="2" y="4" width="12" height="9" rx="1.5" stroke="#0E7490" stroke-width="1.3"/><path d="M5 4V3a1 1 0 011-1h4a1 1 0 011 1v1" stroke="#0E7490" stroke-width="1.3"/></svg></div>
                <div><h3>Billing &amp; subscription</h3><p>Plans, payments, upgrades, and cancellation</p></div>
            </div>
            <div class="kb-cat">
                <div class="kb-cat-icon"><svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M2 4l6 4 6-4" stroke="#0E7490" stroke-width="1.3" stroke-linecap="round"/><rect x="2" y="3" width="12" height="10" rx="1.5" stroke="#0E7490" stroke-width="1.3"/></svg></div>
                <div><h3>Sending documents</h3><p>Email, SMS, reminders, and packet delivery</p></div>
            </div>
            <div class="kb-cat">
                <div class="kb-cat-icon"><svg width="16" height="16" viewBox="0 0 16 16" fill="none"><rect x="2" y="2" width="12" height="12" rx="2" stroke="#0E7490" stroke-width="1.3"/><path d="M5 8h6M5 5.5h6M5 10.5h3" stroke="#0E7490" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                <div><h3>Forms &amp; templates</h3><p>Form builder, packets, and template management</p></div>
            </div>
            <div class="kb-cat">
                <div class="kb-cat-icon"><svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M8 2a6 6 0 100 12A6 6 0 008 2z" stroke="#0E7490" stroke-width="1.3"/><path d="M8 6v2l1.5 1.5" stroke="#0E7490" stroke-width="1.3" stroke-linecap="round"/></svg></div>
                <div><h3>HIPAA &amp; BAA</h3><p>Compliance, BAA process, and PHI handling</p></div>
            </div>
            <div class="kb-cat">
                <div class="kb-cat-icon"><svg width="16" height="16" viewBox="0 0 16 16" fill="none"><circle cx="6" cy="5" r="2.5" stroke="#0E7490" stroke-width="1.3"/><path d="M2 13c0-2.2 1.8-4 4-4s4 1.8 4 4" stroke="#0E7490" stroke-width="1.3" stroke-linecap="round"/><path d="M11 7l1.5 1.5L15 6" stroke="#0E7490" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                <div><h3>Account &amp; users</h3><p>Invitations, roles, and user management</p></div>
            </div>
        </div>
    </div>
</div>
@endsection
