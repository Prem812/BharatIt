<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'category',
        'tags',
        'keywords',
        'slug',
        'country',
        'state',
        'city',
        'description',
        'author',
        'is_published',
        'date',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'date' => 'date',
        'tags' => 'array',
        'keywords' => 'array',
    ];

    public function blogCategory()
    {
        return $this->belongsTo(BlogCategory::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($blog) {
            $blog->slug = Str::slug($blog->title);
        });
    }

    public function setTagsAttribute($value)
    {
        $this->attributes['tags'] = json_encode($value);
    }

    public function setKeywordsAttribute($value)
    {
        $this->attributes['keywords'] = json_encode($value);
    }
}