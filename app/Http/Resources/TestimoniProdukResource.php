<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TestimoniProdukResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id_testimoni_produk' => $this->id_testimoni_produk, 
            'user' => [
                //'id_user' => $this->user->id_user ?? null,
                'name' => $this->user->name ?? 'Anonim',
                'foto_profil' => $this->user->foto_profil ?? null,
                'email' => $this->user->email ?? null,
            ],
            'isi_testimoni' => $this->isi_testimoni,
            'thumbnail_testimoni' => $this->thumbnail_testimoni,
            'rating' => $this->rating,
            'status' => $this->status,
            'created_at' => $this->created_at->toDateString(),
            'updated_at' => $this->updated_at->toDateString(),
        ];
    }
}
