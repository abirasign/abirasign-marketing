@extends('layouts.app')

@section('title', 'Search — Knowledge Base — AbiraSign')
@section('meta_description', 'Search AbiraSign help articles.')

@push('styles')
<style>
.kb-wrap { min-height: calc(100vh - 60px); background: var(--bg-alt); padding: 48px 20px; }
.kb-inner { max-width: 860px; margin: 0 auto; }
.kb-search-form { position: relative; display: flex; gap: 8px; margin-bottom: 32px; }
.kb-search-input { flex: 1; padding: 13px 16px 13px 44px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 15px; color: var(--text-primary); background: #fff; outline: none; transition: border-color .15s; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
.kb-search-input:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(14,116,144,.08); }
.kb-search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none; }
.kb-search-btn { padding: 0 20px; background: var(--teal); color: #fff; border: none; border-radius: var(--radius-md); font-size: 14px; font-weight: 600; cursor: pointer; }
.kb-article-list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 32px; }
.kb-article-item { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 18px 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; text-decoration: none; transition: box-shadow .15s, border-color .15s; }
.kb-article-item:hover { box-shadow: 0 2px 10px rgba(0,0,0,.07); border-color: var(--teal); }
.kb-article-title { font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 4px; }
.kb-article-excerpt { font-size: 13px; color: var(--text-secondary); line-height: 1.5; }
.kb-article-meta { font-size: 11px; color: var(--text-muted); margin-top: 4px; }
.kb-article-arrow { color: var(--text-muted); flex-shrink: 0; }
.kb-sidebar { display: grid; grid-template-columns: 1fr 240px; gap: 32px; align-items: start; }
.kb-cats-side { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; }
.kb-cats-side-title { font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--text-muted); padding: 14px 16px; border-bottom: 1px solid var(--border); }
.kb-cats-side a { display: flex; align-items: center; justify-content: space-between; padding: 10px 16px; font-size: 13px; color: var(--text-primary); text-decoration: none; border-bottom: 1px solid var(--border); transition: background .12s; }
.kb-cats-side a:last-child { border-bottom: none; }
.kb-cats-side a:hover { background: var(--teal-light); color: var(--teal); }
@media (max-width: 640px) { .kb-sidebar { grid-template-columns: 1fr; } }
</style>
@endpush

@section('content')
<div class="kb-wrap">
  <div class="kb-inner">

    <a href="{{ route('support.kb') }}" style="font-size:14px;color:var(--teal);display:inline-flex;align-items:center;gap:6px;margin-bottom:20px;">
      ← Knowledge Base
    </a>

    <form action="{{ route('support.kb.search') }}" method="GET" class="kb-search-form">
      <svg class="kb-search-icon" width="18" height="18" viewBox="0 0 18 18" fill="none">
        <circle cx="7.5" cy="7.5" r="5" stroke="#9CA3AF" stroke-width="1.5"/>
        <path d="M12 12l3.5 3.5" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round"/>
      </svg>
      <input type="text" name="q" value="{{ e($q) }}" class="kb-search-input" placeholder="Search articles…" autocomplete="off">
      <button type="submit" class="kb-search-btn">Search</button>
    </form>

    <div class="kb-sidebar">
      <div>
        @if(strlen($q) < 2)
          <p style="font-size:14px;color:var(--text-secondary);">Enter at least 2 characters to search.</p>
        @elseif($results->isEmpty())
          <p style="font-size:15px;font-weight:600;color:var(--text-primary);margin-bottom:8px;">No results for "{{ e($q) }}"</p>
          <p style="font-size:14px;color:var(--text-secondary);margin-bottom:24px;">Try different keywords or browse a category.</p>
          <a href="{{ route('support.request') }}" class="btn btn-primary">Submit a support request</a>
        @else
          <p style="font-size:14px;color:var(--text-secondary);margin-bottom:16px;">
            {{ $results->count() }} result{{ $results->count() === 1 ? '' : 's' }} for "<strong>{{ e($q) }}</strong>"
          </p>
          <div class="kb-article-list">
            @foreach($results as $a)
              <a href="{{ route('support.kb.article', $a->slug) }}" class="kb-article-item">
                <div>
                  <div class="kb-article-title">{{ $a->title }}</div>
                  @if($a->excerpt)
                    <div class="kb-article-excerpt">{{ Str::limit(strip_tags($a->excerpt), 120) }}</div>
                  @endif
                  <div class="kb-article-meta">{{ $a->category_name }}</div>
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
        <div class="kb-cats-side-title">Browse categories</div>
        @foreach($categories as $cat)
          <a href="{{ route('support.kb.category', $cat->slug) }}">
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
