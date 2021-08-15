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
    const TYPE_LINK = 1;

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

    public function link()
    {
        return $this->hasOne(Link::class, 'uuid', 'uuid');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_uuid', 'uuid');
    }

    public function score()
    {
        return $this->hasOne(Score::class, 'uuid', 'uuid');
    }

    public function postView()
    {
        return $this->hasOne(PostView::class, 'post_uuid', 'uuid')->where('user_uuid', auth()->user()->uuid);
    }

    public function voted()
    {
        return $this->hasOne(Vote::class, 'uuid', 'uuid')->where('user_uuid', auth()->user()->uuid);
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_uuid', 'uuid');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'root_uuid', 'uuid')->with(['markdown', 'author'])->withTrashed()->orderBy('created_at', 'DESC');
    }
}
