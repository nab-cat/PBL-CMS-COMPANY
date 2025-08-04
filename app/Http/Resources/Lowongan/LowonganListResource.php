<?php

namespace App\Http\Resources\Lowongan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LowonganListResource extends JsonResource
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
            'id_lowongan' => $this->id_lowongan,
            'judul_lowongan' => $this->judul_lowongan,
            'thumbnail_lowongan' => $this->thumbnail_lowongan,
            'deskripsi_pekerjaan' => $this->deskripsi_pekerjaan,
            'jenis_lowongan' => $this->jenis_lowongan,
            'gaji' => $this->gaji,
            'tanggal_dibuka' => $this->tanggal_dibuka->format('Y-m-d'),
            'tanggal_ditutup' => $this->tanggal_ditutup->format('Y-m-d'),
            'tenaga_dibutuhkan' => $this->tenaga_dibutuhkan,
            'slug' => $this->slug,
        ];
    }
}