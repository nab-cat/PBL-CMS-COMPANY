<?php

namespace App\Filament\Pages\Auth\EmailVerification;

use Filament\Pages\Auth\EmailVerification\EmailVerificationPrompt as BaseEmailVerificationPrompt;
use DiogoGPinto\AuthUIEnhancer\Pages\Auth\Concerns\HasCustomLayout;

class EmailVerificationPrompt extends BaseEmailVerificationPrompt
{
    use HasCustomLayout;
}
