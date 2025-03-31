<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $table = 'facilities'; // Określenie nazwy tabeli

    protected $fillable = [
        'user_id', 'name', 'description', 'address', 'city', 'province', 'postal_code', 
        'phone', 'email', 'latitude', 'longitude', 'available_spots'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
