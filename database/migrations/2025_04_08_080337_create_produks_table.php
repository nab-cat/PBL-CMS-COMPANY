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
        Schema::create('produk', function (Blueprint $table) {
            $table->id('id_produk');
            $table->foreignId('id_kategori_produk')->constrained('kategori_produk', 'id_kategori_produk')->onDelete('cascade');
            $table->string('nama_produk', 100);
            $table->json('thumbnail_produk')->nullable();
            $table->boolean('tampilkan_harga')->default(true);
            $table->string('harga_produk', 50)->nullable();
            $table->string('slug', 100)->unique();
            $table->string('link_produk')->nullable();
            $table->text('deskripsi_produk')->nullable();
            $table->string('status_produk', 50)->default('tidak terpublikasi');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};