<?php

namespace App\Http\Resources\Unduhan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UnduhanDownloadResource extends JsonResource
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
            'lokasi_file' => $this->lokasi_file,
            'jumlah_unduhan' => $this->jumlah_unduhan,
        ];
    }
}
