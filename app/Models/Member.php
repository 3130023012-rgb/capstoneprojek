<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Member extends Model
{
    use HasFactory;

    // Pastikan user_id masuk ke fillable
    protected $fillable = [
        'user_id', 
        'name', 
        'member_id', 
        'study_program', 
        'phone_number',
        // Tambahkan kolom lain yang relevan di sini
    ]; 

    /**
     * Relasi: Member dimiliki oleh satu User (Foreign Key: user_id)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    // Asumsi: Jika model Anda memiliki relasi lain seperti attendances(), biarkan tetap ada.
    /*
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
    */
}
