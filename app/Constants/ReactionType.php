<?php

namespace App\Constants;

enum ReactionType: string
{
    case UPVOTE = 'upvote';
    case DOWNVOTE = 'downvote';
}
