<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LamaranResource extends JsonResource
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
            'id_lamaran' => $this->id_lamaran,
            'surat_lamaran' => $this->surat_lamaran ? url('storage/' . $this->surat_lamaran) : null,
            'user' => [
                'id_user' => $this->user->id_user,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'pesan_pelamar' => $this->pesan_pelamar,
            'lowongan' => [
                'id_lowongan' => $this->lowongan->id_lowongan,
                'judul_lowongan' => $this->lowongan->judul_lowongan,
                'slug' => $this->lowongan->slug,
            ],
            'status_lamaran' => $this->status_lamaran,
            'cv' => $this->cv ? url('storage/' . $this->cv) : null,
            'portfolio' => $this->portfolio ? url('storage/' . $this->portfolio) : null,
            'created_at' => $this->created_at->format('Y-m-d'),
            'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }
}
