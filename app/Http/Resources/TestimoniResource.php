<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

class TestimoniResource extends JsonResource
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
            'id_testimoni_slider' => $this->id_testimoni_slider,
            'user' => [
                //'id_user' => $this->user->id_user,
                'name' => $this->user->name,
            ],
            'isi_testimoni' => $this->isi_testimoni,
            'thumbnail_testimoni' => $this->thumbnail_testimoni,
            'rating' => $this->rating,
            'status' => $this->status,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }
}
