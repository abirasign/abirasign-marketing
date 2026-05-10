@extends('layouts.app')

@section('title', $category->name . ' — Knowledge Base — AbiraSign')
@section('meta_description', $category->description ?? 'Browse AbiraSign help articles in ' . $category->name . '.')

@push('styles')
<style>
.kb-wrap { min-height: calc(100vh - 60px); background: var(--bg-alt); padding: 48px 20px; }
.kb-inner { max-width: 860px; margin: 0 auto; }
.kb-layout { display: grid; grid-template-columns: 1fr 240px; gap: 32px; align-items: start; }
.kb-article-list { display: flex; flex-direction: column; gap: 10px; }
.kb-article-item { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 18px 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; text-decoration: none; transition: box-shadow .15s, border-color .15s; }
.kb-article-item:hover { box-shadow: 0 2px 10px rgba(0,0,0,.07); border-color: var(--teal); }
.kb-article-title { font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px; }
.kb-article-excerpt { font-size: 13px; color: var(--text-secondary); line-height: 1.5; }
.kb-article-views { font-size: 11px; color: var(--text-muted); margin-top: 4px; }
.kb-article-arrow { color: var(--text-muted); flex-shrink: 0; }
.kb-cats-side { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; }
.kb-cats-side-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--text-muted); padding: 14px 16px; border-bottom: 1px solid var(--border); }
.kb-cats-side a { display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; font-size: 13px; color: var(--text-primary); text-decoration: none; border-bottom: 1px solid var(--border); transition: background .12s; }
.kb-cats-side a:last-child { border-bottom: none; }
.kb-cats-side a:hover, .kb-cats-side a.active { background: var(--teal-light); color: var(--teal); }
@media (max-width: 640px) { .kb-layout { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="kb-wrap">
  <div class="kb-inner">

    <a href="{{ route('support.kb') }}" style="font-size:14px;color:var(--teal);display:inline-flex;align-items:center;gap:6px;margin-bottom:20px;">
      ← Knowledge Base
    </a>

    <div style="margin-bottom:28px;">
      <h1 style="font-size:26px;font-weight:700;color:var(--text-primary);margin-bottom:6px;">{{ $category->name }}</h1>
      @if($category->description)
        <p style="font-size:15px;color:var(--text-secondary);">{{ $category->description }}</p>
      @endif
    </div>

    <div class="kb-layout">
      <div>
        @if($articles->isEmpty())
          <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius-lg);padding:40px;text-align:center;">
            <p style="font-size:14px;color:var(--text-secondary);margin-bottom:16px;">No articles in this category yet.</p>
            <a href="{{ route('support.request') }}" class="btn btn-primary">Submit a support request</a>
          </div>
        @else
          <div class="kb-article-list">
            @foreach($articles as $a)
              <a href="{{ route('support.kb.article', $a->slug) }}" class="kb-article-item">
                <div>
                  <div class="kb-article-title">{{ $a->title }}</div>
                  @if($a->excerpt)
                    <div class="kb-article-excerpt">{{ Str::limit(strip_tags($a->excerpt), 120) }}</div>
                  @endif
                  <div class="kb-article-views">{{ number_format($a->views) }} view{{ $a->views === 1 ? '' : 's' }}</div>
                </div>
                <svg class="kb-article-arrow" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </a>
            @endforeach
          </div>
        @endif
      </div>

      {{-- Category sidebar --}}
      <div class="kb-cats-side">
        <div class="kb-cats-side-title">All categories</div>
        @foreach($categories as $cat)
          <a href="{{ route('support.kb.category', $cat->slug) }}" class="{{ $cat->id === $category->id ? 'active' : '' }}">
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
@endsection
