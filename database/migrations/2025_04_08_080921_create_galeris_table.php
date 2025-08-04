<?php

use App\Enums\ContentStatus;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('galeri', function (Blueprint $table) {
            $table->id('id_galeri');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->foreignId('id_kategori_galeri')->constrained('kategori_galeri', 'id_kategori_galeri')->onDelete('cascade');
            $table->string('judul_galeri', 200);
            $table->json('thumbnail_galeri')->nullable();
            $table->text('deskripsi_galeri')->nullable();
            $table->string('slug', 100)->unique();
            $table->bigInteger('jumlah_unduhan')->default(0);
            $table->string('status_galeri')->default(ContentStatus::TIDAK_TERPUBLIKASI->value);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('galeri');
    }
};