<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    
    /**
     * Kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'member_id',
        'activity_id',
        'status', // Status: present, absent, sick_leave, permission
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
}