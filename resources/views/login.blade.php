<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Pembagian Kelompok KKN Reguler</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e7e34 0%, #0f5f37 100%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        /* Background decoration */
        body::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: rgba(82, 214, 82, 0.1);
            border-radius: 50%;
            top: -100px;
            right: -100px;
        }

        body::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            background: rgba(82, 214, 82, 0.08);
            border-radius: 50%;
            bottom: -50px;
            left: -50px;
        }

        .login-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            padding: 50px 40px;
            border-radius: 15px;
            width: 100%;
            max-width: 420px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }

        .login-icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .login-container h2 {
            font-size: 28px;
            font-weight: 800;
            color: #1e7e34;
            margin-bottom: 8px;
            letter-spacing: 0.5px;
        }

        .login-container .subtitle {
            font-size: 14px;
            color: #666;
            font-weight: 500;
        }

        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .input-group label {
            font-size: 13px;
            font-weight: 600;
            color: #333;
            display: block;
            margin-bottom: 8px;
            letter-spacing: 0.3px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .input-group input:focus {
            outline: none;
            border-color: #52d652;
            background: white;
            box-shadow: 0 0 0 4px rgba(82, 214, 82, 0.1);
        }

        .input-group input::placeholder {
            color: #999;
        }

        .btn-login {
            width: 100%;
            padding: 13px 20px;
            background: linear-gradient(135deg, #1e7e34 0%, #0f5f37 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 700;
            font-size: 15px;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
            margin-top: 10px;
            box-shadow: 0 4px 15px rgba(30, 126, 52, 0.3);
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(30, 126, 52, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .error {
            background: #fee;
            color: #c33;
            padding: 12px 14px;
            margin-bottom: 20px;
            border-radius: 8px;
            border-left: 4px solid #c33;
            font-size: 14px;
            animation: shake 0.5s ease-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        .error::before {
            content: '⚠️ ';
            margin-right: 8px;
        }

        .footer-text {
            text-align: center;
            font-size: 12px;
            color: #999;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-container {
                padding: 35px 25px;
                border-radius: 12px;
            }

            .login-container h2 {
                font-size: 24px;
            }

            .input-group label {
                font-size: 12px;
            }

            .input-group input {
                padding: 10px 12px;
                font-size: 13px;
            }

            .btn-login {
                padding: 11px 16px;
                font-size: 14px;
            }
        }
    </style>
</head>

<body>

<div class="login-wrapper">
    <div class="login-container">
        <div class="login-header">
            <div class="login-icon">🔐</div>
            <h2>LOGIN</h2>
            <p class="subtitle">Pembagian Kelompok KKN Reguler</p>
        </div>

        @if(session('error'))
            <div class="error">{{ session('error') }}</div>
        @endif

        <form action="/login" method="POST">
            @csrf

            <div class="input-group">
                <label for="username">Username</label>
                <input 
                    type="text" 
                    id="username"
                    name="username" 
                    placeholder="Masukkan username Anda"
                    required
                    autofocus
                >
            </div>

            <div class="input-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password"
                    name="password" 
                    placeholder="Masukkan password Anda"
                    required
                >
            </div>

            <button class="btn-login" type="submit">Login</button>
        </form>

        <div class="footer-text">
            Portal Pembagian Kelompok KKN Reguler © 2026
        </div>
    </div>
</div>

</body>
</html>