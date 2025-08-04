@php
    $images = $images ?? [];
    $totalImages = $total_images ?? 0;
    $remainingCount = $remaining_count ?? 0;
@endphp

<div class="flex items-center">
    @if(count($images) > 0)
        <div class="relative inline-flex"> {{-- Stacked Images Container --}}
            <div class="relative flex">
                {{-- Main Image --}}
                <div class="relative z-[3]">
                    <img src="{{ $images[0] }}" alt="Thumbnail"
                        class="w-10 h-10 rounded-full object-cover border-2 border-white dark:border-gray-900 shadow-sm"
                        loading="lazy" decoding="async" />
                </div>

                {{-- Second Image (if exists) --}}
                @if(count($images) > 1)
                    <div class="absolute left-6 top-0 z-[2]">
                        <img src="{{ $images[1] }}" alt="Thumbnail"
                            class="w-10 h-10 rounded-full object-cover border-2 border-white dark:border-gray-900 shadow-sm"
                            loading="lazy" decoding="async" />
                    </div>
                @endif

                {{-- Third Image (if exists) --}}
                @if(count($images) > 2)
                    <div class="absolute left-12 top-0 z-[1]">
                        <img src="{{ $images[2] }}" alt="Thumbnail"
                            class="w-10 h-10 rounded-full object-cover border-2 border-white dark:border-gray-900 shadow-sm"
                            loading="lazy" decoding="async" />
                    </div>
                @endif

                {{-- More indicator overlay for third image --}}
                @if($totalImages > 3)
                    <div
                        class="absolute left-12 top-0 z-[4] w-10 h-10 rounded-full bg-black bg-opacity-60 border-2 border-white dark:border-gray-900 flex items-center justify-center">
                        <span class="text-white text-xs font-medium">+{{ $totalImages - 2 }}</span>
                    </div>
                @endif
            </div>

            {{-- Count Badge (alternative display for small counts) --}}
            @if($totalImages > 1 && $totalImages <= 3)
                <div
                    class="ml-2 text-xs font-medium text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded-full">
                    {{ $totalImages }}
                </div>
            @endif
        </div>
    @else
        {{-- Placeholder when no image --}}
        <div
            class="w-10 h-10 rounded-full bg-gray-200 dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 flex items-center justify-center">
            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd"
                    d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                    clip-rule="evenodd" />
            </svg>
        </div>
    @endif
</div>