<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $posts = Post::with(['user', 'tags', 'parent'])->get();

        return response($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): Response
    {
        $post = Post::create($request->all());

        if ($post_id = $request->get('post_id')) {
            $post->parent()->save(new Post(['id' => $post_id]));
        }

        $post->tags()->attach($request->get('tag_id'));
        $post->save();

        return response($post, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post): Response
    {
        return response(Post::with(['user', 'tags', 'parent'])->find($post->id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post): Response
    {
        $post->update($request->all());

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
