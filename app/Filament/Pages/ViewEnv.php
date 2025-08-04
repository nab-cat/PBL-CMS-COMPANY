<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\Component;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\MaxWidth;
use GeoSot\EnvEditor\Dto\BackupObj;
use GeoSot\EnvEditor\Dto\EntryObj;
use GeoSot\EnvEditor\Facades\EnvEditor;
use GeoSot\FilamentEnvEditor\Pages\Actions\Backups\DeleteBackupAction;
use GeoSot\FilamentEnvEditor\Pages\Actions\Backups\DownloadEnvFileAction;
use GeoSot\FilamentEnvEditor\Pages\Actions\Backups\MakeBackupAction;
use GeoSot\FilamentEnvEditor\Pages\Actions\Backups\RestoreBackupAction;
use GeoSot\FilamentEnvEditor\Pages\Actions\Backups\ShowBackupContentAction;
use GeoSot\FilamentEnvEditor\Pages\Actions\Backups\UploadBackupAction;
use GeoSot\FilamentEnvEditor\Pages\ViewEnv as BaseViewEnvEditor;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Url;
use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

class ViewEnv extends BaseViewEnvEditor
{
    // Search and filter properties
    #[Url]
    public string $searchTerm = '';

    #[Url]
    public string $categoryFilter = '';

    #[Url]
    public string $sortBy = 'key';
    #[Url]
    public string $sortDirection = 'asc';

    public bool $showEmpty = true;

    // Required for form functionality
    public function save(): void
    {
        // This method is required for Filament forms but we don't need to save anything
        // since we're just filtering and searching existing env data
    }

    protected static ?string $navigationIcon = 'heroicon-o-command-line';
    protected static ?string $navigationLabel = 'Variabel Lingkungan Server';
    protected static ?int $navigationSort = 5;

    public function getTitle(): string
    {
        return 'Manajemen Variabel Lingkungan Server';
    }

    protected function getHeaderActions(): array
    {
        return [
            // Add Enhanced Backup Comparison action
            Action::make('compare_backup')
                ->label('Bandingkan Cadangan')
                ->icon('heroicon-o-arrows-right-left')
                ->color('info')
                ->form([
                    Forms\Components\Select::make('backup_file')
                        ->label('Pilih Cadangan untuk Dibandingkan')
                        ->options(function () {
                            return EnvEditor::getAllBackUps()
                                ->pluck('name', 'name')
                                ->toArray();
                        })
                        ->required()
                        ->searchable()
                        ->placeholder('Pilih file cadangan untuk dibandingkan dengan .env saat ini'),

                    Forms\Components\Radio::make('comparison_mode')
                        ->label('Mode Perbandingan')
                        ->options([
                            'side_by_side' => 'Side-by-Side View',
                            'unified_diff' => 'Unified Diff',
                            'variables_only' => 'Variables Only (Parsed)'
                        ])
                        ->default('side_by_side')
                        ->inline()
                        ->required(),
                ])->action(function (array $data) {
                    $this->showBackupComparison($data['backup_file'], $data['comparison_mode']);
                }),
        ];
    }    // Enhanced Backup Comparison Methods
    protected function showBackupComparison(string $backupFile, string $mode): void
    {
        try {
            $backupContent = $this->getBackupFileContent($backupFile);
            $currentContent = file_get_contents(app()->environmentFilePath());

            $comparison = $this->generateComparison($currentContent, $backupContent, $mode);

            Notification::make()
                ->title('Perbandingan Cadangan')
                ->body(new HtmlString($comparison))
                ->persistent()
                ->actions([
                    \Filament\Notifications\Actions\Action::make('close')
                        ->button()
                        ->label('Tutup')
                        ->close(),
                ])
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Perbandingan Gagal')
                ->body('Tidak dapat membandingkan cadangan: ' . $e->getMessage())
                ->danger()
                ->send();
        }
    }

    protected function generateComparison(string $current, string $backup, string $mode): string
    {
        switch ($mode) {
            case 'unified_diff':
                return $this->generateUnifiedDiff($current, $backup);

            case 'variables_only':
                return $this->generateVariablesComparison($current, $backup);

            case 'side_by_side':
            default:
                return $this->generateSideBySideComparison($current, $backup);
        }
    }

    protected function generateUnifiedDiff(string $current, string $backup): string
    {
        $builder = new UnifiedDiffOutputBuilder("--- .env Saat Ini\n+++ Cadangan\n");
        $differ = new Differ($builder);

        $diff = $differ->diff($current, $backup);

        return "<div class='font-mono text-sm bg-gray-900 text-white p-4 rounded-lg max-h-96 overflow-y-auto'>" .
            "<pre>" . htmlspecialchars($diff) . "</pre>" .
            "</div>";
    }

    protected function generateSideBySideComparison(string $current, string $backup): string
    {
        $currentLines = explode("\n", $current);
        $backupLines = explode("\n", $backup);

        $maxLines = max(count($currentLines), count($backupLines));

        $html = "<div class='grid grid-cols-2 gap-4 max-h-96 overflow-y-auto'>";

        // Headers
        $html .= "<div class='bg-blue-50 p-2 font-semibold text-blue-800 rounded'>.env Saat Ini</div>";
        $html .= "<div class='bg-green-50 p-2 font-semibold text-green-800 rounded'>Cadangan</div>";

        // Content comparison
        for ($i = 0; $i < $maxLines; $i++) {
            $currentLine = $currentLines[$i] ?? '';
            $backupLine = $backupLines[$i] ?? '';

            $currentClass = 'bg-white border p-2 text-sm font-mono';
            $backupClass = 'bg-white border p-2 text-sm font-mono';

            if ($currentLine !== $backupLine) {
                if (empty($currentLine)) {
                    $currentClass = 'bg-red-50 border-red-200 p-2 text-sm font-mono text-red-600';
                } elseif (empty($backupLine)) {
                    $backupClass = 'bg-red-50 border-red-200 p-2 text-sm font-mono text-red-600';
                } else {
                    $currentClass = 'bg-yellow-50 border-yellow-200 p-2 text-sm font-mono text-yellow-800';
                    $backupClass = 'bg-yellow-50 border-yellow-200 p-2 text-sm font-mono text-yellow-800';
                }
            }

            $html .= "<div class='{$currentClass}'>" . htmlspecialchars($currentLine ?: '(kosong)') . "</div>";
            $html .= "<div class='{$backupClass}'>" . htmlspecialchars($backupLine ?: '(kosong)') . "</div>";
        }

        $html .= "</div>";

        return $html;
    }

