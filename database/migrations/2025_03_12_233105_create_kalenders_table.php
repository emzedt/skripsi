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
        if (!Schema::hasTable('kalenders')) {
            Schema::create('kalenders', function (Blueprint $table) {
                $table->id();
                $table->date('tanggal')->nullable();
                $table->enum('jenis_libur', ['Libur', 'Cuti Bersama']);
                $table->string('keterangan')->nullable();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('kalenders');
    }
};
