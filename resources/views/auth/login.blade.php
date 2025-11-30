@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="login-box pt-0">
        <!-- /.login-logo -->
        <div class="card card-outline card-primary" style="border-bottom: 3px solid #febf32">
            <div class="card-header text-center pb-0">
                <a href="{{ route('home') }}" class="h1">
                    <img src="{{ asset('assets/image/logo-mku.png') }}" width="70%" alt="">
                </a>
            </div>
            <div class="card-body pt-2">

                <p class="login-box-msg mb-0">Silahkan login</p>

                <form id="loginForm" method="POST" action="{{ route('login.process') }}">
                    @csrf
                    {{-- <div class="mb-3">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Username" name="username"
                                id="username">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-user"></span>
                                </div>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="username-error"></div>
                    </div>

                    <div class="mb-3">
                        <div class="input-group">
                            <input type="password" class="form-control" placeholder="Password" name="password"
                                id="password">
                            <div class="input-group-append">
                                <div class="input-group-text">
                                    <span class="fas fa-lock"></span>
                                </div>
                            </div>
                        </div>
                        <div class="invalid-feedback" id="password-error"></div>
                    </div> --}}
                    <div class="row">

                        <!-- /.col -->
                        {{-- <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block" id="login-btn">Masuk</button>
                        </div> --}}
                        <!-- /.col -->
                        <div class="col-12 text-center pt-3">
                            {{-- <div class="separator"><span>atau</span></div> --}}
                            <div class="sso-login-container">
                                <a href="https://auth.umk.ac.id/login?redirect={{ base64_encode(config('app.url')) }}" class="btn-sso-login">
                                    <i class="fas fa-university mr-2"></i> Login SSO UMK
                                </a>
                            </div>
                            <div class="mt-4">
                                
                                <div class="mt-3">
                                    <span class="d-block text-muted ">Sistem Layanan Kewirausahaan</span>
                                    <span class="font-weight-bold text-muted ">Universitas Muria Kudus</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>


            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
    </div>
    <!-- /.login-box -->
@endsection

