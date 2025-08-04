<?php

namespace App\Models;

use App\Enums\ContentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseStudy extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'case_study';
    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'case_study_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id_mitra',
        'judul_case_study',
        'slug_case_study',
        'thumbnail_case_study',
        'deskripsi_case_study',
        'isi_case_study',
        'status_case_study',
    ];

    protected $casts = [
        'thumbnail_case_study' => 'array',
        'status_case_study' => ContentStatus::class,
    ];

    /**
     * Get the user that owns this gallery item.
     */
    public function mitra()
    {
        return $this->belongsTo(Mitra::class, 'id_mitra', 'id_mitra');
    }
}