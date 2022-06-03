<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color'
    ];

    public function posts(): Relation
    {
        return $this->belongsToMany(Post::class);
    }

    public function parent(): Relation
    {
        return $this->hasOne(Tag::class, 'tag_id');
    }
}
