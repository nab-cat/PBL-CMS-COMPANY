@props(['label', 'required' => false, 'name', 'type' => 'text', 'class' => '', 'multiple' => false])

@if (!empty($label))
    <label class="mb-1" for="{{ $name }}">
        {{ $label }}
        @if (!empty($required) && $required !== "false")
            <span class="text-danger">*</span>
        @endif
    </label>
@endif

<select {{ $attributes->merge([
    'class' => 'form-select ' . ($class ?? '') . ($errors->has($name) ? ' select_error_border' : ''),
    'name' => $name,
    'id' => $attributes->get('id') ?? $name,
    'required' => (!empty($required) && $required !== "false") ? 'required' : null,
    'multiple' => $multiple ? 'multiple' : null,
]) }}>
    {{ $slot }}
</select>

@error($name)
    <span class="text-danger select_error">{{ $message }}</span>
@enderror