<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use User;
use App\Mail\EmailFailedLogin;

class AuthTracker extends Model
{

	protected $table = 'auth_tracker';

    protected $fillable = [
        'token',
        'ip',
        'user_agent',
        'user_id',
        'tries',
        'blocked_until'
    ];

    /**
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'id', 'user_id');
    }

    /**
     * Reset Tries
     */
    public function resetTries()
    {
        $this->tries = 0;
        $this->save();
    }

    /**
     * Increment Tries
     */
    public function incrementTries() : int
    {
        $this->tries = $this->tries + 1;
        $this->save();

        if($this->tries >= 5){
        	$this->block();
        }

        return $this->tries;
    }

    /**
     * Check if an IP is already blocked
     * 
     * @param  string $ip
     * @return boolean
     */
    public function is_blocked_by_ip(string $ip) : bool
    {
    	if(self::where('ip', '=', $ip)->where('blocked_until', '>', Carbon::now())->count()) return true;

    	return false;
    }

	/**
	 * Reset blocked date
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
     * Block user after too many attempts
     * 
     * @return bool
     */
    public function block() : bool
    {
    	// check how many attempts
    	$attempts = $this->tries;

    	if($attempts >= 5){
    		// set unblock_date to now + 5min * (tries - 4)
    		$minutes = 5 * ($attempts - 4);
    		$unblock_date = Carbon::now()->addMinutes($minutes);

    		$this->blocked_until = $unblock_date;
    		$this->save();

    		// send email to matched user if applicable
    		if($this->user_id !== null){
    			$user = User::where('id','=',$this->user_id)->first();
    			if($user){
    				$emailNotification = new EmailFailedLogin($user->id);
                    \Mail::to('jotassin@gmail.com')->send($emailNotification);
                    // \Mail::to($user->email)->send($emailNotification);
    			}
    		}
    	}
    }


}
