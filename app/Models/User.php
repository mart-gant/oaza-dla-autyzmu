<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $role
 * @property bool $is_suspended
 * @property \Carbon\Carbon|null $suspended_until
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 */
class User extends Authenticatable implements MustVerifyEmail, JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_photo',
        'interests',
        'support_preferences',
        'is_specialist',
        'specialization',
        'description',
        'is_suspended',
        'suspended_until',
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
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_specialist' => 'boolean',
        'is_suspended' => 'boolean',
        'suspended_until' => 'datetime',
    ];
    
    /**
     * Relacja użytkownika z placówkami.
     */
    public function facilities(): HasMany
    {
        return $this->hasMany(Facility::class);
    }

    /**
     * Relacja użytkownika z opiniami.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relacja użytkownika z tematami forum.
     */
    public function forumTopics(): HasMany
    {
        return $this->hasMany(ForumTopic::class);
    }

    /**
     * Relacja użytkownika z postami na forum.
     */
    public function forumPosts(): HasMany
    {
        return $this->hasMany(ForumPost::class);
    }

    /**
     * Wiadomości wysłane przez użytkownika.
     */
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Wiadomości otrzymane przez użytkownika.
     */
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    /**
     * Sprawdź czy użytkownik jest administratorem.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Sprawdź czy użytkownik ma określoną rolę.
     */
    public function hasRole(string $role): bool
    {
        return $this->role === $role;
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     */
    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'name' => $this->name,
            'email' => $this->email,
        ];
    }
}
