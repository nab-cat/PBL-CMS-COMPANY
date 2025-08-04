@props(['label', 'required', 'name', 'value', 'type', 'class', 'placeholder' , 'accept', 'readonly'])
@if (!empty($label))
    <label class="mb-1" for="{{ $name }}">{{ $label }} @if (!empty($required) && empty($readonly))<span class="text-danger">*</span>@endif</label>
@endif
<input type="{{ $type }}" name="{{ $name }}"
    class="form-control {{ $class ?? '' }} @error($name) is-invalid @enderror" @if (!empty($value)) value="{{ $value }}" @endif  placeholder="{{ $placeholder ?? '' }}"  @if (!empty($accept)) accept="{{ $accept }}" @endif @if (!empty($required) && $required != "false" && empty($readonly)) required @endif @if (!empty($readonly)) readonly @endif >
