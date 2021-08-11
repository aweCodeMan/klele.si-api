<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Markdown extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';

    protected $keyType = 'uuid';

    public $incrementing = false;

    public $timestamps = false;

    protected $guarded = [];
}
