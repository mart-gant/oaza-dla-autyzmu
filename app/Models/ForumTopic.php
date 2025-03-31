<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ForumTopic extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['title', 'user_id', 'category_id'];
    
    /**
     * Relacja: temat należy do kategorii forum
     */
    public function category()
    {
        return $this->belongsTo(ForumCategory::class)->withDefault();
    }
    
    /**
     * Relacja: temat należy do użytkownika
     */
    public function user()
    {
        return $this->belongsTo(User::class)->withDefault();
    }
    
    /**
     * Relacja: temat ma wiele postów
     */
    public function posts()
    {
        return $this->hasMany(ForumPost::class);
    }
    
    /**
     * Pobieranie tematów od najnowszych
     */
    public function scopeLatestFirst($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
