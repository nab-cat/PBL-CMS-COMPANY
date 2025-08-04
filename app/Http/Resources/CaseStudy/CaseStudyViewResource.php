<?php

namespace App\Http\Resources\CaseStudy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CaseStudyViewResource extends JsonResource
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
            'case_study_id' => $this->case_study_id,
            'judul_case_study' => $this->judul_case_study,
            'deskripsi_case_study' => $this->deskripsi_case_study,
            'isi_case_study' => $this->isi_case_study,
            'thumbnail_case_study' => $this->thumbnail_case_study,
            'created_at' => $this->created_at,
            'mitra' => [
                'id_mitra' => $this->mitra->id_mitra,
                'nama_mitra' => $this->mitra->nama,
                'logo' => $this->mitra->logo,
            ],
            'slug_case_study' => $this->slug_case_study,
        ];
    }
}
