<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('unduhan', function (Blueprint $table) {
            $table->id('id_unduhan');
            $table->foreignId('id_kategori_unduhan')->constrained('kategori_unduhan', 'id_kategori_unduhan')->onDelete('cascade')->onUpdate('cascade');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade')->onUpdate('cascade');
            $table->string('nama_unduhan', 100);
            $table->json('thumbnail_unduhan')->nullable();
            $table->string('slug', 100)->unique();
            $table->string('lokasi_file', 200);
            $table->text('deskripsi')->nullable();
            $table->bigInteger('jumlah_unduhan')->default(0);
            $table->string('status_unduhan', 50)->default('tidak terpublikasi');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unduhan');
    }
};