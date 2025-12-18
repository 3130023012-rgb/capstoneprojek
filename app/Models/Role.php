<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'roles';
    
    // Kolom yang dapat diisi secara massal
    protected $fillable = ['name'];

    /**
     * Relasi: Satu Role (Peran) dimiliki oleh banyak User.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}