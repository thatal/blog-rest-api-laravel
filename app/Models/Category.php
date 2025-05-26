<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = ['name', 'slug', 'description'];

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('category_images')
            ->singleFile();
    }

    // Override the route key name to use 'slug' instead of 'id'
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
