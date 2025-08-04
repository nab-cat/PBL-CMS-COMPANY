@php
    $images = $images ?? [];
    $totalImages = $total_images ?? 0;
@endphp

<div class="flex items-center space-x-2">
    @if(count($images) > 0) {{-- Image Stack Container --}}
        <div class="relative flex">
            {{-- Display up to 3 images with stacking effect --}}
            @foreach(array_slice($images, 0, 3) as $index => $image)
                <div class="relative {{ $index > 0 ? '-ml-2' : '' }}" style="z-index: {{ 3 - $index }}">
                    @if($index === 2 && $totalImages > 3)
                        {{-- Show overlay with count on last visible image --}}
                        <div class="relative">
                            <img src="{{ $image }}" alt="Thumbnail {{ $index + 1 }}"
                                class="w-8 h-8 rounded-full object-cover border-2 border-white dark:border-gray-900 shadow-sm"
                                loading="lazy" decoding="async" />
                            <div class="absolute inset-0 bg-gray-900 bg-opacity-70 rounded-full flex items-center justify-center">
                                <span class="text-white text-xs font-medium">+{{ $totalImages - 2 }}</span>
                            </div>
                        </div>
                    @else
                        <img src="{{ $image }}" alt="Thumbnail {{ $index + 1 }}"
                            class="w-8 h-8 rounded-full object-cover border-2 border-white dark:border-gray-900 shadow-sm hover:scale-105 transition-transform duration-200"
                            loading="lazy" decoding="async" />
                    @endif
                </div>
            @endforeach
        </div>

        {{-- Count Badge --}}
        @if($totalImages > 1)
            <span
                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                {{ $totalImages }}
            </span>
        @endif
    @else
        {{-- No Image Placeholder --}}
        <div
            class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 border-2 border-gray-200 dark:border-gray-700 flex items-center justify-center">
            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
        </div>
    @endif
</div>