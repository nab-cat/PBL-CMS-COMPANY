<?php

namespace Database\Seeders;

use App\Enums\ContentStatus;
use App\Models\CaseStudy;
use App\Models\Mitra;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CaseStudySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Get mitra IDs to associate with case studies
        $mitraIds = Mitra::pluck('id_mitra')->toArray();
        if (empty($mitraIds)) {
            // If no mitra exists, create some placeholder mitra data
            $mitraIds = [1, 2, 3];
        }

        // Setup paths for images
        $sourcePath = database_path('seeders/seeder_image/');
        $targetPath = 'case-study-images';

        // Ensure target directory exists
        Storage::disk('public')->makeDirectory($targetPath);

        // Get all available image files from seeder_image directory
        $imageFiles = array_values(array_filter(scandir($sourcePath), fn($f) => !in_array($f, ['.', '..'])));

        // Create case studies with detailed content
        $caseStudies = [
            [
                'judul_case_study' => 'Transformasi Digital PT. ABC',
                'deskripsi_case_study' => 'Bagaimana PT. ABC memanfaatkan teknologi untuk meningkatkan efisiensi operasional dan mengoptimalkan proses bisnis mereka melalui penerapan solusi digital terintegrasi.',
                'isi_case_study' => '<h2>Latar Belakang</h2><p>PT. ABC adalah perusahaan manufaktur yang telah beroperasi selama lebih dari 20 tahun. Dengan jumlah karyawan lebih dari 500 orang, perusahaan ini menghadapi tantangan dalam mengintegrasikan berbagai proses bisnis mereka yang masih dilakukan secara manual. Hal ini menyebabkan inefisiensi, kesalahan data, dan lambatnya pengambilan keputusan strategis.</p><h2>Tantangan</h2><p>Beberapa tantangan utama yang dihadapi oleh PT. ABC antara lain:</p><ul><li>Proses pencatatan persediaan yang masih manual</li><li>Kurangnya visibilitas real-time terhadap rantai pasok</li><li>Kesulitan dalam mengintegrasikan data dari berbagai departemen</li><li>Proses pengambilan keputusan yang lambat karena keterbatasan akses data</li></ul><h2>Solusi</h2><p>Kami mengimplementasikan sistem ERP terintegrasi yang mencakup beberapa modul utama:</p><ol><li>Modul Manajemen Persediaan dengan fitur tracking real-time</li><li>Sistem Business Intelligence untuk analisis data dan pembuatan laporan</li><li>Platform komunikasi terintegrasi antar departemen</li><li>Otomatisasi proses administratif untuk mengurangi beban kerja manual</li></ol><h2>Implementasi</h2><p>Implementasi dilakukan secara bertahap selama 6 bulan, meliputi:</p><ul><li>Analisis kebutuhan dan desain sistem (1 bulan)</li><li>Pengembangan dan kustomisasi (3 bulan)</li><li>Migrasi data dan pengujian (1 bulan)</li><li>Pelatihan karyawan dan implementasi penuh (1 bulan)</li></ul><h2>Hasil</h2><p>Setelah implementasi selama 1 tahun, PT. ABC berhasil mencapai:</p><ul><li>Peningkatan efisiensi sebesar 30% dalam operasional harian</li><li>Pengurangan kesalahan dalam pencatatan data sebesar 75%</li><li>Penghematan biaya operasional sebesar 25%</li><li>Pengambilan keputusan lebih cepat dengan akses data real-time</li><li>Peningkatan kepuasan karyawan karena berkurangnya tugas administratif yang repetitif</li></ul>',
            ],
            [
                'judul_case_study' => 'Optimasi SEO Website PT. XYZ',
                'deskripsi_case_study' => 'Strategi SEO komprehensif yang meningkatkan trafik organik PT. XYZ hingga 200% dan konversi penjualan sebesar 45% dalam waktu 6 bulan.',
                'isi_case_study' => '<h2>Latar Belakang</h2><p>PT. XYZ adalah perusahaan e-commerce yang menjual produk fashion lokal. Meskipun memiliki website yang menarik dari segi desain, mereka menghadapi masalah rendahnya trafik organik dan tingkat konversi yang tidak optimal. Mereka mengandalkan iklan berbayar yang mahal untuk mendapatkan pelanggan baru.</p><h2>Tantangan</h2><p>Beberapa tantangan utama yang dihadapi PT. XYZ meliputi:</p><ul><li>Peringkat SEO yang rendah untuk kata kunci penting</li><li>Tingkat bounce rate yang tinggi (sekitar 75%)</li><li>Kurangnya konten yang relevan dan berkualitas</li><li>Struktur website yang tidak optimal untuk SEO</li><li>Pengalaman pengguna mobile yang buruk</li></ul><h2>Strategi</h2><p>Kami mengembangkan strategi SEO komprehensif yang meliputi:</p><ol><li><strong>SEO On-Page:</strong><ul><li>Optimasi meta tag dan judul halaman</li><li>Perbaikan struktur URL dan navigasi website</li><li>Optimasi kecepatan loading halaman</li><li>Implementasi schema markup</li></ul></li><li><strong>SEO Off-Page:</strong><ul><li>Membangun backlink berkualitas dari website otoritatif</li><li>Kolaborasi dengan influencer industri fashion</li><li>Meningkatkan kehadiran di platform media sosial</li></ul></li><li><strong>Konten Marketing:</strong><ul><li>Pembuatan blog artikel informatif seputar fashion</li><li>Pengembangan konten visual yang menarik</li><li>Optimasi konten produk dengan keyword yang relevan</li></ul></li></ol><h2>Implementasi</h2><p>Implementasi dilakukan selama 6 bulan dengan fokus pada:</p><ul><li>Bulan 1-2: Audit website dan implementasi perubahan teknis</li><li>Bulan 2-4: Pengembangan konten dan optimasi on-page</li><li>Bulan 4-6: Pengembangan backlink dan strategi off-page</li></ul><h2>Hasil</h2><p>Setelah implementasi selama 6 bulan, PT. XYZ berhasil mencapai:</p><ul><li>Peningkatan trafik organik sebesar 200%</li><li>Penurunan bounce rate dari 75% menjadi 35%</li><li>Peningkatan konversi sebesar 45%</li><li>Peningkatan peringkat untuk 80% kata kunci target</li><li>Pengurangan biaya akuisisi pelanggan sebesar 60%</li><li>ROI dari kampanye SEO mencapai 320%</li></ul>',
            ],
            [
                'judul_case_study' => 'Pengembangan Aplikasi Mobile untuk Perusahaan Retail',
                'deskripsi_case_study' => 'Aplikasi mobile omnichannel yang mengintegrasikan pengalaman belanja online dan offline, meningkatkan penjualan sebesar 45% dan loyalitas pelanggan hingga 60%.',
                'isi_case_study' => '<h2>Latar Belakang</h2><p>Sebuah perusahaan retail nasional dengan lebih dari 50 cabang fisik di seluruh Indonesia menghadapi persaingan ketat dari e-commerce. Mereka menyadari perlunya transformasi digital untuk tetap relevan bagi konsumen modern yang mencari pengalaman belanja yang mulus antara online dan offline.</p><h2>Tantangan</h2><p>Tantangan utama yang dihadapi perusahaan retail ini:</p><ul><li>Persaingan ketat dengan platform e-commerce murni</li><li>Kesenjangan antara pengalaman belanja offline dan online</li><li>Rendahnya tingkat retensi pelanggan</li><li>Data pelanggan yang tersebar dan tidak terintegrasi</li><li>Proses checkout di toko fisik yang lambat dan kurang efisien</li></ul><h2>Solusi</h2><p>Kami mengembangkan aplikasi mobile omnichannel dengan fitur utama:</p><ol><li><strong>Program Loyalitas Digital:</strong><ul><li>Sistem poin rewards untuk setiap pembelian</li><li>Kartu member digital dengan QR code</li><li>Rekomendasi produk personal berdasarkan histori belanja</li></ul></li><li><strong>Integrasi Offline-Online:</strong><ul><li>Cek ketersediaan stok real-time di toko terdekat</li><li>Click & Collect: beli online, ambil di toko</li><li>Scan produk di toko untuk melihat detail dan ulasan</li></ul></li><li><strong>Personalisasi:</strong><ul><li>Notifikasi promosi berdasarkan lokasi</li><li>Rekomendasi produk dengan AI</li><li>Wishlist dan reminder untuk produk favorit</li></ul></li><li><strong>Pembayaran Digital:</strong><ul><li>Integrasi dengan berbagai metode pembayaran</li><li>Express checkout untuk transaksi di toko fisik</li><li>Riwayat pembelian terintegrasi</li></ul></li></ol><h2>Proses Pengembangan</h2><p>Aplikasi dikembangkan menggunakan pendekatan agile dengan timeline:</p><ul><li>Research & Design Thinking: 1 bulan</li><li>Pengembangan MVP: 3 bulan</li><li>Testing & Refinement: 1 bulan</li><li>Peluncuran bertahap & Iterasi: 2 bulan</li></ul><h2>Hasil</h2><p>Setelah satu tahun implementasi, perusahaan retail mencapai:</p><ul><li>Peningkatan penjualan omnichannel sebesar 45%</li><li>Tingkat retensi pelanggan meningkat 60%</li><li>Nilai belanja rata-rata per pelanggan naik 25%</li><li>Frekuensi belanja pelanggan meningkat 38%</li><li>Data pelanggan terkonsolidasi, meningkatkan efektivitas marketing</li><li>Pengurangan waktu antrean kasir sebesar 65%</li><li>ROI pada tahun pertama mencapai 280%</li></ul>',
            ],
            [
                'judul_case_study' => 'Implementasi Smart Factory untuk Industri Manufaktur',
                'deskripsi_case_study' => 'Transformasi pabrik konvensional menjadi smart factory dengan IoT dan AI, meningkatkan produktivitas hingga 55% dan mengurangi downtime sebesar 70%.',
                'isi_case_study' => '<h2>Latar Belakang</h2><p>PT Manufaktur Presisi Indonesia adalah perusahaan manufaktur komponen otomotif yang telah beroperasi selama 15 tahun. Dengan meningkatnya kompetisi global dan tuntutan efisiensi, perusahaan ini perlu beralih dari manufaktur konvensional ke smart factory yang lebih efisien dan terkontrol.</p><h2>Tantangan</h2><p>Tantangan yang dihadapi meliputi:</p><ul><li>Tingginya downtime mesin yang tidak terprediksi</li><li>Inefisiensi dalam manajemen persediaan</li><li>Kesulitan dalam mengontrol kualitas secara konsisten</li><li>Tingginya biaya operasional</li><li>Kurangnya visibilitas real-time terhadap proses produksi</li></ul><h2>Solusi</h2><p>Implementasi sistem smart factory komprehensif dengan komponen:</p><ol><li><strong>IoT dan Sensor Network:</strong><ul><li>Instalasi sensor pada semua mesin produksi utama</li><li>Sistem monitoring real-time untuk parameter operasional</li><li>Jaringan IoT terintegrasi di seluruh lantai produksi</li></ul></li><li><strong>Predictive Maintenance:</strong><ul><li>Algoritma AI untuk memprediksi kegagalan mesin</li><li>Penjadwalan pemeliharaan otomatis berdasarkan analisis data</li><li>Sistem peringatan dini untuk anomali mesin</li></ul></li><li><strong>Quality Control Automation:</strong><ul><li>Computer vision untuk inspeksi kualitas otomatis</li><li>Pelacakan kualitas end-to-end</li><li>Analisis akar masalah secara real-time</li></ul></li><li><strong>Digital Twin:</strong><ul><li>Representasi digital dari seluruh operasi pabrik</li><li>Simulasi untuk optimasi proses</li><li>Perencanaan skenario what-if</li></ul></li></ol><h2>Proses Implementasi</h2><p>Implementasi dilakukan secara bertahap:</p><ul><li>Fase 1 (3 bulan): Instalasi sensor dan infrastruktur dasar</li><li>Fase 2 (4 bulan): Implementasi sistem monitoring dan analitik</li><li>Fase 3 (3 bulan): Pengembangan predictive maintenance dan quality control</li><li>Fase 4 (2 bulan): Integrasi digital twin dan optimasi</li></ul><h2>Hasil</h2><p>Setelah implementasi penuh, PT Manufaktur Presisi Indonesia mencapai:</p><ul><li>Peningkatan produktivitas sebesar 55%</li><li>Pengurangan downtime mesin tidak terencana sebesar 70%</li><li>Peningkatan kualitas produk (pengurangan defect rate dari 5% ke 0.8%)</li><li>Penghematan biaya operasional sebesar 35%</li><li>Reduksi lead time produksi sebesar 40%</li><li>Peningkatan kapasitas produksi sebesar 30% tanpa penambahan mesin baru</li><li>ROI dalam 18 bulan</li></ul>',
            ],
            [
                'judul_case_study' => 'Transformasi Cloud untuk Institusi Keuangan',
                'deskripsi_case_study' => 'Migrasi infrastruktur IT bank ke cloud hybrid, meningkatkan ketahanan sistem sebesar 99.99% dan mengurangi biaya operasional IT hingga 42%.',
                'isi_case_study' => '<h2>Latar Belakang</h2><p>Bank Nusantara adalah bank lokal dengan jaringan 120 cabang dan 5 juta nasabah. Bank ini mengoperasikan infrastruktur IT on-premise yang sudah berusia 10 tahun, menyebabkan tantangan dalam skalabilitas, keamanan, dan biaya pemeliharaan.</p><h2>Tantangan</h2><p>Tantangan utama yang dihadapi meliputi:</p><ul><li>Infrastruktur IT yang sudah usang dan mahal untuk dipertahankan</li><li>Keterbatasan dalam merespon peningkatan beban transaksi</li><li>Kekhawatiran tentang keamanan dan kepatuhan regulasi perbankan</li><li>Kesulitan dalam mengimplementasikan teknologi baru</li><li>Disaster recovery yang tidak memadai</li></ul><h2>Solusi</h2><p>Kami merancang dan mengimplementasikan strategi cloud hybrid yang mencakup:</p><ol><li><strong>Arsitektur Cloud Hybrid:</strong><ul><li>Core banking tetap on-premise dengan keamanan tinggi</li><li>Aplikasi pendukung dan analitik di public cloud</li><li>Private cloud untuk data sensitif</li><li>Jaringan terdedikasi antar lingkungan</li></ul></li><li><strong>Modernisasi Aplikasi:</strong><ul><li>Refactoring aplikasi legacy ke arsitektur microservices</li><li>Kontainerisasi dengan Kubernetes untuk portabilitas</li><li>CI/CD pipeline untuk deployment otomatis</li></ul></li><li><strong>Keamanan & Kepatuhan:</strong><ul><li>Enkripsi end-to-end untuk data sensitif</li><li>Pengelolaan identitas dan akses terpusat</li><li>Monitoring dan audit kepatuhan otomatis</li><li>Integrasi dengan solusi anti-fraud</li></ul></li><li><strong>Disaster Recovery & Business Continuity:</strong><ul><li>Multi-region deployment untuk high availability</li><li>Automated failover dengan RTO < 15 menit</li><li>Backup dan replikasi data otomatis</li></ul></li></ol><h2>Proses Migrasi</h2><p>Migrasi dilakukan dengan pendekatan wave model:</p><ul><li>Wave 1 (3 bulan): Aplikasi non-kritis dan lingkungan pengembangan</li><li>Wave 2 (4 bulan): Sistem analitik dan CRM</li><li>Wave 3 (6 bulan): Sistem pendukung perbankan</li><li>Wave 4 (ongoing): Modernisasi core banking</li></ul><h2>Hasil</h2><p>Setelah 18 bulan implementasi, Bank Nusantara mencapai:</p><ul><li>Peningkatan uptime sistem ke 99.99%</li><li>Pengurangan biaya operasional IT sebesar 42%</li><li>Penurunan waktu deployment aplikasi baru dari minggu menjadi jam</li><li>Peningkatan kapasitas untuk menangani transaksi hingga 300% tanpa penambahan biaya signifikan</li><li>Perbaikan disaster recovery dengan RTO dari 4 jam menjadi 15 menit</li><li>Kemampuan untuk mengimplementasikan teknologi baru (AI, analytics) lebih cepat</li><li>Kepatuhan terhadap regulasi yang meningkat dengan audit trail komprehensif</li></ul>',
            ],
        ];

        foreach ($caseStudies as $index => $caseStudy) {
            // Generate array untuk menyimpan thumbnail images
            $thumbnails = [];

            // Pilih dan proses 2 gambar acak untuk thumbnail
            for ($j = 0; $j < 2; $j++) {
                // Pilih gambar random
                $originalFile = $imageFiles[array_rand($imageFiles)];

                // Buat nama baru agar unik
                $newFileName = 'case-study-' . ($index + 1) . '-' . Str::random(8) . '-' . $originalFile;

                // Copy ke storage
                Storage::disk('public')->putFileAs($targetPath, new File("$sourcePath/$originalFile"), $newFileName);

                // Tambahkan path gambar ke array thumbnail
                $thumbnails[] = $targetPath . '/' . $newFileName;
            }

            CaseStudy::create([
                'id_mitra' => $mitraIds[array_rand($mitraIds)],
                'judul_case_study' => $caseStudy['judul_case_study'],
                'slug_case_study' => Str::slug($caseStudy['judul_case_study']),
                'deskripsi_case_study' => $caseStudy['deskripsi_case_study'],
                'isi_case_study' => $caseStudy['isi_case_study'],
                'thumbnail_case_study' => $thumbnails,
                'status_case_study' => $faker->randomElement([
                    ContentStatus::TERPUBLIKASI->value,
                    ContentStatus::TIDAK_TERPUBLIKASI->value
                ]),
                'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'updated_at' => $faker->dateTimeBetween('-1 year', 'now'),
            ]);
        }
    }
}
