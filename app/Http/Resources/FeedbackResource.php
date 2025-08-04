<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FeedbackResource extends JsonResource
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
            'id_feedback' => $this->id_feedback,
            'user' => [
                //'id_user' => $this->user->id_user,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'foto_profil' => $this->user->foto_profil,
            ],
            'subjek_feedback' => $this->subjek_feedback,
            'tingkat_kepuasan' => $this->tingkat_kepuasan,
            'isi_feedback' => $this->isi_feedback,
            'tanggapan_feedback' => $this->tanggapan_feedback,
            'created_at' => $this->created_at->format('Y-m-d'),
            // 'updated_at' => $this->updated_at->format('Y-m-d'),
        ];
    }
}
