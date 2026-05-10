@extends('layouts.app')

@section('title', 'Knowledge Base — AbiraSign')
@section('meta_description', 'Search the AbiraSign knowledge base for help with setup, billing, HIPAA compliance, sending documents, and more.')

@push('styles')
<style>
.kb-wrap { min-height: calc(100vh - 60px); background: var(--bg-alt); padding: 64px 20px; }
.kb-inner { max-width: 860px; margin: 0 auto; }
.kb-hero { text-align: center; margin-bottom: 48px; }
.kb-hero h1 { font-size: 34px; font-weight: 700; color: var(--text-primary); margin-bottom: 12px; }
.kb-hero p { font-size: 16px; color: var(--text-secondary); max-width: 460px; margin: 0 auto 28px; line-height: 1.65; }
.kb-search-form { position: relative; max-width: 540px; margin: 0 auto; display: flex; gap: 8px; }
.kb-search-input { flex: 1; padding: 13px 16px 13px 44px; border: 1px solid var(--border); border-radius: var(--radius-md); font-size: 15px; color: var(--text-primary); background: #fff; outline: none; transition: border-color .15s; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
.kb-search-input:focus { border-color: var(--teal); box-shadow: 0 0 0 3px rgba(14,116,144,.08); }
.kb-search-icon { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--text-muted); pointer-events: none; }
.kb-search-btn { padding: 0 20px; background: var(--teal); color: #fff; border: none; border-radius: var(--radius-md); font-size: 14px; font-weight: 600; cursor: pointer; white-space: nowrap; }
.kb-section-title { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: var(--text-muted); margin-bottom: 14px; }
.kb-categories { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; margin-bottom: 48px; }
.kb-cat { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 20px; display: flex; gap: 14px; align-items: flex-start; text-decoration: none; transition: box-shadow .15s, border-color .15s; }
.kb-cat:hover { box-shadow: 0 4px 16px rgba(0,0,0,.08); border-color: var(--teal); }
.kb-cat-icon { width: 36px; height: 36px; border-radius: var(--radius-md); background: var(--teal-light); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.kb-cat h3 { font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 3px; }
.kb-cat p { font-size: 12px; color: var(--text-secondary); line-height: 1.5; }
.kb-cat-count { font-size: 11px; color: var(--teal); font-weight: 600; margin-top: 6px; }
.kb-popular { margin-bottom: 48px; }
.kb-article-list { display: flex; flex-direction: column; gap: 10px; }
.kb-article-item { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 16px 20px; display: flex; align-items: center; justify-content: space-between; gap: 16px; text-decoration: none; transition: box-shadow .15s, border-color .15s; }
.kb-article-item:hover { box-shadow: 0 2px 10px rgba(0,0,0,.07); border-color: var(--teal); }
.kb-article-title { font-size: 14px; font-weight: 600; color: var(--text-primary); margin-bottom: 3px; }
.kb-article-meta { font-size: 12px; color: var(--text-muted); }
.kb-article-arrow { color: var(--text-muted); flex-shrink: 0; }
.kb-support-cta { background: #fff; border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 32px; text-align: center; }
.kb-support-cta h3 { font-size: 18px; font-weight: 700; color: var(--text-primary); margin-bottom: 8px; }
.kb-support-cta p { font-size: 14px; color: var(--text-secondary); margin-bottom: 20px; }
@media (max-width: 700px) {
    .kb-categories { grid-template-columns: 1fr 1fr; }
    .kb-hero h1 { font-size: 26px; }
}
@media (max-width: 480px) {
    .kb-categories { grid-template-columns: 1fr; }
}
</style>
@endpush

@section('content')
<div class="kb-wrap">
  <div class="kb-inner">

    <div class="kb-hero">
      <div class="section-label">Knowledge Base</div>
      <h1>How can we help?</h1>
      <p>Search articles on setup, billing, HIPAA, sending documents, and more.</p>
      <form action="{{ route('support.kb.search') }}" method="GET" class="kb-search-form">
        <svg class="kb-search-icon" width="18" height="18" viewBox="0 0 18 18" fill="none">
          <circle cx="7.5" cy="7.5" r="5" stroke="#9CA3AF" stroke-width="1.5"/>
          <path d="M12 12l3.5 3.5" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <input type="text" name="q" class="kb-search-input" placeholder="Search articles…" autocomplete="off">
        <button type="submit" class="kb-search-btn">Search</button>
      </form>
    </div>

    {{-- Categories --}}
    <p class="kb-section-title">Browse by category</p>
    <div class="kb-categories">
      @foreach($categories as $cat)
        @php $count = $counts[$cat->id]->total ?? 0; @endphp
        <a href="{{ route('support.kb.category', $cat->slug) }}" class="kb-cat">
          <div class="kb-cat-icon">
            <svg width="16" height="16" fill="none" stroke="#0E7490" stroke-width="1.4" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332-.477-4.5-1.253"/>
            </svg>
          </div>
          <div>
            <h3>{{ $cat->name }}</h3>
            <p>{{ $cat->description }}</p>
            <div class="kb-cat-count">{{ $count }} article{{ $count === 1 ? '' : 's' }}</div>
          </div>
        </a>
      @endforeach
    </div>

    {{-- Popular articles --}}
    @if($popular->isNotEmpty())
    <div class="kb-popular">
      <p class="kb-section-title">Popular articles</p>
      <div class="kb-article-list">
        @foreach($popular as $a)
          <a href="{{ route('support.kb.article', $a->slug) }}" class="kb-article-item">
            <div>
              <div class="kb-article-title">{{ $a->title }}</div>
              <div class="kb-article-meta">{{ $a->category_name }}</div>
            </div>
            <svg class="kb-article-arrow" width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </a>
        @endforeach
      </div>
    </div>
    @endif

    {{-- Support CTA --}}
    <div class="kb-support-cta">
      <h3>Can't find what you're looking for?</h3>
      <p>Our team typically responds within one business day.</p>
      <a href="{{ route('support.request') }}" class="btn btn-primary">Submit a support request</a>
    </div>

  </div>
</div>
@endsection
