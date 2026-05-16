@extends('layouts.app')

@section('title', 'Page Not Found — AbiraSign')

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
            <span style="color: var(--teal);">4</span>04
        </div>
        <div style="font-size: 22px; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">Page not found</div>
        <div style="font-size: 15px; color: var(--text-secondary); line-height: 1.65; margin-bottom: 2.5rem;">
            The page you're looking for doesn't exist or has been moved.
        </div>
        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="/" class="btn btn-primary">Go to homepage</a>
            <a href="/pricing" class="btn btn-ghost">View pricing</a>
        </div>
        <div style="margin-top: 2rem; font-size: 13px; color: var(--text-muted);">
            Need help? <a href="/contact" style="color: var(--teal);">Contact us</a>
        </div>
    </div>
</div>
@endsection
