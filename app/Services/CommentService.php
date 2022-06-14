<?php

namespace App\Services;

use App\Enums\ReactionType;
use App\Models\Comment;

class CommentService
{
    public function getBaseQuery(int $commentId = null): array
    {
        $comments = Comment::query();

        if (isset($commentId)) {
            $comments = $comments->where('id', $commentId);
        }
        $comments = $comments->with(['user', 'post'])
            ->get()
            ->toArray();

        $reactionsCount = Comment::query();
        foreach (ReactionType::cases() as $type) {
            $reactionsCount = $reactionsCount->withCount([
                "reactions as {$type->value}" => function ($query) use ($type) {
                    $query->where('type', $type->value);
                },
            ]);
        }

        $commentReaction = $reactionsCount->get();

        foreach ($comments as $key => $comment) {
            foreach ($commentReaction as $reaction) {
                if ($comment['id'] === $reaction['id']) { // @phpstan-ignore-line
                    $comments[$key]['reactions'] = array_filter( // @phpstan-ignore-line
                        $reaction->toArray(), function ($value, $key) {
                            return in_array($key, ReactionType::values());
                        }, ARRAY_FILTER_USE_BOTH);
                }
            }
        }
        return $comments;
    }
}