@section('scripts')
    <script>
        $(function() {
            // Add modern styling to form elements
            $('.form-control').on('focus', function() {
                $(this).parent().addClass('input-group-focus');
            }).on('blur', function() {
                $(this).parent().removeClass('input-group-focus');
            });

            // Handle form submission with AJAX
            $('#loginForm').on('submit', function(e) {
                e.preventDefault();

                // Reset previous errors
                $('.is-invalid').removeClass('is-invalid');
                $('.invalid-feedback').text('');

                // Show loading state on button
                const loginBtn = $('#login-btn');
                const originalBtnText = loginBtn.html();
                loginBtn.html('<i class="fas fa-spinner fa-spin"></i> Memproses...');
                loginBtn.prop('disabled', true);

                // Get form data
                const formData = $(this).serialize();

                // Send AJAX request
                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Show success toast
                            Swal.fire({
                                title: 'Success!',
                                text: response.message,
                                icon: 'success',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000,
                                timerProgressBar: true
                            });

                            // Redirect after delay
                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 1000);
                        } else {
                            // Reset button
                            loginBtn.html(originalBtnText);
                            loginBtn.prop('disabled', false);

                            // Show error message
                            Swal.fire({
                                title: 'Error!',
                                text: response.message,
                                icon: 'error',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    },
                    error: function(xhr) {
                        // Reset button
                        loginBtn.html(originalBtnText);
                        loginBtn.prop('disabled', false);

                        if (xhr.status === 422) {
                            // Validation errors
                            const errors = xhr.responseJSON.errors;

                            // Display each error on the form
                            $.each(errors, function(field, messages) {
                                const inputField = $('#' + field);
                                const errorDisplay = $('#' + field + '-error');

                                inputField.addClass('is-invalid');
                                errorDisplay.text(messages[0]);
                            });
                        } else if (xhr.status === 401) {
                            // Authentication error
                            const errorMsg = xhr.responseJSON.errors.credentials[0];
                            $('#username').addClass('is-invalid');
                            $('#password').addClass('is-invalid');
                            $('#username-error').text(errorMsg);
                        } else {
                            // General error
                            Swal.fire({
                                title: 'Error!',
                                text: 'An unexpected error occurred. Please try again.',
                                icon: 'error',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    }
                });
            });
            
            // Light effect that follows mouse on SSO button
            const ssoButton = document.querySelector('.btn-sso-login');
            
            if (ssoButton) {
                // Add mouseenter event to prepare the button
                ssoButton.addEventListener('mouseenter', function() {
                    // Stop the pulse animation when hovering
                    this.style.animation = 'none';
                    // Add initial glow effect
                    this.style.boxShadow = `0 7px 14px rgba(40, 90, 166, 0.4), 0 0 40px rgba(255, 255, 255, 0.3), 0 0 20px rgba(255, 255, 255, 0.5) inset`;
                    
                    // Tidak ada efek shine otomatis lagi
                });
                
                // Efek cahaya yang mengikuti kursor mouse
                ssoButton.addEventListener('mousemove', function(e) {
                    // Calculate mouse position relative to the button
                    const rect = this.getBoundingClientRect();
                    const x = e.clientX - rect.left; // x position within the element
                    const y = e.clientY - rect.top;  // y position within the element
                    
                    // Calculate the percentage position
                    const xPercent = Math.round((x / rect.width) * 100);
                    const yPercent = Math.round((y / rect.height) * 100);
                    
                    // Apply the light effect with fixed intensity
                    this.style.backgroundImage = `
                        linear-gradient(135deg, #285aa6 0%, #3a7bd5 100%),
                        radial-gradient(circle at ${xPercent}% ${yPercent}%, rgba(255,255,255,0.5) 0%, rgba(255,255,255,0) 60%)
                    `;
                    
                    // Dynamic shadow based on mouse position
                    const shadowX = (xPercent - 50) / 10;
                    const shadowY = (yPercent - 50) / 10;
                    this.style.boxShadow = `
                        ${shadowX}px ${shadowY}px 15px rgba(40, 90, 166, 0.4),
                        0 4px 10px rgba(40, 90, 166, 0.3)
                    `;
                });
                
                // Reset everything when mouse leaves
                ssoButton.addEventListener('mouseleave', function() {
                    // Restore original styles
                    this.style.background = `linear-gradient(135deg, #285aa6 0%, #3a7bd5 100%)`;
                    this.style.boxShadow = `0 4px 10px rgba(40, 90, 166, 0.3)`;
                    this.style.backgroundImage = `linear-gradient(135deg, #285aa6 0%, #3a7bd5 100%)`;
                    this.style.backgroundPosition = 'center';
                    this.style.backgroundSize = '100% 100%';
                    // Restore pulse animation
                    this.style.animation = 'pulse 2s infinite';
                });
            }
        });
    </script>
@endsection

@section('styles')
    <style>
        .login-box {
            width: 400px;
            margin: 0 auto;
            padding-top: 10%;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
            overflow: hidden;
            transition: all 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 18px 35px rgba(50, 50, 93, 0.1), 0 8px 15px rgba(0, 0, 0, 0.07);
        }

        .card-header {
            background-color: #fff;
            border-bottom: none;
            padding: 25px 0;
        }

        .card-body {
            padding: 2rem;
        }

        .form-control {
            height: 45px;
            border-radius: 5px;
            border: 1px solid #ced4da;
            transition: all 0.2s;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .input-group-text {
            border-radius: 0 5px 5px 0;
            background-color: #f8f9fa;
        }

        .input-group-focus .input-group-text {
            border-color: #80bdff;
        }

        .btn-primary {
            height: 45px;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.2s;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        }

        .btn-primary:active {
            transform: translateY(1px);
        }

        /* SSO Login Button Styling */
        .sso-login-container {
            /* margin-top: 20px; */
            margin-bottom: 15px;
        }

        .btn-sso-login {
            display: inline-block;
            padding: 12px 24px;
            background: linear-gradient(135deg, #285aa6 0%, #3a7bd5 100%);
            color: #fff;
            border-radius: 30px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 10px rgba(40, 90, 166, 0.3);
            border: none;
            position: relative;
            overflow: hidden;
            z-index: 1;
            letter-spacing: 0.5px;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        .btn-sso-login:before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #3a7bd5 0%, #285aa6 100%);
            opacity: 0;
            z-index: -1;
            transition: opacity 0.3s ease;
        }
        
        /* Efek cahaya sekarang dikelola sepenuhnya oleh JavaScript */
        
        .btn-sso-login:hover {
            transform: translateY(-2px) scale(1.03);
            box-shadow: 0 7px 14px rgba(40, 90, 166, 0.4);
            color: #fff;
            text-decoration: none;
            letter-spacing: 0.7px;
        }
        
        /* Efek hover dikelola oleh JavaScript */

        .btn-sso-login:hover:before {
            opacity: 1;
        }

        .btn-sso-login:active {
            transform: translateY(1px);
            box-shadow: 0 3px 6px rgba(40, 90, 166, 0.4);
        }
        
        /* Separator styling */
        .separator {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 15px 0;
            color: #6c757d;
        }
        
        .separator::before,
        .separator::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #dee2e6;
        }
        
        .separator::before {
            margin-right: 10px;
        }
        
        .separator::after {
            margin-left: 10px;
        }
        
        /* Add subtle animation to SSO button */
        @keyframes pulse {
            0% {
                box-shadow: 0 4px 10px rgba(40, 90, 166, 0.3);
            }
            50% {
                box-shadow: 0 4px 15px rgba(40, 90, 166, 0.5);
            }
            100% {
                box-shadow: 0 4px 10px rgba(40, 90, 166, 0.3);
            }
        }
        
        .btn-sso-login {
            animation: pulse 2s infinite;
            transition: all 0.3s ease, background-image 0.1s ease, letter-spacing 0.3s ease, transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background-size: 100% 100%;
            background-position: center;
        }

        .invalid-feedback {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #dc3545;
        }

        .login-box-msg {
            font-size: 1.1rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
            color: #6c757d;
        }

        /* Modern checkbox styling */
        .icheck-primary label {
            padding-left: 5px;
            cursor: pointer;
        }
    </style>
@endsection
