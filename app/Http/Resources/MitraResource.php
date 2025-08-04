<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MitraResource extends JsonResource
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
            // 'id_mitra' => $this->id_mitra,
            'nama' => $this->nama,
            'logo' => $this->logo,
            'alamat_mitra' => $this->alamat_mitra,
            // 'tanggal_kemitraan' => $this->tanggal_kemitraan ? $this->tanggal_kemitraan->format('Y-m-d') : null,
            'status' => $this->status,
        ];
    }
}
