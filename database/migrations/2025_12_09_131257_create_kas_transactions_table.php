<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kas_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->enum('type', ['in', 'out']); // Tipe transaksi
            $table->unsignedBigInteger('amount'); // Jumlah (misalnya: Rp)
            $table->string('description', 255);
            // Foreign key ke tabel users (siapa yang mencatat)
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); 
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kas_transactions');
    }
};