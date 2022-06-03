<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'user_id',
        'post_id',
        'upvotes',
        'downvotes'
    ];

    public function user(): Relation
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): Relation
    {
        return $this->belongsToMany(Tag::class);
    }

    public function comments(): Relation
    {
        return $this->hasMany(Comment::class);
    }

    public function parent(): Relation
    {
        return $this->hasOne(Post::class, 'post_id');
    }
}
