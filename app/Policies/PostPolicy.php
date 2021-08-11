<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PostPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Post $post)
    {
        return $user->uuid === $post->author_uuid;
    }

    public function delete(User $user, Post $post)
    {
        return $user->uuid === $post->author_uuid;
    }
}
