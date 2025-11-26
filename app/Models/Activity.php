<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    
    /**
     * Kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'trainer_id',
        'date',
        'material',
        'total_members',
    ];

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}