<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * EmailVerification
 *
 * @category Mail
 * @license  Proprietary and confidential
 */
class EmailVerification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var mixed New User
     */
    public $newuser;

    /**
     * EmailVerification constructor.
     *
     * @param \App\User $newuser
     */
    public function __construct(User $newuser)
    {
        $this->user = $newuser;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.verification');
    }
}
