<?php

/**
 * File Bahasa Inggris untuk Proses Instalasi CMS.
 * Dikelompokkan berdasarkan halaman, komponen, dan jenis pesan.
 */

return [
    //======================================================================
    // 1. Teks Umum & Navigasi
    //======================================================================

    'install_title' => 'CMS Company Installation',
    'language' => 'Language',
    'select_language' => 'Select Language',
    'english' => 'English',
    'indonesian' => 'Indonesian',

    // Tombol Aksi
    'back' => 'Back',
    'next' => 'Next',
    'continue' => 'Continue',
    'save' => 'Save',
    'finish' => 'Finish',
    'skip' => 'Skip',
    'install' => 'Install',
    'test_connection' => 'Test Connection',
    'test_email' => 'Test Email',
    'create_account' => 'Create Account',
    'finalize_installation' => 'Finalize Installation',

    // Status & Label Umum
    'enable' => 'Enable',
    'disable' => 'Disable',
    'version' => 'Version',
    'required' => 'Required',
    'supported' => 'Supported',
    'not_supported' => 'Not Supported',
    'writable' => 'Writable',
    'not_writable' => 'Not Writable',


    //======================================================================
    // 2. Langkah-langkah Instalasi (Steps)
    //======================================================================

    'step_welcome' => 'Welcome',
    'step_requirements' => 'Requirements',
    'step_database' => 'Database',
    'step_company' => 'Company',
    'step_admin' => 'Admin',
    'step_roles' => 'Roles',
    'step_features' => 'Features',
    'step_finish' => 'Finish',


    //======================================================================
    // 3. Halaman Instalasi
    //======================================================================

    // Halaman: Welcome
    'welcome_title' => 'Welcome to CMS Company Installation',
    'welcome_subtitle' => 'Thank you for choosing our CMS product',
    'installation_process' => 'Installation Process',
    'get_started' => 'Get Started',
    'installation_time' => 'Installation Time',
    'installation_time_description' => 'This process usually takes 5-10 minutes to complete.',
    'preparation_note' => 'Make sure you have prepared your database credentials and company information.',

    // Halaman: Requirements
    'requirements_title' => 'System Requirements & Permissions',
    'requirements_subtitle' => 'Please ensure your server meets all requirements and has proper permissions.',
    'server_requirements' => 'Server Requirements',
    'software_type' => 'Software',
    'php_extensions' => 'Extensions',
    'php_requirements' => 'PHP Requirements',
    'folder_permissions' => 'Folder Permissions',
    'folder' => 'Folder',

    // Messages for disabled button
    'requirements_not_met' => 'System requirements not met',
    'requirements_not_met_message' => 'Please fix all system requirements before proceeding.',
    'php_requirements_failed' => 'Some PHP requirements are not met.',
    'php_version_not_supported' => 'PHP version is not supported.',
    'folder_permissions_failed' => 'Some folders do not have proper permissions.',

    // Halaman: Database & Email
    'database_title' => 'Database & Email Configuration',
    'database_configuration' => 'Database Configuration',
    'database_connection' => 'Database Connection',
    'database_connection_type' => 'Database Connection Type',
    'database_host' => 'Database Host',
    'database_port' => 'Database Port',
    'database_name' => 'Database Name',
    'database_username' => 'Database Username',
    'database_password' => 'Database Password',
    'database_user' => 'Database User',
    'sqlite_help_text' => 'SQLite file will be automatically created in the storage/ directory',
    'email_configuration' => 'Email Configuration',
    'mail_driver' => 'Mail Driver',
    'mail_host' => 'Mail Host',
    'mail_port' => 'Mail Port',
    'mail_username' => 'Mail Username',
    'mail_password' => 'Mail Password',
    'mail_encryption' => 'Mail Encryption',
    'mail_from_address' => 'Mail From Address',
    'mail_from_name' => 'Mail From Name',
    'mail_from_name_description' => 'Mail sender name will be automatically set to your company name',
    'app_debug' => 'App Debug',
    'app_log_level' => 'Application Log Level',
    'app_url' => 'Application URL',
    'app_timezone' => 'Timezone',
    'app_locale' => 'Application Language',
    'example' => 'example',

    // Halaman: Company Profile
    'company_title' => 'Company Profile Configuration',
    'company_name' => 'Company Name',
    'company_email' => 'Company Email',
    'company_address' => 'Company Address',
    'company_description' => 'Company Description',
    'company_location_link' => 'Company Location Link',
    'company_logo' => 'Company Logo',
    'logo_requirements' => 'Format: JPEG, PNG, JPG, WebP, SVG | Maximum: 5MB | Minimum dimensions: 100x100px',

    // Halaman: Super Admin
    'super_admin_title' => 'Super Admin Configuration',
    'super_admin_configuration' => 'Super Admin Account Configuration',
    'full_name' => 'Full Name',
    'email' => 'Email',
    'password' => 'Password',
    'password_confirmation' => 'Confirm Password',
    'password_description' => 'Password must be at least 8 characters long.',
    'include_dummy_data' => 'Include Dummy Data',
    'dummy_data_description' => 'Check this option if you want to populate the database with sample data for testing or demo purposes. Dummy data includes articles, products, galleries, and other content.',

    // Halaman: User Roles
    'roles_title' => 'User Roles & Permissions',
    'user_roles_list' => 'User List with Roles',
    'user_roles_subtitle' => 'Review user roles and email addresses',
    'user_accounts' => 'User Accounts',
    'name' => 'Name',
    'role' => 'Role',
    'no_role' => 'No Role',
    'super_admin_created' => 'Super Admin account created successfully! You can login using email:',
    'dummy_password_info' => 'Default password for sample accounts is',
    'continue_to_features' => 'Continue',

    // Halaman: Features
    'features_title' => 'Feature Configuration',
    'features_subtitle' => 'Select features to be enabled and displayed on the website frontend.',
    'feature_status' => 'Status',
    'configure_features' => 'Configure Features',

    // Halaman: Finish
    'finish_title' => 'Installation Complete!',
    'finish_subtitle' => 'Congratulations! Your CMS Company has been successfully installed.',
    'next_steps' => 'Next Steps',
    'next_steps_description' => 'Click the button below to complete the installation and start using your CMS.',
    'support' => 'Get Support',
    'database_configured' => 'Database Configured',
    'database_ready' => 'Database connection established and ready',
    'admin_created' => 'Admin Account Created',
    'admin_ready' => 'Super admin account is set up',
    'system_configured' => 'System Configured',
    'system_ready' => 'All system features configured',

    // Deskripsi Fitur pada Halaman Welcome (dipisah agar lebih rapi)
    'features' => [
        'requirements' => [
            'title' => 'System Requirements',
            'description' => 'Check server requirements and file permissions'
        ],
        'database' => [
            'title' => 'Database Configuration',
            'description' => 'Configure database connection and email settings'
        ],
        'company' => [
            'title' => 'Company Profile',
            'description' => 'Set up your company information and branding'
        ],
        'admin' => [
            'title' => 'Admin Account',
            'description' => 'Create your super admin account'
        ],
        'roles' => [
            'title' => 'User Roles',
            'description' => 'Review and configure user roles and permissions'
        ],
        'features' => [
            'title' => 'Feature Configuration',
            'description' => 'Enable or disable system features'
        ],
        'complete' => [
            'title' => 'Installation Complete',
            'description' => 'Finalize your installation and start using the system'
        ]
    ],


    //======================================================================
    // 4. Pesan Sistem (Alerts, Messages, Feedback)
    //======================================================================

    // Pesan Status Umum
    'success' => 'Success!',
    'error' => 'Error!',
    'warning' => 'Warning!',
    'info' => 'Information',
    'loading' => 'Loading...',
    'please_wait' => 'Please wait...',
    'testing' => 'Testing...',
    'processing' => 'Processing...',

    // Pesan Feedback Aksi Pengguna
    'company_profile_saved' => 'Company profile successfully saved.',
    'company_profile_save_error' => 'Failed to save company profile',
    'feature_toggles_saved' => 'Feature toggles successfully saved.',
    'feature_toggles_save_error' => 'Failed to save feature toggles',

    // Pesan Koneksi & AJAX
    'connection_error' => 'Connection Error!',
    'could_not_test_database' => 'Could not test database connection.',
    'database_connection_successful' => 'Database connection successful!',
    'database_connection_failed' => 'Database connection failed. Please check your settings and try again.',
    'sqlite_file_creation_failed' => 'Failed to create SQLite database file',
    'email_test_success' => 'Email Test Success!',
    'email_test_failed' => 'Email test failed. Please check your SMTP settings and internet connection.',
    'email_test_error' => 'Unable to test email connection. Please check your configuration.',
    'could_not_test_email' => 'Could not test email configuration.',
    'server_communication_error' => 'An error occurred while communicating with the server. Please check your connection and try again.',
    'database_connection_error' => 'Database Connection Error!',
    'please_make_sure' => 'Please make sure',
    'database_server_running' => 'Your database server is running',
    'database_exists' => 'The database exists',
    'credentials_correct' => 'Your username and password are correct',
    'user_has_permissions' => 'The user has proper permissions on the database',

    // Database Error Details (NEW)
    'database_error_details' => 'Technical Details',
    'database_connection_failed_friendly' => 'Unable to connect to the database. Please check your settings and try again.',
    'sqlite_file_creation_failed_friendly' => 'Unable to create SQLite database file. Please check file permissions and try again.',

    // Validation messages
    'validation_error' => 'Validation Error',
    'please_fill_required_fields' => 'Please fill in the required fields',
    'go_to_environment_tab' => 'Go to Environment Tab',
    'go_to_database_tab' => 'Go to Database Tab',
    'go_to_email_tab' => 'Go to Email Tab',

    // Email error messages (user-friendly)
    'email_test_failed_friendly' => 'Unable to send test email. Please check your email configuration and try again.',
    'email_test_error_friendly' => 'Email test could not be completed. Please verify your settings and internet connection.',

    // Email Test Messages  
    'email_config_invalid' => 'Please check your email configuration and try again.',
    'email_smtp_only' => 'Currently only SMTP email is supported. Please select SMTP as your email method.',
    'email_smtp_config_missing' => 'Please fill in the SMTP server address and port number.',
    'email_test_successful' => 'Email test successful! A test email has been sent to :email',
    'email_fields_required' => 'Please fill in all required email fields before testing.',

    // Database Configuration Messages
    'database_name_required' => 'Please enter a database name.',
    'database_name_invalid' => 'Database name can only contain letters, numbers, dashes, and dots.',
    'mysql_credentials_required' => 'Please fill in the server address and username for MySQL database.',
    'config_save_failed' => 'Unable to save configuration. Please check file permissions and try again.',
    'form_validation_failed' => 'Please complete all required fields before continuing.',
    'check_all_tabs' => 'Please check all tabs and fill in the required information.',

    // Super Admin Messages
    'super_admin_exists' => 'Super Admin with email :email already exists and has super admin access. You can continue.',
    'user_exists_role_assigned' => 'User with email :email already exists. Super Admin role has been assigned to that account.',
    'super_admin_created_msg' => 'Super Admin successfully created.',
    'failed_assign_role' => 'Failed to assign Super Admin role',

    // Company Logo Validation Messages
    'logo_upload_success' => 'Company logo uploaded successfully.',
    'logo_upload_failed' => 'Failed to upload company logo.',
    'logo_file_too_large' => 'Logo file size is too large. Maximum 5MB.',
    'logo_invalid_format' => 'Logo file format is not supported. Use: JPEG, PNG, JPG, WebP, or SVG.',
    'logo_invalid_dimensions' => 'Logo dimensions are invalid. Minimum 100x100px.',
    'logo_file_corrupted' => 'Logo file is corrupted or cannot be read.',
    'logo_svg_invalid' => 'SVG file is invalid or contains unsafe content.',
    'logo_processing_failed' => 'Failed to process logo file.',
    'logo_storage_failed' => 'Failed to save logo file to storage.',
    'logo_validation_failed' => 'Logo validation failed. Please check the file and try again.',
    'logo_ratio_warning' => 'Logo aspect ratio is not square. 1:1 ratio is recommended for best results.',

    // Email Test Validation
    'email_test_required' => 'Email test must be successful before continuing',
    'database_test_required' => 'Database test must be successful before continuing',
    'please_test_database_and_email_first' => 'Please test database and email connection first',
    'please_test_email_connection_first' => 'Please test email connection first',

];