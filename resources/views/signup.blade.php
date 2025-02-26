<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>FundHorizon.com Sign Up</title>
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
        .signup-form,
        .password-form {
            display: none;
        }

        .signup-form.active,
        .password-form.active {
            display: block;
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
  <!-- Include jQuery (Make sure jQuery is available in your project) -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!-- Gradient Overlay -->
    <div class="gradient-overlay"></div>

    <!-- Form Container -->
    <div class="form-container">

        <!-- Sign-Up Form -->
        <div class="signup-form active">
            <h2>Sign Up with FundHorizon.com</h2>
            <p>Join our mutual fund investment program and take control of your financial future.</p>
            <form id="signupForm" action="{{ route('signup.store') }}" method="POST">
                @csrf
                <input type="text" id="signupFirstName" name="first_name" placeholder="First Name" required>
                <input type="text" id="signupLastName" name="last_name" placeholder="Last Name" required>
                <input type="email" id="signupEmail" name="email" placeholder="Email Address" required>
                <input type="tel" id="signupPhone" name="phone" placeholder="Phone Number" required>

                <!-- Date of Birth Picker -->
                <div class="dob-picker">
                    <select id="dobDay" name="dob_day" required>
                        <option value="">Day</option>
                    </select>
                    <select id="dobMonth" name="dob_month" required>
                        <option value="">Month</option>
                    </select>
                    <select id="dobYear" name="dob_year" required>
                        <option value="">Year</option>
                    </select>
                </div>

                <button type="button" class="next-btn" id="nextBtn">Next</button>
                <p class="error" id="signupError">Please fill in all fields correctly and make sure you're at least 18 years old.</p>
                <p>Already have an account? <a href="{{ route('login') }}">Login</a></p>
            </form>
        </div>

        <!-- Password Form -->
        <div class="password-form">
            <h2>Create a Password</h2>
            <form id="passwordForm" action="{{ route('signup.store') }}" method="POST">
                @csrf
                <input type="password" id="signupPassword" name="password" placeholder="Password" required minlength="6">
                <input type="password" id="signupConfirmPassword" name="password_confirmation" placeholder="Confirm Password" required minlength="6">
                <button type="submit" class="submit-btn">Submit</button>
                <button type="button" class="back-btn" id="backBtn">Back</button>
            </form>
            <p class="error" id="passwordError"></p>
        </div>

    </div>

    <script>
        // Populate the date of birth select options dynamically
        window.onload = function() {
            // Populate day options
            const daySelect = document.querySelector('[name="dob_day"]');
            for (let i = 1; i <= 31; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = i;
                daySelect.appendChild(option);
            }

            // Populate month options
            const monthSelect = document.querySelector('[name="dob_month"]');
            const months = [
                'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'
            ];
            months.forEach((month, index) => {
                const option = document.createElement('option');
                option.value = index + 1;
                option.textContent = month;
                monthSelect.appendChild(option);
            });

            // Populate year options (current year and last 100 years)
            const yearSelect = document.querySelector('[name="dob_year"]');
            const currentYear = new Date().getFullYear();
            for (let i = currentYear; i >= currentYear - 100; i--) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = i;
                yearSelect.appendChild(option);
            }
        };

        // Validate the signup form
        function validateSignupForm() {
            const firstName = document.getElementById('signupFirstName').value;
            const lastName = document.getElementById('signupLastName').value;
            const email = document.getElementById('signupEmail').value;
            const phone = document.getElementById('signupPhone').value;
            const dobDay = document.getElementById('dobDay').value;
            const dobMonth = document.getElementById('dobMonth').value;
            const dobYear = document.getElementById('dobYear').value;

            // Check if all fields are filled
            if (!firstName || !lastName || !email || !phone || !dobDay || !dobMonth || !dobYear) {
                document.getElementById('signupError').style.display = 'block';
                document.getElementById('signupError').textContent = 'Please fill in all fields.';
                return false;
            }

            // Check if the email format is valid
            const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
            if (!emailPattern.test(email)) {
                document.getElementById('signupError').style.display = 'block';
                document.getElementById('signupError').textContent = 'Please enter a valid email address.';
                return false;
            }

            // Check if phone number format is valid (basic validation: length and number check)
            const phonePattern = /^[0-9]{10}$/;
            if (!phonePattern.test(phone)) {
                document.getElementById('signupError').style.display = 'block';
                document.getElementById('signupError').textContent = 'Please enter a valid phone number.';
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

            // No errors, hide the error message
            document.getElementById('signupError').style.display = 'none';
            return true;
        }

        // Switch to password form when "Next" button is clicked
        document.getElementById('nextBtn').addEventListener('click', function() {
            if (validateSignupForm()) {
                document.querySelector('.signup-form').classList.remove('active');
                document.querySelector('.password-form').classList.add('active');
            }
        });

        // Switch back to the signup form when "Back" button is clicked
        document.getElementById('backBtn').addEventListener('click', function() {
            document.querySelector('.password-form').classList.remove('active');
            document.querySelector('.signup-form').classList.add('active');
        });

        // Password Validation & Submit with AJAX
        document.getElementById('passwordForm').onsubmit = function(e) {
            e.preventDefault();
            
            const password = document.getElementById('signupPassword').value;
            const confirmPassword = document.getElementById('signupConfirmPassword').value;
            const passwordError = document.getElementById('passwordError');

            // Validate password length and match
            if (password.length < 6) {
                passwordError.textContent = 'Password must be at least 6 characters long.';
                passwordError.style.display = 'block';
                return;
            }

            if (password !== confirmPassword) {
                passwordError.textContent = 'Passwords do not match.';
                passwordError.style.display = 'block';
                return;
            }

            // Hide error message
            passwordError.style.display = 'none';

            // Collect form data
            const formData = {
                first_name: document.getElementById('signupFirstName').value,
                last_name: document.getElementById('signupLastName').value,
                email: document.getElementById('signupEmail').value,
                phone: document.getElementById('signupPhone').value,
                dob_day: document.getElementById('dobDay').value,
                dob_month: document.getElementById('dobMonth').value,
                dob_year: document.getElementById('dobYear').value,
                password: password,
                password_confirmation: confirmPassword,
                _token: document.querySelector('meta[name="csrf-token"]').content
            };

            // Send the data via AJAX to store it in the database
            $.ajax({
                url: "{{ route('signup.store') }}", // Make sure this route matches your backend
                type: 'POST',
                data: formData,
                success: function(response) {
                    alert('Account created successfully!');
                    // Optionally, redirect or show a confirmation message
                },
                error: function(xhr, status, error) {
                    alert('An error occurred. Please try again later.');
                }
            });
        };
    </script>
</body>

</html>