<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('pendaftaran_event', function (Blueprint $table) {
            $table->id('id_pendaftaran_event');
            $table->foreignId('id_event')->constrained('event', 'id_event')->onDelete('cascade');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->timestamps();

            // Add unique constraint to prevent duplicate registrations
            $table->unique(['id_event', 'id_user']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_event');
    }
};
