@php
    $dbErrors = array_filter([
        session('db_connection_error'),
        session('database_error'),
        $errors->first('database_connection'),
        $errors->first('database_fields'),
        $errors->first('save_error')
    ]);
@endphp

@if(!empty($dbErrors))
    <div class="alert alert-danger">
        <strong>{{ __('installer.database_connection_error') }}</strong>
        <ul>
            @foreach($dbErrors as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <p>{{ __('installer.please_make_sure') }}:</p>
        <ul>
            <li>{{ __('installer.database_server_running') }}</li>
            <li>{{ __('installer.database_exists') }}</li>
            <li>{{ __('installer.credentials_correct') }}</li>
            <li>{{ __('installer.user_has_permissions') }}</li>
        </ul>
    </div>
@endif