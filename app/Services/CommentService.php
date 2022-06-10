<?php

namespace App\Services;

use App\Models\Comment;

class CommentService
{
    public function getBaseQuery(): \Illuminate\Database\Eloquent\Builder | Comment
    {
        return Comment::withCount([
            'reactions as upvotes' => function ($query) {
                $query->where('type', 'upvote');
            },
            'reactions as downvotes' => function ($query) {
                $query->where('type', 'downvote');
            },
        ])->with(['user', 'post']);
    }
}
