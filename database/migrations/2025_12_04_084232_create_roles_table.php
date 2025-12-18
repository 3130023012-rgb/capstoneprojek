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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
        
        // --- DATA INSERTER (SEEDER SIMPEL) ---
        // PENTING: Anda bisa memindahkan ini ke seeder, tapi untuk kemudahan, kita masukkan di sini.
        DB::table('roles')->insert([
            ['name' => 'pengurus'],
            ['name' => 'anggota'],
            ['name' => 'pembina'],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};