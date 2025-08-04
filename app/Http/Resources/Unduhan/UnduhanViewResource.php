<?php

namespace App\Http\Resources\Unduhan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnduhanViewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id_unduhan,
            'nama_unduhan' => $this->nama_unduhan,
            'slug' => $this->slug,
            'lokasi_file' => $this->lokasi_file,
            'deskripsi' => $this->deskripsi,
            'jumlah_unduhan' => $this->jumlah_unduhan,
            'kategori' => [
                'id' => $this->kategoriUnduhan->id_kategori_unduhan,
                'nama_kategori_unduhan' => $this->kategoriUnduhan->nama_kategori_unduhan,
            ],
            'thumbnail' => $this->thumbnail_unduhan,
            'created_at' => $this->created_at,
        ];
    }
}
