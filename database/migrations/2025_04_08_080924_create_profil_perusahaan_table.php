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
        Schema::create('profil_perusahaan', function (Blueprint $table) {
            $table->id('id_profil_perusahaan');
            $table->string('nama_perusahaan', 100);
            $table->json('thumbnail_perusahaan')->nullable();
            $table->string('logo_perusahaan', 200)->nullable();
            $table->text('deskripsi_perusahaan')->nullable();
            $table->string('alamat_perusahaan', 200);
            $table->string('link_alamat_perusahaan')->nullable();
            $table->text('map_embed_perusahaan')->nullable();
            $table->string('email_perusahaan', 50);
            $table->string('telepon_perusahaan', 50)->nullable();
            $table->json('sejarah_perusahaan')->nullable();
            $table->text('visi_perusahaan')->nullable();
            $table->text('misi_perusahaan')->nullable();
            $table->enum('tema_perusahaan', ['#31487A', '#793354', '#796C2F', '#1B4332', '#3E1F47'])->default('#31487A')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profil_perusahaan');
    }
};