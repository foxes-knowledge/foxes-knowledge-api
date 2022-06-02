<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): JsonResponse
    {
        $users = User::with([
            'posts',
        ])->get();

        return new JsonResponse(
            $users,
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
        User::create($data);

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
        $user = User::with([
            'posts',
        ])->findOrFail($id);


        return new JsonResponse([
            $user
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
        $user = User::FindOrFail($id);
        $data = $request->all();
        $user->fill($data)->save();

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
        User::destroy($id);

        return new JsonResponse([
            'deleted' => true,
        ], Response::HTTP_OK);
    }
}
