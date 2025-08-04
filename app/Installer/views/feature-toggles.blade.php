@section('title', __('installer.features_title'))
@extends('InstallerEragViews::app-layout')
@section('content')
    <section class="mt-4 installer-content bg-radial-gradient">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <form action="{{ route('saveFeatureToggles') }}" method="post">
                        @csrf
                        <div class="card mb-4 shadow-lg border-0 features-config-card">
                            <div class="card-body py-5">
                                <!-- Header Section with Icon and Title -->
                                <div class="text-center mb-5">
                                    <div class="mb-4">
                                        <i class="bi bi-toggles display-1" style="color: var(--primary-color);"></i>
                                    </div>
                                    <h1 class="display-5 mb-3 features-title" style="color: var(--primary-color);">
                                        {{ __('installer.features_title') }}
                                    </h1>
                                    <p class="lead mb-0 text-muted features-subtitle">
                                        {{ __('installer.features_subtitle') }}
                                    </p>
                                </div>

                                <!-- Error Messages -->
                                @if($errors->any())
                                    <div class="alert alert-danger mb-4">
                                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                        <ul class="mb-0">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <!-- Feature Toggles Section -->
                                <div class="mb-4">
                                    <h4 class="mb-4 text-center" style="color: var(--primary-color);">
                                        <i class="bi bi-sliders me-2"></i>{{ __('installer.configure_features') }}
                                    </h4>

                                    <div class="row">
                                        @foreach ($features as $feature)
                                            <div class="col-md-6 mb-4">
                                                <div class="feature-toggle-card p-3 rounded">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input feature-switch" type="checkbox"
                                                            role="switch" id="feature_{{ $feature['key'] }}"
                                                            name="features[{{ $feature['key'] }}]" value="1" {{ $feature['status_aktif'] ? 'checked' : '' }}>
                                                        <label class="form-check-label fw-medium"
                                                            for="feature_{{ $feature['key'] }}">
                                                            {{ $feature['label'] }}
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer with Action Buttons -->
                            <div class="card-footer bg-light border-top p-4">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('user_roles_list') }}" class="btn btn-outline-primary btn-lg px-5">
                                        {{ __('installer.back') }}
                                    </a>
                                    <button type="submit" class="btn btn-primary btn-lg px-5 features-btn">
                                        {{ __('installer.next') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* Features page specific styles - matching unified design */
        .features-config-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9ff 100%);
            border-radius: 20px !important;
            overflow: hidden;
            animation: slideInUp 0.8s ease-out;
        }

        .features-title {
            font-weight: 700;
            animation: fadeInDown 1s ease-out 0.3s both;
        }

        .features-subtitle {
            animation: fadeInUp 1s ease-out 0.5s both;
        }

        .features-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, #4338ca 100%);
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .features-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
        }

        /* Feature toggle card styling */
        .feature-toggle-card {
            background: rgba(99, 102, 241, 0.05);
            border: 1px solid rgba(99, 102, 241, 0.1);
            transition: all 0.3s ease;
        }

        .feature-toggle-card:hover {
            background: rgba(99, 102, 241, 0.08);
            border-color: rgba(99, 102, 241, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        /* Enhanced form switch styling */
        .feature-switch {
            width: 3rem !important;
            height: 1.5rem !important;
            cursor: pointer;
        }

        .feature-switch:checked {
            background-color: var(--primary-color) !important;
            border-color: var(--primary-color) !important;
        }

        .form-check-label {
            cursor: pointer;
            margin-left: 0.5rem;
        }

        /* Alert styling */
        .alert {
            border-radius: 10px;
            border: none;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #842029;
        }

        /* Button styling */
        .btn-outline-primary {
            border-radius: 10px;
            border-width: 2px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.2);
        }

        /* Section headers */
        h4 {
            font-weight: 600;
            position: relative;
        }

        h4::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #4338ca 100%);
            border-radius: 2px;
        }

        /* Animations */
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
        // Form submission handling (if needed for additional validation)
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('form[action*="saveFeatureToggles"]');
            if (form) {
                // Add any additional form handling here if needed
                console.log('Feature toggles form initialized');
            }
        });
    </script>
@endsection