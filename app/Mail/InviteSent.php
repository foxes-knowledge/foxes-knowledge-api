<?php

namespace App\Mail;

use App\Models\Invitation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteSent extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Invitation $invitation, public User $user, public string $url)
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->to($this->invitation->email)
            ->subject('Invite to join the Foxes Knowledge service')
            ->markdown('emails.invitations.sent')
            ->text('emails.invitations.sent_plain')
            ->tag('invitation');
    }
}
