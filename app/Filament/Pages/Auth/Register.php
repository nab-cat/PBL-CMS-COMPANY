<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;
use DiogoGPinto\AuthUIEnhancer\Pages\Auth\Concerns\HasCustomLayout;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class Register extends BaseRegister
{
    use HasCustomLayout;

    protected function handleRegistration(array $data): Model
    {
        $data['status_kepegawaian'] = 'Non Pegawai';
        return $this->getUserModel()::create($data);
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(10);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        return parent::register();
    }

    protected function getNameFormComponent(): Component
    {
        return TextInput::make('name')
            ->label(__('filament-panels::pages/auth/register.form.name.label'))
            ->required()
            ->maxLength(255)
            ->autofocus()
            ->regex('/^[a-zA-Z\s\d]+$/')
            ->validationMessages([
                'regex' => 'Nama hanya boleh mengandung huruf, angka, dan spasi.',
                'required' => 'Nama wajib diisi.',
                'max' => 'Nama tidak boleh lebih dari 100 karakter.',
            ]);
    }

    protected function getEmailFormComponent(): Component
    {
        return TextInput::make('email')
            ->label(__('filament-panels::pages/auth/register.form.email.label'))
            ->email()
            ->required()
            ->maxLength(255)
            ->unique($this->getUserModel())
            ->validationMessages([
                'unique' => 'Email ini sudah terdaftar. Silakan gunakan email lain atau masuk dengan akun yang sudah ada.',
                'required' => 'Email wajib diisi.',
                'email' => 'Format email tidak valid.',
                'max' => 'Email tidak boleh lebih dari 150 karakter.',
            ]);
    }

    protected function getPasswordFormComponent(): Component
    {
        return TextInput::make('password')
            ->label(__('filament-panels::pages/auth/register.form.password.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->rule(Password::default())
            ->dehydrateStateUsing(fn($state) => Hash::make($state))
            ->same('passwordConfirmation')
            ->validationAttribute(__('filament-panels::pages/auth/register.form.password.validation_attribute'))
            ->validationMessages([
                'min' => 'Password harus memiliki minimal 8 karakter.',
                'required' => 'Password wajib diisi.',
                'same' => 'Konfirmasi password tidak cocok dengan password.',
            ]);
    }

    protected function getPasswordConfirmationFormComponent(): Component
    {
        return TextInput::make('passwordConfirmation')
            ->label(__('filament-panels::pages/auth/register.form.password_confirmation.label'))
            ->password()
            ->revealable(filament()->arePasswordsRevealable())
            ->required()
            ->dehydrated(false)
            ->validationMessages([
                'required' => 'Konfirmasi password wajib diisi.',
            ]);
    }
}
