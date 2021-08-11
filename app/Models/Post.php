<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Post extends Model
{
    use HasFactory, HasSlug, SoftDeletes;

    const TYPE_MARKDOWN = 0;
    const TEXT_DELETED = '[izbrisano]';

    protected $primaryKey = 'uuid';

    protected $keyType = 'uuid';

    public $incrementing = false;

    protected $guarded = [];

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->usingSeparator('-')
            ->usingLanguage('sl')
            ->doNotGenerateSlugsOnUpdate()
            ->preventOverwrite()
            ->slugsShouldBeNoLongerThan(240);
    }

    public function markdown()
    {
        return $this->hasOne(Markdown::class, 'uuid', 'uuid');
    }
}
