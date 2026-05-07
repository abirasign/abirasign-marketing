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
    .version-notice { background: #EFF6FF; border: 1px solid #BFDBFE; border-radius: var(--radius-md); padding: 14px 18px; margin-bottom: 32px; display: flex; gap: 10px; align-items: flex-start; }
    .version-notice p { font-size: 13px; color: #1E40AF; margin: 0; line-height: 1.55; }
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
        @if($policy)
            <p>
                Effective {{ \Carbon\Carbon::parse($policy->effective_date)->format('F j, Y') }}
                &mdash; Version {{ $policy->version }}
                &mdash; BrightNet Technologies LLC, DBA AbiraSign
            </p>
            <p style="margin-top:8px;">
                <a href="/privacy/archive" style="font-size:13px;color:var(--teal);">View all versions →</a>
            </p>
        @else
            <p>BrightNet Technologies LLC, DBA AbiraSign</p>
        @endif
    </div>
</section>

<section class="legal-body">
    <div class="wrap">
        <div class="legal-content">

            @if(!$policy)
                <div class="draft-notice">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="flex-shrink:0;margin-top:1px"><circle cx="8" cy="8" r="7" stroke="#D97706" stroke-width="1.5"/><path d="M8 5v3M8 10.5v.5" stroke="#D97706" stroke-width="1.5" stroke-linecap="round"/></svg>
                    <p>This privacy policy is currently being finalized by legal counsel. Final policy will be published prior to general availability. Questions? Contact us at <a href="/contact">Contact us</a>..</p>
                </div>
            @else
                @php
                    $isCurrentVersion = \DB::table('policy_versions')
                        ->where('type', 'pp')
                        ->where('effective_date', '<=', now()->toDateString())
                        ->orderByDesc('effective_date')
                        ->orderByDesc('id')
                        ->value('id') === $policy->id;
                @endphp
                @if(!$isCurrentVersion)
                    <div class="version-notice">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" style="flex-shrink:0;margin-top:1px"><circle cx="8" cy="8" r="7" stroke="#1E40AF" stroke-width="1.5"/><path d="M8 5v3M8 10.5v.5" stroke="#1E40AF" stroke-width="1.5" stroke-linecap="round"/></svg>
                        <p>You are viewing an archived version (v{{ $policy->version }}) of this Privacy Policy. <a href="/privacy">View the current version →</a></p>
                    </div>
                @endif
                <div class="legal-policy-body">
                    {!! $policy->full_text !!}
                </div>
            @endif

        </div>
    </div>
</section>

@endsection
