<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;
    
    /**
     * Kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'name',
        'member_id',
        'phone_number', // Kolom tambahan dari skema awal
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}