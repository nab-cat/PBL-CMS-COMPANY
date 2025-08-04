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
        Schema::create('testimoni_slider', function (Blueprint $table) {
            $table->id('id_testimoni_slider');
            $table->unsignedBigInteger('id_testimoni_produk')->nullable();
            $table->unsignedBigInteger('id_testimoni_lowongan')->nullable();
            $table->unsignedBigInteger('id_testimoni_event')->nullable();
            $table->unsignedBigInteger('id_testimoni_artikel')->nullable();
            $table->timestamps();

            // Add foreign key constraints separately
            $table->foreign('id_testimoni_produk')->references('id_testimoni_produk')->on('testimoni_produk')->onDelete('cascade');
            $table->foreign('id_testimoni_lowongan')->references('id_testimoni_lowongan')->on('testimoni_lowongan')->onDelete('cascade');
            $table->foreign('id_testimoni_event')->references('id_testimoni_event')->on('testimoni_event')->onDelete('cascade');
            $table->foreign('id_testimoni_artikel')->references('id_testimoni_artikel')->on('testimoni_artikel')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimoni_slider');
    }
};
