<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $posts = Post::with([
            'user',
            'parent',
            'tags',
            'comments'
        ])->get();

        return new JsonResponse(
            $posts,
            Response::HTTP_OK
        );
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->all();
        $post = Post::create($data);
        $post->user()->associate($data['user_id']);

        if (isset($data['post_id'])) {
            $post->parent()->save($data['post_id']);
        }

        $post->tags()->attach($data['tag_id']);
        $post->save();


        return new JsonResponse([
            'created' => true,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id): JsonResponse
    {
        $post = Post::with([
            'user',
            'parent',
            'tags',
            'comments'
        ])->findOrFail($id);

        return new JsonResponse([
            $post,
        ], Response::HTTP_CREATED);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id): JsonResponse
    {
        $post = Post::FindOrFail($id);
        $data = $request->all();
        $post->fill($data)->save();

        return new JsonResponse([
            'updated' => true
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id): JsonResponse
    {
        Post::destroy($id);

        return new JsonResponse([
            'deleted' => true,
        ], Response::HTTP_OK);
    }
}
