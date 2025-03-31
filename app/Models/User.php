<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo',  // Zdjęcie profilowe
        'interests',      // Zainteresowania
        'support_preferences', // Preferencje wsparcia
        'is_specialist',  // Czy użytkownik to specjalista?
        'specialization', // Opis specjalizacji (dla specjalistów)
    ];
    
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'is_specialist' => 'boolean',
    ];
    
    /**
     * Relacja użytkownika z placówkami.
     */
    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }
}
