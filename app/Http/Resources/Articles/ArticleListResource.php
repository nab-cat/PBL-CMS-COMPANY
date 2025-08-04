<?php

namespace App\Http\Resources\Articles;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(Request $request)
    {
        return [
            'id_artikel' => $this->id_artikel,
            'judul_artikel' => $this->judul_artikel,
            'konten_artikel' => substr($this->konten_artikel, 0, 100) . '...',
            'thumbnail_artikel' => $this->thumbnail_artikel,
            'created_at' => $this->created_at,
            'kategoriArtikel' => [
                'id_kategori_artikel' => optional($this->kategoriArtikel)->id_kategori_artikel,
                'nama_kategori_artikel' => optional($this->kategoriArtikel)->nama_kategori_artikel,
            ],
            'user' => [
                //'id_user' => optional($this->user)->id_user,
                'name' => optional($this->user)->name,
                'foto_profil' => optional($this->user)->foto_profil,
                'email' => optional($this->user)->email,
            ],
            'jumlah_view' => $this->jumlah_view,
            'slug' => $this->slug,
        ];
    }
}
