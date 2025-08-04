<!doctype html>
<html lang="{{ str_replace('_', '-', $lang ?? app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <link rel="shortcut icon" href="{{ asset('logo.png') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Google Fonts - Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200..800;1,200..800&display=swap"
        rel="stylesheet">

    @php
        $primaryColor = config('install.colors.primary');
        $secondaryColor = config('install.colors.secondary');
        $boxRgba = config('install.colors.boxRgba');
    @endphp
    <style>
        :root {
            --primary-color:
                {{ $primaryColor }}
            ;
            --secondary-color:
                {{ $secondaryColor }}
            ;
        }

        /* Apply Plus Jakarta Sans font to all installer elements */
        body,
        html {
            font-family: "Plus Jakarta Sans", sans-serif;
        }

        /* Ensure all text elements use the custom font */
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        span,
        div,
        label,
        input,
        select,
        textarea,
        button,
        .btn,
        .card,
        .form-control,
        .form-label,
        .badge {
            font-family: "Plus Jakarta Sans", sans-serif !important;
        }

        /* Apply font to installer content areas */
        .installer-content *,
        .installer-welcome * {
            font-family: "Plus Jakarta Sans", sans-serif !important;
        }

        /* Specific font weights for different elements */
        .installer-content h1,
        .installer-content h2,
        .installer-content h3 {
            font-weight: 700 !important;
        }

        .installer-content h4,
        .installer-content h5,
        .installer-content h6 {
            font-weight: 600 !important;
        }

        .installer-content p,
        .installer-content span,
        .installer-content div {
            font-weight: 400 !important;
        }

        .installer-content .btn {
            font-weight: 500 !important;
        }

        /* Smooth font rendering */
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
    <link href="{{ asset('install/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('install/style.css') }}" rel="stylesheet">
    <link href="{{ asset('install/bootstrap-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('install/progress-steps.css') }}" rel="stylesheet">
</head>

<body>
    @include('InstallerEragViews::language-switcher')
    @include('InstallerEragViews::step')
    @yield('content')

    <script src="{{ asset('install/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Language switcher initialization
            const languageDropdown = document.getElementById('languageDropdown');
            if (languageDropdown) {
                // Initialize Bootstrap dropdown
                const dropdown = new bootstrap.Dropdown(languageDropdown);

                // Handle dropdown clicks
                document.querySelectorAll('.language-switcher .dropdown-item').forEach(item => {
                    item.addEventListener('click', function (e) {
                        // Allow the link to be followed normally
                        window.location.href = this.href;
                    });
                });
            }

            // Password visibility toggle
            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const passwordInput = document.getElementById(targetId);
                    const icon = this.querySelector('i');

                    // Toggle the type attribute
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                });
            });
        });
    </script>
</body>

</html>