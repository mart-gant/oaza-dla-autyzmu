<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ForumPost;
use Illuminate\Auth\Access\HandlesAuthorization;

class ForumPostPolicy
{
    use HandlesAuthorization;

    public function update(User $user, ForumPost $post)
    {
        return $user->id === $post->user_id;
    }

    public function delete(User $user, ForumPost $post)
    {
        return $user->id === $post->user_id;
    }
}
