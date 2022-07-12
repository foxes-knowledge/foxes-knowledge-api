<?php

namespace App\Http\Controllers;

use App\Http\Requests\InvitationRequest;
use App\Models\Invitation;
use Illuminate\Http\Response;

class InvitationController extends Controller
{
    /**
     * Store a newly created invitation in storage.
     */
    public function store(InvitationRequest $request): Response
    {
        $invitation = Invitation::create([
            ...$request->validated(),
            'token' => str()->random(32),
        ]);

        return response($invitation, Response::HTTP_OK);
    }
}
