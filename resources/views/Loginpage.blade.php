<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FundHorizon.com Login and Signup</title>
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

        h2 {
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

        .form-container .next-btn,
        .form-container .back-btn,
        .form-container .submit-btn {
            width: 100%;
            padding: 16px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            transition: all 0.3s ease;
        }

        .next-btn {
            background-color: rgb(6, 37, 149);
            color: white;
        }

        .next-btn:hover {
            background-color: rgb(0, 27, 107);
        }

        .back-btn {
            background-color: rgb(207, 30, 30);
            color: white;
            margin-bottom: 10px;
        }

        .back-btn:hover {
            background-color: rgb(167, 24, 24);
        }

        .submit-btn {
            background-color: rgb(13, 10, 177);
            color: white;
        }

        .submit-btn:hover {
            background-color: rgb(10, 7, 141);
        }

        /* Error Message Styling */
        .error {
            display: none;
            color: red;
            font-size: 14px;
            margin-top: 10px;
            text-align: center;
        }

        /* Signup Form */
        .signup-form {
            display: block;
        }

        .login-form {
            display: none;
        }

        .dob-picker {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .dob-picker select {
            width: 30%;
            padding: 10px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .dob-picker select:focus {
            border-color: #0057d9;
            box-shadow: 0 0 8px rgba(0, 87, 217, 0.5);
        }

        /* Mobile Responsiveness */
        @media (max-width: 480px) {
            .form-container {
                padding: 20px;
            }

            h2 {
                font-size: 24px;
            }

            .form-container input,
            .form-container select {
                font-size: 14px;
                padding: 12px;
            }

            .form-container .next-btn,
            .form-container .back-btn,
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

        <!-- Sign-Up Form -->
        <div class="signup-form">
            <h1>Sign Up with FundHorizon.com</h1>
            <p>Join our mutual fund investment program and take control of your financial future.</p>
            <form id="signupForm">
                <input type="text" id="signupFirstName" placeholder="First Name" required>
                <input type="text" id="signupLastName" placeholder="Last Name" required>
                <input type="email" id="signupEmail" placeholder="Email Address" required>
                <input type="tel" id="signupPhone" placeholder="Phone Number" required>

                <!-- Date of Birth Picker -->
                <div class="dob-picker">
                    <select id="dobDay" required>
                        <option value="">Day</option>
                    </select>
                    <select id="dobMonth" required>
                        <option value="">Month</option>
                    </select>
                    <select id="dobYear" required>
                        <option value="">Year</option>
                    </select>
                </div>

                <button type="button" class="next-btn" onclick="showPasswordForm()">Next</button>
                <p class="error" id="signupError">Please fill in all fields correctly and make sure you're at least 18 years old.</p>
                <p>Already have an account? <a href="#" onclick="showLoginForm()">Login</a></p>
            </form>
        </div>

        <!-- Password Form (Second Step of Sign-Up) -->
        <div class="password-form" style="display: none;">
            <h2>Create Your Password</h2>
            <p>Secure your account with a strong password.</p>
            <form id="passwordForm">
                <input type="password" id="signupPassword" placeholder="Password" required>
                <input type="password" id="signupConfirmPassword" placeholder="Confirm Password" required>
                <button type="button" class="back-btn" onclick="goBackToSignup()">Back</button>
                <button type="submit" class="submit-btn">Submit</button>
            </form>
            <p class="error" id="passwordError">Passwords do not match or are too short (min 8 characters).</p>
        </div>

        <!-- Login Form -->
        <div class="login-form">
            <h1>Login with FundHorizon.com</h1>
            <p>Already registered? Log in to access your account.</p>
            <form id="loginForm">
                <input type="email" id="loginEmail" placeholder="Email Address" required>
                <input type="password" id="loginPassword" placeholder="Password" required>
                <p><a href="#" onclick="forgotPasswordForm()">Forgot Password?</a></p>
                <button type="button" class="submit-btn" onclick="login()">Login</button>
                <p class="error" id="loginError">Invalid email or password.</p>
                <p>Don't have an account? <a href="#" onclick="showSignupForm()">Sign up</a></p>
            </form>
        </div>

        <!-- Forgot Password Form -->
        <div class="forgot-password-form" style="display: none;">
            <h2>Forgot Password</h2>
            <p>Enter your email address to receive a password reset link.</p>
            <form id="forgotPasswordForm">
                <input type="email" id="forgotPasswordEmail" placeholder="Email Address" required>
                <button type="submit" class="submit-btn">Submit</button>
                <button type="button" class="back-btn" onclick="goBackToLogin()">Back to Login</button>
            </form>
            <p class="error" id="forgotPasswordError">Please enter a valid email address.</p>
        </div>

    </div>

    <script>
        // Populate days, months, and years dynamically
        const daySelect = document.getElementById('dobDay');
        const monthSelect = document.getElementById('dobMonth');
        const yearSelect = document.getElementById('dobYear');

        // Days (1-31)
        for (let i = 1; i <= 31; i++) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            daySelect.appendChild(option);
        }

        // Months (January - December)
        const months = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        months.forEach((month, index) => {
            const option = document.createElement('option');
            option.value = index + 1;
            option.textContent = month;
            monthSelect.appendChild(option);
        });

        // Years (1900 to current year)
        const currentYear = new Date().getFullYear();
        for (let i = currentYear; i >= 1900; i--) {
            const option = document.createElement('option');
            option.value = i;
            option.textContent = i;
            yearSelect.appendChild(option);
        }

        // Show the forgot password form
        function forgotPasswordForm() {
            document.querySelector('.login-form').style.display = 'none';
            document.querySelector('.forgot-password-form').style.display = 'block';
        }

        // Go back to login form from forgot password
        function goBackToLogin() {
            document.querySelector('.forgot-password-form').style.display = 'none';
            document.querySelector('.login-form').style.display = 'block';
        }

        // Validate the signup form for age and complete fields
        function validateSignupForm() {
            const firstName = document.getElementById('signupFirstName').value;
            const lastName = document.getElementById('signupLastName').value;
            const email = document.getElementById('signupEmail').value;
            const phone = document.getElementById('signupPhone').value;
            const dobDay = document.getElementById('dobDay').value;
            const dobMonth = document.getElementById('dobMonth').value;
            const dobYear = document.getElementById('dobYear').value;

            if (!firstName || !lastName || !email || !phone || !dobDay || !dobMonth || !dobYear) {
                document.getElementById('signupError').style.display = 'block';
                return false;
            }

            // Check if user is at least 18 years old
            const birthDate = new Date(dobYear, dobMonth - 1, dobDay);
            const age = new Date().getFullYear() - birthDate.getFullYear();
            if (age < 18) {
                document.getElementById('signupError').textContent = 'You must be at least 18 years old to register.';
                document.getElementById('signupError').style.display = 'block';
                return false;
            }

            document.getElementById('signupError').style.display = 'none';
            return true;
        }

        // Show the signup form from login
        function showSignupForm() {
            document.querySelector('.login-form').style.display = 'none';
            document.querySelector('.signup-form').style.display = 'block';
        }

        function showLoginForm() {
            document.querySelector('.signup-form').style.display = 'none';
            document.querySelector('.login-form').style.display = 'block';
        }

        // Show password form
        function showPasswordForm() {
            if (validateSignupForm()) {
                document.querySelector('.signup-form').style.display = 'none';
                document.querySelector('.password-form').style.display = 'block';
            }
        }

        function goBackToSignup() {
            document.querySelector('.password-form').style.display = 'none';
            document.querySelector('.signup-form').style.display = 'block';
        }

        // Password Validation
        document.getElementById('passwordForm').onsubmit = function(e) {
            e.preventDefault();
            const password = document.getElementById('signupPassword').value;
            const confirmPassword = document.getElementById('signupConfirmPassword').value;

            if (password.length < 8) {
                document.getElementById('passwordError').textContent = 'Password must be at least 8 characters long.';
                document.getElementById('passwordError').style.display = 'block';
            } else if (password !== confirmPassword) {
                document.getElementById('passwordError').textContent = 'Passwords do not match.';
                document.getElementById('passwordError').style.display = 'block';
            } else {
                alert('Account created successfully!');
                document.querySelector('.password-form').style.display = 'none';
                document.querySelector('.login-form').style.display = 'block';
            }
        }

        // Simulated Login Function (for demonstration)
        function login() {
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;

            // Basic validation
            if (email === "user@example.com" && password === "password123") {
                alert("Login successful!");
            } else {
                document.getElementById('loginError').style.display = 'block';
            }
        }
        // After successful password submission
        document.getElementById('passwordForm').onsubmit = function(e) {
            e.preventDefault();
            const password = document.getElementById('signupPassword').value;
            const confirmPassword = document.getElementById('signupConfirmPassword').value;

            if (password.length < 8) {
                document.getElementById('passwordError').textContent = 'Password must be at least 8 characters long.';
                document.getElementById('passwordError').style.display = 'block';
            } else if (password !== confirmPassword) {
                document.getElementById('passwordError').textContent = 'Passwords do not match.';
                document.getElementById('passwordError').style.display = 'block';
            } else {
                // Redirect to dashboard after successful account creation
                window.location.href = "{{ route('dashboard') }}"; // Corrected to use Blade's route helper
            }
        }
    </script>

</body>

</html>