    protected function generateVariablesComparison(string $current, string $backup): string
    {
        $currentVars = $this->parseEnvContent($current);
        $backupVars = $this->parseEnvContent($backup);

        $allKeys = array_unique(array_merge(array_keys($currentVars), array_keys($backupVars)));
        sort($allKeys);

        $html = "<div class='space-y-2 max-h-96 overflow-y-auto'>";

        foreach ($allKeys as $key) {
            $currentValue = $currentVars[$key] ?? null;
            $backupValue = $backupVars[$key] ?? null;

            $status = 'unchanged';
            $statusColor = 'gray';
            $statusIcon = '○';

            if ($currentValue === null && $backupValue !== null) {
                $status = 'dihapus';
                $statusColor = 'red';
                $statusIcon = '✕';
            } elseif ($currentValue !== null && $backupValue === null) {
                $status = 'ditambahkan';
                $statusColor = 'green';
                $statusIcon = '✓';
            } elseif ($currentValue !== $backupValue) {
                $status = 'dimodifikasi';
                $statusColor = 'yellow';
                $statusIcon = '~';
            }

            $html .= "<div class='grid grid-cols-12 gap-2 items-center p-2 border rounded'>";
            $html .= "<div class='col-span-1 text-{$statusColor}-600 font-bold text-center'>{$statusIcon}</div>";
            $html .= "<div class='col-span-3 font-mono font-semibold'>" . htmlspecialchars($key) . "</div>";
            $html .= "<div class='col-span-4 font-mono text-sm bg-blue-50 p-1 rounded'>" .
                htmlspecialchars($currentValue ?? '(tidak diatur)') . "</div>";
            $html .= "<div class='col-span-4 font-mono text-sm bg-green-50 p-1 rounded'>" .
                htmlspecialchars($backupValue ?? '(tidak diatur)') . "</div>";
            $html .= "</div>";
        }

        $html .= "</div>";

        // Add legend
        $html .= "<div class='mt-4 text-xs text-gray-600'>";
        $html .= "<span class='text-green-600 mr-4'>✓ Ditambahkan</span>";
        $html .= "<span class='text-red-600 mr-4'>✕ Dihapus</span>";
        $html .= "<span class='text-yellow-600 mr-4'>~ Dimodifikasi</span>";
        $html .= "<span class='text-gray-600'>○ Tidak Berubah</span>";
        $html .= "</div>";

        return $html;
    }

    protected function parseEnvContent(string $content): array
    {
        $variables = [];
        $lines = explode("\n", $content);

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip empty lines and comments
            if (empty($line) || str_starts_with($line, '#')) {
                continue;
            }

            // Parse KEY=VALUE format
            if (str_contains($line, '=')) {
                [$key, $value] = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);

                // Remove quotes if present
                if (
                    (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                    (str_starts_with($value, "'") && str_ends_with($value, "'"))
                ) {
                    $value = substr($value, 1, -1);
                }

                $variables[$key] = $value;
            }
        }

