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
    Schema::create('galleries', function (Blueprint $table) {
        $table->id();
        $table->string('title');
        $table->string('file_name'); // Path file di server
        $table->text('description')->nullable();
        $table->boolean('is_active')->default(1); // Status tampil di Landing Page
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('galleries');
    }
};
