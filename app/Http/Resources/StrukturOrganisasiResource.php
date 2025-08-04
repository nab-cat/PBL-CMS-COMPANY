<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StrukturOrganisasiResource extends JsonResource
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
            'id_struktur_organisasi' => $this->id_struktur_organisasi,
            'user' => [
                'id_user' => $this->user->id_user,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'no_hp' => $this->user->no_hp,
                'foto_profil' => $this->user->foto_profil
                    ? asset('storage/' . $this->user->foto_profil)
                    : 'https://ui-avatars.com/api/?name=' . urlencode($this->user->name) . '&color=7F9CF5&background=EBF4FF',
                'status' => $this->user->status,
                'status_kepegawaian' => $this->user->status_kepegawaian,
                'roles' => $this->user->roles->pluck('name')->toArray(),
                'primary_role' => $this->user->roles->first()?->name ?? null,
            ],
            'jabatan' => $this->jabatan,
            'deskripsi' => $this->deskripsi,
            'urutan' => $this->urutan,
            'tanggal_mulai' => $this->tanggal_mulai ? $this->tanggal_mulai->format('Y-m-d') : null,
            'tanggal_selesai' => $this->tanggal_selesai ? $this->tanggal_selesai->format('Y-m-d') : null,
            'is_active' => $this->tanggal_selesai === null || $this->tanggal_selesai >= now(),
            'created_at' => $this->created_at->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at->format('Y-m-d H:i:s'),
        ];
    }
}
