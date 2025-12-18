<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // Mengubah kolom trainer_id menjadi nullable
            $table->unsignedBigInteger('trainer_id')->nullable()->change(); 
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            // Mengubah kolom trainer_id kembali menjadi not null (opsional, tergantung kebutuhan)
            $table->unsignedBigInteger('trainer_id')->nullable(false)->change();
        });
    }
};