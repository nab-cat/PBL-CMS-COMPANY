<x-filament-widgets::widget>
    <div class="h-[300px] flex flex-col space-y-2 overflow-y-auto lg:overflow-visible">
        @foreach ($this->getStats() as $stat)
            <div @class([
                'flex-1 p-4 shadow-sm border border-gray-200 rounded-lg bg-white dark:bg-gray-900 dark:border-gray-700',
            ])>
                <div class="flex flex-col gap-4">
                    <div class="flex items-center gap-2">
                        @if ($stat->getIcon())
                            <x-filament::icon :icon="$stat->getIcon()" @class([
                                'text-gray-500 dark:text-gray-400' => !($color = $stat->getColor()),
                                match ($color ?? null) {
                                    'gray' => 'text-gray-500 dark:text-gray-400',
                                    default => 'text-custom-500',
                                } => $color,
                            ])
                                style="{{ $color ? "--c-400: {$chartColor[$color]}" : '' }}" />
                        @endif

                        <div class="flex flex-col gap-1">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                {{ $stat->getLabel() }}
                            </span>

                            <div class="text-3xl font-semibold tracking-tight text-gray-950 dark:text-white">
                                {{ $stat->getValue() }}
                            </div>
                        </div>
                    </div> @if ($stat->getDescription())
                        <div class="flex items-center gap-2">
                            @if ($stat->getDescriptionIcon())
                                @php
                                    $colorClass = match (true) {
                                        str_contains($stat->getExtraAttributes()['class'] ?? '', 'primary') => 'text-primary-500',
                                        str_contains($stat->getExtraAttributes()['class'] ?? '', 'success') => 'text-success-500',
                                        str_contains($stat->getExtraAttributes()['class'] ?? '', 'warning') => 'text-warning-500',
                                        default => 'text-gray-400'
                                    };
                                @endphp
                                <x-filament::icon :icon="$stat->getDescriptionIcon()" class="h-4 w-4 {{ $colorClass }}" />
                            @endif

                            <span @class([
                                'text-sm',
                                'text-primary-500' => str_contains($stat->getExtraAttributes()['class'] ?? '', 'primary'),
                                'text-success-500' => str_contains($stat->getExtraAttributes()['class'] ?? '', 'success'),
                                'text-warning-500' => str_contains($stat->getExtraAttributes()['class'] ?? '', 'warning'),
                                'text-gray-500' => !isset($stat->getExtraAttributes()['class'])
                            ])>
                                {{ $stat->getDescription() }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</x-filament-widgets::widget>