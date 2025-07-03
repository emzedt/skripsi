<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('cutis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_cuti');
            $table->enum('jenis_cuti', ['Cuti Biasa', 'Cuti Spesial']);
            $table->date('tanggal_mulai_cuti');
            $table->date('tanggal_selesai_cuti');
            $table->string('alasan_cuti');
            $table->string('foto_cuti')->nullable();
            $table->enum('status', ['Disetujui', 'Ditolak', 'Menunggu'])->nullable();
            $table->string('alasan_persetujuan_cuti')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('cutis');
    }
};
