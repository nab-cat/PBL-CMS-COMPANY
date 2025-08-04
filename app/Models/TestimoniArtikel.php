<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestimoniArtikel extends Model
{
    protected $table = 'testimoni_artikel';
    protected $primaryKey = 'id_testimoni_artikel';

    protected $fillable = [
        'id_user',
        'id_artikel',
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

    public function artikel()
    {
        return $this->belongsTo(Artikel::class, 'id_artikel');
    }
}
