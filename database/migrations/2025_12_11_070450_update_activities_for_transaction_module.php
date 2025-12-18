<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
{
    Schema::table('activities', function (Blueprint $table) {
        // A. Tambahkan kolom nominal
        $table->unsignedBigInteger('nominal')->after('total_members')->default(0); 

        // B. Tambahkan kolom status konfirmasi (default pending)
        $table->enum('confirmation_status', ['pending', 'approved', 'rejected'])
              ->after('nominal')->default('pending'); 

        // C. Jadikan trainer_id nullable agar form bisa diisi tanpa memilih pelatih (opsional)
        $table->unsignedBigInteger('trainer_id')->nullable()->change();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            //
        });
    }
};
