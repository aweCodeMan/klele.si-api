<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CommentPolicy
{
    use HandlesAuthorization;

    public function update(User $user, Comment $comment)
    {
        if ($user->can('update comments')) {
            return true;
        }

        return $user->uuid === $comment->author_uuid;
    }

    public function delete(User $user, Comment $comment)
    {
        if ($user->can('delete comments')) {
            return true;
        }

        return $user->uuid === $comment->author_uuid;
    }

    public function restore(User $user, Comment $comment)
    {
        return $user->can('restore comments');
    }
}
