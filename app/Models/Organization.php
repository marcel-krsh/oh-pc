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
    public function phone() : HasOne
    {
        return $this->hasOne(\App\Models\Phone::class, 'phone_number_key', 'default_phone_number_key');
    }

    public function phone_number_formatted() : string
    {
        $endNumber = substr($this->phone->phone_number, 0,2). '-'.substr($this->phone->phone_number, 3,6);
        $number = '('.$this->phone->area_code.') '.$endNumber;
        if(!is_null($this->phone->extension) && $this->phone->extension !=''){
            $number .= ' ext:'.$this->phone->extension;
        }
        return $number;
    }

    /**
     * Fax
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function fax() : HasOne
    {
        return $this->hasOne(\App\Models\Phone::class, 'phone_number_key', 'default_fax_number_key');
    }

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
