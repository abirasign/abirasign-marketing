@extends('layouts.app')

@section('title', ($type === 'tos' ? 'Terms of Service' : 'Privacy Policy') . ' Archive — AbiraSign')
@section('meta_description', 'Version history for AbiraSign ' . ($type === 'tos' ? 'Terms of Service' : 'Privacy Policy') . '.')

@push('styles')
<style>
    .legal-hero { padding: 64px 32px 48px; background: #fff; border-bottom: 1px solid var(--border); }
    .legal-hero h1 { font-size: 36px; font-weight: 700; color: var(--text-primary); margin-bottom: 10px; }
    .legal-hero p { font-size: 15px; color: var(--text-secondary); }
    .legal-body { padding: 64px 0; background: var(--bg-alt); border-bottom: 1px solid var(--border); }
    .archive-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 28px 32px; margin-bottom: 16px; display: flex; align-items: flex-start; justify-content: space-between; gap: 24px; }
    .archive-card:last-child { margin-bottom: 0; }
    .archive-meta { flex: 1; }
    .archive-version { font-size: 16px; font-weight: 700; color: var(--text-primary); margin-bottom: 4px; display: flex; align-items: center; gap: 10px; }
    .archive-dates { font-size: 13px; color: var(--text-secondary); margin-bottom: 10px; }
    .archive-summary { font-size: 14px; color: var(--text-secondary); line-height: 1.6; }
    .badge-current { display: inline-block; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 20px; background: #D1FAE5; color: #065F46; }
    .badge-pending { display: inline-block; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 20px; background: #FEF9C3; color: #92400E; }
    .badge-archived { display: inline-block; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 20px; background: #F3F4F6; color: #6B7280; }
    .archive-actions { flex-shrink: 0; display: flex; align-items: center; }
    @media (max-width: 640px) {
        .archive-card { flex-direction: column; }
    }
</style>
@endpush

@section('content')

<section class="legal-hero">
    <div class="wrap">
        <div class="section-label">Legal</div>
        <h1>{{ $type === 'tos' ? 'Terms of Service' : 'Privacy Policy' }} — Version History</h1>
        <p>
            All published versions are permanently archived and accessible at a permanent URL.
            &mdash; <a href="{{ $type === 'tos' ? '/terms' : '/privacy' }}" style="color:var(--teal);">View current version →</a>
        </p>
    </div>
</section>

<section class="legal-body">
    <div class="wrap">

        @forelse($versions as $v)
            @php
                $today     = now()->toDateString();
                $isCurrent = $current && $v->id === $current->id;
                $isPending = $v->effective_date > $today;
                $path      = $type === 'tos' ? 'terms' : 'privacy';
            @endphp
            <div class="archive-card">
                <div class="archive-meta">
                    <div class="archive-version">
                        Version {{ $v->version }}
                        @if($isCurrent)
                            <span class="badge-current">Current</span>
                        @elseif($isPending)
                            <span class="badge-pending">Pending — effective {{ \Carbon\Carbon::parse($v->effective_date)->format('M j, Y') }}</span>
                        @else
                            <span class="badge-archived">Archived</span>
                        @endif
                    </div>
                    <div class="archive-dates">
                        Effective {{ \Carbon\Carbon::parse($v->effective_date)->format('F j, Y') }}
                        &nbsp;·&nbsp;
                        Published {{ \Carbon\Carbon::parse($v->published_at)->format('F j, Y') }}
                    </div>
                    @if($v->change_summary)
                        <div class="archive-summary">{!! $v->change_summary !!}</div>
                    @endif
                </div>
                <div class="archive-actions">
                    <a href="/{{ $path }}/v/{{ $v->version }}"
                       style="display:inline-block;padding:8px 18px;border:1px solid var(--border);border-radius:var(--radius-md);font-size:13px;font-weight:600;color:var(--text-primary);text-decoration:none;white-space:nowrap;">
                        View →
                    </a>
                </div>
            </div>
        @empty
            <div style="text-align:center;padding:64px 0;color:var(--text-secondary);font-size:15px;">
                No versions published yet.
            </div>
        @endforelse

    </div>
</section>

@endsection
