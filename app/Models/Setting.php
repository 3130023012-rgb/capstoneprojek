<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';
    public $timestamps = false; // Karena hanya menyimpan nilai statis

    protected $fillable = [
        'key',   // e.g., 'hero_description', 'visi', 'misi', 'kontak_email'
        'value', // e.g., 'Mencetak generasi yang unggul...'
        'description', // untuk deskripsi di admin
    ];
}