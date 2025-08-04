<?php

namespace App\Filament\Widgets\Admin;

use App\Models\User;
use Filament\Support\RawJs;
use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class UsersByRoleWidget extends ApexChartWidget
{
    protected static ?string $chartId = 'usersByRole';
    protected static ?string $heading = 'Pegawai berdasarkan role';
    protected static ?int $sort = 2;
    protected static bool $deferLoading = true;
    protected int|string|array $columnSpan = 'sm';
    protected static ?string $pollingInterval = '150s';


    public static function canView(): bool
    {
        return auth()->user()?->can('widget_UsersByRoleWidget');
    }

    protected function getOptions(): array
    {
        $roleData = DB::table('model_has_roles')
            ->join('roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->select('roles.name', DB::raw('count(*) as count'))
            ->groupBy('roles.name')
            ->get();

        return [
            'chart' => [
                'type' => 'pie',
                'height' => 300,
            ],
            'series' => $roleData->pluck('count')->toArray(),
            'labels' => $roleData->pluck('name')->toArray(),
            'legend' => [
                'position' => 'bottom',
                'horizontalAlign' => 'center',
            ],
            'colors' => ['#0ea5e9', '#22c55e', '#f59e0b', '#ef4444', '#6366f1'],
            'stroke' => [
                'show' => true,
                'width' => 2,
            ],
            'responsive' => [
                [
                    'breakpoint' => 480,
                    'options' => [
                        'chart' => [
                            'height' => 300
                        ],
                        'legend' => [
                            'position' => 'bottom'
                        ]
                    ]
                ]
            ]
        ];
    }

    protected function extraJsOptions(): ?RawJs
    {
        return RawJs::make(<<<'JS'
        {
            plotOptions: {
                pie: {
                    dataLabels: {
                        formatter: function (val, opts) {
                            return opts.w.config.labels[opts.seriesIndex] + '\n' + Math.round(val) + '%';
                        }
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return val + ' pengguna';
                    }
                }
            }
        }
        JS);
    }
}
