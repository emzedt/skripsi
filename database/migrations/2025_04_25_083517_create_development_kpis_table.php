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
        Schema::create('development_kpis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('objective_id')->constrained('development_objectives')->onDelete('cascade');
            $table->string('kpi');
            $table->string('tipe_kpi')->default('nominal');
            $table->decimal('target', 10, 2);
            $table->decimal('realisasi', 10, 2);
            $table->decimal('bobot', 5, 2);
            $table->enum('status', ['Tercapai', 'Tidak Tercapai']);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('development_kpis');
    }
};
