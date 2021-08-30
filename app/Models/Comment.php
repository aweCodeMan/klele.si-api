<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Comment extends Model
{
    use HasFactory, SoftDeletes;

    const TEXT_DELETED = '[Izbrisan komentar]';

    protected $primaryKey = 'uuid';

    protected $keyType = 'uuid';

    public $incrementing = false;

    protected $guarded = [];

    public $comments = [];

    protected $casts = [
        'locked_at' => 'date',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'author_uuid', 'uuid');
    }

    public function markdown()
    {
        return $this->hasOne(Markdown::class, 'uuid', 'uuid');
    }

    public function score()
    {
        return $this->hasOne(Score::class, 'uuid', 'uuid');
    }

    public function voted()
    {
        return $this->hasOne(Vote::class, 'uuid', 'uuid')->where('user_uuid', auth()->user()->uuid);
    }
}
