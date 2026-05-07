<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'AbiraSign — legally binding e-signatures and digital intake forms for any business. HIPAA-compliant add-on available for healthcare practices.')">
    @if(app()->environment('production'))
        <meta name="robots" content="index, follow">
    @else
        <meta name="robots" content="noindex, nofollow">
    @endif
    <title>@yield('title', 'AbiraSign — E-Signatures for Any Business')</title>
    <link rel="canonical" href="{{ url()->current() }}">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html { font-size: 16px; -webkit-font-smoothing: antialiased; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background: #ffffff; color: #111827; line-height: 1.6; }
        a { color: inherit; text-decoration: none; }
        img { max-width: 100%; display: block; }

        /* ── Tokens ── */
        :root {
            --teal: #0E7490;
            --teal-light: #E0F2FE;
            --teal-dark: #0C4A6E;
            --text-primary: #111827;
            --text-secondary: #6B7280;
            --text-muted: #9CA3AF;
            --border: #E5E7EB;
            --bg-page: #F8FAFC;
            --bg-surface: #F9FAFB;
            --bg-surface-2: #F3F4F6;
            --bg-alt: #F3F4F6;
            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-pill: 9999px;
        }

        /* ── Nav ── */
        .nav { background: #ffffff; border-bottom: 1px solid var(--border); padding: 0 32px; height: 60px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 100; }
        .nav-logo { font-size: 18px; font-weight: 600; color: var(--text-primary); }
        .nav-logo span { color: var(--teal); }
        .nav-links { display: flex; gap: 24px; align-items: center; }
        .nav-links a { font-size: 14px; color: var(--text-secondary); transition: color .15s; }
        .nav-links a:hover { color: var(--text-primary); }
        .nav-cta { background: var(--teal); color: #fff !important; padding: 8px 18px; border-radius: var(--radius-md); font-size: 14px; font-weight: 500; transition: opacity .15s; }
        .nav-cta:hover { opacity: .9; }
        .nav-login { background: transparent; color: var(--teal) !important; border: 1px solid var(--teal); padding: 7px 16px; border-radius: var(--radius-md); font-size: 14px; font-weight: 500; transition: background .15s; }
        .nav-login:hover { background: var(--teal-light); }
        .nav-mobile-btn { display: none; background: none; border: none; cursor: pointer; padding: 4px; color: var(--text-secondary); }

        /* ── Buttons ── */
        .btn { display: inline-block; padding: 11px 24px; border-radius: var(--radius-md); font-size: 15px; font-weight: 500; cursor: pointer; border: none; transition: opacity .15s, background .15s; }
        .btn-primary { background: var(--teal); color: #fff; }
        .btn-primary:hover { opacity: .9; }
        .btn-ghost { background: transparent; color: var(--text-secondary); border: 1px solid var(--border); }
        .btn-ghost:hover { border-color: #9CA3AF; color: var(--text-primary); }

        /* ── Footer ── */
        .footer { padding: 24px 32px; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px; background: #fff; }
        .footer-logo { font-size: 16px; font-weight: 600; color: var(--text-primary); }
        .footer-logo span { color: var(--teal); }
        .footer-links { display: flex; gap: 20px; flex-wrap: wrap; }
        .footer-links a { font-size: 13px; color: var(--text-secondary); }
        .footer-links a:hover { color: var(--text-primary); }
        .footer-copy { font-size: 13px; color: var(--text-muted); }

        /* ── Utilities ── */
        .section-label { font-size: 11px; font-weight: 600; color: var(--teal); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 10px; }
        .section-title { font-size: 28px; font-weight: 600; color: var(--text-primary); margin-bottom: 10px; line-height: 1.25; }
        .section-sub { font-size: 16px; color: var(--text-secondary); line-height: 1.65; margin-bottom: 36px; }
        .wrap { max-width: 900px; margin: 0 auto; padding: 0 32px; }
        .check { color: var(--teal); font-weight: 600; flex-shrink: 0; }

        /* ── Mobile ── */
        @media (max-width: 768px) {
            .nav { padding: 0 20px; }
            .nav-links { display: none; }
            .nav-links.open { display: flex; flex-direction: column; position: absolute; top: 60px; left: 0; right: 0; background: #fff; border-bottom: 1px solid var(--border); padding: 16px 20px; gap: 16px; z-index: 99; }
            .nav-mobile-btn { display: block; }
            .wrap { padding: 0 20px; }
            .footer { flex-direction: column; align-items: flex-start; padding: 24px 20px; }
        }
    </style>
   @stack('styles')
    <!-- Privacy-friendly analytics by Plausible -->
    <script async src="https://plausible.io/js/pa-flFpansLGlPd2zppBxaBg.js"></script>
    <script>
        window.plausible=window.plausible||function(){(plausible.q=plausible.q||[]).push(arguments)},plausible.init=plausible.init||function(i){plausible.o=i||{}};
        plausible.init()
    </script>
</head> 

<body>

<nav class="nav">
    <a href="{{ route('home') }}" class="nav-logo">Abira<span>Sign</span></a>
    <div class="nav-links" id="navLinks">
        <a href="{{ route('home') }}">Features</a>
        <a href="{{ route('pricing') }}">Pricing</a>
        <a href="{{ route('home') }}#hipaa">HIPAA</a>
        <a href="{{ route('contact') }}">Contact</a>
        <a href="{{ env('APP_LOGIN_URL', 'https://dev.abirasign.com/login') }}" class="nav-login">Log in</a>
        <a href="{{ route('pricing') }}" class="nav-cta">Get started</a>
    </div>
    <button class="nav-mobile-btn" onclick="document.getElementById('navLinks').classList.toggle('open')" aria-label="Menu">
        <svg width="22" height="22" viewBox="0 0 22 22" fill="none"><path d="M3 6h16M3 11h16M3 16h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
    </button>
</nav>

<main>
    @yield('content')
</main>

<footer class="footer">
    <div class="footer-logo">Abira<span>Sign</span></div>
    <div class="footer-links">
        <a href="{{ route('pricing') }}">Pricing</a>
        <a href="{{ route('contact') }}">Contact</a>
        <a href="{{ route('terms') }}">Terms of service</a>
        <a href="{{ route('privacy') }}">Privacy policy</a>
        <a href="{{ route('pricing') }}">Get started</a>
    </div>
    <div class="footer-copy">© {{ date('Y') }} BrightNet Technologies LLC</div>
</footer>
    @stack('scripts')
</body>
</html>
