<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReactionRequest\ReactionCommentRequest;
use App\Http\Requests\ReactionRequest\ReactionPostRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Reaction;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ReactionController extends Controller
{
    public function storePost(Post $post, ReactionPostRequest $request): Response
    {
        $reaction = Reaction::where([
            ['user_id', Auth::id()],
            ['post_id', $post->id]
        ])->first();
        $data = $request->all();

        if ($reaction !== null) {
            if ($reaction->type === $data['type']) {
                Reaction::destroy($reaction->id);
            } else {
                $reaction->type = $data['type'];
                $reaction->save();
            }
        } else {
            Reaction::create([
                'user_id' => Auth::id(),
                'post_id' => $post->id,
                'type' => $data['type']
            ]);
        }

        return response([
            'created' => true
        ]);
    }

    public function storeComment(Comment $comment, ReactionCommentRequest $request): Response
    {
        $reaction = Reaction::where([
            ['user_id', Auth::id()],
            ['comment_id', $comment->id]
        ])->first();
        $data = $request->all();

        if ($reaction !== null) {
            if ($reaction->type === $data['type']) {
                Reaction::destroy($reaction->id);
            } else {
                $reaction->type = $data['type'];
                $reaction->save();
            }
        } else {
            Reaction::create([
                'user_id' => Auth::id(),
                'comment_id' => $comment->id,
                'type' => $data['type']
            ]);
        }

        return response([
            'created' => true
        ]);
    }
}
