<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriUnduhan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kategori_unduhan';
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_kategori_unduhan';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama_kategori_unduhan',
    ];

    /**
     * Get the downloads for this category.
     */
    public function unduhans()
    {
        return $this->hasMany(Unduhan::class, 'id_kategori_unduhan', 'id_kategori_unduhan');
    }
}