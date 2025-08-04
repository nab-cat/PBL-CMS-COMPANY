<?php

namespace App\Http\Resources\Articles;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleViewResource extends JsonResource
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
            'id_artikel' => $this->id_artikel,
            'judul_artikel' => $this->judul_artikel,
            'konten_artikel' => $this->konten_artikel,
            'thumbnail_artikel' => $this->thumbnail_artikel,
            'created_at' => $this->created_at,
            'kategoriArtikel' => [
                'id_kategori_artikel' => $this->kategoriArtikel->id_kategori_artikel,
                'nama_kategori_artikel' => $this->kategoriArtikel->nama_kategori_artikel,
            ],
            'user' => [
                //'id_user' => $this->user->id_user,
                'name' => $this->user->name,
                'foto_profil' => $this->user->foto_profil,
                'email' => $this->user->email,
            ],
            'jumlah_view' => $this->jumlah_view,
            'slug' => $this->slug,
        ];
    }
}
