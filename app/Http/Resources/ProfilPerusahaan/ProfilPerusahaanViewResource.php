<?php

namespace App\Http\Resources\ProfilPerusahaan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfilPerusahaanViewResource extends JsonResource
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
            // 'id_galeri' => $this->id_galeri,
            'nama_perusahaan' => $this->nama_perusahaan,
            'thumbnail_perusahaan' => $this->thumbnail_perusahaan,
            'logo_perusahaan' => $this->logo_perusahaan,
            'sejarah_perusahaan' => $this->sejarah_perusahaan,
            'deskripsi_perusahaan' => $this->deskripsi_perusahaan,
            'visi_perusahaan' => $this->visi_perusahaan,
            'misi_perusahaan' => $this->misi_perusahaan,
            'alamat_perusahaan' => $this->alamat_perusahaan,
            'link_alamat_perusahaan' => $this->link_alamat_perusahaan,
            'map_embed_perusahaan' => $this->map_embed_perusahaan,
            'email_perusahaan' => $this->email_perusahaan,
            'telepon_perusahaan' => $this->telepon_perusahaan,
        ];
    }
}