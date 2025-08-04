<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestimoniLowongan extends Model
{
    protected $table = 'testimoni_lowongan';
    protected $primaryKey = 'id_testimoni_lowongan';

    protected $fillable = [
        'id_user',
        'id_lowongan',
        'isi_testimoni',
        'rating',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'integer',
        'status' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function lowongan()
    {
        return $this->belongsTo(Lowongan::class, 'id_lowongan');
    }
}
