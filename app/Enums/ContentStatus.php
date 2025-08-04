<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum ContentStatus: string implements HasColor, HasIcon, HasLabel
{
    case TERPUBLIKASI = 'terpublikasi';
    case TIDAK_TERPUBLIKASI = 'tidak terpublikasi';

    public function getLabel(): string
    {
        return match ($this) {
            self::TERPUBLIKASI => 'Terpublikasi',
            self::TIDAK_TERPUBLIKASI => 'Tidak Terpublikasi',
        };
    }

    public function getColor(): string | array | null
    {
        return match ($this) {
            self::TERPUBLIKASI => 'success',
            self::TIDAK_TERPUBLIKASI => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::TERPUBLIKASI => 'heroicon-m-eye',
            self::TIDAK_TERPUBLIKASI => 'heroicon-m-eye-slash',
        };
    }
}