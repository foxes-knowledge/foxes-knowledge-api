<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest\CommentStoreRequest;
use App\Http\Requests\CommentRequest\CommentUpdateRequest;
use App\Models\Comment;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return response(Comment::withCount([
            'reactions as upvotes' => function ($query) {
                $query->where('type', 'upvote');
            },
            'reactions as downvotes' => function ($query) {
                $query->where('type', 'downvote');
            },
        ])
            ->with(['user', 'post'])
            ->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentStoreRequest $request): Response
    {
        $comment = Comment::create($request->all());

        return response($comment, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment): Response
    {
        return response(Comment::withCount([
            'reactions as upvotes' => function ($query) {
                $query->where('type', 'upvote');
            },
            'reactions as downvotes' => function ($query) {
                $query->where('type', 'downvote');
            },
        ])
            ->with(['user', 'post'])
            ->find($comment->id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommentUpdateRequest $request, Comment $comment): Response
    {
        $comment->update($request->all());

        return response($comment, Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment): Response
    {
        $comment->delete();

        return response([], Response::HTTP_NO_CONTENT);
    }
}
