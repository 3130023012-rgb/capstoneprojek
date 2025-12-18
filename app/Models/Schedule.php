<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';

    // Tambahkan 'proof_photo' ke fillable
    protected $fillable = [
        'trainer_id',
        'name',
        'day_of_week',
        'time',
        'location',
        'proof_photo', // Kolom baru untuk path foto bukti latihan
    ];
}