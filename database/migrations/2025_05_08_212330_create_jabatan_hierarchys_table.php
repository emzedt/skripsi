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
        Schema::create('jabatan_hierarchys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_jabatan_id')->constrained('jabatans');
            $table->foreignId('child_jabatan_id')->constrained('jabatans');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jabatan_hierarchys');
    }
};
