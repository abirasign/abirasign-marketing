@extends('layouts.app')

@section('title', 'Support — AbiraSign')
@section('meta_description', 'Get help with AbiraSign. Search our knowledge base or submit a support request and we\'ll get back to you within one business day.')

@push('styles')
<style>
    .support-wrap { min-height: calc(100vh - 60px); background: var(--bg-alt); display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 80px 20px; }
    .support-hero { text-align: center; margin-bottom: 48px; }
    .support-hero h1 { font-size: 36px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px; }
    .support-hero p { font-size: 17px; color: var(--text-secondary); max-width: 460px; margin: 0 auto; line-height: 1.65; }
    .support-cards { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; max-width: 760px; width: 100%; }
    .support-card { background: #fff; border: 2px solid var(--border); border-radius: var(--radius-lg); padding: 40px 36px; text-align: center; text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 16px; transition: border-color .2s, box-shadow .2s; }
    .support-card:hover { border-color: var(--teal); box-shadow: 0 4px 24px rgba(14,116,144,.10); }
    .support-card-icon { width: 56px; height: 56px; border-radius: 50%; background: var(--teal-light); display: flex; align-items: center; justify-content: center; }
    .support-card h2 { font-size: 18px; font-weight: 700; color: var(--text-primary); }
    .support-card p { font-size: 14px; color: var(--text-secondary); line-height: 1.65; }
    .support-card-cta { font-size: 14px; font-weight: 600; color: var(--teal); margin-top: 4px; }
    @media (max-width: 600px) {
        .support-cards { grid-template-columns: 1fr; }
        .support-hero h1 { font-size: 28px; }
        .support-wrap { padding: 56px 20px; }
    }
</style>
@endpush

@section('content')
<div class="support-wrap">
    <div class="support-hero">
        <div class="section-label">Support</div>
        <h1>How can we help?</h1>
        <p>Search our knowledge base for quick answers, or submit a request and we'll get back to you within one business day.</p>
    </div>

    <div class="support-cards">
        <a href="{{ route('support.kb') }}" class="support-card">
            <div class="support-card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="18" height="18" rx="2" stroke="#0E7490" stroke-width="1.6"/><path d="M7 8h10M7 12h10M7 16h6" stroke="#0E7490" stroke-width="1.6" stroke-linecap="round"/></svg>
            </div>
            <h2>Knowledge Base</h2>
            <p>Browse articles on getting started, billing, HIPAA compliance, sending documents, and more.</p>
            <span class="support-card-cta">Search articles →</span>
        </a>

        <a href="{{ route('support.request') }}" class="support-card">
            <div class="support-card-icon">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z" stroke="#0E7490" stroke-width="1.6" stroke-linejoin="round"/></svg>
            </div>
            <h2>Submit a Request</h2>
            <p>Can't find what you're looking for? Submit a support request and our team will get back to you.</p>
            <span class="support-card-cta">Open a request →</span>
        </a>
    </div>
</div>
@endsection
