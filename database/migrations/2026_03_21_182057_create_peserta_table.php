<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('peserta', function (Blueprint $table) {
            $table->char('nim', 8)->primary();
            $table->string('nama', 255);
            $table->string('email', 255)->unique();
            $table->string('prodi', 255);
            $table->string('gender', 255);
            $table->boolean('bahasa_jawa')->default(false)->nullable();
            $table->boolean('riwayat_penyakit')->default(false);
            $table->boolean('berkebutuhan_khusus')->default(false);
            $table->unsignedBigInteger('id_kelompok')->nullable();
            $table->foreign('id_kelompok')->references('id_kelompok')->on('kelompok')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('peserta');
    }
};
