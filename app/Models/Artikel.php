<?php

namespace App\Models;

use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Artikel extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'artikel';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_artikel';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_kategori_artikel',
        'id_user',
        'thumbnail_artikel',
        'judul_artikel',
        'konten_artikel',
        'jumlah_view',
        'slug',
        'status_artikel',
    ];

    protected $casts = [
        'thumbnail_artikel' => 'array',
        'status_artikel' => ContentStatus::class,
        'jumlah_view' => 'integer',
    ];



    /**
     * Get the category that this article belongs to.
     */
    public function kategoriArtikel()
    {
        return $this->belongsTo(KategoriArtikel::class, 'id_kategori_artikel', 'id_kategori_artikel');
    }

    /**
     * Get the user that authored this article.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    /**
     * Get the sliders that feature this article.
     */
    public function kontenSliders()
    {
        return $this->hasMany(KontenSlider::class, 'id_artikel', 'id_artikel');
    }
}