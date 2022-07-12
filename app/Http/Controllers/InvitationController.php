<?php

namespace App\Http\Controllers;

use App\Http\Requests\InviteRequest;
use App\Models\Invitation;
use Illuminate\Http\Response;

class InvitationController extends Controller
{
    /**
     * Store a newly created invitation in storage.
     */
    public function store(InviteRequest $request): Response
    {
        $invite = Invitation::create([
            ...$request->validated(),
            'token' => str()->random(32),
        ]);

        return response($invite, Response::HTTP_OK);
    }
}
