<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $keyType = 'uuid';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $guarded = [];
}
