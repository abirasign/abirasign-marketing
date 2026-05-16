@extends('layouts.app')

@section('title', 'Server Error — AbiraSign')

@section('content')
<div style="
    min-height: calc(100vh - 60px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 4rem 1.5rem;
    background: var(--bg-page);
">
    <div style="text-align: center; max-width: 480px; width: 100%;">
        <div style="font-size: 80px; font-weight: 700; color: #E0F2FE; line-height: 1; margin-bottom: 0.5rem; letter-spacing: -3px;">
            <span style="color: var(--teal);">5</span>00
        </div>
        <div style="font-size: 22px; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Something went wrong</div>
        <div style="font-size: 15px; color: var(--text-secondary); line-height: 1.65; margin-bottom: 2.5rem;">
            We encountered an unexpected error. Our team has been notified. Please try again in a moment.
        </div>
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="/" class="btn btn-primary">Go to homepage</a>
            <a href="/contact" class="btn btn-ghost">Contact us</a>
        </div>
    </div>
</div>
@endsection
