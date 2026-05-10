@extends('layouts.app')

@section('title', 'Support Request Received — AbiraSign')

@push('styles')
<style>
    .ty-wrap { min-height: calc(100vh - 60px); background: var(--bg-alt); display: flex; align-items: center; justify-content: center; padding: 64px 20px; }
    .ty-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 56px 48px; max-width: 520px; width: 100%; text-align: center; }
    .ty-icon { width: 56px; height: 56px; border-radius: 50%; background: #DCFCE7; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
    .ty-card h1 { font-size: 24px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px; }
    .ty-card p { font-size: 15px; color: var(--text-secondary); line-height: 1.7; margin-bottom: 10px; }
    @media (max-width: 480px) { .ty-card { padding: 36px 24px; } }
</style>
@endpush

@section('content')
<div class="ty-wrap">
    <div class="ty-card">
        <div class="ty-icon">
            <svg width="26" height="26" viewBox="0 0 26 26" fill="none"><path d="M5 13l5.5 5.5L21 8" stroke="#16A34A" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </div>
        <h1>Request received!</h1>
        <p>Thanks{{ session('support_name') ? ', ' . session('support_name') : '' }}. We've received your support request and will get back to you within one business day.</p>
        <p>Check your inbox for a confirmation email.</p>
        <div style="margin-top: 28px; display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="{{ route('home') }}" class="btn btn-ghost">Back to home</a>
            <a href="{{ route('support') }}" class="btn btn-ghost">Submit another request</a>
        </div>
    </div>
</div>
@endsection
