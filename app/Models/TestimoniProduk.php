<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestimoniProduk extends Model
{
    protected $table = 'testimoni_produk';
    protected $primaryKey = 'id_testimoni_produk';

    protected $fillable = [
        'id_user',
        'id_produk',
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

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'id_produk');
    }
}
