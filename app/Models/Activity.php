<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'trainer_id',
        'date',
        'material',
        'total_members',
        'nominal',              // WAJIB: Kolom baru untuk pengajuan biaya
        'confirmation_status',  // WAJIB: Kolom baru
        'description',          // Opsional: Jika Anda menggunakan description
    ];

    public function trainer()
    {
        return $this->belongsTo(Trainer::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    public function kasTransactions()
    {
        return $this->hasMany(KasTransaction::class, 'activity_id');
    }
}
