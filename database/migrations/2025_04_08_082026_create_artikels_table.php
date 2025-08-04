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
        Schema::create('artikel', function (Blueprint $table) {
            $table->id('id_artikel');
            $table->foreignId('id_kategori_artikel')->constrained('kategori_artikel', 'id_kategori_artikel')->onDelete('cascade');
            $table->foreignId('id_user')->constrained('users', 'id_user')->onDelete('cascade');
            $table->json('thumbnail_artikel')->nullable();
            $table->string('judul_artikel', 100);
            $table->text('konten_artikel');
            $table->integer('jumlah_view')->default(0);
            $table->string('slug', 100)->unique();
            $table->string('status_artikel')->default(ContentStatus::TIDAK_TERPUBLIKASI->value);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artikel');
    }
};