@section('title', __('installer.database_title'))
@extends('InstallerEragViews::app-layout')
@section('content')

    <section class="mt-4 installer-content bg-radial-gradient">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card mb-4 shadow-lg border-0 database-config-card">
                        <div class="card-body py-5">
                            <!-- Header Section with Logo and Title -->
                            <div class="text-center mb-5">
                                <!-- Database Icon -->
                                <div class="mb-4">
                                    <i class="bi bi-database-gear display-1" style="color: var(--primary-color);"></i>
                                </div>
                                <!-- Title -->
                                <h1 class="display-5 mb-3 database-title" style="color: var(--primary-color);">
                                    {{ __('installer.database_title') }}
                                </h1>
                                <p class="lead mb-0 text-muted database-subtitle">
                                    {{ __('installer.features.database.description') }}
                                </p>
                            </div>

                            <!-- Alert Section -->
                            @include('InstallerEragViews::includes.database-connection-error')

                            @if ($errors->any() && !$errors->hasAny(['database_connection', 'database_fields', 'save_error']))
                                <div class="alert alert-danger mb-4">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <!-- Tabbed Configuration Form -->
                            <form action="{{ route('saveWizard') }}" method="post" id="database-form" novalidate>
                                @csrf

                                <!-- Tab Navigation -->
                                <div class="row justify-content-center mb-4">
                                    <div class="col-md-10">
                                        <ul class="nav nav-pills nav-justified config-tabs" id="configTabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="environment-tab" data-bs-toggle="pill"
                                                    data-bs-target="#environment-config" type="button" role="tab">
                                                    <i class="bi bi-gear me-2"></i>Environment
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="database-tab" data-bs-toggle="pill"
                                                    data-bs-target="#database-config" type="button" role="tab">
                                                    <i class="bi bi-database me-2"></i>Database
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="email-tab" data-bs-toggle="pill"
                                                    data-bs-target="#email-config" type="button" role="tab">
                                                    <i class="bi bi-envelope me-2"></i>Email
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Tab Content -->
                                <div class="tab-content" id="configTabContent">
                                    <!-- Environment Configuration Tab -->
                                    <div class="tab-pane fade show active" id="environment-config" role="tabpanel">
                                        <div class="row justify-content-center">
                                            <div class="col-md-10">
                                                <h5 class="mb-4 text-center">Environment Settings</h5>
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <x-install-select label="{{ __('installer.database_connection') }}"
                                                            class="form-control" name="environment" required="true">
                                                            <option value="production" selected>Production</option>
                                                            <option value="local">Local</option>
                                                        </x-install-select>
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <x-install-select label="{{ __('installer.app_debug') }}"
                                                            class="form-control" name="app_debug">
                                                            <option value="true" {{ old('app_debug') == 'true' ? 'selected' : '' }}>
                                                                True</option>
                                                            <option value="false" {{ old('app_debug', 'false') == 'false' ? 'selected' : '' }}>
                                                                False</option>
                                                        </x-install-select>
                                                        <x-install-error for="app_debug" />
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <x-install-input label="{{ __('installer.app_log_level') }}"
                                                            name="app_log_level" type="text" value="debug" readonly>
                                                        </x-install-input>
                                                    </div>

                                                    @php
                                                        $isHttps = app('request')->isSecure();
                                                        $protocol = $isHttps ? 'https://' : 'http://';
                                                        $base_url = $protocol . app('request')->getHttpHost();
                                                    @endphp

                                                    <div class="col-md-4 mb-3">
                                                        <x-install-input label="{{ __('installer.app_url') }}"
                                                            name="app_url" type="url" required="true"
                                                            value="{{ old('app_url', $base_url) }}" />
                                                        <x-install-error for="app_url" />
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        @component('InstallerEragViews::components.timezone-select', [
                                                            'label' => __('installer.app_timezone'),
                                                            'required' => true,
                                                            'name' => 'app_timezone'
                                                        ])
                                                        @endcomponent
                                                        <x-install-error for="app_timezone" />
                                                    </div>
                                                    <div class="col-md-4 mb-3">
                                                        <x-install-select label="{{ __('installer.app_locale') }}"
                                                            class="form-control" name="app_locale" required="true">
                                                            <option value="en" {{ old('app_locale') == 'en' ? 'selected' : '' }}>English
                                                            </option>
                                                            <option value="id" {{ old('app_locale', 'id') == 'id' ? 'selected' : '' }}>Indonesian
                                                            </option>
                                                        </x-install-select>
                                                        <x-install-error for="app_locale" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Database Configuration Tab -->
                                    <div class="tab-pane fade" id="database-config" role="tabpanel">
                                        <div class="row justify-content-center">
                                            <div class="col-md-10">
                                                <h5 class="mb-4 text-center">Database Connection</h5>
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <x-install-select
                                                            label="{{ __('installer.database_connection_type') }}"
                                                            class="form-control" name="database_connection"
                                                            id="database_connection" required="true">
                                                            <option value="mysql" selected>MySQL</option>
                                                            <option value="sqlite" {{ old('database_connection') == 'sqlite' ? 'selected' : '' }}>
                                                                SQLite</option>
                                                        </x-install-select>
                                                    </div>

                                                    <!-- Database Name field (always shown) -->
                                                    <div class="col-md-4 mb-3" id="database_name_container">
                                                        <x-install-input label="{{ __('installer.database_name') }}"
                                                            name="database_name" type="text" required="true"
                                                            value="{{ old('database_name') }}" />
                                                        <x-install-error for="database_name" />
                                                        <div class="text-muted small mt-1" id="sqlite_help_text"
                                                            style="display: none;">
                                                            {{ __('installer.sqlite_help_text') }}
                                                            ({{ __('installer.example') }}:
                                                            <code>mydb.sqlite</code>)
                                                        </div>
                                                    </div>

                                                    <!-- MySQL specific fields -->
                                                    <div class="mysql-only col-md-4 mb-3" id="database_host_container">
                                                        <x-install-input label="{{ __('installer.database_host') }}"
                                                            name="database_hostname" type="text" required="true"
                                                            value="{{ old('database_hostname', '127.0.0.1') }}" />
                                                        <x-install-error for="database_hostname" />
                                                    </div>

                                                    <div class="mysql-only col-md-4 mb-3" id="database_port_container">
                                                        <x-install-input label="{{ __('installer.database_port') }}"
                                                            name="database_port" type="text" required="true"
                                                            value="{{ old('database_port', '3306') }}" />
                                                        <x-install-error for="database_port" />
                                                    </div>

                                                    <div class="mysql-only col-md-4 mb-3" id="database_user_container">
                                                        <x-install-input label="{{ __('installer.database_username') }}"
                                                            name="database_username" type="text" required="true"
                                                            value="{{ old('database_username') }}" />
                                                        <x-install-error for="database_username" />
                                                    </div>

                                                    <div class="mysql-only col-md-4 mb-3" id="database_password_container">
                                                        <label class="mb-1"
                                                            for="database_password">{{ __('installer.database_password') }}</label>
                                                        <div class="input-group">
                                                            <input type="password" name="database_password"
                                                                id="database_password"
                                                                class="form-control @error('database_password') is-invalid @enderror"
                                                                value="{{ old('database_password') }}">
                                                            <button type="button"
                                                                class="btn btn-outline-secondary toggle-password"
                                                                data-target="database_password">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                        </div>
                                                        <x-install-error for="database_password" />
                                                    </div>
                                                </div>

                                                <!-- Test Connection Button -->
                                                <div class="text-center mt-4">
                                                    <button type="button" id="test_connection_button"
                                                        class="btn btn-secondary btn-lg px-4" disabled
                                                        title="{{ __('installer.please_fill_required_fields') }}">
                                                        <i class="bi bi-shield-check me-2"></i>
                                                        {{ __('installer.test_connection') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Email Configuration Tab -->
                                    <div class="tab-pane fade" id="email-config" role="tabpanel">
                                        <div class="row justify-content-center">
                                            <div class="col-md-10">
                                                <h5 class="mb-4 text-center">Email Settings</h5>
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <x-install-input label="{{ __('installer.mail_driver') }}"
                                                            type="text" value="smtp" name="mail_mailer" readonly>
                                                        </x-install-input>
                                                        <x-install-error for="mail_mailer" />
                                                    </div>

                                                    <div class="col-md-4 mb-3" id="mail_host_container">
                                                        <x-install-input label="{{ __('installer.mail_host') }}"
                                                            name="mail_host" type="text" required="true"
                                                            value="{{ old('mail_host', 'smtp.gmail.com') }}" readonly />
                                                        <x-install-error for="mail_host" />
                                                    </div>

                                                    <div class="col-md-4 mb-3" id="mail_port_container">
                                                        <x-install-input label="{{ __('installer.mail_port') }}"
                                                            name="mail_port" type="number" required="true"
                                                            value="{{ old('mail_port', '587') }}" readonly />
                                                        <x-install-error for="mail_port" />
                                                    </div>

                                                    <div class="col-md-4 mb-3" id="mail_username_container">
                                                        <x-install-input label="{{ __('installer.mail_username') }}"
                                                            name="mail_username" type="text" required="true"
                                                            value="{{ old('mail_username') }}" />
                                                        <x-install-error for="mail_username" />
                                                    </div>

                                                    <div class="col-md-4 mb-3" id="mail_password_container">
                                                        <label class="mb-1"
                                                            for="mail_password">{{ __('installer.mail_password') }} <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="password" name="mail_password" id="mail_password"
                                                                class="form-control @error('mail_password') is-invalid @enderror"
                                                                value="{{ old('mail_password') }}">
                                                            <button type="button"
                                                                class="btn btn-outline-secondary toggle-password"
                                                                data-target="mail_password">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                        </div>
                                                        <x-install-error for="mail_password" />
                                                    </div>

                                                    <div class="col-md-4 mb-3" id="mail_encryption_container">
                                                        <x-install-select label="{{ __('installer.mail_encryption') }}"
                                                            class="form-control" name="mail_encryption" required="true">
                                                            <option value="">None</option>
                                                            <option value="tls" {{ old('mail_encryption', 'tls') == 'tls' ? 'selected' : '' }}>TLS
                                                            </option>
                                                            <option value="ssl" {{ old('mail_encryption') == 'ssl' ? 'selected' : '' }}>SSL</option>
                                                        </x-install-select>
                                                        <x-install-error for="mail_encryption" />
                                                    </div>

                                                    <div class="col-md-6 mb-3" hidden>
                                                        <x-install-input label="{{ __('installer.mail_from_address') }}"
                                                            name="mail_from_address" type="email" required="true"
                                                            value="{{ old('mail_from_address', 'noreply@example.com') }}"
                                                            readonly />
                                                        <x-install-error for="mail_from_address" />
                                                        <div class="text-muted small mt-1">
                                                            {{ __('installer.mail_from_name_description') }}
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Test Email Button -->
                                                <div class="text-center mt-4">
                                                    <button type="button" id="test_email_button"
                                                        class="btn btn-info btn-lg px-4">
                                                        <i class="bi bi-envelope-check me-2"></i>
                                                        {{ __('installer.test_email') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Card Footer with Action Buttons -->
                        <div class="card-footer bg-light border-top p-4">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('installs') }}" class="btn btn-outline-primary btn-lg px-5">
                                    {{ __('installer.back') }}
                                </a>
                                <form action="{{ route('saveWizard') }}" method="post" class="mb-0">
                                    @csrf
                                    <!-- Include all form data as hidden inputs -->
                                    <input type="hidden" name="environment" id="footer-environment">
                                    <input type="hidden" name="app_debug" id="footer-app_debug">
                                    <input type="hidden" name="app_log_level" id="footer-app_log_level">
                                    <input type="hidden" name="database_connection" id="footer-database_connection">
                                    <input type="hidden" name="database_name" id="footer-database_name">
                                    <input type="hidden" name="database_hostname" id="footer-database_hostname">
                                    <input type="hidden" name="database_port" id="footer-database_port">
                                    <input type="hidden" name="database_username" id="footer-database_username">
                                    <input type="hidden" name="database_password" id="footer-database_password">
                                    <input type="hidden" name="app_url" id="footer-app_url">
                                    <input type="hidden" name="app_timezone" id="footer-app_timezone">
                                    <input type="hidden" name="app_locale" id="footer-app_locale">
                                    <input type="hidden" name="mail_mailer" id="footer-mail_mailer">
                                    <input type="hidden" name="mail_host" id="footer-mail_host">
                                    <input type="hidden" name="mail_port" id="footer-mail_port">
                                    <input type="hidden" name="mail_username" id="footer-mail_username">
                                    <input type="hidden" name="mail_password" id="footer-mail_password">
                                    <input type="hidden" name="mail_encryption" id="footer-mail_encryption">
                                    <input type="hidden" name="mail_from_address" id="footer-mail_from_address">
                                    <button type="submit" id="next_button" class="btn btn-primary btn-lg px-5 database-btn"
                                        disabled title="{{ __('installer.please_test_database_connection_first') }}">
                                        {{ __('installer.next') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* Database config page specific styles - matching finish page */
        .database-config-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9ff 100%);
            border-radius: 20px !important;
            overflow: hidden;
            animation: slideInUp 0.8s ease-out;
        }

        .database-title {
            font-weight: 700;
            animation: fadeInDown 1s ease-out 0.3s both;
        }

        .database-subtitle {
            animation: fadeInUp 1s ease-out 0.5s both;
        }

        .database-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, #4338ca 100%);
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .database-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
        }

        /* Tab styling */
        .config-tabs {
            background: rgba(255, 255, 255, 0.8);
            border-radius: 15px;
            padding: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .config-tabs .nav-link {
            border: none;
            border-radius: 10px;
            color: #6b7280;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 0 4px;
            cursor: pointer;
            user-select: none;
            pointer-events: auto;
            position: relative;
            z-index: 10;
        }

        .config-tabs .nav-link.active {
            background: linear-gradient(135deg, var(--primary-color) 0%, #4338ca 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .config-tabs .nav-link:hover:not(.active) {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary-color);
            cursor: pointer;
        }

        /* Tab error indicators */
        .config-tabs .nav-link.has-error {
            position: relative;
        }

        .config-tabs .nav-link.has-error::after {
            content: '!';
            position: absolute;
            top: -5px;
            right: -5px;
            width: 18px;
            height: 18px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            font-size: 12px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
            }
        }

        /* Tab content styling */
        .tab-content {
            min-height: 400px;
            padding: 20px 0;
        }

        .tab-pane h5 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 2rem;
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
        }

        /* Test button improvements */
        .btn-warning,
        .btn-info {
            border-radius: 10px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-warning:hover,
        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        /* Technical details styling */
        .technical-details-toggle {
            border-radius: 6px;
            font-size: 0.875rem;
            padding: 4px 12px;
            transition: all 0.3s ease;
        }

        .technical-details-toggle:hover {
            background-color: rgba(108, 117, 125, 0.1);
        }

        .technical-details-content {
            border-left: 3px solid #dee2e6;
            padding-left: 15px;
        }

        .technical-details-content code {
            color: #e83e8c;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 0.85rem;
            word-break: break-word;
            white-space: pre-wrap;
        }

        /* Test button disabled state styling */
        .btn-secondary:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-secondary:disabled:hover {
            transform: none;
            box-shadow: none;
        }

        /* Tooltip styling for disabled button */
        .btn[title]:disabled {
            position: relative;
        }

        /* Required field indicators - Red asterisk for mandatory fields */
        .required-field::after {
            content: ' *';
            color: #dc3545;
            font-weight: bold;
        }

        /* Validation error message styling */
        .validation-error-message {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
            border: 1px solid #fca5a5;
            border-radius: 10px;
            animation: fadeInDown 0.5s ease-out;
        }

        .missing-field {
            display: inline-block;
            background: #dc3545;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
            margin: 2px;
        }

        /* Validation error styling */
        .validation-error-message {
            border-left: 4px solid #dc3545;
            background: linear-gradient(90deg, rgba(220, 53, 69, 0.1) 0%, rgba(220, 53, 69, 0.05) 100%);
        }

        .validation-error-message .btn {
            font-size: 0.875rem;
            padding: 6px 12px;
            margin: 2px;
            border-radius: 6px;
            transition: all 0.3s ease;
        }

        .validation-error-message .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }

        .missing-field {
            background: rgba(220, 53, 69, 0.1);
            color: #721c24;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: 500;
            margin: 2px;
            display: inline-block;
        }
    </style>

    <script>
        /**
         * Database Import Page JavaScript
         * 
         * Features:
         * 1. Manage form display based on selected database type (MySQL or SQLite)
         * 2. Handle Test Connection button for database connection testing
         * 3. Handle form submission with AJAX for validation and error processing
         */
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize database connection test status
            let isDatabaseTestSuccessful = false;
            // Initialize email test status
            let isEmailTestSuccessful = false;

            // Helper function to remove existing alerts
            function removeExistingAlerts() {
                const existingAlerts = document.querySelectorAll('.alert-dismissible');
                existingAlerts.forEach(alert => {
                    alert.remove();
                });
            }

            // Manual tab switching
            document.querySelectorAll('.config-tabs .nav-link').forEach(tabButton => {
                tabButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Remove active class from all tabs and content
                    document.querySelectorAll('.config-tabs .nav-link').forEach(btn => {
                        btn.classList.remove('active');
                    });
                    document.querySelectorAll('.tab-pane').forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });

                    // Add active class to clicked tab
                    this.classList.add('active');

                    // Show corresponding content
                    const targetId = this.getAttribute('data-bs-target');
                    const targetPane = document.querySelector(targetId);
                    if (targetPane) {
                        targetPane.classList.add('show', 'active');
                    }
                });
            });

            // Get form element and important elements
            const form = document.getElementById('database-form');
            if (!form) {
                return;
            }

            const dbConnectionSelect = document.getElementById('database_connection');
            const mysqlOnlyFields = document.querySelectorAll('.mysql-only');
            const sqliteHelpText = document.getElementById('sqlite_help_text');
            const testConnectionBtn = document.getElementById('test_connection_button');

            // Function to toggle form fields based on database selection
            function toggleDatabaseFields() {
                const selectedConnection = dbConnectionSelect.value;

                if (selectedConnection === 'sqlite') {
                    // Hide MySQL fields
                    mysqlOnlyFields.forEach(field => {
                        field.style.display = 'none';
                    });

                    // Remove required attribute from MySQL fields
                    document.querySelectorAll('.mysql-only input').forEach(input => {
                        // Store the original required state if needed for MySQL
                        if (input.hasAttribute('required')) {
                            input.setAttribute('data-was-required', 'true');
                            input.removeAttribute('required');
                        }
                    });

                    // Move database_name field to take up more space
                    const dbNameContainer = document.getElementById('database_name_container');
                    if (dbNameContainer) {
                        dbNameContainer.className = 'col-md-8 mb-3';

                        // Show SQLite help text within the database_name_container
                        if (sqliteHelpText) {
                            sqliteHelpText.style.display = 'block';
                        }
                    }
                } else {
                    // Show MySQL fields
                    mysqlOnlyFields.forEach(field => {
                        field.style.display = 'block';
                    });

                    // Add required attribute to MySQL fields that need it
                    const hostnameInput = document.querySelector('input[name="database_hostname"]');
                    const portInput = document.querySelector('input[name="database_port"]');
                    const usernameInput = document.querySelector('input[name="database_username"]');

                    if (hostnameInput) hostnameInput.setAttribute('required', 'required');
                    if (portInput) portInput.setAttribute('required', 'required');
                    if (usernameInput) usernameInput.setAttribute('required', 'required');

                    // Reset database_name field size
                    const dbNameContainer = document.getElementById('database_name_container');
                    if (dbNameContainer) {
                        dbNameContainer.className = 'col-md-4 mb-3';

                        // Hide SQLite help text
                        if (sqliteHelpText) {
                            sqliteHelpText.style.display = 'none';
                        }
                    }
                }
            }

            // Function to validate required database fields
            function validateDatabaseFields() {
                const selectedConnection = dbConnectionSelect.value;
                let isValid = true;
                let missingFields = [];

                // Always check database name
                const databaseName = document.querySelector('input[name="database_name"]');
                if (!databaseName || !databaseName.value.trim()) {
                    isValid = false;
                    missingFields.push('{{ __("installer.database_name") }}');
                }

                // Check MySQL specific fields if MySQL is selected
                if (selectedConnection === 'mysql') {
                    const hostname = document.querySelector('input[name="database_hostname"]');
                    const port = document.querySelector('input[name="database_port"]');
                    const username = document.querySelector('input[name="database_username"]');

                    if (!hostname || !hostname.value.trim()) {
                        isValid = false;
                        missingFields.push('{{ __("installer.database_host") }}');
                    }
                    if (!port || !port.value.trim()) {
                        isValid = false;
                        missingFields.push('{{ __("installer.database_port") }}');
                    }
                    if (!username || !username.value.trim()) {
                        isValid = false;
                        missingFields.push('{{ __("installer.database_username") }}');
                    }
                }

                return { isValid, missingFields };
            }

            // Function to update test connection button state
            function updateTestConnectionButtonState() {
                if (!testConnectionBtn) return;

                const validation = validateDatabaseFields();

                if (validation.isValid) {
                    testConnectionBtn.disabled = false;
                    testConnectionBtn.classList.remove('btn-secondary');
                    testConnectionBtn.classList.add('btn-warning');
                    testConnectionBtn.title = '';
                } else {
                    testConnectionBtn.disabled = true;
                    testConnectionBtn.classList.remove('btn-warning');
                    testConnectionBtn.classList.add('btn-secondary');
                    testConnectionBtn.title = '{{ __("installer.please_fill_required_fields") }}: ' + validation.missingFields.join(', ');
                }
            }

            // Function to update Next button state based on both database and email test success
            function updateNextButtonState() {
                const nextButton = document.getElementById('next_button');
                if (!nextButton) return;

                // Both tests must be successful to enable the Next button
                if (isDatabaseTestSuccessful && isEmailTestSuccessful) {
                    nextButton.disabled = false;
                    nextButton.title = '';
                    nextButton.classList.remove('btn-secondary');
                    nextButton.classList.add('btn-primary');
                } else {
                    nextButton.disabled = true;

                    // Create appropriate tooltip message based on which tests are missing
                    let tooltipMessage = '';
                    if (!isDatabaseTestSuccessful && !isEmailTestSuccessful) {
                        tooltipMessage = '{{ __("installer.please_test_database_and_email_first") }}';
                    } else if (!isDatabaseTestSuccessful) {
                        tooltipMessage = '{{ __("installer.please_test_database_connection_first") }}';
                    } else if (!isEmailTestSuccessful) {
                        tooltipMessage = '{{ __("installer.please_test_email_connection_first") }}';
                    }

                    nextButton.title = tooltipMessage;
                    nextButton.classList.remove('btn-primary');
                    nextButton.classList.add('btn-secondary');
                }
            }

            // Add event listener to database connection dropdown
            if (dbConnectionSelect) {
                dbConnectionSelect.addEventListener('change', function () {
                    toggleDatabaseFields();
                    // Reset database test status when connection type changes
                    isDatabaseTestSuccessful = false;
                    updateTestConnectionButtonState();
                    updateNextButtonState();
                });
                // Run toggle on page load
                toggleDatabaseFields();
            }

            // Add event listeners to required fields to update button state
            const requiredFields = [
                'input[name="database_name"]',
                'input[name="database_hostname"]',
                'input[name="database_port"]',
                'input[name="database_username"]',
                'input[name="database_password"]'
            ];

            requiredFields.forEach(selector => {
                const field = document.querySelector(selector);
                if (field) {
                    field.addEventListener('input', function () {
                        // Reset database test status when fields change
                        isDatabaseTestSuccessful = false;
                        updateTestConnectionButtonState();
                        updateNextButtonState();
                    });
                    field.addEventListener('blur', function () {
                        // Reset database test status when fields change
                        isDatabaseTestSuccessful = false;
                        updateTestConnectionButtonState();
                        updateNextButtonState();
                    });
                }
            });

            // Initialize button states after all event listeners are set
            updateTestConnectionButtonState();
            updateNextButtonState();

            // Add input event listeners to all form fields to clear tab error indicators when user starts typing
            const allFormFields = document.querySelectorAll('input, select, textarea');
            allFormFields.forEach(field => {
                field.addEventListener('input', clearTabErrorIndicators);
                field.addEventListener('change', clearTabErrorIndicators);
            });

            // Add test connection functionality
            if (testConnectionBtn) {
                testConnectionBtn.addEventListener('click', function () {
                    // Validate fields before proceeding
                    const validation = validateDatabaseFields();
                    if (!validation.isValid) {
                        // Show validation error
                        const validationAlert = document.createElement('div');
                        validationAlert.className = 'alert alert-warning mb-4';
                        validationAlert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>{{ __("installer.validation_error") }}</strong> {{ __("installer.please_fill_required_fields") }}: ' + validation.missingFields.join(', ');

                        // Add message at the top of the card body
                        const cardBody = document.querySelector('.database-config-card .card-body');
                        const headerSection = cardBody.querySelector('.text-center');
                        if (headerSection) {
                            headerSection.insertAdjacentElement('afterend', validationAlert);
                        }

                        // Remove alert after 5 seconds
                        setTimeout(() => {
                            validationAlert.remove();
                        }, 5000);

                        // Scroll to top to show validation error
                        window.scrollTo(0, 0);
                        return;
                    }
                    // Clear any previous messages
                    const previousMessages = document.querySelectorAll('.alert');
                    previousMessages.forEach(msg => msg.remove());

                    // Disable test button
                    testConnectionBtn.disabled = true;
                    testConnectionBtn.classList.add('test-button-loading');
                    testConnectionBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>{{ __("installer.testing") }}';

                    // Collect form data
                    const formData = new FormData(form);

                    fetch('{{ route("test_database_connection") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            // Re-enable the button and update state based on validation
                            testConnectionBtn.classList.remove('test-button-loading');
                            testConnectionBtn.innerHTML = '<i class="bi bi-shield-check me-2"></i>{{ __("installer.test_connection") }}';
                            updateTestConnectionButtonState();

                            // Remove existing alerts before showing new one
                            removeExistingAlerts();

                            // Create message div
                            const messageDiv = document.createElement('div');
                            messageDiv.className = data.success ? 'alert alert-success alert-dismissible mb-4' : 'alert alert-danger alert-dismissible mb-4';

                            if (data.success) {
                                messageDiv.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i><strong>{{ __("installer.success") }}</strong> ' + data.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                                // Set database test as successful and enable Next button
                                isDatabaseTestSuccessful = true;
                                updateNextButtonState();
                            } else {
                                // Create user-friendly error message with expandable technical details
                                let errorContent = '<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>{{ __("installer.error") }}</strong> ' + data.message;

                                if (data.technical_details) {
                                    const detailsId = 'technical-details-' + Date.now();
                                    errorContent += '<div class="mt-3">';
                                    errorContent += '<button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#' + detailsId + '" aria-expanded="false" aria-controls="' + detailsId + '">';
                                    errorContent += '<i class="bi bi-info-circle me-1"></i>{{ __("installer.database_error_details") }}';
                                    errorContent += '</button>';
                                    errorContent += '<div class="collapse mt-2" id="' + detailsId + '">';
                                    errorContent += '<div class="alert alert-secondary small technical-details-content">';
                                    errorContent += '<code>' + data.technical_details + '</code>';
                                    errorContent += '</div>';
                                    errorContent += '</div>';
                                    errorContent += '</div>';
                                }

                                errorContent += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                                messageDiv.innerHTML = errorContent;

                                // Database test failed, keep Next button disabled
                                isDatabaseTestSuccessful = false;
                                updateNextButtonState();
                            }

                            // Add message at the top of the card body
                            const cardBody = document.querySelector('.database-config-card .card-body');
                            const headerSection = cardBody.querySelector('.text-center');
                            if (headerSection) {
                                headerSection.insertAdjacentElement('afterend', messageDiv);
                            }

                            // Scroll to top to show message
                            window.scrollTo(0, 0);
                        })
                        .catch(error => {
                            // Re-enable the button and update state based on validation
                            testConnectionBtn.classList.remove('test-button-loading');
                            testConnectionBtn.innerHTML = '<i class="bi bi-shield-check me-2"></i>{{ __("installer.test_connection") }}';
                            updateTestConnectionButtonState();

                            // Remove existing alerts before showing new one
                            removeExistingAlerts();

                            // Create error message
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'alert alert-danger alert-dismissible mb-4';
                            errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>{{ __("installer.connection_error") }}</strong> {{ __("installer.could_not_test_database") }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';

                            // Database test failed, keep Next button disabled
                            isDatabaseTestSuccessful = false;
                            updateNextButtonState();

                            // Add message at the top of the card body
                            const cardBody = document.querySelector('.database-config-card .card-body');
                            const headerSection = cardBody.querySelector('.text-center');
                            if (headerSection) {
                                headerSection.insertAdjacentElement('afterend', errorDiv);
                            }

                            // Scroll to top to show error
                            window.scrollTo(0, 0);
                        });
                });
            }

            // Function to validate required email fields
            function validateEmailFields() {
                let isValid = true;
                let missingFields = [];

                // Check required email fields
                const mailHost = document.querySelector('input[name="mail_host"]');
                const mailPort = document.querySelector('input[name="mail_port"]');
                const mailUsername = document.querySelector('input[name="mail_username"]');

                if (!mailHost || !mailHost.value.trim()) {
                    isValid = false;
                    missingFields.push('{{ __("installer.mail_host") }}');
                }
                if (!mailPort || !mailPort.value.trim()) {
                    isValid = false;
                    missingFields.push('{{ __("installer.mail_port") }}');
                }
                if (!mailUsername || !mailUsername.value.trim()) {
                    isValid = false;
                    missingFields.push('{{ __("installer.mail_username") }}');
                }

                return { isValid, missingFields };
            }

            // Function to update email test status indicator
            function updateEmailTestStatusIndicator(success) {
                const statusIndicator = document.getElementById('email_test_status');
                if (!statusIndicator) return;

                if (success) {
                    statusIndicator.className = 'mt-3';
                    statusIndicator.innerHTML = `
                                            <div class="alert alert-success d-flex align-items-center mb-0">
                                                <i class="bi bi-check-circle-fill me-2"></i>
                                                <span>{{ __('installer.email_test_success') }}</span>
                                            </div>
                                        `;
                } else {
                    statusIndicator.className = 'mt-3 d-none';
                    statusIndicator.innerHTML = '';
                }
            }

            // Function to update test email button state
            function updateTestEmailButtonState() {
                const testEmailBtn = document.getElementById('test_email_button');
                if (!testEmailBtn) return;

                const validation = validateEmailFields();

                if (validation.isValid) {
                    testEmailBtn.disabled = false;
                    testEmailBtn.classList.remove('btn-secondary');
                    testEmailBtn.classList.add('btn-info');
                    testEmailBtn.title = '';
                } else {
                    testEmailBtn.disabled = true;
                    testEmailBtn.classList.remove('btn-info');
                    testEmailBtn.classList.add('btn-secondary');
                    testEmailBtn.title = '{{ __("installer.please_fill_required_fields") }}: ' + validation.missingFields.join(', ');
                }
            }

            // Add event listeners to required email fields to update button state
            const requiredEmailFields = [
                'input[name="mail_host"]',
                'input[name="mail_port"]',
                'input[name="mail_username"]'
            ];

            requiredEmailFields.forEach(selector => {
                const field = document.querySelector(selector);
                if (field) {
                    field.addEventListener('input', function () {
                        // Reset email test status when any email field changes
                        isEmailTestSuccessful = false;
                        updateEmailTestStatusIndicator(false);
                        updateNextButtonState(); // Update Next button state
                        updateTestEmailButtonState();
                    });
                    field.addEventListener('blur', updateTestEmailButtonState);
                }
            });

            // Also add listeners to other email fields that might affect the configuration
            const otherEmailFields = [
                'input[name="mail_password"]',
                'select[name="mail_encryption"]',
                'input[name="mail_from_address"]',
                'select[name="mail_mailer"]'
            ];

            otherEmailFields.forEach(selector => {
                const field = document.querySelector(selector);
                if (field) {
                    field.addEventListener('input', function () {
                        // Reset email test status when any email field changes
                        isEmailTestSuccessful = false;
                        updateEmailTestStatusIndicator(false);
                        updateNextButtonState(); // Update Next button state
                    });
                    field.addEventListener('change', function () {
                        // Reset email test status when any email field changes
                        isEmailTestSuccessful = false;
                        updateEmailTestStatusIndicator(false);
                        updateNextButtonState(); // Update Next button state
                    });
                }
            });

            // Initialize email button state
            updateTestEmailButtonState();

            // Add test email functionality
            testEmailBtn = document.getElementById('test_email_button');
            if (testEmailBtn) {
                testEmailBtn.addEventListener('click', function () {
                    // Validate fields before proceeding
                    const validation = validateEmailFields();
                    if (!validation.isValid) {
                        // Show validation error
                        const validationAlert = document.createElement('div');
                        validationAlert.className = 'alert alert-warning mb-4';
                        validationAlert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>{{ __("installer.validation_error") }}</strong> {{ __("installer.please_fill_required_fields") }}: ' + validation.missingFields.join(', ');

                        // Add message at the top of the card body
                        const cardBody = document.querySelector('.database-config-card .card-body');
                        const headerSection = cardBody.querySelector('.text-center');
                        if (headerSection) {
                            headerSection.insertAdjacentElement('afterend', validationAlert);
                        }

                        // Remove alert after 5 seconds
                        setTimeout(() => {
                            validationAlert.remove();
                        }, 5000);

                        // Scroll to top to show validation error
                        window.scrollTo(0, 0);
                        return;
                    }
                    // Clear any previous email test messages
                    const previousEmailMessages = document.querySelectorAll('.alert');
                    previousEmailMessages.forEach(msg => {
                        // Only remove messages that are above the email config card
                        const emailCard = document.getElementById('email-config-card');
                        if (emailCard && msg.nextElementSibling === emailCard) {
                            msg.remove();
                        }
                    });

                    // Disable test button
                    testEmailBtn.disabled = true;
                    testEmailBtn.innerHTML = '<i class="bi bi-envelope-check me-2"></i>{{ __("installer.testing") }}';

                    // Collect form data with null checks
                    const formData = new FormData();
                    formData.append('_token', document.querySelector('input[name="_token"]').value);

                    // Get form elements with safety checks
                    const mailMailer = document.querySelector('select[name="mail_mailer"]') || document.querySelector('input[name="mail_mailer"]');
                    const mailHost = document.querySelector('input[name="mail_host"]');
                    const mailPort = document.querySelector('input[name="mail_port"]');
                    const mailUsername = document.querySelector('input[name="mail_username"]');
                    const mailPassword = document.querySelector('input[name="mail_password"]');
                    const mailEncryption = document.querySelector('select[name="mail_encryption"]');
                    const mailFromAddress = document.querySelector('input[name="mail_from_address"]');

                    // Add values only if elements exist
                    if (mailMailer) formData.append('mail_mailer', mailMailer.value || 'smtp');
                    if (mailHost) formData.append('mail_host', mailHost.value || 'smtp.gmail.com');
                    if (mailPort) formData.append('mail_port', mailPort.value || '587');
                    if (mailUsername) formData.append('mail_username', mailUsername.value || '');
                    if (mailPassword) formData.append('mail_password', mailPassword.value || '');
                    if (mailEncryption) formData.append('mail_encryption', mailEncryption.value || 'tls');
                    if (mailFromAddress) formData.append('mail_from_address', mailFromAddress.value || 'noreply@example.com');

                    fetch('{{ route("test_email_connection") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            // Enable the button again
                            testEmailBtn.disabled = false;
                            testEmailBtn.innerHTML = '<i class="bi bi-envelope-check me-2"></i>{{ __("installer.test_email") }}';

                            // Remove existing alerts before showing new one
                            removeExistingAlerts();

                            // Create message div
                            const messageDiv = document.createElement('div');
                            messageDiv.className = data.success ? 'alert alert-success alert-dismissible mb-4' : 'alert alert-danger alert-dismissible mb-4';

                            if (data.success) {
                                // Update email test status
                                isEmailTestSuccessful = true;
                                updateEmailTestStatusIndicator(true);
                                updateNextButtonState(); // Update Next button state
                                messageDiv.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i><strong>{{ __("installer.email_test_success") }}</strong> ' + data.message + '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                            } else {
                                // Update email test status
                                isEmailTestSuccessful = false;
                                updateEmailTestStatusIndicator(false);
                                updateNextButtonState(); // Update Next button state
                                // Create user-friendly error message with expandable technical details
                                let errorContent = '<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>{{ __("installer.error") }}</strong> ' + data.message;

                                if (data.technical_details) {
                                    const detailsId = 'email-technical-details-' + Date.now();
                                    errorContent += '<div class="mt-3">';
                                    errorContent += '<button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#' + detailsId + '" aria-expanded="false" aria-controls="' + detailsId + '">';
                                    errorContent += '<i class="bi bi-info-circle me-1"></i>{{ __("installer.database_error_details") }}';
                                    errorContent += '</button>';
                                    errorContent += '<div class="collapse mt-2" id="' + detailsId + '">';
                                    errorContent += '<div class="alert alert-secondary small technical-details-content">';
                                    errorContent += '<code>' + data.technical_details + '</code>';
                                    errorContent += '</div>';
                                    errorContent += '</div>';
                                    errorContent += '</div>';
                                }

                                errorContent += '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
                                messageDiv.innerHTML = errorContent;
                            }

                            // Add message at the top of the card body
                            const cardBody = document.querySelector('.database-config-card .card-body');
                            const headerSection = cardBody.querySelector('.text-center');
                            if (headerSection) {
                                headerSection.insertAdjacentElement('afterend', messageDiv);
                            }

                            // Scroll to top to show message
                            window.scrollTo(0, 0);
                        }).catch(error => {
                            // Update email test status
                            isEmailTestSuccessful = false;
                            updateEmailTestStatusIndicator(false);
                            updateNextButtonState(); // Update Next button state

                            // Enable the button again
                            testEmailBtn.disabled = false;
                            testEmailBtn.innerHTML = '<i class="bi bi-envelope-check me-2"></i>{{ __("installer.test_email") }}';

                            // Remove existing alerts before showing new one
                            removeExistingAlerts();

                            // Create error message
                            const errorDiv = document.createElement('div');
                            errorDiv.className = 'alert alert-danger alert-dismissible mb-4';
                            errorDiv.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>{{ __("installer.email_test_error") }}</strong> {{ __("installer.could_not_test_email") }}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';

                            // Add message at the top of the card body
                            const cardBody = document.querySelector('.database-config-card .card-body');
                            const headerSection = cardBody.querySelector('.text-center');
                            if (headerSection) {
                                headerSection.insertAdjacentElement('afterend', errorDiv);
                            }

                            // Scroll to top to show error
                            window.scrollTo(0, 0);
                        });
                });
            }

            // Function to clear previous errors and tab indicators
            function clearPreviousErrors() {
                const previousErrors = document.querySelectorAll('.alert.alert-danger, .validation-error-message');
                previousErrors.forEach(error => error.remove());

                // Remove error indicators from all tabs
                document.querySelectorAll('.config-tabs .nav-link').forEach(tab => {
                    tab.classList.remove('has-error');
                });
            }

            // Function to clear tab error indicators only (when user is typing)
            function clearTabErrorIndicators() {
                document.querySelectorAll('.config-tabs .nav-link').forEach(tab => {
                    tab.classList.remove('has-error');
                });
            }

            // Function to validate all form fields across all tabs
            function validateAllFormFields(showTabErrors = false) {
                const validation = {
                    isValid: true,
                    errors: {
                        environment: [],
                        database: [],
                        email: []
                    }
                };

                // Environment tab validation
                const appUrl = document.querySelector('input[name="app_url"]');
                const appTimezone = document.querySelector('select[name="app_timezone"]');
                const appLocale = document.querySelector('select[name="app_locale"]');

                if (!appUrl || !appUrl.value.trim()) {
                    validation.isValid = false;
                    validation.errors.environment.push('{{ __("installer.app_url") }}');
                }
                if (!appTimezone || !appTimezone.value.trim()) {
                    validation.isValid = false;
                    validation.errors.environment.push('{{ __("installer.app_timezone") }}');
                }
                if (!appLocale || !appLocale.value.trim()) {
                    validation.isValid = false;
                    validation.errors.environment.push('{{ __("installer.app_locale") }}');
                }

                // Database tab validation
                const databaseName = document.querySelector('input[name="database_name"]');
                if (!databaseName || !databaseName.value.trim()) {
                    validation.isValid = false;
                    validation.errors.database.push('{{ __("installer.database_name") }}');
                }

                // MySQL specific validation
                if (dbConnectionSelect.value === 'mysql') {
                    const hostname = document.querySelector('input[name="database_hostname"]');
                    const port = document.querySelector('input[name="database_port"]');
                    const username = document.querySelector('input[name="database_username"]');

                    if (!hostname || !hostname.value.trim()) {
                        validation.isValid = false;
                        validation.errors.database.push('{{ __("installer.database_host") }}');
                    }
                    if (!port || !port.value.trim()) {
                        validation.isValid = false;
                        validation.errors.database.push('{{ __("installer.database_port") }}');
                    }
                    if (!username || !username.value.trim()) {
                        validation.isValid = false;
                        validation.errors.database.push('{{ __("installer.database_username") }}');
                    }
                }

                // Check if database test was successful
                if (!isDatabaseTestSuccessful) {
                    validation.isValid = false;
                    validation.errors.database.push('{{ __("installer.database_test_required") }}');
                }

                // Email tab validation
                const mailHost = document.querySelector('input[name="mail_host"]');
                const mailPort = document.querySelector('input[name="mail_port"]');
                const mailUsername = document.querySelector('input[name="mail_username"]');

                if (!mailHost || !mailHost.value.trim()) {
                    validation.isValid = false;
                    validation.errors.email.push('{{ __("installer.mail_host") }}');
                }
                if (!mailPort || !mailPort.value.trim()) {
                    validation.isValid = false;
                    validation.errors.email.push('{{ __("installer.mail_port") }}');
                }
                if (!mailUsername || !mailUsername.value.trim()) {
                    validation.isValid = false;
                    validation.errors.email.push('{{ __("installer.mail_username") }}');
                }

                // Check if email test was successful
                if (!isEmailTestSuccessful) {
                    validation.isValid = false;
                    validation.errors.email.push('{{ __("installer.email_test_required") }}');
                }

                return validation;
            }

            // Function to switch to a specific tab
            function switchToTab(tabId) {
                // Remove active from all tabs
                document.querySelectorAll('.config-tabs .nav-link').forEach(tab => {
                    tab.classList.remove('active');
                });
                document.querySelectorAll('.tab-pane').forEach(pane => {
                    pane.classList.remove('show', 'active');
                });

                // Activate target tab
                const targetTab = document.getElementById(tabId);
                const targetPane = document.querySelector(targetTab.getAttribute('data-bs-target'));

                if (targetTab && targetPane) {
                    targetTab.classList.add('active');
                    targetPane.classList.add('show', 'active');
                }
            }

            // Function to show comprehensive validation error
            function showValidationError(validation, showTabErrors = false) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger validation-error-message mb-4';

                let errorContent = '<i class="bi bi-exclamation-triangle-fill me-2"></i>';
                errorContent += '<strong>{{ __("installer.validation_error") }}</strong><br>';
                errorContent += '{{ __("installer.form_validation_failed") }} {{ __("installer.check_all_tabs") }}<br><br>';

                // Add tab-specific errors with navigation buttons
                if (validation.errors.environment.length > 0) {
                    errorContent += '<div class="mb-3">';
                    errorContent += '<strong><i class="bi bi-gear me-1"></i>Environment:</strong> ';
                    validation.errors.environment.forEach(field => {
                        errorContent += `<span class="missing-field">${field}</span> `;
                    });
                    errorContent += '</div>';

                    // Mark environment tab as having errors only if requested
                    if (showTabErrors) {
                        document.getElementById('environment-tab').classList.add('has-error');
                    }
                }

                if (validation.errors.database.length > 0) {
                    errorContent += '<div class="mb-3">';
                    errorContent += '<strong><i class="bi bi-database me-1"></i>Database:</strong> ';
                    validation.errors.database.forEach(field => {
                        errorContent += `<span class="missing-field">${field}</span> `;
                    });
                    errorContent += '</div>';

                    // Mark database tab as having errors only if requested
                    if (showTabErrors) {
                        document.getElementById('database-tab').classList.add('has-error');
                    }
                }

                if (validation.errors.email.length > 0) {
                    errorContent += '<div class="mb-3">';
                    errorContent += '<strong><i class="bi bi-envelope me-1"></i>Email:</strong> ';
                    validation.errors.email.forEach(field => {
                        errorContent += `<span class="missing-field">${field}</span> `;
                    });
                    errorContent += '</div>';

                    // Mark email tab as having errors only if requested
                    if (showTabErrors) {
                        document.getElementById('email-tab').classList.add('has-error');
                    }
                }

                errorDiv.innerHTML = errorContent;

                // Add error message at the top of the card body
                const cardBody = document.querySelector('.database-config-card .card-body');
                const headerSection = cardBody.querySelector('.text-center');
                if (headerSection) {
                    headerSection.insertAdjacentElement('afterend', errorDiv);
                }

                // Scroll to top to show error
                window.scrollTo(0, 0);
            }

            // Function to collect all form data from all tabs
            function collectAllFormData() {
                const allFormData = {};

                // Get all form inputs from the main form
                const formInputs = document.querySelectorAll('#database-form input, #database-form select, #database-form textarea');

                formInputs.forEach(input => {
                    if (input.name) {
                        // Handle different input types
                        if (input.type === 'checkbox') {
                            allFormData[input.name] = input.checked;
                        } else if (input.type === 'radio') {
                            if (input.checked) {
                                allFormData[input.name] = input.value;
                            }
                        } else {
                            allFormData[input.name] = input.value || '';
                        }
                    }
                });

                // Ensure all required fields have values with sensible defaults
                const requiredDefaults = {
                    'environment': 'production',
                    'app_debug': 'false',
                    'app_log_level': 'debug',
                    'app_url': window.location.origin,
                    'app_timezone': 'UTC',
                    'app_locale': 'id',
                    'database_connection': 'mysql',
                    'database_name': '',
                    'database_hostname': '127.0.0.1',
                    'database_port': '3306',
                    'database_username': '',
                    'database_password': '',
                    'mail_mailer': 'smtp',
                    'mail_host': 'smtp.gmail.com',
                    'mail_port': '587',
                    'mail_username': '',
                    'mail_password': '',
                    'mail_encryption': 'tls',
                    'mail_from_address': 'noreply@example.com'
                };

                // Apply defaults for any missing fields
                Object.keys(requiredDefaults).forEach(key => {
                    if (!allFormData.hasOwnProperty(key) || allFormData[key] === '') {
                        allFormData[key] = requiredDefaults[key];
                    }
                });

                return allFormData;
            }

            // Function to prepare the form for submission
            function prepareFormForSubmission() {
                // Get all form data
                const allFormData = collectAllFormData();

                // Handle SQLite specific adjustments
                if (allFormData.database_connection === 'sqlite') {
                    // Make sure SQLite databases have .sqlite extension
                    if (allFormData.database_name && !allFormData.database_name.toLowerCase().endsWith('.sqlite')) {
                        allFormData.database_name = allFormData.database_name + '.sqlite';
                    }

                    // Set MySQL fields to default values for SQLite
                    allFormData.database_hostname = '127.0.0.1';
                    allFormData.database_port = '3306';
                    allFormData.database_username = 'sqlite';
                }

                // Update all form inputs with the collected data
                Object.keys(allFormData).forEach(key => {
                    const input = document.querySelector(`#database-form [name="${key}"]`);
                    if (input) {
                        if (input.type === 'checkbox') {
                            input.checked = allFormData[key];
                        } else if (input.type === 'radio') {
                            if (input.value === allFormData[key]) {
                                input.checked = true;
                            }
                        } else {
                            input.value = allFormData[key];
                        }
                    }
                });

                // Sync to footer hidden inputs
                syncFormDataToFooter();

                return allFormData;
            }

            form.addEventListener('submit', function (e) {
                // Prevent default form submission immediately
                e.preventDefault();

                // Clear any previous error messages
                clearPreviousErrors();

                // Validate all form fields across all tabs
                const validation = validateAllFormFields(true); // Show tab errors during form submission

                if (!validation.isValid) {
                    // Show comprehensive validation error with tab indicators
                    showValidationError(validation, true);
                    return;
                }

                // Additional safety check: ensure both tests have been completed successfully
                if (!isDatabaseTestSuccessful || !isEmailTestSuccessful) {
                    console.error('Form submission blocked: Tests not completed', {
                        isDatabaseTestSuccessful,
                        isEmailTestSuccessful
                    });

                    // Create error message
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-danger alert-dismissible mb-4';

                    let errorMessage = '';
                    if (!isDatabaseTestSuccessful && !isEmailTestSuccessful) {
                        errorMessage = '{{ __("installer.please_test_database_and_email_first") }}';
                    } else if (!isDatabaseTestSuccessful) {
                        errorMessage = '{{ __("installer.please_test_database_connection_first") }}';
                    } else if (!isEmailTestSuccessful) {
                        errorMessage = '{{ __("installer.please_test_email_connection_first") }}';
                    }

                    errorDiv.innerHTML = `<i class="bi bi-exclamation-triangle-fill me-2"></i><strong>{{ __("installer.validation_error") }}</strong> ${errorMessage}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>`;

                    // Add message at the top of the card body
                    const cardBody = document.querySelector('.database-config-card .card-body');
                    const headerSection = cardBody.querySelector('.text-center');
                    if (headerSection) {
                        headerSection.insertAdjacentElement('afterend', errorDiv);
                    }

                    // Scroll to top to show error
                    window.scrollTo(0, 0);
                    return;
                }

                // Get submit button and disable it
                const nextButton = document.getElementById('next_button');
                if (nextButton) {
                    nextButton.disabled = true;
                    nextButton.innerHTML = '{{ __("installer.processing") }}';
                }

                // Prepare the form and collect all data before submission
                const allFormData = prepareFormForSubmission();

                // Create new FormData with all collected data
                const formData = new FormData();

                // Add CSRF token
                const csrfToken = document.querySelector('input[name="_token"]');
                if (csrfToken) {
                    formData.append('_token', csrfToken.value);
                }

                // Add all form data to FormData object
                Object.keys(allFormData).forEach(key => {
                    formData.append(key, allFormData[key]);
                });

                // Debug log to verify all required fields are present
                console.log('Form data being submitted:', Object.fromEntries(formData.entries()));

                // Submit via fetch API
                fetch('{{ route('saveWizard') }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(response => {
                        // Store the response status for later use
                        const responseStatus = response.status;
                        return response.text().then(text => {
                            return { text, status: responseStatus };
                        });
                    })
                    .then(({ text: data, status }) => {
                        // Enable the submit button again
                        if (nextButton) {
                            nextButton.disabled = false;
                            nextButton.innerHTML = '{{ __("installer.next") }}';
                        }

                        try {
                            const jsonData = JSON.parse(data);

                            // Handle error responses
                            if (!jsonData.success && status >= 400) {
                                // Display error messages
                                if (jsonData.errors) {
                                    const errorDiv = document.createElement('div');
                                    errorDiv.className = 'alert alert-danger';
                                    errorDiv.innerHTML = '<strong>{{ __("installer.database_connection_error") }}</strong>';

                                    // Process each error
                                    Object.keys(jsonData.errors).forEach(key => {
                                        const errorMessages = jsonData.errors[key];

                                        if (Array.isArray(errorMessages)) {
                                            // Handle simple array of error messages
                                            errorMessages.forEach(message => {
                                                errorDiv.innerHTML += `<p class="mb-2">${message}</p>`;
                                            });
                                        } else if (typeof errorMessages === 'object' && errorMessages.message) {
                                            // Handle structured error with technical details
                                            errorDiv.innerHTML += `<p class="mb-2">${errorMessages.message}</p>`;

                                            if (errorMessages.technical_details) {
                                                const detailsId = 'form-technical-details-' + Date.now();
                                                errorDiv.innerHTML += '<div class="mt-3">';
                                                errorDiv.innerHTML += '<button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#' + detailsId + '" aria-expanded="false" aria-controls="' + detailsId + '">';
                                                errorDiv.innerHTML += '<i class="bi bi-info-circle me-1"></i>{{ __("installer.database_error_details") }}';
                                                errorDiv.innerHTML += '</button>';
                                                errorDiv.innerHTML += '<div class="collapse mt-2" id="' + detailsId + '">';
                                                errorDiv.innerHTML += '<div class="alert alert-secondary small technical-details-content">';
                                                errorDiv.innerHTML += '<code>' + errorMessages.technical_details + '</code>';
                                                errorDiv.innerHTML += '</div>';
                                                errorDiv.innerHTML += '</div>';
                                                errorDiv.innerHTML += '</div>';
                                            }
                                        }
                                    });

                                    errorDiv.innerHTML += '<p class="mt-3 mb-2">{{ __("installer.please_make_sure") }}:</p><ul>' +
                                        '<li>{{ __("installer.database_server_running") }}</li>' +
                                        '<li>{{ __("installer.database_exists") }}</li>' +
                                        '<li>{{ __("installer.credentials_correct") }}</li>' +
                                        '<li>{{ __("installer.user_has_permissions") }}</li></ul>';

                                    // Add error div at the top of the form content
                                    const formCard = form.querySelector('.card-body');
                                    if (formCard) {
                                        formCard.insertAdjacentElement('afterbegin', errorDiv);
                                    } else {
                                        form.insertAdjacentElement('afterbegin', errorDiv);
                                    }

                                    // Scroll to top to show error
                                    window.scrollTo(0, 0);
                                    return;
                                }
                            }

                            // Handle success redirect
                            if (jsonData.redirect) {
                                window.location.href = jsonData.redirect;
                            } else if (jsonData.success) {
                                window.location.href = '{{ route('profil_perusahaan') }}';
                            }
                        } catch (e) {
                            // If response is not JSON, check if it contains a redirect URL
                            if (data.includes('window.location') || data.includes('redirect')) {
                                // Try to extract URL
                                const match = data.match(/window\.location\.href\s*=\s*['"]([^'"]+)['"]/);
                                if (match) {
                                    window.location.href = match[1];
                                } else if (status < 400) { // Only redirect on success status
                                    window.location.href = '{{ route('profil_perusahaan') }}';
                                }
                            } else {
                                // Probably HTML response, replace current page
                                document.open();
                                document.write(data);
                                document.close();
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);

                        // Re-enable submit button
                        if (nextButton) {
                            nextButton.disabled = false;
                            nextButton.innerHTML = '{{ __("installer.next") }}';
                        }

                        // Create error message
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'alert alert-danger';
                        errorDiv.innerHTML = '<strong>{{ __("installer.connection_error") }}</strong><p>{{ __("installer.server_communication_error") }}</p>';

                        // Add error message at top of form content
                        const formCard = form.querySelector('.card-body');
                        if (formCard) {
                            formCard.insertAdjacentElement('afterbegin', errorDiv);
                        } else {
                            form.insertAdjacentElement('afterbegin', errorDiv);
                        }

                        window.scrollTo(0, 0);
                    });
            });

            // Email Configuration Toggle Logic
            const mailDriverSelect = document.querySelector('select[name="mail_mailer"]');
            const mailRequiredFields = [
                'mail_host_container',
                'mail_port_container',
                'mail_username_container',
                'mail_password_container',
                'mail_encryption_container'
            ];

            function toggleMailFields() {
                const selectedDriver = mailDriverSelect.value;

                if (selectedDriver === 'log' || selectedDriver === 'sendmail') {
                    // Hide SMTP fields for log and sendmail drivers
                    mailRequiredFields.forEach(fieldId => {
                        const container = document.getElementById(fieldId);
                        if (container) {
                            container.style.display = 'none';
                            // Remove required attribute
                            const input = container.querySelector('input, select');
                            if (input && input.hasAttribute('required')) {
                                input.removeAttribute('required');
                            }
                        }
                    });
                } else {
                    // Show SMTP fields for other drivers
                    mailRequiredFields.forEach(fieldId => {
                        const container = document.getElementById(fieldId);
                        if (container) {
                            container.style.display = 'block';
                            // Add required attribute for critical fields
                            const input = container.querySelector('input, select');
                            if (input && (fieldId === 'mail_host_container' || fieldId === 'mail_port_container')) {
                                input.setAttribute('required', 'required');
                            }
                        }
                    });
                }
            }

            // Add event listener to mail driver dropdown
            if (mailDriverSelect) {
                mailDriverSelect.addEventListener('change', toggleMailFields);
                // Run toggle on page load
                toggleMailFields();
            }

            // Function to set app_debug based on environment
            function setAppDebugBasedOnEnvironment() {
                const environmentSelect = document.querySelector('select[name="environment"]');
                const appDebugSelect = document.querySelector('select[name="app_debug"]');

                if (environmentSelect && appDebugSelect) {
                    const environment = environmentSelect.value;

                    // Set app_debug based on environment
                    if (environment === 'production') {
                        appDebugSelect.value = 'false';
                    } else if (environment === 'local') {
                        appDebugSelect.value = 'true';
                    }
                }
            }

            // Add event listener to environment dropdown
            const environmentSelect = document.querySelector('select[name="environment"]');
            if (environmentSelect) {
                environmentSelect.addEventListener('change', setAppDebugBasedOnEnvironment);
                // Run on page load to set initial state
                setAppDebugBasedOnEnvironment();
            }
        });

        // Global function to switch tabs - accessible from validation error buttons
        function switchToTab(tabId) {
            // Remove active classes from all tabs
            const allTabs = document.querySelectorAll('.nav-link');
            const allTabPanes = document.querySelectorAll('.tab-pane');

            allTabs.forEach(tab => tab.classList.remove('active'));
            allTabPanes.forEach(pane => pane.classList.remove('active', 'show'));

            // Activate the target tab
            const targetTab = document.getElementById(tabId);
            const targetTabButton = document.querySelector(`[data-bs-target="#${tabId}"]`);

            if (targetTab && targetTabButton) {
                targetTabButton.classList.add('active');
                targetTab.classList.add('active', 'show');

                // Scroll to the target tab
                targetTab.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }

        // Enhanced sync form data to footer hidden inputs
        function syncFormDataToFooter() {
            // Get all form data using the comprehensive collection function
            const allFormData = collectAllFormData();

            // Sync each field to footer hidden inputs
            Object.keys(allFormData).forEach(key => {
                const footerInput = document.getElementById('footer-' + key);
                if (footerInput) {
                    footerInput.value = allFormData[key];
                }
            });

            // Also create any missing footer inputs dynamically
            const footerForm = document.querySelector('.card-footer form');
            if (footerForm) {
                Object.keys(allFormData).forEach(key => {
                    let footerInput = document.getElementById('footer-' + key);
                    if (!footerInput) {
                        // Create missing hidden input
                        footerInput = document.createElement('input');
                        footerInput.type = 'hidden';
                        footerInput.name = key;
                        footerInput.id = 'footer-' + key;
                        footerInput.value = allFormData[key];
                        footerForm.appendChild(footerInput);
                    } else {
                        footerInput.value = allFormData[key];
                    }
                });
            }
        }

        // Make collectAllFormData available globally for sync function
        function collectAllFormData() {
            const allFormData = {};

            // Get all form inputs from the main form
            const formInputs = document.querySelectorAll('#database-form input, #database-form select, #database-form textarea');

            formInputs.forEach(input => {
                if (input.name) {
                    // Handle different input types
                    if (input.type === 'checkbox') {
                        allFormData[input.name] = input.checked;
                    } else if (input.type === 'radio') {
                        if (input.checked) {
                            allFormData[input.name] = input.value;
                        }
                    } else {
                        allFormData[input.name] = input.value || '';
                    }
                }
            });

            // Ensure all required fields have values with sensible defaults
            const requiredDefaults = {
                'environment': 'production',
                'app_debug': 'false',
                'app_log_level': 'debug',
                'app_url': window.location.origin,
                'app_timezone': 'UTC',
                'app_locale': 'id',
                'database_connection': 'mysql',
                'database_name': '',
                'database_hostname': '127.0.0.1',
                'database_port': '3306',
                'database_username': '',
                'database_password': '',
                'mail_mailer': 'smtp',
                'mail_host': 'smtp.gmail.com',
                'mail_port': '587',
                'mail_username': '',
                'mail_password': '',
                'mail_encryption': 'tls',
                'mail_from_address': 'noreply@example.com'
            };

            // Apply defaults for any missing fields
            Object.keys(requiredDefaults).forEach(key => {
                if (!allFormData.hasOwnProperty(key) || allFormData[key] === '') {
                    allFormData[key] = requiredDefaults[key];
                }
            });

            return allFormData;
        }

        // Enhanced sync on form change with more comprehensive event handling
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('database-form');
            if (form) {
                // Add multiple event listeners for comprehensive sync
                form.addEventListener('input', syncFormDataToFooter);
                form.addEventListener('change', syncFormDataToFooter);
                form.addEventListener('keyup', syncFormDataToFooter);
                form.addEventListener('blur', syncFormDataToFooter, true); // Use capture phase

                // Sync when tabs are switched
                document.querySelectorAll('.config-tabs .nav-link').forEach(tabButton => {
                    tabButton.addEventListener('click', function () {
                        // Delay sync to ensure tab content is loaded
                        setTimeout(syncFormDataToFooter, 100);
                    });
                });

                // Initial sync after a short delay to ensure all form elements are loaded
                setTimeout(syncFormDataToFooter, 500);

                // Periodic sync every 2 seconds as backup
                setInterval(syncFormDataToFooter, 2000);
            }
        });
    </script>

    <style>
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

        /* Next button disabled state styling */
        .card-footer .btn-primary:disabled,
        .card-footer .btn-secondary:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none !important;
            box-shadow: none !important;
        }

        .card-footer .btn-primary:disabled:hover,
        .card-footer .btn-secondary:disabled:hover {
            transform: none !important;
            box-shadow: none !important;
        }

        /* Test button animation when testing */
        .test-button-loading .bi-hourglass-split {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
@endsection