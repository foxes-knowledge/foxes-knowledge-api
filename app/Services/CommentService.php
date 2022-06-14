<?php

namespace App\Services;

use App\Enums\ReactionType;
use App\Models\Comment;

class CommentService
{
    private function getCounts(): array
    {
        $counts = [];
        foreach (ReactionType::cases() as $type) {
            $counts["reactions as {$type->value}"] = fn ($query) => $query->where('type', $type->value);
        }

        return $counts;
    }

    /**
     * @return Comment|Comment[] $comments
     */
    public function getBaseQuery(int $commentId = null)
    {
        if (!!$commentId) {
            return $this->withReactions(Comment::find($commentId));
        }

        /**
         * @var Comment[] $comments
         */
        $comments = [];
        foreach (Comment::all() as $comment) {
            $comments[] = $this->withReactions($comment);
        }

        return $comments;
    }

    public function withReactions(Comment $comment): Comment
    {
        $copy = Comment::find($comment->id);
        $copy->loadCount($this->getCounts());

        $comment->reactions = array_filter(
            $copy->toArray(),
            fn ($value, $key) => in_array($key, ReactionType::values()),
            ARRAY_FILTER_USE_BOTH
        );

        return $comment->load(['user', 'post']);
    }
}
