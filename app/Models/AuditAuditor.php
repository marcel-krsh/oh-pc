<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditAuditor extends Model
{
    public $timestamps = true;
	//protected $dateFormat = 'Y-m-d\TH:i:s.u';
    //
    protected $guarded = ['id'];

    /**
     * Person
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function person() : HasOne
    {
        return $this->hasOne(\App\Models\People::class, 'person_key', 'user_key');
    }

    /**
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'person_key', 'user_key');
    }
}
