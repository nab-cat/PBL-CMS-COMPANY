<?php

/**
 * File Bahasa Indonesia untuk Proses Instalasi CMS.
 * Dikelompokkan berdasarkan halaman, komponen, dan jenis pesan.
 */

return [
    //======================================================================
    // 1. Teks Umum & Navigasi
    //======================================================================

    'install_title' => 'Instalasi CMS Perusahaan',
    'language' => 'Bahasa',
    'select_language' => 'Pilih Bahasa',
    'english' => 'English',
    'indonesian' => 'Bahasa Indonesia',

    // Tombol Aksi
    'back' => 'Kembali',
    'next' => 'Lanjut',
    'continue' => 'Lanjutkan',
    'save' => 'Simpan',
    'finish' => 'Selesai',
    'skip' => 'Lewati',
    'install' => 'Install',
    'test_connection' => 'Tes Koneksi',
    'test_email' => 'Tes Email',
    'create_account' => 'Buat Akun',
    'finalize_installation' => 'Finalisasi Instalasi',

    // Status & Label Umum
    'enable' => 'Aktifkan',
    'disable' => 'Nonaktifkan',
    'version' => 'Versi',
    'required' => 'Diperlukan',
    'supported' => 'Didukung',
    'not_supported' => 'Tidak Didukung',
    'writable' => 'Dapat Dikelola', // Atau 'Dapat Ditulis'
    'not_writable' => 'Tidak Dapat Dikelola', // Atau 'Tidak Dapat Ditulis'


    //======================================================================
    // 2. Langkah-langkah Instalasi (Steps)
    //======================================================================

    'step_welcome' => 'Selamat Datang',
    'step_requirements' => 'Persyaratan',
    'step_database' => 'Database',
    'step_company' => 'Perusahaan',
    'step_admin' => 'Admin',
    'step_roles' => 'Peran',
    'step_features' => 'Fitur',
    'step_finish' => 'Selesai',


    //======================================================================
    // 3. Halaman Instalasi
    //======================================================================

    // Halaman: Welcome
    'welcome_title' => 'Selamat Datang di Instalasi CMS Company',
    'welcome_subtitle' => 'Terima kasih telah memilih produk CMS kami',
    'installation_process' => 'Proses Instalasi',
    'get_started' => 'Mulai',
    'installation_time' => 'Waktu Instalasi',
    'installation_time_description' => 'Proses ini biasanya memakan waktu 5-10 menit untuk diselesaikan.',
    'preparation_note' => 'Pastikan Anda telah menyiapkan kredensial database dan informasi perusahaan.',

    // Halaman: Requirements
    'requirements_title' => 'Persyaratan Sistem & Izin',
    'requirements_subtitle' => 'Pastikan server Anda memenuhi semua persyaratan dan memiliki izin yang tepat.',
    'server_requirements' => 'Versi Minimal',
    'software_type' => 'Software',
    'php_extensions' => 'Ekstensi',
    'php_requirements' => 'Persyaratan PHP',
    'folder_permissions' => 'Izin Folder',
    'folder' => 'Folder',

    // Pesan untuk tombol disabled
    'requirements_not_met' => 'Persyaratan sistem belum terpenuhi',
    'requirements_not_met_message' => 'Silakan perbaiki semua persyaratan sistem sebelum melanjutkan.',
    'php_requirements_failed' => 'Beberapa persyaratan PHP tidak terpenuhi.',
    'php_version_not_supported' => 'Versi PHP tidak didukung.',
    'folder_permissions_failed' => 'Beberapa folder tidak memiliki izin yang tepat.',

    // Halaman: Database & Email
    'database_title' => 'Konfigurasi Database & Email',
    'database_configuration' => 'Konfigurasi Database',
    'database_connection' => 'Koneksi Database',
    'database_connection_type' => 'Tipe Koneksi Database',
    'database_host' => 'Host Database',
    'database_port' => 'Port Database',
    'database_name' => 'Nama Database',
    'database_username' => 'Username Database',
    'database_password' => 'Password Database',
    'database_user' => 'Pengguna Database',
    'sqlite_help_text' => 'File SQLite akan otomatis dibuat di direktori storage/',
    'email_configuration' => 'Konfigurasi Email',
    'mail_driver' => 'Driver Email',
    'mail_host' => 'Host Email',
    'mail_port' => 'Port Email',
    'mail_username' => 'Username Email',
    'mail_password' => 'Password Email',
    'mail_encryption' => 'Enkripsi Email',
    'mail_from_address' => 'Alamat Email Pengirim',
    'mail_from_name' => 'Nama Pengirim Email',
    'mail_from_name_description' => 'Nama pengirim email akan otomatis disetel ke nama perusahaan Anda',
    'app_debug' => 'App Debug',
    'app_log_level' => 'Level Log Aplikasi',
    'app_url' => 'URL Aplikasi',
    'app_timezone' => 'Zona Waktu',
    'app_locale' => 'Bahasa Aplikasi',
    'example' => 'contoh',

    // Halaman: Company Profile
    'company_title' => 'Konfigurasi Profil Perusahaan',
    'company_name' => 'Nama Perusahaan',
    'company_email' => 'Email Perusahaan',
    'company_address' => 'Alamat Perusahaan',
    'company_description' => 'Deskripsi Perusahaan',
    'company_location_link' => 'Link Lokasi Perusahaan',
    'company_logo' => 'Logo Perusahaan',
    'logo_requirements' => 'Format: JPEG, PNG, JPG, WebP, SVG | Maksimal: 5MB | Dimensi minimal: 100x100px',

    // Halaman: Super Admin
    'super_admin_title' => 'Konfigurasi Super Admin',
    'super_admin_configuration' => 'Konfigurasi Akun Super Admin',
    'full_name' => 'Nama Lengkap',
    'email' => 'Email',
    'password' => 'Password',
    'password_confirmation' => 'Konfirmasi Password',
    'password_description' => 'Password harus minimal 8 karakter',
    'include_dummy_data' => 'Isi dengan Data Sample',
    'dummy_data_description' => 'Centang opsi ini jika Anda ingin mengisi database dengan data contoh untuk keperluan testing atau demo. Data Sample termasuk user, artikel, produk, galeri, dan konten lainnya.',

    // Halaman: User Roles
    'roles_title' => 'Peran Pengguna & Izin',
    'user_roles_list' => 'Daftar User dengan Role',
    'user_roles_subtitle' => 'Review role user dan alamat email',
    'user_accounts' => 'Akun Pengguna',
    'name' => 'Nama',
    'role' => 'Role',
    'no_role' => 'Tidak Ada Role',
    'super_admin_created' => 'Akun Super Admin berhasil dibuat! Anda bisa login menggunakan email:',
    'dummy_password_info' => 'Password default untuk akun Sample adalah',
    'continue_to_features' => 'Lanjut',

    // Halaman: Features
    'features_title' => 'Konfigurasi Fitur',
    'features_subtitle' => 'Pilih fitur yang akan diaktifkan dan ditampilkan pada frontend website.',
    'feature_status' => 'Status',
    'configure_features' => 'Konfigurasi Fitur',

    // Halaman: Finish
    'finish_title' => 'Instalasi Selesai!',
    'finish_subtitle' => 'Selamat! CMS Company Anda telah berhasil diinstall.',
    'next_steps' => 'Langkah Selanjutnya',
    'next_steps_description' => 'Klik tombol di bawah untuk menyelesaikan instalasi dan mulai menggunakan CMS Anda.',
    'support' => 'Dapatkan Dukungan',
    'database_configured' => 'Database Terkonfigurasi',
    'database_ready' => 'Koneksi database telah terhubung dan siap',
    'admin_created' => 'Akun Admin Dibuat',
    'admin_ready' => 'Akun super admin telah diatur',
    'system_configured' => 'Sistem Terkonfigurasi',
    'system_ready' => 'Semua fitur sistem telah dikonfigurasi',

    // Deskripsi Fitur pada Halaman Welcome (dipisah agar lebih rapi)
    'features' => [
        'requirements' => [
            'title' => 'Persyaratan Sistem',
            'description' => 'Periksa persyaratan server dan izin file'
        ],
        'database' => [
            'title' => 'Konfigurasi Database',
            'description' => 'Konfigurasi database dan pengaturan email'
        ],
        'company' => [
            'title' => 'Profil Perusahaan',
            'description' => 'Atur informasi dan branding perusahaan Anda'
        ],
        'admin' => [
            'title' => 'Akun Admin',
            'description' => 'Buat akun super administrator Anda'
        ],
        'roles' => [
            'title' => 'Peran Pengguna',
            'description' => 'Tinjau dan konfigurasi peran dan izin pengguna'
        ],
        'features' => [
            'title' => 'Konfigurasi Fitur',
            'description' => 'Aktifkan atau nonaktifkan fitur sistem'
        ],
        'complete' => [
            'title' => 'Instalasi Selesai',
            'description' => 'Finalisasi instalasi Anda dan mulai gunakan sistem'
        ]
    ],


    //======================================================================
    // 4. Pesan Sistem (Alerts, Messages, Feedback)
    //======================================================================

    // Pesan Status Umum
    'success' => 'Berhasil!',
    'error' => 'Error!',
    'warning' => 'Peringatan!',
    'info' => 'Informasi',
    'loading' => 'Memuat...',
    'please_wait' => 'Mohon tunggu...',
    'testing' => 'Menguji...',
    'processing' => 'Memproses...',

    // Pesan Feedback Aksi Pengguna
    'company_profile_saved' => 'Profil perusahaan berhasil disimpan.',
    'company_profile_save_error' => 'Gagal menyimpan profil perusahaan.',
    'feature_toggles_saved' => 'Pengaturan fitur berhasil disimpan.',
    'feature_toggles_save_error' => 'Gagal menyimpan pengaturan fitur.',

    // Pesan Koneksi & AJAX
    'connection_error' => 'Error Koneksi!',
    'could_not_test_database' => 'Tidak dapat menguji koneksi database.',
    'database_connection_successful' => 'Koneksi database berhasil!',
    'database_connection_failed' => 'Koneksi database gagal. Mohon periksa pengaturan Anda dan coba lagi.',
    'sqlite_file_creation_failed' => 'Tidak dapat membuat file database SQLite',
    'email_test_success' => 'Tes Email Berhasil!',
    'email_test_failed' => 'Tes email gagal. Mohon periksa pengaturan SMTP dan koneksi internet Anda.',
    'email_test_error' => 'Tidak dapat menguji koneksi email. Mohon periksa konfigurasi Anda.',
    'could_not_test_email' => 'Tidak dapat menguji konfigurasi email.',
    'server_communication_error' => 'Terjadi kesalahan saat berkomunikasi dengan server. Silakan periksa koneosi Anda dan coba lagi.',
    'database_connection_error' => 'Kesalahan Koneksi Database!',
    'please_make_sure' => 'Pastikan hal berikut',
    'database_server_running' => 'Server database Anda berjalan',
    'database_exists' => 'Database sudah ada',
    'credentials_correct' => 'Username dan password Anda benar',
    'user_has_permissions' => 'Pengguna memiliki izin yang tepat pada database',

    // Detail Error Database (BARU)
    'database_error_details' => 'Detail Teknis',
    'database_connection_failed_friendly' => 'Tidak dapat terhubung ke database. Mohon periksa pengaturan Anda dan coba lagi.',
    'sqlite_file_creation_failed_friendly' => 'Tidak dapat membuat file database SQLite. Mohon periksa izin file dan coba lagi.',

    // Validation messages
    'validation_error' => 'Kesalahan Validasi',
    'please_fill_required_fields' => 'Mohon isi field yang wajib diisi',
    'go_to_environment_tab' => 'Pergi ke Tab Environment',
    'go_to_database_tab' => 'Pergi ke Tab Database',
    'go_to_email_tab' => 'Pergi ke Tab Email',

    // Email error messages (user-friendly)
    'email_test_failed_friendly' => 'Tidak dapat mengirim email tes. Mohon periksa konfigurasi email Anda dan coba lagi.',
    'email_test_error_friendly' => 'Tes email tidak dapat diselesaikan. Mohon verifikasi pengaturan dan koneksi internet Anda.',

    // Pesan Tes Email (Baru)
    'email_config_invalid' => 'Mohon periksa konfigurasi email Anda dan coba lagi.',
    'email_smtp_only' => 'Saat ini hanya email SMTP yang didukung. Silakan pilih SMTP sebagai metode email Anda.',
    'email_smtp_config_missing' => 'Mohon isi alamat server SMTP dan nomor port.',
    'email_test_successful' => 'Tes email berhasil! Email tes telah dikirim ke :email',
    'email_fields_required' => 'Mohon isi semua field email yang wajib sebelum melakukan tes.',

    // Pesan Konfigurasi Database (Baru)
    'database_name_required' => 'Mohon masukkan nama database.',
    'database_name_invalid' => 'Nama database hanya boleh berisi huruf, angka, tanda strip, dan titik.',
    'mysql_credentials_required' => 'Mohon isi alamat server dan username untuk database MySQL.',
    'config_save_failed' => 'Tidak dapat menyimpan konfigurasi. Mohon periksa izin file dan coba lagi.',
    'form_validation_failed' => 'Mohon lengkapi semua field yang wajib diisi sebelum melanjutkan.',
    'check_all_tabs' => 'Mohon periksa semua tab dan isi informasi yang diperlukan.',

    // Pesan Khusus (Logika Super Admin)
    'super_admin_exists' => 'Super Admin dengan email :email sudah ada dan memiliki akses super admin. Anda dapat melanjutkan.',
    'user_exists_role_assigned' => 'User dengan email :email sudah ada. Role Super Admin telah diberikan pada akun tersebut.',
    'super_admin_created_msg' => 'Super Admin berhasil dibuat.',
    'failed_assign_role' => 'Gagal memberikan role Super Admin.',

    // Pesan Validasi Logo Perusahaan
    'logo_upload_success' => 'Logo perusahaan berhasil diunggah.',
    'logo_upload_failed' => 'Gagal mengunggah logo perusahaan.',
    'logo_file_too_large' => 'Ukuran file logo terlalu besar. Maksimal 5MB.',
    'logo_invalid_format' => 'Format file logo tidak didukung. Gunakan: JPEG, PNG, JPG, WebP, atau SVG.',
    'logo_invalid_dimensions' => 'Dimensi logo tidak sesuai. Minimal 100x100px.',
    'logo_file_corrupted' => 'File logo rusak atau tidak dapat dibaca.',
    'logo_svg_invalid' => 'File SVG tidak valid atau mengandung konten yang tidak aman.',
    'logo_processing_failed' => 'Gagal memproses file logo.',
    'logo_storage_failed' => 'Gagal menyimpan file logo ke penyimpanan.',
    'logo_validation_failed' => 'Validasi logo gagal. Silakan periksa file dan coba lagi.',
    'logo_ratio_warning' => 'Rasio logo tidak persegi. Rasio 1:1 direkomendasikan untuk hasil terbaik.',

    // Email Test Validation
    'email_test_required' => 'Tes email harus berhasil sebelum melanjutkan',
    'database_test_required' => 'Tes database harus berhasil sebelum melanjutkan',
    'please_test_database_and_email_first' => 'Silakan tes koneksi database dan email terlebih dahulu',
    'please_test_email_connection_first' => 'Silakan tes koneksi email terlebih dahulu',

];