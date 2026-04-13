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
        Schema::create('apl', function (Blueprint $table) {
            $table->char('nim', 8)->primary();
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
        Schema::dropIfExists('apl');
    }
};
