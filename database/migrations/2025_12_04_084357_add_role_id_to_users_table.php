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
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom role_id
            $table->foreignId('role_id')
                  ->nullable() // Anggap sementara bisa null
                  ->after('password')
                  ->constrained() // Membuat foreign key ke tabel roles
                  ->onDelete('set null'); // Jika role dihapus, user tetap ada tapi role_id null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign key sebelum drop kolom
            $table->dropConstrainedForeignId('role_id'); 
            $table->dropColumn('role_id');
        });
    }
};