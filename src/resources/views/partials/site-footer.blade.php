<footer class="site-footer">
    <div>
        <strong>Walkthrough Game Hub</strong>
        <span>Database-driven game guides for fast route planning.</span>
    </div>

    <nav aria-label="Footer navigation">
        <a href="{{ route('home') }}">Home</a>
        <a href="{{ route('videos.index') }}">Videos</a>
        @auth
            <a href="{{ route('dashboard') }}">My Account</a>
        @else
            <a href="{{ route('login') }}">Login</a>
        @endauth
    </nav>
</footer>
