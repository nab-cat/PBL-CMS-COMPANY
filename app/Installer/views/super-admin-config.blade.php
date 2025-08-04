@section('title', __('installer.super_admin_title'))
@extends('InstallerEragViews::app-layout')
@section('content')
    <section class="mt-4 installer-content bg-radial-gradient">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    {{-- FIX: The <form> tag now wraps the entire card content, including the footer. --}}
                        <form id="admin-form" action="{{ route('saveSuperAdmin') }}" method="post">
                            @csrf
                            <div class="card mb-4 shadow-lg border-0 admin-config-card">
                                <div class="card-body py-5">
                                    <!-- Header Section with Logo and Title -->
                                    <div class="text-center mb-5">
                                        <!-- Admin Icon -->
                                        <div class="mb-4">
                                            <i class="bi bi-person-fill-gear display-1"
                                                style="color: var(--primary-color);"></i>
                                        </div>
                                        <!-- Title -->
                                        <h1 class="display-5 mb-3 admin-title" style="color: var(--primary-color);">
                                            {{ __('installer.super_admin_configuration') }}
                                        </h1>
                                        <p class="lead mb-0 text-muted admin-subtitle">
                                            {{ __('installer.features.admin.description') }}
                                        </p>
                                    </div>

                                    <!-- Alerts Section -->
                                    @if (session('account_exists'))
                                        <div class="alert alert-warning mb-4">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                            {{ session('account_exists') }}
                                        </div>
                                    @endif
                                    @if (session('database_error'))
                                        <div class="alert alert-danger mb-4">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                            {{ session('database_error') }}
                                        </div>
                                    @endif
                                    @if ($errors->any())
                                        <div class="alert alert-danger mb-4">
                                            <ul class="mb-0">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    <!-- Form Section -->
                                    <div class="row justify-content-center">
                                        <div class="col-md-8">
                                            <div class="mb-4">
                                                <x-install-input label="{{ __('installer.full_name') }}" required="true"
                                                    name="name" type="text" value="{{ old('name') }}" />
                                                <x-install-error for="name" />
                                            </div>
                                            <div class="mb-4">
                                                <x-install-input label="{{ __('installer.email') }}" required="true"
                                                    name="email" type="email" value="{{ old('email') }}" />
                                                <x-install-error for="email" />
                                            </div>
                                            <div class="mb-4">
                                                <label class="mb-1" for="password">{{ __('installer.password') }} <span
                                                        class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" name="password" id="password"
                                                        class="form-control @error('password') is-invalid @enderror"
                                                        required>
                                                    <button type="button" class="btn btn-outline-secondary toggle-password"
                                                        data-target="password" aria-label="Show password">
                                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                                <small
                                                    class="form-text text-muted">{{__("installer.password_description") }}</small>
                                                <x-install-error for="password" />
                                            </div>
                                            <div class="mb-4">
                                                <label class="mb-1"
                                                    for="password_confirmation">{{ __('installer.password_confirmation') }}
                                                    <span class="text-danger">*</span></label>
                                                <div class="input-group">
                                                    <input type="password" name="password_confirmation"
                                                        id="password_confirmation"
                                                        class="form-control @error('password_confirmation') is-invalid @enderror"
                                                        required>
                                                    <button type="button" class="btn btn-outline-secondary toggle-password"
                                                        data-target="password_confirmation" aria-label="Show password">
                                                        <i class="bi bi-eye" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                                <x-install-error for="password_confirmation" />
                                            </div>
                                            <div class="mb-5">
                                                <div class="form-check">
                                                    <input type="checkbox" name="include_dummy_data" id="include_dummy_data"
                                                        class="form-check-input" value="1" {{ old('include_dummy_data') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="include_dummy_data">
                                                        <strong>{{ __('installer.include_dummy_data') }}</strong>
                                                    </label>
                                                    <div class="form-text text-muted">
                                                        {{ __('installer.dummy_data_description') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- BUG: The first <form> was closed here, separating it from the submit button. This
                                        has been removed. --}}
                                </div>

                                <!-- Card Footer with Action Buttons -->
                                <div class="card-footer bg-light border-top p-4">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('profil_perusahaan') }}"
                                            class="btn btn-outline-primary btn-lg px-5">
                                            {{ __('installer.back') }}
                                        </a>
                                        {{-- BUG: A second <form> element was here. It has been removed. --}}
                                            {{-- The button is now a standard submit button for the main form. --}}
                                            <button type="submit" class="btn btn-primary btn-lg px-5 admin-btn">
                                                {{ __('installer.create_account') }}
                                            </button>
                                    </div>
                                </div>
                            </div>
                        </form> {{-- FIX: The single <form> tag is now closed here. --}}
                </div>
            </div>
        </div>
    </section>

    <style>
        /* Admin config page specific styles - matching finish page */
        .admin-config-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9ff 100%);
            border-radius: 20px !important;
            overflow: hidden;
            animation: slideInUp 0.8s ease-out;
        }

        .admin-title {
            font-weight: 700;
            animation: fadeInDown 1s ease-out 0.3s both;
        }

        .admin-subtitle {
            animation: fadeInUp 1s ease-out 0.5s both;
        }

        .admin-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, #4338ca 100%);
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .admin-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
        }

        @keyframes slideInUp {
            from {
                transform: translateY(30px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeInDown {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Form styling improvements */
        .form-control {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 12px 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .btn-outline-secondary {
            border-radius: 0 8px 8px 0;
            cursor: pointer;
            z-index: 10;
        }

        .btn-outline-secondary:hover {
            background-color: #f8f9fa;
            border-color: #6c757d;
        }

        .toggle-password {
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            padding: 0;
            position: relative;
            background-color: white !important;
            border: 1px solid #ced4da !important;
        }

        .toggle-password:hover {
            background-color: #f8f9fa !important;
            border-color: #6c757d !important;
            cursor: pointer;
        }

        .toggle-password:active {
            background-color: #e9ecef !important;
            transform: scale(0.98);
        }

        .toggle-password i {
            font-size: 16px;
            color: #6c757d;
            pointer-events: none;
        }

        .toggle-password:hover i {
            color: #495057;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        /* Card Footer Styling */
        .card-footer {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
            border-top: 1px solid #dee2e6 !important;
            border-radius: 0 0 20px 20px !important;
            margin-top: 0 !important;
        }

        .card-footer .btn {
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .card-footer .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        }

        .card-footer .btn-outline-primary {
            border-width: 2px;
            font-weight: 600;
        }

        .card-footer .btn-outline-primary:hover {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.3);
        }
    </style>

    <script>
        // Form validation before submit
        function validateFormBeforeSubmit() {
            const form = document.getElementById('admin-form');
            if (!form) return;

            form.addEventListener('submit', function (e) {
                // Check required fields
                const requiredFields = [
                    { name: 'name', label: 'Full Name' },
                    { name: 'email', label: 'Email' },
                    { name: 'password', label: 'Password' },
                    { name: 'password_confirmation', label: 'Password Confirmation' }
                ];

                for (let field of requiredFields) {
                    const input = document.querySelector(`[name="${field.name}"]`);
                    if (!input || !input.value.trim()) {
                        e.preventDefault(); // Stop submission
                        alert(`${field.label} is required.`);
                        input?.focus();
                        return; // Exit
                    }
                }

                // Validate email format
                const emailInput = document.querySelector('[name="email"]');
                if (emailInput) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(emailInput.value.trim())) {
                        e.preventDefault(); // Stop submission
                        alert('Please enter a valid email address.');
                        emailInput.focus();
                        return; // Exit
                    }
                }

                // Validate password match
                const passwordInput = document.querySelector('[name="password"]');
                const passwordConfirmInput = document.querySelector('[name="password_confirmation"]');
                if (passwordInput && passwordConfirmInput) {
                    if (passwordInput.value !== passwordConfirmInput.value) {
                        e.preventDefault(); // Stop submission
                        alert('Password and Password Confirmation do not match.');
                        passwordConfirmInput.focus();
                        return; // Exit
                    }
                }

                // If all checks pass, the form will submit naturally.
            });
        }

        // Initialize everything when DOM is loaded
        document.addEventListener('DOMContentLoaded', function () {
            validateFormBeforeSubmit();
        });
    </script>
@endsection