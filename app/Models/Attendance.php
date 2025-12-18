<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;
    
    // Default nama tabel: 'attendances'
    
    protected $fillable = [
        'member_id',
        'activity_id',
        'status', 
    ];

    // Relasi untuk mendapatkan informasi anggota
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    // Relasi untuk mendapatkan informasi kegiatan dan tanggalnya
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
    
    // Catatan: Model ini secara default menggunakan kolom 'created_at' untuk filter tanggal
}
