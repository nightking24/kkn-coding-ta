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
        Schema::create('kelompok', function (Blueprint $table) {
            $table->id('id_kelompok');
            $table->integer('nomor_kelompok');
            $table->string('desa', 255);
            $table->string('dusun', 255);
            $table->boolean('faskes')->default(false);
            $table->integer('kapasitas');
            $table->string('semester', 10);
            $table->year('tahun_kkn');
            $table->string('nama_dukuh', 255);
            $table->string('alamat', 255);
            $table->string('nomor_telepon', 12);
            $table->string('nama_tuan_rumah', 255)->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->char('nik', 18)->nullable();
            $table->char('nim', 8)->nullable();
            $table->foreign('nik')->references('nik')->on('dpl')->onDelete('set null');
            $table->foreign('nim')->references('nim')->on('apl')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelompok');
    }
};
