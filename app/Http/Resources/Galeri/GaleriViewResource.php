<?php

namespace App\Http\Resources\Galeri;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GaleriViewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(Request $request): array
    {
        return [
            'id_galeri' => $this->id_galeri,
            'user' => [
                //'id_user' => $this->user->id_user,
                'name' => $this->user->name,
                'foto_profil' => $this->user->foto_profil ?? 'FOTO NULL', // debug
            ],
            'judul_galeri' => $this->judul_galeri,
            'thumbnail_galeri' => $this->thumbnail_galeri,
            'deskripsi_galeri' => $this->deskripsi_galeri,
            'kategoriGaleri' => [
                'id_kategori_galeri' => $this->kategoriGaleri->id_kategori_galeri,
                'nama_kategori_galeri' => $this->kategoriGaleri->nama_kategori_galeri,
            ],
            'slug' => $this->slug,
            'jumlah_unduhan' => $this->jumlah_unduhan,
            'created_at' => $this->created_at,
        ];
    }
}