        return $variables;
    }

    public function form(Form $form): Form
    {
        $tabs = Forms\Components\Tabs::make('Tabs')
            ->tabs([
                Forms\Components\Tabs\Tab::make(__('filament-env-editor::filament-env-editor.tabs.current-env.title'))
                    ->schema([
                        $this->getSearchAndFilterSection(),
                        $this->getEnvironmentStatsSection(),
                        ...$this->getFirstTab(),
                    ]),
                Forms\Components\Tabs\Tab::make(__('filament-env-editor::filament-env-editor.tabs.backups.title'))
                    ->schema($this->getSecondTab()),
            ]);

        return $form
            ->schema([$tabs]);
    }
    protected function getSearchAndFilterSection(): Component
    {
        return Forms\Components\Section::make('Pencarian & Filter')
            ->schema([
                Forms\Components\Grid::make(4)
                    ->schema([
                        Forms\Components\TextInput::make('searchTerm')
                            ->label('Pencarian')
                            ->placeholder('Cari berdasarkan kunci atau nilai...')
                            ->prefixIcon('heroicon-o-magnifying-glass')
                            ->extraInputAttributes(['wire:model.live.debounce.300ms' => 'searchTerm']),

                        Forms\Components\Select::make('categoryFilter')
                            ->label('Kategori')
                            ->placeholder('Semua Kategori')
                            ->options([
                                'app' => 'Aplikasi',
                                'database' => 'Database',
                                'email' => 'Email',
                                'cache' => 'Cache',
                                'antrian' => 'Antrian',
                                'sesi' => 'Sesi',
                                'keamanan' => 'Keamanan',
                                'api' => 'API',
                                'penyimpanan' => 'Penyimpanan',
                                'logging' => 'Logging',
                                'lainnya' => 'Lainnya',
                            ])
                            ->extraInputAttributes(['wire:model.live' => 'categoryFilter']),

                        Forms\Components\Select::make('sortBy')
                            ->label('Urutkan Berdasarkan')
                            ->options([
                                'key' => 'Kunci',
                                'value' => 'Nilai',
                                'category' => 'Kategori',
                            ])
                            ->default('key')
                            ->extraInputAttributes(['wire:model.live' => 'sortBy']),

                        Forms\Components\Select::make('sortDirection')
                            ->label('Urutan')
                            ->options([
                                'asc' => 'Ascending',
                                'desc' => 'Descending',
                            ])
                            ->default('asc')
                            ->extraInputAttributes(['wire:model.live' => 'sortDirection']),
                    ]),

                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Toggle::make('showEmpty')
                            ->label('Tampilkan Variabel Kosong')
                            ->default(true)
                            ->afterStateUpdated(fn() => $this->resetPage()),

                        Forms\Components\Placeholder::make('filteredCount')
                            ->label('Hasil Filter')
                            ->content(fn() => $this->getFilteredCount() . ' variabel'),

                        Forms\Components\Placeholder::make('quickActions')
                            ->label('Filter Cepat')
                            ->content(new HtmlString('
                                <div class="flex gap-2 flex-wrap">
                                    <button wire:click="filterByCategory(\'app\')" class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded hover:bg-blue-200">App</button>
                                    <button wire:click="filterByCategory(\'database\')" class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded hover:bg-green-200">Database</button>
                                    <button wire:click="filterByCategory(\'email\')" class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded hover:bg-purple-200">Email</button>
                                    <button wire:click="filterByCategory(\'keamanan\')" class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded hover:bg-red-200">Keamanan</button>
                                </div>
                            ')),
                    ]),
            ])
            ->collapsible()
            ->collapsed(false);
    }

    protected function getEnvironmentStatsSection(): Component
    {
        $stats = $this->getEnvironmentStats();
        return Forms\Components\Section::make('Statistik Lingkungan')
            ->schema([
                Forms\Components\Grid::make(3)
                    ->schema([
                        Forms\Components\Placeholder::make('total')
                            ->label('Total Variabel')
                            ->content(new HtmlString("<span class='text-2xl font-bold text-primary-600'>{$stats['total']}</span>")),
                        Forms\Components\Placeholder::make('empty')
                            ->label('Variabel Kosong')
                            ->content(new HtmlString("<span class='text-2xl font-bold text-danger-600'>{$stats['empty']}</span>")),
                        Forms\Components\Placeholder::make('categories')
                            ->label('Kategori')
                            ->content(new HtmlString("<span class='text-2xl font-bold text-success-600'>{$stats['categories']}</span>")),
                    ])
            ])
            ->collapsible()
            ->collapsed(true);
    }    /**
         * @return list<Component>
         */
    protected function getFirstTab(): array
    {
        $envData = $this->getFilteredAndSortedEnvData();

        $header = Forms\Components\Group::make([
            Forms\Components\Actions::make([
                $this->createCustomCreateAction(),
            ])->alignEnd(),
        ]);

        return [
            $header,
            Forms\Components\Section::make('Variabel Lingkungan')
                ->schema([
                    Forms\Components\Tabs::make('env_tabs')
                        ->tabs([
                            Forms\Components\Tabs\Tab::make('by_category')
                                ->label('Berdasarkan Kategori')
                                ->schema($this->getEnvironmentVariablesByCategory($envData)),

                            Forms\Components\Tabs\Tab::make('all_variables')
                                ->label('Semua Variabel')
                                ->schema($this->getAllEnvironmentVariables($envData)),
                        ])
                ]),
        ];
    }

    /**
     * @return list<Component>
     */
    protected function getSecondTab(): array
    {
        $data = EnvEditor::getAllBackUps()
            ->map(function (BackupObj $obj) {
                return Forms\Components\Group::make([
                    Forms\Components\Actions::make([
                        DeleteBackupAction::make("delete_{$obj->name}")->setEntry($obj),
                        DownloadEnvFileAction::make("download_{$obj->name}")->setEntry($obj->name)->hiddenLabel()->size(ActionSize::Small),
                        RestoreBackupAction::make("restore_{$obj->name}")->setEntry($obj->name),
                        ShowBackupContentAction::make("show_raw_content_{$obj->name}")->setEntry($obj),
                    ])->alignEnd(),
                    Forms\Components\Placeholder::make('name')
                        ->label('')
                        ->content(new HtmlString("<strong>{$obj->name}</strong>"))
                        ->columnSpan(2),
                    Forms\Components\Placeholder::make('created_at')
                        ->label('')
                        ->content(function () use ($obj) {
                            $timezone = config('app.timezone', env('APP_TIMEZONE', 'UTC'));
                            return $obj->createdAt->setTimezone($timezone)->format('Y-m-d H:i:s T');
                        })
                        ->columnSpan(2),
                ])->columns(5);
            })->all();

        $header = Forms\Components\Group::make([
            Forms\Components\Actions::make([
                DownloadEnvFileAction::make('download_current')->tooltip('Download current .env file')->outlined(false),
                $this->createCustomUploadBackupAction(),
                $this->createCustomMakeBackupAction(),
            ])->alignEnd(),
        ]);

        return [$header, ...$data];
    }

    protected function getFilteredAndSortedEnvData(): Collection
    {
        $envData = EnvEditor::getEnvFileContent()
            ->filter(fn(EntryObj $obj) => !$obj->isSeparator())
            ->filter(fn(EntryObj $obj) => $this->matchesSearchAndFilter($obj));

        // Apply sorting
        return $envData->sortBy(function (EntryObj $obj) {
            return match ($this->sortBy) {
                'key' => $obj->key,
                'value' => $obj->getValue() ?? '',
                'category' => $this->getVariableCategory($obj->key),
                default => $obj->key,
            };
        }, SORT_REGULAR, $this->sortDirection === 'desc');
    }
    protected function getEnvironmentVariablesByCategory(Collection $envData): array
    {
        $groupedData = $envData->groupBy(fn(EntryObj $obj) => $this->getVariableCategory($obj->key));
        $sections = [];

        foreach ($groupedData as $category => $variables) {
            $categoryColor = $this->getCategoryColor($category);
            $count = $variables->count();

            $categoryIcon = $this->getCategoryIcon($category);

            $sections[] = Forms\Components\Section::make(
                new HtmlString("
                    <div class='flex items-center gap-2'>
                        <span class='text-lg'>" . e($categoryIcon) . "</span>
                        <span class='font-semibold'>" . e(ucfirst($category)) . "</span>
                        <span class='inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{$categoryColor}-100 text-{$categoryColor}-800'>
                            " . e($count) . " variabel
                        </span>
                    </div>
                ")
            )
                ->description("Variabel lingkungan dalam kategori " . e($category))
                ->schema($this->createVariableFields($variables))
                ->collapsible()
                ->collapsed($category === 'lainnya');
        }

        return $sections;
    }

    protected function getAllEnvironmentVariables(Collection $envData): array
    {
        return $this->createVariableFields($envData);
    }

    protected function createVariableFields(Collection $variables): array
    {
        return $variables
            ->reject(fn(EntryObj $obj) => $this->shouldHideEnvVariable($obj->key))
            ->map(function (EntryObj $obj) {
                $color = $this->getVariableColor($obj->key);
                $displayValue = $obj->getValue();

                return Forms\Components\Group::make([
                    Forms\Components\Grid::make(3)
                        ->schema([
                            Forms\Components\Placeholder::make("key_{$obj->key}")
                                ->label('Kunci')
                                ->content(new HtmlString("
                                    <div class='flex items-center gap-2'>
                                        <span class='inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{$color}-100 text-{$color}-800'>
                                            " . e($this->getVariableCategory($obj->key)) . "
                                        </span>
                                        <code class='font-mono text-sm'>" . e($obj->key) . "</code>
                                        " . ($this->isSensitiveKey($obj->key) ? '<span class="text-warning-500 text-xs">🔒 Sensitif</span>' : '') . "
                                        " . ($this->isCriticalSystemKey($obj->key) ? '<span class="text-red-500 text-xs">🛡️ Kritis</span>' : '') . "
                                    </div>
                                ")),

                            Forms\Components\Placeholder::make("value_{$obj->key}")
                                ->label('Nilai')
                                ->content(new HtmlString("
                                    <div class='font-mono text-sm p-2 bg-gray-50 rounded border'>
                                        " . (empty($obj->getValue()) ? '<em class="text-gray-400">Kosong</em>' : e($this->maskSensitiveValue($obj->key, $displayValue))) . "
                                    </div>
                                ")),

                            Forms\Components\Actions::make([
                                $this->createCustomEditAction($obj),
                                ...($this->isCriticalSystemKey($obj->key) ? [] : [$this->createCustomDeleteAction($obj)]),
                            ])->alignEnd(),
                        ]),
                ])->columnSpanFull();
            })
            ->toArray();
    }

    // Helper Methods
    private function shouldHideEnvVariable(string $key): bool
    {
        return in_array($key, \GeoSot\FilamentEnvEditor\FilamentEnvEditorPlugin::get()->getHiddenKeys());
    }

    private function matchesSearchAndFilter(EntryObj $obj): bool
    {
        // Search filter
        $searchMatch = empty($this->searchTerm) ||
            str_contains(strtolower($obj->key), strtolower($this->searchTerm)) ||
            str_contains(strtolower($obj->getValue() ?? ''), strtolower($this->searchTerm));

        // Category filter
        $categoryMatch = empty($this->categoryFilter) ||
            $this->getVariableCategory($obj->key) === $this->categoryFilter;

        // Empty variables filter
        $emptyMatch = $this->showEmpty || !empty($obj->getValue());

        return $searchMatch && $categoryMatch && $emptyMatch;
    }
    private function getVariableCategory(string $key): string
    {
        $key = strtoupper($key);

        return match (true) {
            // Security keys should be checked first before APP_ prefix
            $key === 'APP_KEY' || str_starts_with($key, 'JWT_') || str_starts_with($key, 'ENCRYPTION_') => 'keamanan',
            str_starts_with($key, 'APP_') => 'app',
            str_starts_with($key, 'DB_') || str_starts_with($key, 'DATABASE_') => 'database',
            str_starts_with($key, 'MAIL_') => 'email',
            str_starts_with($key, 'CACHE_') || str_starts_with($key, 'REDIS_') => 'cache',
            str_starts_with($key, 'QUEUE_') => 'antrian',
            str_starts_with($key, 'SESSION_') => 'sesi',
            str_starts_with($key, 'API_') || str_starts_with($key, 'STRIPE_') || str_starts_with($key, 'PAYPAL_') || str_starts_with($key, 'GOOGLE_') || str_starts_with($key, 'FACEBOOK_') => 'api',
            str_starts_with($key, 'AWS_') || str_starts_with($key, 'FILESYSTEM_') || str_starts_with($key, 'STORAGE_') => 'penyimpanan',
            str_starts_with($key, 'LOG_') => 'logging',
            default => 'lainnya',
        };
    }

    private function getVariableColor(string $key): string
    {
        return match ($this->getVariableCategory($key)) {
            'app' => 'blue',
            'database' => 'green',
            'email' => 'purple',
            'cache' => 'orange',
            'antrian' => 'yellow',
            'sesi' => 'pink',
            'keamanan' => 'red',
            'api' => 'indigo',
            'penyimpanan' => 'cyan',
            'logging' => 'gray',
            default => 'slate',
        };
    }

    private function getCategoryColor(string $category): string
    {
        return match ($category) {
            'app' => 'primary',
            'database' => 'success',
            'email' => 'info',
            'cache' => 'warning',
            'antrian' => 'danger',
            'sesi' => 'gray',
            'keamanan' => 'danger',
            'api' => 'info',
            'penyimpanan' => 'success',
            'logging' => 'gray',
            default => 'gray',
        };
    }

    private function getCategoryIcon(string $category): string
    {
        return match ($category) {
            'app' => '⚙️',
            'database' => '🗄️',
            'email' => '📧',
            'cache' => '🚀',
            'antrian' => '📋',
            'sesi' => '🔐',
            'keamanan' => '🛡️',
            'api' => '🔌',
            'penyimpanan' => '💾',
            'logging' => '📝',
            default => '📦',
        };
    }

    private function isSensitiveKey(string $key): bool
    {
        $sensitiveKeys = [
            'APP_KEY',
            'DB_PASSWORD',
            'JWT_SECRET',
            'ENCRYPTION_KEY',
            'MAIL_PASSWORD',
            'REDIS_PASSWORD',
            'STRIPE_SECRET',
            'PAYPAL_SECRET',
            'GOOGLE_CLIENT_SECRET',
            'FACEBOOK_CLIENT_SECRET',
            'AWS_SECRET_ACCESS_KEY',
            'PUSHER_APP_SECRET'
        ];

        return in_array(strtoupper($key), $sensitiveKeys) ||
            str_contains(strtolower($key), 'password') ||
            str_contains(strtolower($key), 'secret') ||
            str_contains(strtolower($key), 'key');
    }
    private function isCriticalSystemKey(string $key): bool
    {
        $criticalKeys = [
            // Core Laravel Application Keys
            'APP_KEY',
            'APP_ENV',
            'APP_DEBUG',
            'APP_URL',

            // Database Configuration
            'DB_CONNECTION',
            'DB_HOST',
            'DB_PORT',
            'DB_DATABASE',
            'DB_USERNAME',
            'DB_PASSWORD',

            // Cache and Session
            'CACHE_DRIVER',
            'SESSION_DRIVER',
            'SESSION_LIFETIME',

            // Queue Configuration
            'QUEUE_CONNECTION',

            // Mail Configuration
            'MAIL_MAILER',
            'MAIL_HOST',
            'MAIL_PORT',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',

            // Broadcasting
            'BROADCAST_DRIVER',

            // Logging
            'LOG_CHANNEL',

            // Security Critical
            'BCRYPT_ROUNDS',
            'SANCTUM_STATEFUL_DOMAINS',
        ];

        return in_array(strtoupper($key), $criticalKeys);
    }

    /**
     * Get helper text for the key input based on whether it's critical or not
     */
    protected function getKeyHelperText(string $key): string
    {
        if ($this->isCriticalSystemKey($key)) {
            return '🔒 Ini adalah kunci sistem kritis dan tidak dapat diubah namanya untuk melindungi aplikasi Anda.';
        }

        return '✏️ Anda dapat mengubah nama kunci. Gunakan huruf KAPITAL, angka, dan garis bawah saja.';
    }

    private function getEnvironmentStats(): array
    {
        $envData = EnvEditor::getEnvFileContent()
            ->filter(fn(EntryObj $obj) => !$obj->isSeparator());
        $total = $envData->count();
        $empty = $envData->filter(fn(EntryObj $obj) => empty($obj->getValue()))->count();
        $categories = $envData->groupBy(fn(EntryObj $obj) => $this->getVariableCategory($obj->key))->count();

        return compact('total', 'empty', 'categories');
    }

    private function getFilteredCount(): int
    {
        return $this->getFilteredAndSortedEnvData()->count();
    }    // Livewire Actions
    public function resetPage(): void
    {
        // Just refresh the component without saving
    }

    public function filterByCategory(string $category): void
    {
        $this->categoryFilter = $this->categoryFilter === $category ? '' : $category;
    }

    public function updatedSearchTerm(): void
    {
        // Validate search term length to prevent DoS attacks
        if (strlen($this->searchTerm) > 100) {
            $this->searchTerm = substr($this->searchTerm, 0, 100);

            Notification::make()
                ->title('Kata Pencarian Terlalu Panjang')
                ->body('Kata pencarian telah dipotong menjadi 100 karakter.')
                ->warning()
                ->send();
        }
    }

    public function updatedCategoryFilter(): void
    {
        // This method is called automatically when categoryFilter changes  
    }

    public function updatedSortBy(): void
    {
        // This method is called automatically when sortBy changes
    }

    public function updatedSortDirection(): void
    {
        // This method is called automatically when sortDirection changes
    }
    public function updatedShowEmpty(): void
    {
        // This method is called automatically when showEmpty changes
    }

    /**
     * Get backup file content from storage
     */
    protected function getBackupFileContent(string $backupFile): string
    {
        // Validate and sanitize the backup filename
        $sanitizedBackupFile = $this->validateAndSanitizeBackupFilename($backupFile);

        $backupPath = storage_path('env-editor/' . $sanitizedBackupFile);

        if (!file_exists($backupPath) || !is_readable($backupPath)) {
            throw new \Exception("File cadangan tidak ditemukan atau tidak dapat dibaca: " . e($sanitizedBackupFile));
        }

        $content = file_get_contents($backupPath);

        if ($content === false) {
            throw new \Exception("Tidak dapat membaca file cadangan: " . e($sanitizedBackupFile));
        }

        return $content;
    }

    /**
     * Validate environment variable key format
     */
    protected function validateEnvKey(string $key): bool
    {
        // Environment variable keys should:
        // - Start with a letter or underscore
        // - Only contain uppercase letters, numbers, and underscores
        // - Not be empty
        return !empty($key) && preg_match('/^[A-Z_][A-Z0-9_]*$/', $key);
    }

    /**
     * Get validation error message for environment key
     */
    protected function getEnvKeyValidationMessage(): string
    {
        return 'Kunci variabel lingkungan harus dimulai dengan huruf atau garis bawah, hanya boleh berisi huruf kapital, angka, dan garis bawah.';
    }

    /**
     * Check if environment variable key already exists
     */
    protected function keyExists(string $key, ?string $excludeKey = null): bool
    {
        $envData = EnvEditor::getEnvFileContent();

        foreach ($envData as $entry) {
            // Skip separators and the excluded key (for rename operations)
            if ($entry->isSeparator() || ($excludeKey && $entry->key === $excludeKey)) {
                continue;
            }

            if ($entry->key === $key) {
                return true;
            }
        }

        return false;
    }    /**
         * Create custom CreateAction with validation
         */
    protected function createCustomCreateAction(): FormAction
    {
        return FormAction::make('add_new')
            ->label('Tambah Variabel Baru')
            ->icon('heroicon-o-plus')
            ->color('success')
            ->form([
                Forms\Components\TextInput::make('key')
                    ->label('Kunci')
                    ->required()
                    ->rules([
                        function () {
                            return function (string $attribute, $value, \Closure $fail) {
                                if (!$this->validateEnvKey($value)) {
                                    $fail($this->getEnvKeyValidationMessage());
                                }

                                if ($this->keyExists($value)) {
                                    $fail('Kunci variabel lingkungan ini sudah ada.');
                                }
                            };
                        },
                    ])
                    ->validationMessages([
                        'required' => 'Kunci variabel lingkungan wajib diisi.',
                    ])
                    ->placeholder('contoh: VARIABEL_BARU_SAYA')
                    ->helperText('Gunakan huruf KAPITAL, angka, dan garis bawah saja.'),

                Forms\Components\TextInput::make('value')
                    ->label('Nilai')
                    ->placeholder('Nilai variabel')
                    ->helperText('Masukkan nilai untuk variabel lingkungan ini.'),
            ])
            ->action(function (array $data) {
                try {
                    // Create backup before making changes
                    $backupResult = $this->createTimezoneAwareBackup();

                    // Sanitize inputs
                    $sanitizedKey = $this->sanitizeEnvKey($data['key']);
                    $sanitizedValue = $this->sanitizeEnvValue($data['value'] ?? '');

                    $result = EnvEditor::addKey($sanitizedKey, $sanitizedValue);

                    if ($result) {
                        Notification::make()
                            ->title('Berhasil')
                            ->body("Variabel lingkungan '" . e($sanitizedKey) . "' berhasil ditambahkan. Cadangan otomatis dibuat.")
                            ->success()
                            ->send();

                        // Refresh the page data
                        $this->dispatch('$refresh');
                    } else {
                        throw new \Exception('Gagal menambahkan variabel lingkungan');
                    }
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Kesalahan')
                        ->body('Gagal menambahkan variabel lingkungan: ' . e($e->getMessage()))
                        ->danger()
                        ->send();
                }
            })
            ->modalWidth(MaxWidth::Large)
            ->modalHeading('Tambah Variabel Lingkungan Baru')
            ->modalDescription('Buat variabel lingkungan baru untuk aplikasi Anda.')
            ->modalIcon('heroicon-o-plus-circle');
    }    /**
         * Create custom EditAction with validation for both key and value editing
         */
    protected function createCustomEditAction(EntryObj $obj): FormAction
    {
        return FormAction::make("edit_{$obj->key}")
            ->label('Edit')
            ->icon('heroicon-o-pencil')
            ->size(ActionSize::Small)
            ->form([
                Forms\Components\TextInput::make('key')
                    ->label('Kunci')
                    ->default($obj->key)
                    ->required()
                    ->disabled($this->isCriticalSystemKey($obj->key))
                    ->rules([
                        function () use ($obj) {
                            return function (string $attribute, $value, \Closure $fail) use ($obj) {
                                // Skip validation if key hasn't changed
                                if ($value === $obj->key) {
                                    return;
                                }

                                // Prevent renaming critical system keys
                                if ($this->isCriticalSystemKey($obj->key)) {
                                    $fail('Kunci sistem ini tidak dapat diubah namanya karena alasan keamanan.');
                                }

                                // Validate key format
                                if (!$this->validateEnvKey($value)) {
                                    $fail($this->getEnvKeyValidationMessage());
                                }

                                // Check if new key already exists
                                if ($this->keyExists($value, $obj->key)) {
                                    $fail('Kunci variabel lingkungan ini sudah ada. Silakan pilih nama yang berbeda.');
                                }
                            };
                        },
                    ])
                    ->helperText($this->getKeyHelperText($obj->key))
                    ->suffixIcon($this->isCriticalSystemKey($obj->key) ? 'heroicon-o-lock-closed' : 'heroicon-o-pencil'),

                Forms\Components\TextInput::make('value')
                    ->label('Nilai')
                    ->default($obj->getValue())
                    ->helperText('Masukkan nilai untuk variabel lingkungan ini'),

                Forms\Components\Placeholder::make('warning')
                    ->label('⚠️ Pemberitahuan Penting')
                    ->content(function () use ($obj) {
                        return new HtmlString('
                            <div class="p-3 bg-yellow-50 border border-yellow-200 rounded-md">
                                <p class="text-sm text-yellow-800">
                                    <strong>Warning:</strong> Changing key names may break your application if the new key name is not recognized by your code.
                                    An automatic backup will be created before making changes.
                                </p>
                                <p class="text-xs text-yellow-700 mt-2">
                                    💾 An automatic backup will be created before making changes.
                                </p>
                            </div>
                        ');
                    })
                    ->visible(fn(callable $get) => $get('key') !== $obj->key && !$this->isCriticalSystemKey($obj->key)),
            ])
            ->action(function (array $data) use ($obj) {
                try {
                    // For critical system keys, use the original key since the field is disabled
                    $sanitizedNewKey = $this->isCriticalSystemKey($obj->key)
                        ? $obj->key
                        : $this->sanitizeEnvKey($data['key'] ?? $obj->key);
                    $sanitizedNewValue = $this->sanitizeEnvValue($data['value'] ?? '');
                    $oldKey = $obj->key;

                    // Always create backup before making changes
                    $backupResult = $this->createTimezoneAwareBackup();

                    if ($sanitizedNewKey !== $oldKey) {
                        // Key name changed - preserve position in .env file
                        $result = $this->updateEnvironmentVariablePreservePosition($oldKey, $sanitizedNewKey, $sanitizedNewValue);

                        if (!$result) {
                            throw new \Exception('Gagal mengubah nama variabel lingkungan sambil mempertahankan posisi');
                        }

                        Notification::make()
                            ->title('Berhasil')
                            ->body("Variabel lingkungan berhasil diubah namanya dari '" . e($oldKey) . "' menjadi '" . e($sanitizedNewKey) . "' dan diperbarui. Posisi terjaga. Cadangan otomatis dibuat.")
                            ->success()
                            ->send();
                    } else {
                        // Only value changed
                        $result = EnvEditor::editKey($oldKey, $sanitizedNewValue);
                        if (!$result) {
                            throw new \Exception('Gagal memperbarui variabel lingkungan');
                        }

                        Notification::make()
                            ->title('Berhasil')
                            ->body("Variabel lingkungan '" . e($oldKey) . "' berhasil diperbarui. Cadangan otomatis dibuat.")
                            ->success()
                            ->send();
                    }

                    // Refresh the page data
                    $this->dispatch('$refresh');
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Kesalahan')
                        ->body('Gagal memperbarui variabel lingkungan: ' . e($e->getMessage()))
                        ->danger()
                        ->send();
                }
            })
            ->modalWidth(MaxWidth::Large)
            ->requiresConfirmation(fn(callable $get) => $get('key') !== $obj->key && !$this->isCriticalSystemKey($obj->key))
            ->modalHeading('Edit Variabel Lingkungan')
            ->modalDescription(function (callable $get) use ($obj) {
                if ($get('key') !== $obj->key && !$this->isCriticalSystemKey($obj->key)) {
                    return "Anda akan mengubah nama '" . e($obj->key) . "' menjadi '" . e($get('key')) . "'. Tindakan ini akan membuat cadangan otomatis dan mempertahankan posisi variabel dalam file .env.";
                }
                return 'Edit nilai variabel lingkungan.';
            })
            ->modalIcon('heroicon-o-pencil-square');
    }

    /**
     * Create custom DeleteAction with confirmation and backup
     */
    protected function createCustomDeleteAction(EntryObj $obj): FormAction
    {
        return FormAction::make("delete_{$obj->key}")
            ->label('Hapus')
            ->icon('heroicon-o-trash')
            ->size(ActionSize::Small)
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Hapus Variabel Lingkungan')
            ->modalDescription("Apakah Anda yakin ingin menghapus variabel lingkungan '" . e($obj->key) . "'?")
            ->modalSubmitActionLabel('Ya, Hapus')
            ->modalIcon('heroicon-o-exclamation-triangle')
            ->form([
                Forms\Components\Placeholder::make('warning')
                    ->label('⚠️ Peringatan')
                    ->content(function () use ($obj) {
                        return new HtmlString('
                            <div class="p-3 bg-red-50 border border-red-200 rounded-md">
                                <p class="text-sm text-red-800 mb-2">
                                    <strong>Anda akan menghapus:</strong>
                                </p>
                                <div class="bg-white p-2 rounded border font-mono text-sm">
                                    <strong>' . e($obj->key) . '</strong> = ' . e($obj->getValue() ?: '(kosong)') . '
                                </div>
                                <p class="text-sm text-red-800 mt-2">
                                    Tindakan ini tidak dapat dibatalkan, tetapi cadangan otomatis akan dibuat sebelum penghapusan.
                                </p>
                                <p class="text-xs text-red-700 mt-2">
                                    💾 Cadangan otomatis akan dibuat sebelum melakukan perubahan.
                                </p>
                            </div>
                        ');
                    }),
            ])
            ->action(function () use ($obj) {
                try {
                    // Always create backup before making changes
                    $backupResult = $this->createTimezoneAwareBackup();

                    // Delete the environment variable
                    $result = EnvEditor::deleteKey($obj->key);

                    if (!$result) {
                        throw new \Exception('Gagal menghapus variabel lingkungan');
                    }

                    Notification::make()
                        ->title('Berhasil')
                        ->body("Variabel lingkungan '" . e($obj->key) . "' berhasil dihapus. Cadangan otomatis dibuat.")
                        ->success()
                        ->send();

                    // Refresh the page data
                    $this->dispatch('$refresh');
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Kesalahan')
                        ->body('Gagal menghapus variabel lingkungan: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    /**
     * Perform secure backup restoration
     */
    protected function performBackupRestore(string $backupFile): void
    {
        try {
            // Validate and sanitize backup filename
            $sanitizedBackupFile = $this->validateAndSanitizeBackupFilename($backupFile);

            // Use the EnvEditor's restore functionality
            $result = EnvEditor::restoreBackUp($sanitizedBackupFile);

            if ($result) {
                Notification::make()
                    ->title('Cadangan Dipulihkan')
                    ->body("Berhasil memulihkan cadangan: " . e($sanitizedBackupFile))
                    ->success()
                    ->send();

                $this->dispatch('$refresh');
            } else {
                throw new \Exception('Gagal memulihkan cadangan');
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Pemulihan Gagal')
                ->body('Tidak dapat memulihkan cadangan: ' . e($e->getMessage()))
                ->danger()
                ->send();
        }
    }

    /**
     * Validate and sanitize backup filename to prevent path traversal
     */
    protected function validateAndSanitizeBackupFilename(string $filename): string
    {
        // Remove any path separators and null bytes
        $sanitized = str_replace(['/', '\\', "\0", '..'], '', $filename);

        // Only allow alphanumeric characters, dots, hyphens, and underscores
        $sanitized = preg_replace('/[^a-zA-Z0-9._-]/', '', $sanitized);

        // Ensure filename is not empty after sanitization
        if (empty($sanitized)) {
            throw new \Exception('Nama file cadangan tidak valid');
        }

        // Additional validation - check if file actually exists in backup directory
        $backupPath = storage_path('env-editor/' . $sanitized);
        if (!file_exists($backupPath) || !is_readable($backupPath)) {
            throw new \Exception('File cadangan tidak ditemukan atau tidak dapat dibaca');
        }

        return $sanitized;
    }

    /**
     * Sanitize environment variable key
     */
    protected function sanitizeEnvKey(string $key): string
    {
        // Remove any potentially dangerous characters
        $sanitized = preg_replace('/[^A-Z0-9_]/', '', strtoupper($key));

        // Ensure key starts with letter or underscore
        if (!preg_match('/^[A-Z_]/', $sanitized)) {
            throw new \Exception('Kunci variabel lingkungan harus dimulai dengan huruf atau garis bawah');
        }

        return $sanitized;
    }

    /**
     * Sanitize environment variable value
     */
    protected function sanitizeEnvValue(string $value): string
    {
        // Remove null bytes and other potentially dangerous characters
        $sanitized = str_replace(["\0", "\r"], '', $value);

        // Limit length to prevent DoS attacks
        if (strlen($sanitized) > 4096) {
            throw new \Exception('Nilai variabel lingkungan terlalu panjang (maksimal 4096 karakter)');
        }

        return $sanitized;
    }

    /**
     * Mask sensitive values for display
     */
    protected function maskSensitiveValue(string $key, ?string $value): string
    {
        if (empty($value)) {
            return '';
        }

        if ($this->isSensitiveKey($key)) {
            // Show first 2 and last 2 characters for very short values
            if (strlen($value) <= 8) {
                return str_repeat('*', strlen($value));
            }

            // Show first 3 and last 3 characters with asterisks in between
            return substr($value, 0, 3) . str_repeat('*', max(4, strlen($value) - 6)) . substr($value, -3);
        }

        return $value;
    }

    /**
     * Show confirmation dialog for backup restoration
     */
    protected function showRestoreConfirmation(string $backupFile): void
    {
        Notification::make()
            ->title('Konfirmasi Pemulihan Cadangan')
            ->body("Apakah Anda yakin ingin memulihkan '" . e($backupFile) . "' dan mengganti file .env saat ini?")
            ->warning()
            ->persistent()
            ->actions([
                \Filament\Notifications\Actions\Action::make('confirm_restore')
                    ->button()
                    ->label('Ya, Pulihkan')
                    ->color('danger')
                    ->action(function () use ($backupFile) {
                        $this->performBackupRestore($backupFile);
                    }),
                \Filament\Notifications\Actions\Action::make('cancel_restore')
                    ->button()
                    ->label('Batal')
                    ->color('gray')
                    ->close(),
            ])
            ->send();
    }

    /**
     * Update environment variable while preserving its position in the .env file
     */
    protected function updateEnvironmentVariablePreservePosition(string $oldKey, string $newKey, string $newValue): bool
    {
        try {
            // Read current .env content
            $envPath = app()->environmentFilePath();
            $content = file_get_contents($envPath);

            if ($content === false) {
                return false;
            }

            $lines = explode("\n", $content);
            $updated = false;

            // Find and update the specific line
            foreach ($lines as $index => $line) {
                $trimmedLine = trim($line);

                // Skip empty lines and comments
                if (empty($trimmedLine) || str_starts_with($trimmedLine, '#')) {
                    continue;
                }

                // Check if this line contains our target key
                if (str_contains($trimmedLine, '=')) {
                    [$currentKey] = explode('=', $trimmedLine, 2);
                    $currentKey = trim($currentKey);

                    if ($currentKey === $oldKey) {
                        // Quote the value if it contains spaces or special characters
                        $quotedValue = $newValue;
                        if (str_contains($newValue, ' ') || str_contains($newValue, '#') || str_contains($newValue, '"')) {
                            $quotedValue = '"' . str_replace('"', '\\"', $newValue) . '"';
                        }

                        // Replace the line
                        $lines[$index] = $newKey . '=' . $quotedValue;
                        $updated = true;
                        break;
                    }
                }
            }

            if ($updated) {
                // Write the updated content back
                $newContent = implode("\n", $lines);
                return file_put_contents($envPath, $newContent) !== false;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Create backup with timezone-aware naming
     */
    protected function createTimezoneAwareBackup(): bool
    {
        try {
            // Get the timezone from environment or fallback to UTC
            $timezone = config('app.timezone', env('APP_TIMEZONE', 'UTC'));

            // Create filename with timezone-aware timestamp
            $timestamp = now()->setTimezone($timezone)->format('Y_m_d_H_i_s');
            $backupName = "backup_{$timestamp}.env";

            // Get current .env content
            $envContent = file_get_contents(app()->environmentFilePath());

            if ($envContent === false) {
                throw new \Exception('Tidak dapat membaca file .env saat ini');
            }

            // Ensure backup directory exists
            $backupDir = storage_path('env-editor');
            if (!is_dir($backupDir)) {
                if (!mkdir($backupDir, 0755, true)) {
                    throw new \Exception('Tidak dapat membuat direktori backup');
                }
            }

            // Write backup file
            $backupPath = $backupDir . DIRECTORY_SEPARATOR . $backupName;
            $result = file_put_contents($backupPath, $envContent);

            if ($result === false) {
                throw new \Exception('Gagal menulis file backup');
            }

            return true;

        } catch (\Exception $e) {
            // Log error for debugging
            Log::error('Backup creation failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create custom Make Backup Action with timezone-aware naming
     */
    protected function createCustomMakeBackupAction(): FormAction
    {
        return FormAction::make('custom_backup')
            ->label('Buat Cadangan')
            ->icon('heroicon-o-document-duplicate')
            ->color('info')
            ->action(function () {
                try {
                    $result = $this->createTimezoneAwareBackup();

                    if ($result) {
                        // Get timezone for notification
                        $timezone = config('app.timezone', env('APP_TIMEZONE', 'UTC'));
                        $timestamp = now()->setTimezone($timezone)->format('Y-m-d H:i:s T');

                        Notification::make()
                            ->title('Cadangan Berhasil Dibuat')
                            ->body("Cadangan file .env berhasil dibuat pada {$timestamp}")
                            ->success()
                            ->send();

                        // Refresh the page data
                        $this->dispatch('$refresh');
                    } else {
                        throw new \Exception('Gagal membuat cadangan');
                    }
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Gagal Membuat Cadangan')
                        ->body('Tidak dapat membuat cadangan: ' . $e->getMessage())
                        ->danger()
                        ->send();
                }
            })
            ->tooltip('Buat cadangan dari file .env saat ini dengan timestamp sesuai timezone');
    }

    /**
     * Create custom Upload Backup Action with proper naming
     */
    protected function createCustomUploadBackupAction(): FormAction
    {
        return FormAction::make('custom_upload')
            ->label('Unggah Cadangan')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('warning')
            ->form([
                Forms\Components\FileUpload::make('backup_file')
                    ->label('File Cadangan')
                    ->acceptedFileTypes([]) // Accept all file types
                    ->required()
                    ->disk('local')
                    ->directory('livewire-tmp')
                    ->visibility('private')
                    ->maxSize(1024) // 1MB max
                    ->storeFileNamesIn('backup_file_names')
                    ->helperText('Pilih file untuk dijadikan cadangan environment'),
            ])
            ->action(function (array $data) {
                try {
                    if (!isset($data['backup_file']) || empty($data['backup_file'])) {
                        throw new \Exception('File cadangan tidak dipilih');
                    }

                    // Handle file upload - Filament may return array or string
                    $fileData = $data['backup_file'];
                    if (is_array($fileData)) {
                        $fileData = $fileData[0] ?? null;
                    }

                    if (!$fileData) {
                        throw new \Exception('File cadangan tidak valid');
                    }

                    // Use Storage facade to check if file exists
                    $disk = Storage::disk('local');

                    // Try different possible paths based on Livewire/Filament conventions
                    $possiblePaths = [
                        'livewire-tmp/' . $fileData,
                        $fileData,
                        'public/' . $fileData,
                        'temp-uploads/' . $fileData
                    ];

                    $uploadedFilePath = null;
                    $relativePath = null;

                    foreach ($possiblePaths as $path) {
                        if ($disk->exists($path)) {
                            $relativePath = $path;
                            $uploadedFilePath = storage_path('app/' . $path);
                            break;
                        }
                    }

                    if (!$uploadedFilePath) {
                        // Debug information - log more detailed info
                        $debugInfo = [
                            'received_data' => $data,
                            'file_data' => $fileData,
                            'tried_relative_paths' => $possiblePaths,
                            'storage_disk_files' => $disk->allFiles(),
                            'livewire_tmp_files' => $disk->exists('livewire-tmp') ? $disk->allFiles('livewire-tmp') : 'livewire-tmp not found'
                        ];

                        Log::warning('Upload file not found - debug info', $debugInfo);

                        throw new \Exception('File tidak ditemukan.');
                    }

                    // Read the uploaded file content using Storage
                    $content = $disk->get($relativePath);

                    if ($content === false || $content === null) {
                        throw new \Exception('Tidak dapat membaca file.');
                    }

                    // Validate that it's a valid .env file (basic validation)
                    // Note: We'll be more lenient now as we accept any file type
                    if (!$this->isValidEnvContent($content)) {
                        // If it's not a valid .env format, we'll still allow it but warn the user
                        Log::warning('Uploaded file may not be in standard .env format', [
                            'file' => $uploadedFilePath,
                            'content_preview' => substr($content, 0, 200)
                        ]);
                    }

                    // Create proper backup filename with timezone
                    $timezone = config('app.timezone', env('APP_TIMEZONE', 'UTC'));
                    $timestamp = now()->setTimezone($timezone)->format('Y_m_d_H_i_s');
                    $backupName = "uploaded_{$timestamp}.env";

                    // Ensure backup directory exists
                    $backupDir = storage_path('env-editor');
                    if (!is_dir($backupDir)) {
                        if (!mkdir($backupDir, 0755, true)) {
                            throw new \Exception('Tidak dapat membuat direktori backup');
                        }
                    }

                    // Copy to backup directory with proper name
                    $backupPath = $backupDir . DIRECTORY_SEPARATOR . $backupName;
                    $result = file_put_contents($backupPath, $content);

                    if ($result === false) {
                        throw new \Exception('Gagal menyimpan file.');
                    }

                    // Clean up temporary file using Storage
                    if ($relativePath) {
                        $disk->delete($relativePath);
                    }

                    // Success notification
                    Notification::make()
                        ->title('Upload Berhasil')
                        ->body("File berhasil disimpan sebagai '{$backupName}'")
                        ->success()
                        ->send();

                    // Refresh the page data
                    $this->dispatch('$refresh');

                } catch (\Exception $e) {
                    // Clean up temporary file if it exists
                    if (isset($relativePath) && $relativePath && isset($disk)) {
                        $disk->delete($relativePath);
                    }

                    Notification::make()
                        ->title('Upload Gagal')
                        ->body($e->getMessage())
                        ->danger()
                        ->send();
                }
            })
            ->modalWidth(MaxWidth::Large)
            ->modalHeading('Unggah File Cadangan')
            ->modalDescription('Unggah file untuk dijadikan cadangan environment.')
            ->modalIcon('heroicon-o-arrow-up-tray')
            ->tooltip('Upload cadangan environment');
    }

    /**
     * Validate if content is a valid .env file
     */
    protected function isValidEnvContent(string $content): bool
    {
        // Basic validation - check if it contains environment variable patterns
        $lines = explode("\n", $content);
        $validLines = 0;
        $totalNonEmptyLines = 0;

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip empty lines
            if (empty($line)) {
                continue;
            }

            $totalNonEmptyLines++;

            // Comments are valid
            if (str_starts_with($line, '#')) {
                $validLines++;
                continue;
            }

            // Environment variable format: KEY=VALUE
            if (str_contains($line, '=')) {
                [$key] = explode('=', $line, 2);
                $key = trim($key);

                // Check if key is valid environment variable name
                if (preg_match('/^[A-Za-z_][A-Za-z0-9_]*$/', $key)) {
                    $validLines++;
                }
            }
        }

        // Consider valid if at least 50% of non-empty lines are valid env format
        // or if file is empty (which is also a valid .env file)
        return $totalNonEmptyLines === 0 || ($validLines / $totalNonEmptyLines) >= 0.5;
    }
}