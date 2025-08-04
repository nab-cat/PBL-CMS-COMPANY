@props(['name' => 'app_timezone', 'label' => 'Timezone', 'required' => false])

<div class="form-group">
    <label for="{{ $name }}" class="form-label">
        {{ $label }} @if($required) <span class="text-danger">*</span> @endif
    </label>
    <select name="{{ $name }}" id="{{ $name }}" class="form-control" {{ $required ? 'required' : '' }}>
        <optgroup label="Indonesia">
            <option value="Asia/Jakarta" {{ old($name, 'Asia/Jakarta') == 'Asia/Jakarta' ? 'selected' : '' }}>Asia/Jakarta
                (WIB)</option>
            <option value="Asia/Makassar" {{ old($name) == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)
            </option>
            <option value="Asia/Jayapura" {{ old($name) == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)
            </option>
        </optgroup>
        <optgroup label="UTC">
            <option value="UTC" {{ old($name) == 'UTC' ? 'selected' : '' }}>UTC</option>
        </optgroup>
        <optgroup label="Africa">
            <option value="Africa/Abidjan" {{ old($name) == 'Africa/Abidjan' ? 'selected' : '' }}>Africa/Abidjan</option>
            <option value="Africa/Accra" {{ old($name) == 'Africa/Accra' ? 'selected' : '' }}>Africa/Accra</option>
            <option value="Africa/Addis_Ababa" {{ old($name) == 'Africa/Addis_Ababa' ? 'selected' : '' }}>Africa/Addis
                Ababa</option>
            <option value="Africa/Algiers" {{ old($name) == 'Africa/Algiers' ? 'selected' : '' }}>Africa/Algiers</option>
            <option value="Africa/Cairo" {{ old($name) == 'Africa/Cairo' ? 'selected' : '' }}>Africa/Cairo</option>
            <option value="Africa/Casablanca" {{ old($name) == 'Africa/Casablanca' ? 'selected' : '' }}>Africa/Casablanca
            </option>
            <option value="Africa/Johannesburg" {{ old($name) == 'Africa/Johannesburg' ? 'selected' : '' }}>
                Africa/Johannesburg</option>
            <option value="Africa/Lagos" {{ old($name) == 'Africa/Lagos' ? 'selected' : '' }}>Africa/Lagos</option>
            <option value="Africa/Nairobi" {{ old($name) == 'Africa/Nairobi' ? 'selected' : '' }}>Africa/Nairobi</option>
        </optgroup>
        <optgroup label="America">
            <option value="America/Anchorage" {{ old($name) == 'America/Anchorage' ? 'selected' : '' }}>America/Anchorage
            </option>
            <option value="America/Argentina/Buenos_Aires" {{ old($name) == 'America/Argentina/Buenos_Aires' ? 'selected' : '' }}>America/Buenos Aires</option>
            <option value="America/Bogota" {{ old($name) == 'America/Bogota' ? 'selected' : '' }}>America/Bogota</option>
            <option value="America/Chicago" {{ old($name) == 'America/Chicago' ? 'selected' : '' }}>America/Chicago
            </option>
            <option value="America/Denver" {{ old($name) == 'America/Denver' ? 'selected' : '' }}>America/Denver</option>
            <option value="America/Halifax" {{ old($name) == 'America/Halifax' ? 'selected' : '' }}>America/Halifax
            </option>
            <option value="America/Los_Angeles" {{ old($name) == 'America/Los_Angeles' ? 'selected' : '' }}>America/Los
                Angeles</option>
            <option value="America/Mexico_City" {{ old($name) == 'America/Mexico_City' ? 'selected' : '' }}>America/Mexico
                City</option>
            <option value="America/New_York" {{ old($name) == 'America/New_York' ? 'selected' : '' }}>America/New York
            </option>
            <option value="America/Sao_Paulo" {{ old($name) == 'America/Sao_Paulo' ? 'selected' : '' }}>America/Sao Paulo
            </option>
            <option value="America/Toronto" {{ old($name) == 'America/Toronto' ? 'selected' : '' }}>America/Toronto
            </option>
        </optgroup>
        <optgroup label="Asia">
            <option value="Asia/Baghdad" {{ old($name) == 'Asia/Baghdad' ? 'selected' : '' }}>Asia/Baghdad</option>
            <option value="Asia/Bangkok" {{ old($name) == 'Asia/Bangkok' ? 'selected' : '' }}>Asia/Bangkok</option>
            <option value="Asia/Dhaka" {{ old($name) == 'Asia/Dhaka' ? 'selected' : '' }}>Asia/Dhaka</option>
            <option value="Asia/Dubai" {{ old($name) == 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai</option>
            <option value="Asia/Ho_Chi_Minh" {{ old($name) == 'Asia/Ho_Chi_Minh' ? 'selected' : '' }}>Asia/Ho Chi Minh
            </option>
            <option value="Asia/Hong_Kong" {{ old($name) == 'Asia/Hong_Kong' ? 'selected' : '' }}>Asia/Hong Kong</option>
            <option value="Asia/Jerusalem" {{ old($name) == 'Asia/Jerusalem' ? 'selected' : '' }}>Asia/Jerusalem</option>
            <option value="Asia/Karachi" {{ old($name) == 'Asia/Karachi' ? 'selected' : '' }}>Asia/Karachi</option>
            <option value="Asia/Kolkata" {{ old($name) == 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata</option>
            <option value="Asia/Kuala_Lumpur" {{ old($name) == 'Asia/Kuala_Lumpur' ? 'selected' : '' }}>Asia/Kuala Lumpur
            </option>
            <option value="Asia/Manila" {{ old($name) == 'Asia/Manila' ? 'selected' : '' }}>Asia/Manila</option>
            <option value="Asia/Qatar" {{ old($name) == 'Asia/Qatar' ? 'selected' : '' }}>Asia/Qatar</option>
            <option value="Asia/Riyadh" {{ old($name) == 'Asia/Riyadh' ? 'selected' : '' }}>Asia/Riyadh</option>
            <option value="Asia/Seoul" {{ old($name) == 'Asia/Seoul' ? 'selected' : '' }}>Asia/Seoul</option>
            <option value="Asia/Shanghai" {{ old($name) == 'Asia/Shanghai' ? 'selected' : '' }}>Asia/Shanghai</option>
            <option value="Asia/Singapore" {{ old($name) == 'Asia/Singapore' ? 'selected' : '' }}>Asia/Singapore</option>
            <option value="Asia/Taipei" {{ old($name) == 'Asia/Taipei' ? 'selected' : '' }}>Asia/Taipei</option>
            <option value="Asia/Tokyo" {{ old($name) == 'Asia/Tokyo' ? 'selected' : '' }}>Asia/Tokyo</option>
        </optgroup>
        <optgroup label="Australia">
            <option value="Australia/Adelaide" {{ old($name) == 'Australia/Adelaide' ? 'selected' : '' }}>
                Australia/Adelaide</option>
            <option value="Australia/Brisbane" {{ old($name) == 'Australia/Brisbane' ? 'selected' : '' }}>
                Australia/Brisbane</option>
            <option value="Australia/Darwin" {{ old($name) == 'Australia/Darwin' ? 'selected' : '' }}>Australia/Darwin
            </option>
            <option value="Australia/Melbourne" {{ old($name) == 'Australia/Melbourne' ? 'selected' : '' }}>
                Australia/Melbourne</option>
            <option value="Australia/Perth" {{ old($name) == 'Australia/Perth' ? 'selected' : '' }}>Australia/Perth
            </option>
            <option value="Australia/Sydney" {{ old($name) == 'Australia/Sydney' ? 'selected' : '' }}>Australia/Sydney
            </option>
        </optgroup>
        <optgroup label="Europe">
            <option value="Europe/Amsterdam" {{ old($name) == 'Europe/Amsterdam' ? 'selected' : '' }}>Europe/Amsterdam
            </option>
            <option value="Europe/Athens" {{ old($name) == 'Europe/Athens' ? 'selected' : '' }}>Europe/Athens</option>
            <option value="Europe/Berlin" {{ old($name) == 'Europe/Berlin' ? 'selected' : '' }}>Europe/Berlin</option>
            <option value="Europe/Brussels" {{ old($name) == 'Europe/Brussels' ? 'selected' : '' }}>Europe/Brussels
            </option>
            <option value="Europe/Budapest" {{ old($name) == 'Europe/Budapest' ? 'selected' : '' }}>Europe/Budapest
            </option>
            <option value="Europe/Copenhagen" {{ old($name) == 'Europe/Copenhagen' ? 'selected' : '' }}>Europe/Copenhagen
            </option>
            <option value="Europe/Dublin" {{ old($name) == 'Europe/Dublin' ? 'selected' : '' }}>Europe/Dublin</option>
            <option value="Europe/Helsinki" {{ old($name) == 'Europe/Helsinki' ? 'selected' : '' }}>Europe/Helsinki
            </option>
            <option value="Europe/Istanbul" {{ old($name) == 'Europe/Istanbul' ? 'selected' : '' }}>Europe/Istanbul
            </option>
            <option value="Europe/Lisbon" {{ old($name) == 'Europe/Lisbon' ? 'selected' : '' }}>Europe/Lisbon</option>
            <option value="Europe/London" {{ old($name) == 'Europe/London' ? 'selected' : '' }}>Europe/London</option>
            <option value="Europe/Madrid" {{ old($name) == 'Europe/Madrid' ? 'selected' : '' }}>Europe/Madrid</option>
            <option value="Europe/Moscow" {{ old($name) == 'Europe/Moscow' ? 'selected' : '' }}>Europe/Moscow</option>
            <option value="Europe/Oslo" {{ old($name) == 'Europe/Oslo' ? 'selected' : '' }}>Europe/Oslo</option>
            <option value="Europe/Paris" {{ old($name) == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris</option>
            <option value="Europe/Prague" {{ old($name) == 'Europe/Prague' ? 'selected' : '' }}>Europe/Prague</option>
            <option value="Europe/Rome" {{ old($name) == 'Europe/Rome' ? 'selected' : '' }}>Europe/Rome</option>
            <option value="Europe/Stockholm" {{ old($name) == 'Europe/Stockholm' ? 'selected' : '' }}>Europe/Stockholm
            </option>
            <option value="Europe/Vienna" {{ old($name) == 'Europe/Vienna' ? 'selected' : '' }}>Europe/Vienna</option>
            <option value="Europe/Warsaw" {{ old($name) == 'Europe/Warsaw' ? 'selected' : '' }}>Europe/Warsaw</option>
            <option value="Europe/Zurich" {{ old($name) == 'Europe/Zurich' ? 'selected' : '' }}>Europe/Zurich</option>
        </optgroup>
        <optgroup label="Pacific">
            <option value="Pacific/Auckland" {{ old($name) == 'Pacific/Auckland' ? 'selected' : '' }}>Pacific/Auckland
            </option>
            <option value="Pacific/Fiji" {{ old($name) == 'Pacific/Fiji' ? 'selected' : '' }}>Pacific/Fiji</option>
            <option value="Pacific/Guam" {{ old($name) == 'Pacific/Guam' ? 'selected' : '' }}>Pacific/Guam</option>
            <option value="Pacific/Honolulu" {{ old($name) == 'Pacific/Honolulu' ? 'selected' : '' }}>Pacific/Honolulu
            </option>
        </optgroup>
    </select>
</div>