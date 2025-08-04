<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray(Request $request)
    {
        return [
            'id_produk' => $this->id_produk,
            'nama_produk' => $this->nama_produk,
            'tampilkan_harga' => $this->tampilkan_harga,
            'harga_produk' => $this->harga_produk,
            'thumbnail_produk' => $this->thumbnail_produk,
            'slug' => $this->slug,
            'deskripsi_produk' => $this->deskripsi_produk,
            'kategori_produk' => [
                'id_kategori_produk' => $this->kategoriProduk->id_kategori_produk,
                'nama_kategori_produk' => $this->kategoriProduk->nama_kategori_produk,
            ],
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}

