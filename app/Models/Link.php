<?php

namespace App\Models;

use App\Services\LinkService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';

    protected $keyType = 'uuid';

    public $incrementing = false;

    protected $guarded = [];

    protected $casts = ['meta' => 'array'];

    public static function updateMetaData(string $aggregateRootUuid)
    {
        $link = self::where('uuid', $aggregateRootUuid)->first();

        if ($link) {
            $link->meta = LinkService::parse($link->link);
            $link->save();
        }
    }
}
