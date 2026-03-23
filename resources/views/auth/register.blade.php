<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>新規登録 - Store Dashboard</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Noto Sans JP', sans-serif;
            background: #0f172a;
            color: #e2e8f0;
            min-height: 100vh;
            display: flex; flex-direction: column;
            align-items: center; justify-content: center;
            padding: 40px 0;
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
            position: fixed; top: -200px; left: 50%; transform: translateX(-50%);
            width: 600px; height: 600px; border-radius: 50%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.08), transparent 70%);
            pointer-events: none;
        }

        .register-container {
            position: relative; z-index: 10;
            width: 100%; max-width: 420px; padding: 0 20px;
        }

        .logo {
            text-align: center; margin-bottom: 40px;
        }
        .logo a {
            font-size: 24px; font-weight: 700; color: #3b82f6;
            text-decoration: none; letter-spacing: 1px;
        }
        .logo p {
            font-size: 13px; color: #64748b; margin-top: 8px;
        }

        .card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 16px;
            padding: 40px 32px;
            backdrop-filter: blur(10px);
        }

        .form-group { margin-bottom: 24px; }
        .form-group label {
            display: block; font-size: 13px; font-weight: 500;
            color: #94a3b8; margin-bottom: 8px;
        }
        .form-group input {
            width: 100%; padding: 12px 16px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px; color: #e2e8f0;
            font-size: 15px; font-family: 'Noto Sans JP', sans-serif;
            transition: all 0.3s; outline: none;
        }
        .form-group input:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }
        .form-group input::placeholder { color: #475569; }

        .error-text {
            font-size: 12px; color: #f87171; margin-top: 6px;
        }

        .btn-submit {
            width: 100%; padding: 14px;
            background: #3b82f6; color: white; border: none;
            border-radius: 8px; font-size: 16px; font-weight: 500;
            font-family: 'Noto Sans JP', sans-serif;
            cursor: pointer; transition: all 0.3s;
            margin-top: 8px;
        }
        .btn-submit:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 8px 30px rgba(59, 130, 246, 0.3);
        }

        .login-link {
            text-align: center; margin-top: 24px;
            font-size: 14px; color: #64748b;
        }
        .login-link a {
            color: #3b82f6; text-decoration: none; font-weight: 500;
        }
        .login-link a:hover { text-decoration: underline; }

        @media (max-width: 480px) {
            .card { padding: 32px 24px; }
        }
    </style>
</head>
<body>
    <div class="bg-grid"></div>
    <div class="glow"></div>

    <div class="register-container">
        <div class="logo">
            <a href="{{ url('/') }}">📊 Store Dashboard</a>
            <p>新規アカウント作成</p>
        </div>

        <div class="card">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label for="name">氏名</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="山田 太郎">
                    @error('name')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">メールアドレス</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="your@email.com">
                    @error('email')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">パスワード</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="8文字以上">
                    @error('password')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">パスワード（確認）</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="もう一度入力">
                    @error('password_confirmation')
                        <p class="error-text">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">アカウントを作成</button>
            </form>
        </div>

        <div class="login-link">
            すでにアカウントをお持ちの方は <a href="{{ route('login') }}">ログイン</a>
        </div>
    </div>
</body>
</html>