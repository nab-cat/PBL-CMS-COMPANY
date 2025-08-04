<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\FeatureToggle;

class FeatureToggleSeeder extends Seeder
{
    public function run()
    {
        // Data modul yang akan dimasukkan ke dalam tabel feature_toggles
        $modules = [
            ['key' => 'artikel_module', 'label' => 'Modul Artikel', 'status_aktif' => true],
            ['key' => 'case_study_module', 'label' => 'Modul Case Study', 'status_aktif' => true],
            ['key' => 'event_module', 'label' => 'Modul Event', 'status_aktif' => true],
            ['key' => 'feedback_module', 'label' => 'Modul Feedback', 'status_aktif' => true],
            ['key' => 'galeri_module', 'label' => 'Modul Galeri', 'status_aktif' => true],
            ['key' => 'lamaran_module', 'label' => 'Modul Lamaran', 'status_aktif' => true],
            ['key' => 'lowongan_module', 'label' => 'Modul Lowongan', 'status_aktif' => true],
            ['key' => 'mitra_module', 'label' => 'Modul Mitra', 'status_aktif' => true],
            ['key' => 'produk_module', 'label' => 'Modul Produk', 'status_aktif' => true],
            ['key' => 'testimoni_module', 'label' => 'Modul Testimoni', 'status_aktif' => true],
            ['key' => 'unduhan_module', 'label' => 'Modul Unduhan', 'status_aktif' => true],
            ['key' => 'kontenslider_module', 'label' => 'Modul Konten Slider', 'status_aktif' => true],
        ];

        // Loop untuk memasukkan data modul ke dalam tabel feature_toggles
        foreach ($modules as $module) {
            FeatureToggle::create($module);
        }
    }
}
