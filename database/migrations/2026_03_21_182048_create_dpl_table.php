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
        Schema::create('dpl', function (Blueprint $table) {
            $table->char('nik', 18)->primary();
            $table->char('nidn', 10)->nullable();
            $table->string('nama', 255);
            $table->string('email', 255)->unique();
            $table->string('no_telp', 15);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dpl');
    }
};
