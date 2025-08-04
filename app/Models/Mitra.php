<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mitra extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'mitra';
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id_mitra';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'nama',
        'logo',
        'alamat_mitra',
        'dok_siup',
        'dok_npwp',
        'tanggal_kemitraan',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'tanggal_kemitraan' => 'date',
        'status' => 'boolean',
    ];
}