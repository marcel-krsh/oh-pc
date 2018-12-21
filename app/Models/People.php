<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class People extends Model
{
    public $timestamps = true;
    protected $table = "people";
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';


    protected $guarded = ['id'];

    /**
     * Email
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    // public function email() : HasOne
    // {
    //     return $this->hasOne(\App\Models\Email::class, 'email_key', 'default_email_address_key');
    // }

    /**
     * Phone
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    // public function phone() : HasOne
    // {
    //     return $this->hasOne(\App\Models\Phone::class, 'phone_key', 'default_phone_number_key');
    // }
}
