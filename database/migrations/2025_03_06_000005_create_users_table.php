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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('foto_face_recognition')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('no_rekening')->nullable();
            $table->integer('sisa_hak_cuti')->nullable();
            $table->integer('sisa_hak_cuti_bersama')->nullable();
            $table->string('password');
            $table->foreignId('jabatan_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('hak_cuti_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('status_karyawan_id')->nullable()->constrained()->onDelete('set null');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });


        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['jabatan_id']);
            $table->dropForeign(['hak_cuti_id']);
            $table->dropForeign(['status_karyawan_id']);
        });
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
