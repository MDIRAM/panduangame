<footer class="site-footer">
    <div>
        <strong>Walkthrough Game Hub</strong>
        <span>Database-driven game walkthroughs for fast route planning.</span>
    </div>

    <nav aria-label="Footer navigation">
        <a href="{{ route('home') }}">Home</a>
        @auth
            <a href="{{ route('dashboard') }}">My Account</a>
        @else
            <a href="{{ route('login') }}">Login</a>
        @endauth
    </nav>
</footer>
