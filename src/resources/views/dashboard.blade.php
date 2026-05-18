<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | Walkthrough Game Hub</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>
<body class="auth-page">
    <div class="dashboard-shell auth-shell">
        <section class="dashboard-panel">
            <span class="hero-chip">Dashboard</span>
            <h1>Welcome back, {{ auth()->user()->name }}. Your next walkthrough starts here.</h1>
            <p>Halaman dashboard ini sudah terhubung ke akun login kamu. Dari sini kamu bisa lanjut browsing panduan dan nanti menyimpan progres walkthrough.</p>
            <div class="dashboard-stats">
                <div class="dashboard-stat">
                    <strong>62</strong>
                    <h3>Active guides</h3>
                    <p>Daftar walkthrough yang sedang trending untuk pemain seperti kamu.</p>
                </div>
                <div class="dashboard-stat">
                    <strong>18</strong>
                    <h3>Saved routes</h3>
                    <p>Guide yang sudah kamu tandai agar bisa dibuka cepat nanti.</p>
                </div>
                <div class="dashboard-stat">
                    <strong>98%</strong>
                    <h3>Completion rate</h3>
                    <p>Perkiraan kemajuan walkthrough berdasarkan rekomendasi yang kamu pilih.</p>
                </div>
            </div>
        </section>

        <aside class="dashboard-card">
            <div class="dashboard-summary">
                <h2>Latest walkthrough highlights</h2>
                <p>Nikmati update konten dan ringkasan panduan paling populer hari ini. Kamu bisa kembangkan halaman ini nanti dengan data nyata.</p>
            </div>
            <div class="dashboard-list">
                <article class="dashboard-item">
                    <h4>Horizon Forbidden West — Main Story</h4>
                    <p>Rute cepat untuk menyelesaikan plot utama tanpa melewatkan misi penting.</p>
                </article>
                <article class="dashboard-item">
                    <h4>The Last of Us Part II — Ending Guide</h4>
                    <p>Strategi untuk memaksimalkan pilihan cerita dan mencapai akhir terbaik.</p>
                </article>
                <article class="dashboard-item">
                    <h4>Elden Ring — Boss Priority</h4>
                    <p>Prioritas boss dan item penting agar kamu tidak terjebak di run pertama.</p>
                </article>
            </div>
            <a href="/" class="dashboard-link">
                Continue browsing guides
            </a>
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit">Log out</button>
            </form>
        </aside>
    </div>
</body>
</html>
