<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $comments = Comment::with([
            'user',
            'post'
        ])->get();

        return new JsonResponse(
            $comments,
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
        $comment = Comment::create($data);
        $comment->user()->associate($data['user_id']);
        $comment->post()->associate($data['post_id']);
        $comment->save();


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
        $comment = Comment::with([
            'user',
            'post'
        ])->findOrFail($id);

        return new JsonResponse([
            $comment,
        ], Response::HTTP_CREATED);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $post = Comment::FindOrFail($id);
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
        Comment::destroy($id);

        return new JsonResponse([
            'deleted' => true,
        ], Response::HTTP_OK);
    }
}
