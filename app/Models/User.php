<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_user';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'foto_profil',
        'alamat',
        'no_hp',
        'nik',
        'tanggal_lahir',
        'status_kepegawaian',
        'status',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tanggal_lahir' => 'date',
        ];
    }
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->foto_profil
            ? asset('storage/' . $this->foto_profil)
            : null;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Izinkan akses berdasarkan status aktif dan status kepegawaian saja
        return $this->status === 'aktif' && $this->status_kepegawaian !== 'Non Pegawai' || $this->status_kepegawaian !== null;
    }

    /**
     * Get the struktur organisasi for this user
     */
    public function strukturOrganisasi()
    {
        return $this->hasOne(StrukturOrganisasi::class, 'id_user', 'id_user');
    }

    /**
     * Get suggested jabatan based on user's primary role
     */
    public function getSuggestedJabatan(): string
    {
        $primaryRole = $this->roles->first();

        if (!$primaryRole) {
            return 'Staff';
        }

        return match ($primaryRole->name) {
            'super_admin' => 'Super Administrator',
            'Director' => 'Direktur',
            'Content Management' => 'Manager Konten',
            'Customer Service' => 'Customer Service',
            default => ucwords(str_replace('_', ' ', $primaryRole->name))
        };
    }

    /**
     * Get primary role name
     */
    public function getPrimaryRoleName(): ?string
    {
        return $this->roles->first()?->name;
    }
}
