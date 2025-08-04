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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id('id_feedback');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->tinyInteger('tingkat_kepuasan')->default(0);
            $table->string('subjek_feedback', 200);
            $table->text('isi_feedback');
            $table->text('tanggapan_feedback')->nullable();
            $table->string('status_feedback')->default(ContentStatus::TIDAK_TERPUBLIKASI->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};