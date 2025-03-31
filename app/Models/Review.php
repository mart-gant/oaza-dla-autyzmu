<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;
    
    // Określamy, które pola mogą być masowo wypełniane
    protected $fillable = ['user_id', 'facility_id', 'rating', 'comment'];
    
    /**
     * Powiązanie z tabelą użytkowników (relacja wiele do jednego)
     * Każda recenzja należy do jednego użytkownika.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Powiązanie z tabelą placówek (relacja wiele do jednego)
     * Każda recenzja dotyczy jednej konkretnej placówki.
     */
    public function facility()
    {
        return $this->belongsTo(Facility::class);
    }
    
    /**
     * Powiązanie z tabelą reakcji (relacja jeden do wielu)
     * Pozwala użytkownikom dodawać reakcje do recenzji.
     */
    public function reactions()
    {
        return $this->hasMany(Reaction::class);
    }
}
