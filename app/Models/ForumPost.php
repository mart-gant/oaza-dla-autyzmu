<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ForumPost extends Model
{
    use HasFactory;
    
    // Określamy pola, które można masowo uzupełniać
    protected $fillable = ['content', 'user_id', 'topic_id', 'is_reported'];
    
    /**
     * Powiązanie z kategorią tematu (relacja wiele do jednego)
     * Każdy post należy do konkretnego tematu na forum.
     */
    public function topic(): BelongsTo
    {
        return $this->belongsTo(ForumTopic::class);
    }
    
    /**
     * Powiązanie z tabelą użytkowników (relacja wiele do jednego)
     * Każdy post należy do jednego użytkownika.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    /**
     * Powiązanie z tabelą reakcji (relacja jeden do wielu)
     * Pozwala użytkownikom dodawać reakcje do postów na forum.
     */
    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }
    
    /**
     * Sprawdza, czy dany użytkownik może edytować lub usuwać post.
     * Pozwala to ograniczyć uprawnienia do autora posta lub administratora.
     */
    public function canBeEditedBy(User $user): bool
    {
        return $this->user_id === $user->id || $user->isAdmin();
    }
    
    /**
     * Usuwanie reakcji po usunięciu posta (zapobiega osieroconym danym w bazie).
     */
    protected static function boot()
    {
        parent::boot();
        
        static::deleting(function ($post) {
            $post->reactions()->delete();
        });
    }
}
