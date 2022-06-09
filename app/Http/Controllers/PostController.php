<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest\PostStoreRequest;
use App\Http\Requests\PostRequest\PostUpdateRequest;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        return response(Post::withCount([
            'reactions as upvotes' => function ($query) {
                $query->where('type', 'upvote');
            },
            'reactions as downvotes' => function ($query) {
                $query->where('type', 'downvote');
            },
        ])
            ->with(['user', 'tags', 'attachments', 'parent', 'child'])
            ->get());
//
//        $posts = Post::with(['user', 'tags'])->get();
//
//        return response($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request, PostService $postService): Response
    {
        $post = $postService->create((array)$request->validated());

        return response($post, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): Response
    {
        return response(Post::withCount([
            'reactions as upvotes' => function ($query) {
                $query->where('type', 'upvote');
            },
            'reactions as downvotes' => function ($query) {
                $query->where('type', 'downvote');
            },
        ])
            ->with(['user', 'tags', 'attachments', 'parent', 'child'])
            ->find($post->id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $request, Post $post, PostService $postService): Response
    {
        $post = $postService->update((array)$request->validated(), $post);

        return response($post, Response::HTTP_CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): Response
    {
        $post->delete();

        return response([], Response::HTTP_NO_CONTENT);
    }
}
