<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfilPerusahaan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'profil_perusahaan';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_profil_perusahaan';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nama_perusahaan',
        'thumbnail_perusahaan',
        'logo_perusahaan',
        'deskripsi_perusahaan',
        'alamat_perusahaan',
        'link_alamat_perusahaan',
        'map_embed_perusahaan',
        'email_perusahaan',
        'telepon_perusahaan',
        'sejarah_perusahaan',
        'visi_perusahaan',
        'misi_perusahaan',
        'tema_perusahaan',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'thumbnail_perusahaan' => 'array',
        'sejarah_perusahaan' => 'array',
    ];
}