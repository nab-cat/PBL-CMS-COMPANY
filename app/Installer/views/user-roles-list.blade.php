@section('title', __('installer.roles_title'))
@extends('InstallerEragViews::app-layout')
@section('content')
    <section class="mt-4 installer-content bg-radial-gradient">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="card mb-4 shadow-lg border-0 user-roles-config-card">
                        <div class="card-body py-5">
                            <!-- Header Section with Icon and Title -->
                            <div class="text-center mb-5">
                                <div class="mb-4">
                                    <i class="bi bi-people-fill display-1" style="color: var(--primary-color);"></i>
                                </div>
                                <h1 class="display-5 mb-3 user-roles-title" style="color: var(--primary-color);">
                                    {{ __('installer.user_roles_list') }}
                                </h1>
                                <p class="lead mb-0 text-muted user-roles-subtitle">
                                    {{ __('installer.user_roles_subtitle') }}
                                </p>
                            </div>

                            <!-- User Roles Content -->
                            <!-- Status Alerts -->
                            @if(session('account_exists'))
                                <div class="alert alert-warning border-0 mb-4" role="alert">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 1.5rem;"></i>
                                        <div>
                                            {!! session('account_exists') !!}
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-success mb-4">
                                    <i class="bi bi-check-circle-fill me-2"></i>
                                    {{ __('installer.super_admin_created') }}
                                    <strong>{{ $superAdmin->email }}</strong>
                                </div>
                                <div class="alert alert-info mb-4">
                                    <i class="bi bi-info-circle-fill me-2"></i>
                                    {{ __('installer.dummy_password_info') }}
                                    <strong>password123</strong>
                                </div>
                            @endif

                            <!-- Users Table Section -->
                            <div class="mb-4">
                                <h4 class="mb-3 text-center" style="color: var(--primary-color);">
                                    <i class="bi bi-table me-2"></i>{{ __('installer.user_accounts') }}
                                </h4>
                                <div class="table-responsive">
                                    <table class="table table-hover modern-table">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>{{ __('installer.name') }}</th>
                                                <th>{{ __('installer.email') }}</th>
                                                <th>{{ __('installer.role') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($users as $index => $user)
                                                <tr>
                                                    <td><span class="fw-medium">{{ $index + 1 }}</span></td>
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>
                                                        @if($user->roles->count() > 0)
                                                            @foreach($user->roles as $role)
                                                                <span
                                                                    class="badge rounded-pill bg-primary me-1">{{ $role->name }}</span>
                                                            @endforeach
                                                        @else
                                                            <span
                                                                class="badge rounded-pill bg-secondary">{{ __('installer.no_role') }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Card Footer with Action Buttons -->
                        <div class="card-footer bg-light border-top p-4">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('super_admin_config') }}" class="btn btn-outline-primary btn-lg px-5">
                                    {{ __('installer.back') }}
                                </a>
                                <a href="{{ route('feature_toggles') }}" class="btn btn-primary btn-lg px-5 user-roles-btn">
                                    {{ __('installer.continue_to_features') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <style>
        /* User roles page specific styles - matching unified design */
        .user-roles-config-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9ff 100%);
            border-radius: 20px !important;
            overflow: hidden;
            animation: slideInUp 0.8s ease-out;
        }

        .user-roles-title {
            font-weight: 700;
            animation: fadeInDown 1s ease-out 0.3s both;
        }

        .user-roles-subtitle {
            animation: fadeInUp 1s ease-out 0.5s both;
        }

        .user-roles-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, #4338ca 100%);
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .user-roles-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
        }

        /* Modern table styling */
        .modern-table {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .modern-table thead th {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: none;
            font-weight: 600;
            color: #495057;
            padding: 1rem;
        }

        .modern-table tbody tr {
            transition: all 0.3s ease;
        }

        .modern-table tbody tr:hover {
            background-color: rgba(99, 102, 241, 0.05);
            transform: translateX(2px);
        }

        .modern-table tbody td {
            padding: 1rem;
            border-color: #e9ecef;
            vertical-align: middle;
        }

        /* Alert styling */
        .alert {
            border-radius: 10px;
            padding: 1rem 1.5rem;
        }

        .alert-success {
            background: rgba(25, 135, 84, 0.1);
            color: #0f5132;
        }

        .alert-info {
            background: rgba(13, 202, 240, 0.1);
            color: #0c4a6e;
        }

        .alert-warning {
            background: rgba(255, 193, 7, 0.1);
            color: #664d03;
        }

        /* Badge styling */
        .badge {
            font-size: 0.75rem;
            padding: 0.5rem 0.75rem;
            font-weight: 500;
        }

        /* Code styling */
        code {
            font-size: 0.875rem;
            font-weight: 500;
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
@endsection