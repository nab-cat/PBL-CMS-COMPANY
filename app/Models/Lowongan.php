<?php

namespace App\Models;

use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lowongan extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'lowongan';
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_lowongan';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_user',
        'judul_lowongan',
        'thumbnail_lowongan',
        'deskripsi_pekerjaan',
        'jenis_lowongan',
        'gaji',
        'tanggal_dibuka',
        'tanggal_ditutup',
        'status_lowongan',
        'tenaga_dibutuhkan',
        'slug',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_dibuka' => 'date',
        'tanggal_ditutup' => 'date',
        'gaji' => 'decimal:2',
        'thumbnail_lowongan' => 'array',
        'status_lowongan' => ContentStatus::class,
        'tenaga_dibutuhkan' => 'integer',
    ];

    /**
     * Get the user that created this job vacancy.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Get the sliders that feature this job vacancy.
     */
    public function kontenSliders()
    {
        return $this->hasMany(KontenSlider::class, 'id_lowongan', 'id_lowongan');
    }
}