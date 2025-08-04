<?php

namespace App\Http\Resources\Galeri;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GaleriDownloadResource extends JsonResource
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
            'thumbnail_galeri' => $this->thumbnail_galeri,
            'jumlah_unduhan' => $this->jumlah_unduhan,
        ];
    }
}