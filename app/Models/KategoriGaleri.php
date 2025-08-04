<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriGaleri extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kategori_galeri';
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_kategori_galeri';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_kategori_galeri',
    ];

    /**
     * Get the gallery items for this category.
     */
    public function galeris()
    {
        return $this->hasMany(Galeri::class, 'id_kategori_galeri', 'id_kategori_galeri');
    }
}