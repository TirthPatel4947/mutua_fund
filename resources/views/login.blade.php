    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>FundHorizon.com Login</title>
        <style>
            /* Global Styles */
            body {
                font-family: 'Arial', sans-serif;
                margin: 0;
                padding: 0;
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                color: #333;
                overflow: hidden;
                position: relative;
                background-image: url('https://images.pexels.com/photos/6770521/pexels-photo-6770521.jpeg');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
            }

            /* Gradient Overlay */
            .gradient-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: linear-gradient(135deg, rgba(0, 0, 0, 0.3), rgba(0, 0, 0, 0.6));
                z-index: -1;
            }

            /* Form Container */
            .form-container {
                background-color: rgba(255, 255, 255, 0.85);
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
                width: 100%;
                max-width: 450px;
                position: relative;
                z-index: 1;
                backdrop-filter: blur(8px);
                transition: all 0.3s ease;
            }

            h1 {
                font-size: 28px;
                margin-bottom: 20px;
                text-align: center;
                color: #2a2a2a;
            }

            p {
                font-size: 16px;
                color: #777;
                text-align: center;
                margin-bottom: 25px;
            }

            .form-container input,
            .form-container select {
                width: 100%;
                padding: 14px;
                margin-bottom: 20px;
                border: 1px solid #ddd;
                border-radius: 8px;
                font-size: 16px;
                transition: all 0.3s ease;
            }

            .form-container input:focus,
            .form-container select:focus {
                border-color: #0057d9;
                box-shadow: 0 0 8px rgba(0, 87, 217, 0.5);
            }

            .form-container .submit-btn {
                width: 100%;
                padding: 16px;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-size: 18px;
                transition: all 0.3s ease;
                background-color: rgb(13, 10, 177);
                color: white;
            }

            .form-container .submit-btn:hover {
                background-color: rgb(10, 7, 141);
            }

            /* Red Button Styling */
            .red-btn {
                background-color: rgb(207, 30, 30);
                color: white;
                width: 100%;
                padding: 16px;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-size: 18px;
                transition: all 0.3s ease;
            }

            .red-btn:hover {
                background-color: rgb(167, 24, 24);
            }

            /* Error Message Styling */
            .error {
                display: none;
                color: red;
                font-size: 14px;
                margin-top: 10px;
                text-align: center;
            }

            /* Mobile Responsiveness */
            @media (max-width: 480px) {
                .form-container {
                    padding: 20px;
                }

                h1 {
                    font-size: 24px;
                }

                .form-container input,
                .form-container select {
                    font-size: 14px;
                    padding: 12px;
                }

                .form-container .submit-btn {
                    font-size: 16px;
                    padding: 14px;
                }
            }
        </style>
    </head>

    <body>

        <!-- Gradient Overlay -->
        <div class="gradient-overlay"></div>

        <!-- Form Container -->
        <div class="form-container">
            <h1>Login with FundHorizon.com</h1>
            <p>Already registered? Log in to access your account.</p> 

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" id="loginForm">
                @csrf

                <input type="email" id="loginEmail" name="email" placeholder="Email Address" required value="{{ old('email') }}">
                @error('email')
                <p class="error">{{ $message }}</p>
                @enderror

                <input type="password" id="loginPassword" name="password" placeholder="Password" required>
                @error('password')
                <p class="error">{{ $message }}</p>
                @enderror

                <p><a href="#" onclick="forgotPasswordForm()">Forgot Password?</a></p>

                <button type="submit" class="submit-btn">Login</button>

                <p class="error" id="loginError" style="display: none;">Invalid email or password.</p>

                <p>Don't have an account? <a href="{{ route('signup') }}">Sign up</a></p>
            </form>
        </div>

        <script>
            // Show the forgot password form
            function forgotPasswordForm() {
                document.querySelector('.form-container').innerHTML = `
                    <h2>Forgot Password</h2>
                    <p>Enter your email address to receive a password reset link.</p>
                    <form method="POST" id="forgotPasswordForm">
                        @csrf
                        <input type="email" name="email" placeholder="Email Address" required>
                        <button type="submit" class="submit-btn">Submit</button>
                        <button type="button" class="red-btn" onclick="goBackToLogin()">Back to Login</button>
                    </form>
                    <p class="error" id="forgotPasswordError" style="display: none;">Please enter a valid email address.</p>
                `;
            }

            // Go back to login form from forgot password
            function goBackToLogin() {
                window.location.href = "{{ route('login') }}";
            }
        </script>

    </body>

    </html>
