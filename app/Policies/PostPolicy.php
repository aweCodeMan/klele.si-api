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
        if ($user->can('update posts')) {
            return true;
        }

        return $user->uuid === $post->author_uuid;
    }

    public function delete(User $user, Post $post)
    {
        if ($user->can('delete posts')) {
            return true;
        }

        return $user->uuid === $post->author_uuid;
    }

    public function restore(User $user, Post $post)
    {
        return $user->can('restore posts');
    }
}
