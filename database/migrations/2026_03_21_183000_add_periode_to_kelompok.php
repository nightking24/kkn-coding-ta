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
        Schema::table('kelompok', function (Blueprint $table) {
            $table->unsignedBigInteger('id_periode')->nullable()->after('tahun_kkn');
            $table->foreign('id_periode')->references('id_periode')->on('periode')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelompok', function (Blueprint $table) {
            $table->dropForeign(['id_periode']);
            $table->dropColumn('id_periode');
        });
    }
};
