<?php

namespace App\Enums;

enum ReactionType: string
{
    case UPVOTE = 'upvote';
    case DOWNVOTE = 'downvote';
}
