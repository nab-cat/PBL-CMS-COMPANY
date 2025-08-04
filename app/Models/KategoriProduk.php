<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriProduk extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kategori_produk';
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_kategori_produk';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_kategori_produk',
    ];

    /**
     * Get the products for this category.
     */
    public function produks()
    {
        return $this->hasMany(Produk::class, 'id_kategori_produk', 'id_kategori_produk');
    }
}