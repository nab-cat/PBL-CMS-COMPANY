<?php

namespace App\Http\Resources\Lowongan;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\User\UserMinimalResource;

class LowonganViewResource extends JsonResource
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
            'id_lowongan' => $this->id_lowongan,
            'judul_lowongan' => $this->judul_lowongan,
            'thumbnail_lowongan' => $this->thumbnail_lowongan,
            'deskripsi_pekerjaan' => $this->deskripsi_pekerjaan,
            'jenis_lowongan' => $this->jenis_lowongan,
            'gaji' => $this->gaji,
            'tanggal_dibuka' => $this->tanggal_dibuka ? $this->tanggal_dibuka->format('Y-m-d') : null,
            'tanggal_ditutup' => $this->tanggal_ditutup ? $this->tanggal_ditutup->format('Y-m-d') : null,
            'tenaga_dibutuhkan' => $this->tenaga_dibutuhkan,
            'status' => $this->tanggal_dibuka && $this->tanggal_ditutup && now()->between($this->tanggal_dibuka, $this->tanggal_ditutup) ? 'Aktif' : 'Tidak Aktif',
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d') : null,
            'slug' => $this->slug,
        ];
    }
}
