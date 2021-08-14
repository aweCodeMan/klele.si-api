<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    const UPVOTE = 1;
    const DOWNVOTE = -1;
    const NEUTRAL = 0;

    protected $keyType = 'uuid';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $guarded = [];
}
