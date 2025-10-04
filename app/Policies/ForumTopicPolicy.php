<?php

namespace App\Policies;

use App\Models\User;
use App\Models\ForumTopic;
use Illuminate\Auth\Access\HandlesAuthorization;

class ForumTopicPolicy
{
    use HandlesAuthorization;

    public function update(User $user, ForumTopic $topic)
    {
        return $user->id === $topic->user_id;
    }

    public function delete(User $user, ForumTopic $topic)
    {
        return $user->id === $topic->user_id;
    }
}
