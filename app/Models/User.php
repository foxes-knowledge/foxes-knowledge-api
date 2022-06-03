<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    protected $fillable = [
        'username',
        'name',
        'email',
        'password',
        'isEmailPublic',
        'picture',
        'bio',
        'color'
    ];

    protected $hidden = [
        'password',
    ];

    public function posts(): Relation
    {
        return $this->hasMany(Post::class);
    }
}
