<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    const TEXT_DELETED = Post::TEXT_DELETED;

    protected $primaryKey = 'uuid';

    protected $keyType = 'uuid';

    public $incrementing = false;

    protected $guarded = [];

    public $comments = [];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_uuid', 'uuid');
    }

    public function markdown()
    {
        return $this->hasOne(Markdown::class, 'uuid', 'uuid');
    }
}
