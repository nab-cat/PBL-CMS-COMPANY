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
        Schema::create('lamaran', function (Blueprint $table) {
            $table->id('id_lamaran');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->foreignId('id_lowongan')->constrained('lowongan', 'id_lowongan')->onDelete('cascade');
            $table->string('surat_lamaran', 200)->nullable();
            $table->string('pesan_pelamar')->nullable();
            $table->string('cv', 200)->nullable();
            $table->string('portfolio', 200)->nullable();
            $table->enum('status_lamaran', ['Diterima', 'Diproses', 'Ditolak'])->default('Diproses');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['id_user', 'id_lowongan'], 'unique_user_lowongan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lamaran');
    }
};