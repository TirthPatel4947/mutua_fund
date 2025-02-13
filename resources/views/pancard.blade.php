@extends('common_template')
@section('content')
<title>Dashbord</title>
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">FundHorizon</h3>
    </div>
</div>
<div class="content-body">
    <!-- Form wizard with number tabs section start -->
    <section id="number-tabs">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Form wizard with number tabs</h4>
                    </div>
                    <div class="card-content collapse show">
                        <div class="card-body">
                            <form action="#" class="number-tab-steps wizard-circle">
                                <!-- Step 1 -->
                                <fieldset id="step1">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="email1">Email Address :</label>
                                                <input type="email" class="form-control" id="email1" placeholder="Enter your email" required>
                                                <small id="email-error" style="color: red;"></small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="panNumber1">PAN Number :</label>
                                                <input type="text" class="form-control" id="panNumber1" pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" placeholder="Enter PAN number" required>
                                                <small id="pan-error" style="color: red;"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Custom Button for Next -->
                                    <button type="button" id="next-btn" class="btn btn-primary btn-block">Next</button>
                                </fieldset>

                                <!-- Step 2 (OTP Step) -->
                                <fieldset id="otp-step" style="display: none;">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="otp">Enter OTP :</label>
                                                <input type="text" class="form-control" id="otp" pattern="\d{6}" placeholder="Enter 6-digit OTP" required>
                                                <small id="otp-error" style="color: red;"></small>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Custom Buttons for Resend OTP and Back -->
                                    <div class="row">
                                        <div class="col-12">
                                            <a href="javascript:void(0);" id="resend-btn" class="btn btn-link btn-block">Resend OTP</a>
                                        </div>
                                        <div class="col-12">
                                            <a href="javascript:void(0);" id="back-btn" class="btn btn-link btn-block">Back to Email & PAN</a>
                                        </div>
                                    </div>
                                    <!-- Custom Button for Submit -->
                                    <button type="button" id="submit-btn" class="btn btn-success btn-block">Submit</button>
                                </fieldset>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Form wizard with number tabs section end -->
</div>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const nextBtn = document.getElementById('next-btn');
        const submitBtn = document.getElementById('submit-btn');
        const resendBtn = document.getElementById('resend-btn');
        const backBtn = document.getElementById('back-btn');
        const emailInput = document.getElementById('email1');
        const panInput = document.getElementById('panNumber1');
        const otpInput = document.getElementById('otp');
        const emailError = document.getElementById('email-error');
        const panError = document.getElementById('pan-error');
        const otpError = document.getElementById('otp-error');
        const step1 = document.getElementById('step1');
        const otpStep = document.getElementById('otp-step');

        // Validate Email
        function validateEmail() {
            const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
            if (!emailPattern.test(emailInput.value)) {
                emailError.textContent = 'Please enter a valid email address.';
                return false;
            }
            emailError.textContent = '';
            return true;
        }

        // Validate PAN
        function validatePAN() {
            const panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
            if (!panPattern.test(panInput.value)) {
                panError.textContent = 'PAN number must follow the format: AAAAA9999A';
                return false;
            }
            panError.textContent = '';
            return true;
        }

        // Validate OTP
        function validateOTP() {
            const otpPattern = /^\d{6}$/;
            if (!otpPattern.test(otpInput.value)) {
                otpError.textContent = 'OTP must be a 6-digit number.';
                return false;
            }
            otpError.textContent = '';
            return true;
        }

        // Step 1: Show OTP step if Email and PAN are valid
        nextBtn.addEventListener('click', function() {
            if (validateEmail() && validatePAN()) {
                step1.style.display = 'none';
                otpStep.style.display = 'block';
            }
        });

        // Resend OTP functionality
        resendBtn.addEventListener('click', function() {
            alert('OTP has been resent!');
            // Add logic to resend OTP
        });

        // Back to Step 1 functionality
        backBtn.addEventListener('click', function() {
            otpStep.style.display = 'none';
            step1.style.display = 'block';
        });

        // Step 2: Validate OTP and Submit form
        submitBtn.addEventListener('click', function() {
            if (validateOTP()) {
                alert('OTP Verified Successfully!');
                // Submit the form or redirect to another page
                window.location.replace('/dashboard');
            } else {
                alert('Please enter a valid OTP');
            }
        });
    });
</script>

@endsection