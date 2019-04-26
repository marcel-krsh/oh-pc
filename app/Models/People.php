<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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
    public function phone() : HasOne
    {
        return $this->hasOne(\App\Models\PhoneNumber::class, 'phone_number_key', 'default_phone_number_key');
    }

    public function fax() : HasOne
    {
        return $this->hasOne(\App\Models\PhoneNumber::class, 'phone_number_key', 'default_fax_number_key');
    }

    public function email() : HasOne
    {
        return $this->hasOne(\App\Models\EmailAddress::class, 'email_address_key', 'default_email_address_key');
    }

    public function allita_phone() : HasOne
    {
        return $this->hasOne(\App\Models\PhoneNumber::class, 'id', 'default_phone_number_id');
    }
}
