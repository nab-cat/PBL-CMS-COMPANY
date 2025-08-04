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
        Schema::create('konten_slider', function (Blueprint $table) {
            $table->id('id_konten_slider');
            $table->integer('durasi_slider')->nullable();
            $table->foreignId('id_galeri')->nullable()->constrained('galeri', 'id_galeri')->onDelete('set null');
            $table->foreignId('id_produk')->nullable()->constrained('produk', 'id_produk')->onDelete('set null');
            $table->foreignId('id_event')->nullable()->constrained('event', 'id_event')->onDelete('set null');
            $table->foreignId('id_artikel')->nullable()->constrained('artikel', 'id_artikel')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('konten_slider');
    }
};