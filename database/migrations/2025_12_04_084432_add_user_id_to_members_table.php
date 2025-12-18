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
        Schema::table('members', function (Blueprint $table) {
            // Menambahkan kolom user_id (Bisa null jika anggota belum punya akun login)
            $table->foreignId('user_id')
                  ->nullable()
                  ->unique() // Penting: Satu anggota hanya punya satu akun user
                  ->after('id') 
                  ->constrained() // Membuat foreign key ke tabel users
                  ->onDelete('set null'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id'); 
            $table->dropColumn('user_id');
        });
    }
};