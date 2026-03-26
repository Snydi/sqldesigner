<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin — SQL Designer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'JetBrains Mono', monospace;
            background: #fff;
            color: #2c3e50;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            text-transform: uppercase;
            -webkit-font-smoothing: antialiased;
        }
        header {
            background: #8f2f2f;
            color: #fff;
            padding: 1rem 1.5rem;
            display: flex;
            align-items: center;
        }
        header span {
            font-size: 14px;
            font-weight: 600;
            letter-spacing: .04em;
        }
        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .card {
            background: #fff;
            border-radius: 6px;
            padding: 2rem;
            width: 100%;
            max-width: 360px;
            box-shadow: 0 4px 12px rgba(0,0,0,.1);
        }
        h1 {
            font-size: 18px;
            font-weight: 600;
            color: #8f2f2f;
            margin-bottom: 1.75rem;
            letter-spacing: .06em;
        }
        label {
            display: block;
            font-size: 11px;
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 6px;
            letter-spacing: .08em;
        }
        input {
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 1px solid #ccc;
            padding: 8px 0;
            color: #2c3e50;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            margin-bottom: 1.25rem;
            outline: none;
            transition: border-color .2s;
            text-transform: none;
        }
        input:focus { border-bottom-color: #8f2f2f; }
        .error {
            background: #fdf0f0;
            border: 1px solid #e0b0b0;
            color: #8f2f2f;
            border-radius: 4px;
            padding: 10px 14px;
            font-size: 12px;
            margin-bottom: 1.25rem;
        }
        button[type="submit"] {
            background: #8f2f2f;
            color: #fff;
            border: none;
            border-radius: 4px;
            padding: 10px 24px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
            font-weight: 600;
            letter-spacing: .06em;
            cursor: pointer;
            transition: background .2s;
            text-transform: uppercase;
        }
        button[type="submit"]:hover { background: #7a2222; }
    </style>
</head>
<body>
    <header>
        <span>SQL Designer — Admin</span>
    </header>

    <main>
        <div class="card">
            <h1>Login</h1>

            @if ($errors->has('credentials'))
                <div class="error">{{ $errors->first('credentials') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.login.post') }}">
                @csrf
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" autocomplete="username" autofocus>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" autocomplete="current-password">

                <button type="submit">Sign In</button>
            </form>
        </div>
    </main>
</body>
</html>
