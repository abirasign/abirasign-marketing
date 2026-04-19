@extends('layouts.app')

@section('title', 'Message Sent — AbiraSign')

@push('styles')
<style>
    .thankyou-wrap { min-height: calc(100vh - 60px); background: var(--bg-alt); display: flex; align-items: center; justify-content: center; padding: 64px 20px; }
    .thankyou-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 56px 48px; max-width: 480px; width: 100%; text-align: center; }
    .thankyou-icon { width: 56px; height: 56px; border-radius: 50%; background: #DCFCE7; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
    .thankyou-card h1 { font-size: 26px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px; }
    .thankyou-card p { font-size: 15px; color: var(--text-secondary); line-height: 1.7; margin-bottom: 10px; }
    .thankyou-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; margin-top: 28px; }
    @media (max-width: 480px) {
        .thankyou-card { padding: 36px 24px; }
    }
</style>
@endpush

@section('content')

<div class="thankyou-wrap">
    <div class="thankyou-card">
        <div class="thankyou-icon">
            <svg width="26" height="26" viewBox="0 0 26 26" fill="none"><path d="M5 13l5.5 5.5L21 8" stroke="#16A34A" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <h1>Message received</h1>
        <p>Thanks{{ session('contact_name') ? ', ' . session('contact_name') : '' }}. We've received your message and will get back to you within one business day.</p>
        <p>In the meantime, feel free to explore our pricing or learn more about the platform.</p>
        <div class="thankyou-actions">
            <a href="{{ route('pricing') }}" class="btn btn-primary">View pricing</a>
            <a href="{{ route('home') }}" class="btn btn-ghost">Back to home</a>
        </div>
    </div>
</div>

@endsection
