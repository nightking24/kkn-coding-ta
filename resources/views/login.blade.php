<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #d9c3a3, #bfa88a);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            background: #f3e3d3;
            padding: 40px;
            border-radius: 10px;
            width: 300px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            text-align: center;
        }

        .login-container h2 {
            margin-bottom: 20px;
        }

        .input-group {
            margin-bottom: 15px;
            text-align: left;
        }

        .input-group label {
            font-size: 14px;
        }

        .input-group input {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn-login {
            width: 100%;
            padding: 10px;
            background-color: #6b4f3b;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        .btn-login:hover {
            background-color: #5a3f2e;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }
    </style>
</head>

<body>

<div class="login-container">
    <h2>LOGIN</h2>

    @if(session('error'))
        <div class="error">{{ session('error') }}</div>
    @endif

    <form action="/login" method="POST">
        @csrf

        <div class="input-group">
            <label>Username</label>
            <input type="text" name="username" required>
        </div>

        <div class="input-group">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <button class="btn-login" type="submit">Login</button>
    </form>
</div>

</body>
</html>