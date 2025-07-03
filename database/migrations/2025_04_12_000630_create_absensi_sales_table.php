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
        Schema::create('absensi_sales', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal')->nullable();
            $table->time('jam')->nullable();
            $table->string('foto')->nullable();
            $table->string('deskripsi')->nullable();
            $table->enum('status', ['Titip Brosur', 'Meeting']);
            $table->enum('status_persetujuan', ['Disetujui', 'Ditolak', 'Menunggu']);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensi_sales');
    }
};
