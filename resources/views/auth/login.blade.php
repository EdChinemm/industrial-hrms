<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | Industrial HRMS</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f1f5f9;
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            margin: 100px auto;
            background: #ffffff;
            padding: 32px;
            border-radius: 10px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }

        h1 {
            margin-bottom: 8px;
        }

        .subtitle {
            color: #64748b;
            margin-bottom: 24px;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 11px;
            margin-bottom: 18px;
            box-sizing: border-box;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #1e293b;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
            padding: 10px;
            margin-bottom: 16px;
        }

        .remember {
            margin-bottom: 18px;
        }
    </style>
</head>

<body>

<div class="login-container">

    <h1>Industrial HRMS</h1>

    <p class="subtitle">
        Sign in to access the Human Resource Management System.
    </p>

    @if ($errors->any())
        <div class="error">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.store') }}">

        @csrf

        <label for="email">Email Address</label>

        <input
            type="email"
            id="email"
            name="email"
            value="{{ old('email') }}"
            required
            autofocus
        >

        <label for="password">Password</label>

        <input
            type="password"
            id="password"
            name="password"
            required
        >

        <div class="remember">
            <label>
                <input
                    type="checkbox"
                    name="remember"
                    value="1"
                >
                Remember me
            </label>
        </div>

        <button type="submit">
            Sign In
        </button>

    </form>

</div>

</body>
</html>
