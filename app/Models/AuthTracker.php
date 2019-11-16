<?php

namespace App\Models;

use App\Mail\EmailFailedLogin;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use User;

class AuthTracker extends Model
{
    protected $table = 'auth_tracker';

    protected $fillable = [
        'token',
        'ip',
        'user_agent',
        'user_id',
        'tries',
        'blocked_until',
        'total_failed_tries',
        'times_locked',
        'unlock_token',
        'unlock_attempts',
        'total_unlock_attempts',
        'last_failed_time',
        'last_locked_time',
        'last_failed_reason',
    ];

    /**
     * User.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'id', 'devco_id');
    }

    /**
     * Reset Tries.
     */
    public function resetTries()
    {
        $this->tries = 0;
        $this->save();
    }

    /**
     * Increment Tries.
     */
    public function incrementTries() : int
    {
        $this->tries = $this->tries + 1;
        $this->save();

        if ($this->tries >= 5) {
            $this->block();
        }

        return $this->tries;
    }

    /**
     * Check if an IP is already blocked.
     *
     * @param  string $ip
     * @return
     */
    public static function is_blocked_by_ip(string $ip)
    {
        $ip_is_blocked = self::where('ip', '=', $ip)->where('blocked_until', '>', Carbon::now())->first();
        if ($ip_is_blocked) {
            return $ip_is_blocked;
        }

        return false;
    }

    /**
     * Reset blocked date.
     *
     * @return bool
     */
    public function reset_block() : bool
    {
        $this->blocked_until = null;
        $this->save();

        return true;
    }

    /**
     * Block user after too many attempts.
     *
     * @return
     */
    public function block()
    {
        // check how many attempts
        $attempts = $this->tries;

        if ($attempts >= 5) {
            // set unblock_date to now + 5min * (tries - 4)
            $minutes = 5 * ($attempts - 4);
            $unblock_date = Carbon::now()->addMinutes($minutes);

            $this->blocked_until = $unblock_date;
            $this->save();

            // send email to matched user if applicable
            if ($this->user_id !== null) {
                $user = User::where('id', '=', $this->user_id)->first();
                if ($user) {
                    $emailNotification = new EmailFailedLogin($user->id);
                    //    \Mail::to('jotassin@gmail.com')->send($emailNotification);
                    \Mail::to($user->email)->send($emailNotification);
                }
            }
        }
    }
}
