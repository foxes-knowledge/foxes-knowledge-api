<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest\PostStoreRequest;
use App\Http\Requests\PostRequest\PostUpdateRequest;
use App\Models\Post;
use App\Services\PostService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, PostService $postService): Response
    {
        return response($postService->getPostsWithMediaCount($request));
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
    public function show(Post $post, PostService $postService): Response
    {
        return response($postService->getBaseQuery($post->id));
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

    public function getListings(PostService $postService): Response
    {
        return response($postService->getListings());
    }

}
