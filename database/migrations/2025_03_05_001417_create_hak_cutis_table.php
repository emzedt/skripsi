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
        Schema::create('hak_cutis', function (Blueprint $table) {
            $table->id();
            $table->integer('hak_cuti');
            $table->integer('hak_cuti_bersama');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('users', function (Blueprint $table) {
        //     $table->dropForeign(['hak_cuti_id']);
        // });

        Schema::dropIfExists('hak_cutis');
    }
};
