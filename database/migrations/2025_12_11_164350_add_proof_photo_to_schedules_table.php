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
    // Dalam method up()
public function up(): void
{
    Schema::table('schedules', function (Blueprint $table) {
        // Tambahkan kolom untuk path foto bukti latihan (nullable)
        $table->string('proof_photo')->nullable()->after('location'); 
    });
}

// Dalam method down()
public function down(): void
{
    Schema::table('schedules', function (Blueprint $table) {
        $table->dropColumn('proof_photo');
    });
}
};
