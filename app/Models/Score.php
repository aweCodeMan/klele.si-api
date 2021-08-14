<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';

    protected $keyType = 'uuid';

    public $timestamps = false;

    public $incrementing = false;

    protected $guarded = [];

}
