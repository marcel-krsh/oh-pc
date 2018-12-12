<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Organization extends Model
{
    public $timestamps = true;
	//protected $dateFormat = 'Y-m-d\TH:i:s.u';

    protected $guarded = ['id'];

    protected $fillable = [
        'organization_name'
    ];

    /**
     * Address
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function address() : HasOne
    {
        return $this->hasOne(\App\Models\Address::class, 'address_key', 'default_address_key');
    }

    /**
     * Phone
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    // public function phone() : HasOne
    // {
    //     return $this->hasOne(\App\Models\Phone::class, 'address_key', 'default_address_key');
    // }

    /**
     * Person
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function person() : HasOne
    {
        return $this->hasOne(\App\Models\People::class, 'person_key', 'default_contact_person_key');
    }
}
