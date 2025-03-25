<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            width: 100%;
            padding: 10px;
            background: blue;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: darkblue;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        @if(session('status'))
            <p class="success">{{ session('status') }}</p>
        @endif

        @if($errors->any())
            <p class="error">{{ $errors->first() }}</p>
        @endif
        <form method="POST" action="{{ route('password.reupdate') }}">
    @csrf  <!-- CSRF protection -->
    @method('PUT')  <!-- Override POST to send PUT request -->

    <input type="hidden" name="token" value="{{ request()->route('token') }}"> <!-- Add Token Field -->
    
    <input type="email" name="email" placeholder="Enter your email" required>
    <input type="password" name="old_password" placeholder="Current Password" required>
    <input type="password" name="password" placeholder="New Password" required>
    <input type="password" name="password_confirmation" placeholder="Confirm Password" required>

    <button type="submit">Update Password</button>
</form>





    </div>
</body>
</html>
