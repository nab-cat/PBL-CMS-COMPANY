@section('title', __('installer.company_title'))
@extends('InstallerEragViews::app-layout')
@section('content')
    <section class="mt-4 installer-content bg-radial-gradient">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-10">
                    {{-- FIX: The <form> tag now wraps the entire card content, including the footer. --}}
                        <form id="company-form" action="{{ route('saveProfilPerusahaan') }}" method="post"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="card mb-4 shadow-lg border-0 company-config-card">
                                <div class="card-body py-5">
                                    <div class="text-center mb-5">
                                        <div class="mb-4">
                                            <i class="bi bi-building display-1" style="color: var(--primary-color);"></i>
                                        </div>
                                        <h1 class="display-5 mb-3 company-title" style="color: var(--primary-color);">
                                            {{ __('installer.company_title') }}
                                        </h1>
                                        <p class="lead mb-0 text-muted company-subtitle">
                                            {{ __('installer.features.company.description') }}
                                        </p>
                                    </div>

                                    @if(session('database_error'))
                                        <div class="alert alert-danger mb-4">
                                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                            <strong>{{ __('installer.error') }}</strong>
                                            <p class="mb-2">{{ session('database_error') }}</p>
                                            <a href="{{ route('database_import') }}" class="btn btn-sm btn-danger">
                                                {{ __('installer.back') }}
                                            </a>
                                        </div>
                                    @endif

                                    <div class="row justify-content-center">
                                        <div class="col-md-8">
                                            <div class="mb-4">
                                                <x-install-input label="{{ __('installer.company_name') }}" required="true"
                                                    name="nama_perusahaan" type="text"
                                                    value="{{ old('nama_perusahaan') }}" />
                                                <x-install-error for="nama_perusahaan" />
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label">
                                                    {{ __('installer.company_logo') }}
                                                </label>

                                                <div class="logo-upload-container">
                                                    <input type="file" class="form-control d-none" name="logo_perusahaan"
                                                        id="logo_perusahaan"
                                                        accept="image/jpeg,image/png,image/jpg,image/webp,image/svg+xml">

                                                    <div class="logo-upload-area" id="logo-upload-area">
                                                        <div class="upload-content">
                                                            <i class="bi bi-cloud-upload display-4 text-muted mb-3"></i>
                                                            <h5 class="mb-2">Pilih Logo Perusahaan</h5>
                                                            <p class="text-muted mb-3">
                                                                Klik atau drag & drop file gambar di sini
                                                            </p>
                                                            <button type="button" class="btn btn-outline-primary">
                                                                <i class="bi bi-folder2-open me-2"></i>Pilih File
                                                            </button>
                                                        </div>
                                                    </div>

                                                    <div class="logo-preview-area d-none" id="logo-preview-area">
                                                        <div class="preview-container">
                                                            <img id="logo-preview" src="" alt="Logo Preview"
                                                                class="logo-preview-img">
                                                            <div class="preview-info">
                                                                <h6 id="file-name" class="mb-1"></h6>
                                                                <small id="file-size" class="text-muted"></small>
                                                                <div class="mt-2">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-secondary me-2"
                                                                        id="change-logo-btn">
                                                                        <i class="bi bi-pencil me-1"></i>Ganti
                                                                    </button>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-outline-danger"
                                                                        onclick="clearLogo()">
                                                                        <i class="bi bi-trash me-1"></i>Hapus
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="upload-progress d-none" id="upload-progress">
                                                        <div class="progress mb-2">
                                                            <div class="progress-bar" role="progressbar" style="width: 0%">
                                                            </div>
                                                        </div>
                                                        <small class="text-muted">Memproses gambar...</small>
                                                    </div>
                                                </div>

                                                <div class="form-text text-muted mt-2">
                                                    <strong>Persyaratan Logo:</strong><br>
                                                    • Format: JPEG, PNG, JPG, WebP, SVG<br>
                                                    • Ukuran maksimal: 5MB<br>
                                                    • Dimensi minimal: 100x100 px<br>
                                                    • Rasio persegi (1:1) direkomendasikan
                                                </div>

                                                <div id="logo-validation-error" class="alert alert-danger mt-2 d-none">
                                                </div>
                                                <x-install-error for="logo_perusahaan" />
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label">{{ __('installer.company_description') }}</label>
                                                <textarea name="deskripsi_perusahaan" class="form-control"
                                                    rows="3">{{ old('deskripsi_perusahaan') }}</textarea>
                                                <x-install-error for="deskripsi_perusahaan" />
                                            </div>

                                            <div class="mb-4">
                                                <x-install-input label="{{ __('installer.company_address') }}"
                                                    required="true" name="alamat_perusahaan" type="text"
                                                    value="{{ old('alamat_perusahaan') }}" />
                                                <x-install-error for="alamat_perusahaan" />
                                            </div>

                                            <div class="mb-4">
                                                <x-install-input label="{{ __('installer.company_location_link') }}"
                                                    name="link_alamat_perusahaan" type="text"
                                                    value="{{ old('link_alamat_perusahaan') }}" />
                                                <x-install-error for="link_alamat_perusahaan" />
                                            </div>

                                            <div class="mb-5">
                                                <x-install-input label="{{ __('installer.company_email') }}" required="true"
                                                    name="email_perusahaan" type="email"
                                                    value="{{ old('email_perusahaan') }}" />
                                                <x-install-error for="email_perusahaan" />
                                            </div>
                                        </div>
                                    </div>
                                    {{-- BUG: The first <form> was closed here, separating it from the submit button. This
                                        has been removed. --}}
                                </div>

                                <div class="card-footer bg-light border-top p-4">
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('database_import') }}"
                                            class="btn btn-outline-primary btn-lg px-5">
                                            {{ __('installer.back') }}
                                        </a>
                                        {{-- BUG: A second <form> element was here. It has been removed. --}}
                                            {{-- The button is now a standard submit button for the main form. --}}
                                            <button type="submit" id="next_button"
                                                class="btn btn-primary btn-lg px-5 company-btn">
                                                {{ __('installer.save') }}
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
        /* All your existing CSS styles remain unchanged here... */
        /* Company config page specific styles - matching finish page */
        .company-config-card {
            background: linear-gradient(135deg, #fff 0%, #f8f9ff 100%);
            border-radius: 20px !important;
            overflow: hidden;
            animation: slideInUp 0.8s ease-out;
        }

        .company-title {
            font-weight: 700;
            animation: fadeInDown 1s ease-out 0.3s both;
        }

        .company-subtitle {
            animation: fadeInUp 1s ease-out 0.5s both;
        }

        .company-btn {
            background: linear-gradient(135deg, var(--primary-color) 0%, #4338ca 100%);
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .company-btn:hover {
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

        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }

        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
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

        /* Logo Upload Styling */
        .logo-upload-container {
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            transition: all 0.3s ease;
        }

        .logo-upload-container:hover {
            border-color: var(--primary-color);
            background-color: rgba(99, 102, 241, 0.02);
        }

        .logo-upload-area {
            padding: 40px 20px;
            text-align: center;
            background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .logo-upload-area:hover {
            background: linear-gradient(135deg, #f0f1ff 0%, #f8f9ff 100%);
            transform: translateY(-2px);
        }

        .logo-upload-area.dragover {
            border-color: var(--primary-color);
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(99, 102, 241, 0.05) 100%);
            transform: scale(1.02);
        }

        .logo-preview-area {
            padding: 20px;
            background: linear-gradient(135deg, #f8f9ff 0%, #fff 100%);
            border-radius: 10px;
        }

        .preview-container {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logo-preview-img {
            width: 120px;
            height: 120px;
            object-fit: contain;
            border-radius: 8px;
            border: 2px solid #e2e8f0;
            background: white;
            padding: 8px;
        }

        .preview-info {
            flex: 1;
        }

        .upload-progress {
            padding: 15px 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        /* Validation Error Styling */
        #logo-validation-error {
            border-left: 4px solid #dc3545;
            background-color: rgba(220, 53, 69, 0.1);
            border-radius: 4px;
        }

        /* Animation for file upload states */
        .logo-upload-container.processing {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                border-color: #e2e8f0;
            }

            50% {
                border-color: var(--primary-color);
            }

            100% {
                border-color: #e2e8f0;
            }
        }
    </style>

    <script>
        // Configuration for logo validation (remains the same)
        const logoConfig = {
            maxSize: 5 * 1024 * 1024, // 5MB
            allowedTypes: ['image/jpeg', 'image/png', 'image/jpg', 'image/webp', 'image/svg+xml'],
            allowedExtensions: ['jpg', 'jpeg', 'png', 'webp', 'svg'],
            minWidth: 100,
            minHeight: 100,
            recommendedRatio: 1 // 1:1 square ratio
        };

        // Logo validation and preview functionality
        function initializeLogoUpload() {
            const fileInput = document.getElementById('logo_perusahaan');
            const uploadArea = document.getElementById('logo-upload-area');
            const previewArea = document.getElementById('logo-preview-area');
            const progressArea = document.getElementById('upload-progress');
            const errorArea = document.getElementById('logo-validation-error');
            const changeLogoBtn = document.getElementById('change-logo-btn');

            // File input change handler
            fileInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    validateAndPreviewLogo(file);
                }
            });

            // Drag and drop functionality
            uploadArea.addEventListener('dragover', function (e) {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });

            uploadArea.addEventListener('dragleave', function (e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
            });

            uploadArea.addEventListener('drop', function (e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');

                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    validateAndPreviewLogo(files[0]);
                }
            });

            // Click to upload - only for upload area
            uploadArea.addEventListener('click', function () {
                fileInput.click();
            });

            // Change logo button click handler
            if (changeLogoBtn) {
                changeLogoBtn.addEventListener('click', function () {
                    fileInput.click();
                });
            }
        }

        // Validate and preview logo
        function validateAndPreviewLogo(file) {
            const uploadArea = document.getElementById('logo-upload-area');
            const previewArea = document.getElementById('logo-preview-area');
            const progressArea = document.getElementById('upload-progress');
            const errorArea = document.getElementById('logo-validation-error');

            // Clear previous errors
            hideError();

            // Show progress
            showProgress();

            // Validate file
            const validation = validateLogoFile(file);
            if (!validation.valid) {
                hideProgress();
                showError(validation.errors.join('<br>'));
                return;
            }

            // Preview the image
            const reader = new FileReader();
            reader.onload = function (e) {
                const img = new Image();
                img.onload = function () {
                    // Validate dimensions
                    const dimensionValidation = validateDimensions(img.width, img.height);
                    if (!dimensionValidation.valid) {
                        hideProgress();
                        showError(dimensionValidation.errors.join('<br>'));
                        return;
                    }

                    // Show preview
                    displayPreview(e.target.result, file);
                    hideProgress();
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }

        // Validate logo file
        function validateLogoFile(file) {
            const errors = [];

            // Check file type
            if (!logoConfig.allowedTypes.includes(file.type)) {
                errors.push(`❌ Format file tidak didukung. Gunakan: ${logoConfig.allowedExtensions.join(', ').toUpperCase()}`);
            }

            // Check file size
            if (file.size > logoConfig.maxSize) {
                const maxSizeMB = (logoConfig.maxSize / 1024 / 1024).toFixed(1);
                const fileSizeMB = (file.size / 1024 / 1024).toFixed(1);
                errors.push(`❌ Ukuran file terlalu besar (${fileSizeMB}MB). Maksimal ${maxSizeMB}MB`);
            }

            // Check if file is empty
            if (file.size === 0) {
                errors.push('❌ File kosong atau rusak');
            }

            return {
                valid: errors.length === 0,
                errors: errors
            };
        }

        // Validate image dimensions
        function validateDimensions(width, height) {
            const errors = [];

            if (width < logoConfig.minWidth || height < logoConfig.minHeight) {
                errors.push(`❌ Dimensi terlalu kecil (${width}x${height}px). Minimal ${logoConfig.minWidth}x${logoConfig.minHeight}px`);
            }

            const ratio = width / height;
            if (Math.abs(ratio - logoConfig.recommendedRatio) > 0.3) {
                errors.push(`⚠️ Peringatan: Rasio tidak persegi (${width}x${height}px). Rasio 1:1 direkomendasikan untuk logo`);
            }

            return {
                valid: errors.filter(e => e.includes('❌')).length === 0,
                errors: errors
            };
        }

        // Display preview
        function displayPreview(src, file) {
            const uploadArea = document.getElementById('logo-upload-area');
            const previewArea = document.getElementById('logo-preview-area');
            const previewImg = document.getElementById('logo-preview');
            const fileName = document.getElementById('file-name');
            const fileSize = document.getElementById('file-size');

            previewImg.src = src;
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);

            uploadArea.classList.add('d-none');
            previewArea.classList.remove('d-none');
        }

        // Clear logo
        function clearLogo() {
            const fileInput = document.getElementById('logo_perusahaan');
            const uploadArea = document.getElementById('logo-upload-area');
            const previewArea = document.getElementById('logo-preview-area');

            fileInput.value = '';
            uploadArea.classList.remove('d-none');
            previewArea.classList.add('d-none');
            hideError();
        }

        // Show progress
        function showProgress() {
            const uploadArea = document.getElementById('logo-upload-area');
            const progressArea = document.getElementById('upload-progress');
            const progressBar = progressArea.querySelector('.progress-bar');

            uploadArea.classList.add('d-none');
            progressArea.classList.remove('d-none');

            // Animate progress bar
            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                progressBar.style.width = progress + '%';
                if (progress >= 100) {
                    clearInterval(interval);
                }
            }, 50);
        }

        // Hide progress
        function hideProgress() {
            const progressArea = document.getElementById('upload-progress');
            const progressBar = progressArea.querySelector('.progress-bar');

            progressArea.classList.add('d-none');
            progressBar.style.width = '0%';
        }

        // Show error
        function showError(message) {
            const errorArea = document.getElementById('logo-validation-error');
            errorArea.innerHTML = '<strong>Error Validasi Logo:</strong><br>' + message;
            errorArea.classList.remove('d-none');

            // Reset upload area
            const uploadArea = document.getElementById('logo-upload-area');
            const previewArea = document.getElementById('logo-preview-area');
            uploadArea.classList.remove('d-none');
            previewArea.classList.add('d-none');

            // Clear file input
            document.getElementById('logo_perusahaan').value = '';
        }

        // Hide error
        function hideError() {
            const errorArea = document.getElementById('logo-validation-error');
            errorArea.classList.add('d-none');
        }

        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // FIX: Replaced the complex, buggy two-form validation logic with a simple one.
        // This function now validates the single form before it submits.
        function validateFormBeforeSubmit() {
            const form = document.getElementById('company-form');
            if (!form) return;

            form.addEventListener('submit', function (e) {
                // Clear previous errors first
                hideError();

                // 1. Check required text/email fields
                const requiredFields = [
                    { name: 'nama_perusahaan', label: 'Nama Perusahaan' },
                    { name: 'alamat_perusahaan', label: 'Alamat Perusahaan' },
                    { name: 'email_perusahaan', label: 'Email Perusahaan' }
                ];

                for (let field of requiredFields) {
                    const input = document.querySelector(`[name="${field.name}"]`);
                    if (!input || !input.value.trim()) {
                        e.preventDefault(); // Stop submission
                        showError(`${field.label} wajib diisi.`);
                        input?.focus();
                        return; // Exit
                    }
                }

                // 2. Validate email format
                const emailInput = document.querySelector('[name="email_perusahaan"]');
                if (emailInput) {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!emailRegex.test(emailInput.value.trim())) {
                        e.preventDefault(); // Stop submission
                        showError('Format email tidak valid.');
                        emailInput.focus();
                        return; // Exit
                    }
                }

                // 3. Validate the logo file if one is selected
                const fileInput = document.getElementById('logo_perusahaan');
                if (fileInput.files.length > 0) {
                    const file = fileInput.files[0];
                    const validation = validateLogoFile(file); // This is your existing function

                    if (!validation.valid) {
                        e.preventDefault(); // Stop submission
                        showError(validation.errors.join('<br>'));
                        return; // Exit
                    }
                }

                // If all checks pass, the form will submit naturally.
            });
        }

        // Initialize everything when DOM is loaded
        document.addEventListener('DOMContentLoaded', function () {
            initializeLogoUpload(); // Your existing function for UI
            validateFormBeforeSubmit(); // The new, simplified validation function

            // FIX: All the complex form syncing logic (syncFormDataToFooter, etc.) is no longer needed and has been removed.
        });
    </script>
@endsection