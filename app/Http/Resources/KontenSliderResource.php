<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class KontenSliderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id_konten_slider' => $this->id_konten_slider,
            'durasi_slider' => $this->durasi_slider,
            'artikel' => $this->whenLoaded('artikel'),
            'galeri' => $this->whenLoaded('galeri'),
            'produk' => $this->whenLoaded('produk'),
            'event' => $this->whenLoaded('event'),
        ];
    }
}
