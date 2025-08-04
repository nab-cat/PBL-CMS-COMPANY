<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StrukturOrganisasi extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'struktur_organisasi';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_struktur_organisasi';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_user',
        'deskripsi',
        'jabatan',
        'tanggal_mulai',
        'tanggal_selesai',
        'urutan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'urutan' => 'integer',
    ];

    /**
     * Get the user associated with the struktur organisasi.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Scope untuk posisi yang masih aktif
     */
    public function scopeActive($query)
    {
        return $query->where(function ($query) {
            $query->whereNull('tanggal_selesai')
                ->orWhere('tanggal_selesai', '>=', now());
        });
    }

    /**
     * Scope untuk user yang aktif dan memiliki status kepegawaian yang valid
     */
    public function scopeWithActiveUsers($query)
    {
        return $query->whereHas('user', function ($query) {
            $query->where('status', 'aktif')
                ->whereIn('status_kepegawaian', ['Tetap', 'Kontrak', 'Magang']);
        });
    }

    /**
     * Scope untuk ordering berdasarkan urutan
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan', 'asc')->orderBy('tanggal_mulai', 'asc');
    }
}