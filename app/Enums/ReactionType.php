<?php

namespace App\Enums;

enum ReactionType: string
{
    case UPVOTE = 'upvote';
    case DOWNVOTE = 'downvote';

    public static function values(): array
    {
        $values = array();
        foreach (self::cases() as $case) {
            $values[] = $case->value;
        }
        return $values;
    }
}
