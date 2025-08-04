@extends('InstallerEragViews::app-layout')

@section('title', __('installer.welcome_title'))

@section('content')
    <section class="mt-4 installer-content bg-radial-gradient">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card mb-4 shadow-lg border-0 welcome-config-card">
                        <div class="card-body py-5">
                            <!-- Header Section with Logo and Title -->
                            <div class="text-center mb-5">
                                <!-- Welcome Icon -->
                                <div class="mb-4">
                                    <i class="bi bi-rocket-takeoff display-1" style="color: var(--primary-color);"></i>
                                </div>
                                <!-- Title -->
                                <h1 class="display-5 mb-3 welcome-title" style="color: var(--primary-color);">
                                    {{ __('installer.welcome_title') }}
                                </h1>
                                <p class="lead mb-0 text-muted welcome-subtitle">
                                    {{ __('installer.welcome_subtitle') }}
                                </p>
                            </div>

                            <!-- Installation Process Content -->
                            <div class="row justify-content-center">
                                <div class="col-md-10">
                                    <h4 class="mb-4 text-center" style="color: var(--primary-color);">
                                        {{ __('installer.installation_process') }}
                                    </h4>

                                    <!-- Features Grid -->
                                    <div class="row mb-4">
                                        <div class="col-md-6 mb-3">
                                            <div class="feature-item p-3 rounded bg-light">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <i class="bi bi-check-circle-fill text-success"
                                                            style="font-size: 2rem;"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">{{ __('installer.features.requirements.title') }}
                                                        </h6>
                                                        <small
                                                            class="text-muted">{{ __('installer.features.requirements.description') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="feature-item p-3 rounded bg-light">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <i class="bi bi-database-fill text-info"
                                                            style="font-size: 2rem;"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">{{ __('installer.features.database.title') }}</h6>
                                                        <small
                                                            class="text-muted">{{ __('installer.features.database.description') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="feature-item p-3 rounded bg-light">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <i class="bi bi-building text-warning" style="font-size: 2rem;"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">{{ __('installer.features.company.title') }}</h6>
                                                        <small
                                                            class="text-muted">{{ __('installer.features.company.description') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="feature-item p-3 rounded bg-light">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <i class="bi bi-person-gear text-danger"
                                                            style="font-size: 2rem;"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">{{ __('installer.features.admin.title') }}</h6>
                                                        <small
                                                            class="text-muted">{{ __('installer.features.admin.description') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="feature-item p-3 rounded bg-light">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <i class="bi bi-toggles"
                                                            style="color: var(--primary-color); font-size: 2rem;"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">{{ __('installer.features.features.title') }}</h6>
                                                        <small
                                                            class="text-muted">{{ __('installer.features.features.description') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <div class="feature-item p-3 rounded bg-light">
                                                <div class="d-flex align-items-center">
                                                    <div class="me-3">
                                                        <i class="bi bi-check-circle text-success"
                                                            style="font-size: 2rem;"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-1">{{ __('installer.features.complete.title') }}</h6>
                                                        <small
                                                            class="text-muted">{{ __('installer.features.complete.description') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Installation Info Alert -->
                                    <div class="alert alert-info mb-4" role="alert">
                                        <i class="bi bi-info-circle-fill me-2"></i>
                                        <strong>{{ __('installer.installation_time') }}:</strong>
                                        {{ __('installer.installation_time_description') }}
                                        {{ __('installer.preparation_note') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="d-grid gap-3 d-md-flex justify-content-md-center mt-4">
                                <form action="{{ route('welcome_continue') }}" method="post" class="mb-0">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-lg px-5 welcome-btn">
                                        <i class="bi bi-rocket-takeoff me-2"></i>
                                        {{ __('installer.get_started') }}
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
        /* Welcome page specific styles - matching unified design */
        .welcome-config-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9ff 100%);
            border-radius: 20px !important;
            overflow: hidden;
            animation: slideInUp 0.8s ease-out;
        }

        .welcome-title {
            font-weight: 700;
            animation: fadeInDown 1s ease-out 0.3s both;
        }

        .welcome-subtitle {
            animation: fadeInUp 1s ease-out 0.5s both;
        }

        .welcome-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, #4338ca 100%);
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .welcome-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
        }

        /* Feature items styling */
        .feature-item {
            transition: all 0.3s ease;
            border: 1px solid rgba(99, 102, 241, 0.1);
        }

        .feature-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            border-color: rgba(99, 102, 241, 0.2);
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

        /* Alert styling */
        .alert-info {
            border-radius: 10px;
            border: none;
            background: rgba(13, 202, 240, 0.1);
            color: #0c4a6e;
        }
    </style>
@endsection