<?php

use App\Enums\ContentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('case_study', function (Blueprint $table) {
            $table->id('case_study_id');
            $table->foreignId('id_mitra')->constrained('mitra', 'id_mitra')->onDelete('cascade');
            $table->string('judul_case_study', 100)->unique();
            $table->string('slug_case_study', 100)->unique();
            $table->json('thumbnail_case_study')->nullable();
            $table->text('deskripsi_case_study')->nullable();
            $table->text('isi_case_study')->nullable();
            $table->string('status_case_study')->default(ContentStatus::TIDAK_TERPUBLIKASI->value);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_study');
    }
};
