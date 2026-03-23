<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Store Dashboard - 店舗売上管理ダッシュボード</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .bg-grid {
            position: fixed; top: 0; left: 0; right: 0; bottom: 0;
            background-image:
                linear-gradient(rgba(59, 130, 246, 0.03) 1px, transparent 1px),
                linear-gradient(90deg, rgba(59, 130, 246, 0.03) 1px, transparent 1px);
            background-size: 60px 60px;
            pointer-events: none;
        }

        .glow {
            position: fixed; top: -200px; right: -200px;
            width: 600px; height: 600px; border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.08), transparent 70%);
            pointer-events: none;
        }

        nav {
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px 40px; position: relative; z-index: 10;
        }
        .logo {
            font-size: 20px; font-weight: 700; letter-spacing: 1px;
            color: #3b82f6;
        }
        .nav-links { display: flex; gap: 16px; }
        .nav-links a {
            padding: 8px 24px; border-radius: 6px; text-decoration: none;
            font-size: 14px; font-weight: 500; transition: all 0.3s;
        }
        .btn-login {
            color: #94a3b8; border: 1px solid #334155;
        }
        .btn-login:hover { color: #e2e8f0; border-color: #3b82f6; }
        .btn-register {
            background: #3b82f6; color: white;
        }
        .btn-register:hover { background: #2563eb; }

        .hero {
            display: flex; flex-direction: column; align-items: center;
            justify-content: center; text-align: center;
            min-height: calc(100vh - 80px); padding: 0 40px;
            position: relative; z-index: 10;
        }
        .badge {
            display: inline-block; padding: 6px 16px; border-radius: 20px;
            font-family: 'JetBrains Mono', monospace; font-size: 12px;
            letter-spacing: 1px; color: #3b82f6;
            background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2);
            margin-bottom: 32px;
        }
        h1 {
            font-size: 52px; font-weight: 700; line-height: 1.2;
            margin-bottom: 24px; letter-spacing: -1px;
        }
        h1 span { color: #3b82f6; }
        .subtitle {
            font-size: 18px; color: #94a3b8; line-height: 1.8;
            max-width: 600px; margin-bottom: 48px;
        }
        .cta-group { display: flex; gap: 16px; margin-bottom: 80px; }
        .cta-primary {
            padding: 14px 40px; background: #3b82f6; color: white;
            border: none; border-radius: 8px; font-size: 16px; font-weight: 500;
            text-decoration: none; transition: all 0.3s;
        }
        .cta-primary:hover { background: #2563eb; transform: translateY(-2px); box-shadow: 0 8px 30px rgba(59,130,246,0.3); }
        .cta-secondary {
            padding: 14px 40px; background: transparent; color: #94a3b8;
            border: 1px solid #334155; border-radius: 8px; font-size: 16px;
            text-decoration: none; transition: all 0.3s;
        }
        .cta-secondary:hover { color: #e2e8f0; border-color: #3b82f6; }

        .features {
            display: grid; grid-template-columns: repeat(3, 1fr); gap: 24px;
            max-width: 900px; width: 100%;
        }
        .feature-card {
            padding: 32px 24px; border-radius: 12px; text-align: left;
            background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06);
            transition: all 0.3s;
        }
        .feature-card:hover {
            border-color: rgba(59, 130, 246, 0.3);
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.2);
        }
        .feature-icon { font-size: 28px; margin-bottom: 16px; }
        .feature-title { font-size: 16px; font-weight: 700; margin-bottom: 8px; }
        .feature-desc { font-size: 13px; color: #64748b; line-height: 1.7; }

        .stats {
            display: flex; gap: 48px; justify-content: center;
            padding: 60px 40px; position: relative; z-index: 10;
        }
        .stat { text-align: center; }
        .stat-number {
            font-family: 'JetBrains Mono', monospace;
            font-size: 32px; font-weight: 700; color: #3b82f6;
        }
        .stat-label { font-size: 13px; color: #64748b; margin-top: 4px; }

        footer {
            text-align: center; padding: 40px;
            border-top: 1px solid rgba(255,255,255,0.06);
            position: relative; z-index: 10;
        }
        footer p { font-size: 12px; color: #475569; }

        @media (max-width: 768px) {
            h1 { font-size: 32px; }
            .features { grid-template-columns: 1fr; }
            .stats { flex-direction: column; gap: 24px; }
            nav { padding: 16px 20px; }
            .hero { padding: 0 20px; }
        }
    </style>
</head>
<body>
    <div class="bg-grid"></div>
    <div class="glow"></div>

    <nav>
        <div class="logo">📊 Store Dashboard</div>
        <div class="nav-links">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-register">ダッシュボード</a>
                @else
                    <a href="{{ route('login') }}" class="btn-login">ログイン</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-register">新規登録</a>
                    @endif
                @endauth
            @endif
        </div>
    </nav>

    <section class="hero">
        <div class="badge">PHP 8.3 / Laravel 11 / PostgreSQL 16</div>
        <h1>店舗の売上を<br><span>一目で把握する</span></h1>
        <p class="subtitle">
            複数店舗の売上データを一元管理し、リアルタイムに可視化。<br>
            KPIダッシュボード、売上分析、Excel出力まで、これひとつで。
        </p>
        <div class="cta-group">
            @auth
                <a href="{{ url('/dashboard') }}" class="cta-primary">ダッシュボードを開く</a>
            @else
                <a href="{{ route('login') }}" class="cta-primary">デモを見る</a>
                <a href="{{ route('register') }}" class="cta-secondary">アカウント作成</a>
            @endauth
        </div>

        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">📈</div>
                <div class="feature-title">リアルタイムダッシュボード</div>
                <div class="feature-desc">KPIカード、月別売上推移、店舗別比較、カテゴリ構成比を一画面で把握</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🔍</div>
                <div class="feature-title">高速な検索・集計</div>
                <div class="feature-desc">最適化されたSQLクエリで14,000件以上のデータを瞬時に検索・ソート・集計</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon">📥</div>
                <div class="feature-title">Excel / CSV出力</div>
                <div class="feature-desc">検索条件を維持したまま、フォーマット済みのExcelファイルをワンクリックで出力</div>
            </div>
        </div>
    </section>

    <div class="stats">
        <div class="stat">
            <div class="stat-number">14,000+</div>
            <div class="stat-label">売上データ件数</div>
        </div>
        <div class="stat">
            <div class="stat-number">7</div>
            <div class="stat-label">管理店舗数</div>
        </div>
        <div class="stat">
            <div class="stat-number">43</div>
            <div class="stat-label">テストケース（全PASS）</div>
        </div>
        <div class="stat">
            <div class="stat-number">0.1s</div>
            <div class="stat-label">クエリ応答速度</div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Store Dashboard. Built with Laravel, Chart.js & PostgreSQL.</p>
    </footer>
</body>
</html>