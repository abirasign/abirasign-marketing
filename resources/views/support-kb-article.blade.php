@extends('layouts.app')

@section('title', $article->title . ' — AbiraSign Help')
@section('meta_description', $article->excerpt ? strip_tags($article->excerpt) : 'AbiraSign help article.')

@push('styles')
<style>
.kb-wrap { min-height: calc(100vh - 60px); background: var(--bg-alt); padding: 48px 20px; }
.kb-inner { max-width: 960px; margin: 0 auto; }
.kb-layout { display: grid; grid-template-columns: 1fr 240px; gap: 40px; align-items: start; }
.kb-breadcrumb { font-size: 13px; color: var(--text-muted); margin-bottom: 24px; display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.kb-breadcrumb a { color: var(--teal); text-decoration: none; }
.kb-breadcrumb a:hover { text-decoration: underline; }
.kb-article-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 40px; }
.kb-article-header { margin-bottom: 32px; padding-bottom: 24px; border-bottom: 1px solid var(--border); }
.kb-article-header h1 { font-size: 26px; font-weight: 700; color: var(--text-primary); margin-bottom: 10px; line-height: 1.35; }
.kb-article-meta { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.kb-plan-badge { display: inline-block; font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
.kb-plan-badge.all          { background: #F3F4F6; color: #6B7280; }
.kb-plan-badge.starter      { background: #DCFCE7; color: #166534; }
.kb-plan-badge.professional { background: #EDE9FE; color: #6D28D9; }
.kb-plan-badge.enterprise   { background: #FEF3C7; color: #92400E; }
.kb-article-body { font-size: 15px; color: var(--text-primary); line-height: 1.8; }
.kb-article-body h2 { font-size: 20px; font-weight: 700; margin: 32px 0 12px; color: var(--text-primary); }
.kb-article-body h3 { font-size: 16px; font-weight: 700; margin: 24px 0 8px; color: var(--text-primary); }
.kb-article-body p { margin-bottom: 16px; }
.kb-article-body ul, .kb-article-body ol { margin: 0 0 16px 20px; }
.kb-article-body li { margin-bottom: 6px; }
.kb-article-body a { color: var(--teal); text-decoration: underline; }
.kb-article-body blockquote { border-left: 3px solid var(--teal); padding: 12px 16px; background: var(--teal-light); border-radius: 0 var(--radius-md) var(--radius-md) 0; margin: 20px 0; font-size: 14px; color: var(--text-secondary); }
.kb-article-body pre { background: #1e293b; color: #e2e8f0; padding: 16px; border-radius: var(--radius-md); overflow-x: auto; font-size: 13px; margin: 16px 0; }
.kb-article-body code { background: #F1F5F9; padding: 2px 6px; border-radius: 4px; font-size: 13px; }
.kb-article-body pre code { background: none; padding: 0; }
.kb-sidebar { display: flex; flex-direction: column; gap: 16px; }
.kb-sidebar-card { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; }
.kb-sidebar-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--text-muted); padding: 14px 16px; border-bottom: 1px solid var(--border); }
.kb-related-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; font-size: 13px; color: var(--text-primary); text-decoration: none; border-bottom: 1px solid var(--border); transition: background .12s; gap: 8px; }
.kb-related-item:last-child { border-bottom: none; }
.kb-related-item:hover { background: var(--teal-light); color: var(--teal); }
.kb-cats-side a { display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; font-size: 13px; color: var(--text-primary); text-decoration: none; border-bottom: 1px solid var(--border); transition: background .12s; }
.kb-cats-side a:last-child { border-bottom: none; }
.kb-cats-side a:hover, .kb-cats-side a.active { background: var(--teal-light); color: var(--teal); }
.kb-helpful { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px; text-align: center; margin-top: 24px; }
.kb-helpful p { font-size: 14px; color: var(--text-secondary); margin-bottom: 12px; }
@media (max-width: 640px) { .kb-layout { grid-template-columns: 1fr; } .kb-article-card { padding: 24px; } }
</style>
@endpush

@section('content')
<div class="kb-wrap">
  <div class="kb-inner">

    <div class="kb-breadcrumb">
      <a href="{{ route('support.kb') }}">Knowledge Base</a>
      <span>›</span>
      <a href="{{ route('support.kb.category', $article->category_slug) }}">{{ $article->category_name }}</a>
      <span>›</span>
      <span style="color:var(--text-primary);">{{ $article->title }}</span>
    </div>

    <div class="kb-layout">

      {{-- Article --}}
      <div>
        <div class="kb-article-card">
          <div class="kb-article-header">
            <h1>{{ $article->title }}</h1>
            <div class="kb-article-meta">
              @if($article->plan_badge !== 'all')
                @php
                  $badgeLabels = ['starter'=>'Starter+','professional'=>'Professional+','enterprise'=>'Enterprise Only'];
                @endphp
                <span class="kb-plan-badge {{ $article->plan_badge }}">
                  {{ $badgeLabels[$article->plan_badge] ?? ucfirst($article->plan_badge) }}
                </span>
              @endif
              <span style="font-size:12px;color:var(--text-muted);">{{ number_format($article->views) }} view{{ $article->views === 1 ? '' : 's' }}</span>
              @if($article->published_at)
                <span style="font-size:12px;color:var(--text-muted);">Updated {{ \Carbon\Carbon::parse($article->published_at)->format('M j, Y') }}</span>
              @endif
            </div>
          </div>
          <div class="kb-article-body">
            {!! $article->body !!}
          </div>
        </div>

        <div class="kb-helpful">
          <p>Was this article helpful?</p>
          <a href="{{ route('support.request') }}" style="font-size:13px;color:var(--teal);">
            Still need help? Submit a support request →
          </a>
        </div>
      </div>

      {{-- Sidebar --}}
      <div class="kb-sidebar">

        @if($related->isNotEmpty())
        <div class="kb-sidebar-card">
          <div class="kb-sidebar-title">Related articles</div>
          @foreach($related as $r)
            <a href="{{ route('support.kb.article', $r->slug) }}" class="kb-related-item">
              <span>{{ $r->title }}</span>
              <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" flex-shrink="0">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </a>
          @endforeach
        </div>
        @endif

        <div class="kb-sidebar-card kb-cats-side">
          <div class="kb-sidebar-title">Categories</div>
          @foreach($categories as $cat)
            <a href="{{ route('support.kb.category', $cat->slug) }}"
               class="{{ $cat->id === $article->category_id ? 'active' : '' }}">
              {{ $cat->name }}
              <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </a>
          @endforeach
        </div>

      </div>
    </div>

  </div>
</div>
@endsection
