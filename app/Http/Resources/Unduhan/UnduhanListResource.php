<?php

namespace App\Http\Resources\Unduhan;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class UnduhanListResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $fileSize = 0;
        if ($this->lokasi_file && Storage::disk('public')->exists($this->lokasi_file)) {
            $fileSize = Storage::disk('public')->size($this->lokasi_file);
        }

        return [
            'id' => $this->id_unduhan,
            'nama_unduhan' => $this->nama_unduhan,
            'slug' => $this->slug,
            'deskripsi' => $this->deskripsi,
            'jumlah_unduhan' => $this->jumlah_unduhan,
            'lokasi_file' => $this->lokasi_file,
            'kategori' => [
                'id' => $this->kategoriUnduhan->id_kategori_unduhan,
                'nama_kategori_unduhan' => $this->kategoriUnduhan->nama_kategori_unduhan,
            ],
            'thumbnail' => $this->thumbnail_unduhan,
            'status_unduhan' => $this->status_unduhan,
            'created_at' => $this->created_at,
            'ukuran_file' => $this->formatSizeUnits($fileSize),
            'jenis_file' => $this->getFileExtension(),
        ];
    }

    /**
     * Format bytes to human readable sizes
     * 
     * @param int $bytes
     * @return string
     */
    private function formatSizeUnits(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } elseif ($bytes > 1) {
            return $bytes . ' bytes';
        } elseif ($bytes == 1) {
            return '1 byte';
        } else {
            return '0 bytes';
        }
    }
    private function getFileExtension()
    {
        return strtolower(pathinfo($this->lokasi_file, PATHINFO_EXTENSION));
    }
}
