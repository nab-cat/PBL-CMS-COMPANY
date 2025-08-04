<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaSosialResource extends JsonResource
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
            // 'id_media_sosial' => $this->id_media_sosial,
            'nama_media_sosial' => $this->nama_media_sosial,
            'link' => $this->link,
        ];
    }
}
