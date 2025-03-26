<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FundHorizon.com Login</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #333;
            background-image: url('https://images.pexels.com/photos/6770521/pexels-photo-6770521.jpeg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            position: relative;
        }
        .gradient-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.6));
            z-index: -1;
        }
        .form-container {
            background-color: rgba(255, 255, 255, 0.85);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            text-align: center;
            backdrop-filter: blur(8px);
        }
        .form-container input {
            width: 100%;
            padding: 14px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
        }
        .submit-btn, .red-btn {
            width: 100%;
            padding: 14px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s ease;
        }
        .submit-btn { background-color: rgb(13, 10, 177); color: white; }
        .submit-btn:hover { background-color: rgb(10, 7, 141); }
        .red-btn { background-color: rgb(207, 30, 30); color: white; }
        .red-btn:hover { background-color: rgb(167, 24, 24); }
        .forgot-link {
            display: block;
            margin: 10px 0;
            font-size: 14px;
        }
        .error {
    color: red;
    font-size: 14px;
    margin-bottom: 10px;
    text-align: center;
}

    </style>
</head>

<body>
    <div class="gradient-overlay"></div>
    <div class="form-container" id="loginContainer">
        <h1>Login with FundHorizon.com</h1>
        <p>Already registered? Log in to access your account.</p>
        @if(session('error'))
            <p class="error">{{ session('error') }}</p>
        @endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="password" name="password" placeholder="Password" required>
            <a href="#" class="forgot-link" onclick="forgotPasswordForm()">Forgot Password?</a>
            <button type="submit" class="submit-btn">Login</button>
            <p>Don't have an account? <a href="{{ route('signup') }}">Sign up</a></p>
        </form>
    </div>
    <script>
        function forgotPasswordForm() {
            document.getElementById('loginContainer').innerHTML = `
                <h2>Forgot Password</h2>
                <p>Enter your email address to receive a password reset link.</p>
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <input type="email" name="email" placeholder="Email Address" required>
                    <button type="submit" class="submit-btn">Submit</button>
                    <button type="button" class="red-btn" onclick="location.reload()">Back to Login</button>
                </form>
            `;
        }
    </script>
</body>
</html>
