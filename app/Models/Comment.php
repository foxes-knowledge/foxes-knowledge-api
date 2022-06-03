<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'user_id',
        'post_id'
    ];

    public function user(): Relation
    {
        return $this->belongsTo(User::class);
    }

    public function post(): Relation
    {
        return $this->belongsTo(Post::class);
    }
}
