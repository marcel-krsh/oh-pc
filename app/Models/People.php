<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class People extends Model
{
    public $timestamps = true;
    protected $table = 'people';
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    protected $guarded = ['id'];

    /**
     * Email.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    // public function email() : HasOne
    // {
    //     return $this->hasOne(\App\Models\Email::class, 'email_key', 'default_email_address_key');
    // }

    /**
     * Phone.
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

    public function matchingUserByEmail() : HasOne
    {
        // do not eager load this relationship it will fail
        if ($this->email) {
            return $this->hasOne(\App\Models\User::class, 'email', $this->email->email_address);
        } else {
            return $this->hasOne(\App\Models\User::class, 'email', 'cccc');
        }
    }

    public function allita_phone() : HasOne
    {
        return $this->hasOne(\App\Models\PhoneNumber::class, 'id', 'default_phone_number_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'person_id');
    }

    public function organizations() : HasMany
    {
        return $this->hasMany(\App\Models\Organization::class, 'default_contact_person_id', 'id');
    }

    public function projects() : HasManyThrough
    {
        // example is on model countries
        // return $this->hasManyThrough('App\Post',
        //     'App\User',
        //     'country_id', // Foreign key on users table...
        //     'user_id', // Foreign key on posts table...
        //     'id', // Local key on countries table...
        //     'id' // Local key on users table..
        // );
        return $this->hasManyThrough(\App\Models\Project::class,
            \App\Models\ProjectContactRole::class,
            'person_id', // Foreign key on ProjectContactRole table...
            'id', // Foreign key on project table...
            'id', // Local key on people table...
            'project_id' // Local key on project_contact_role table..
        )->orderBy('project_name')->distinct();
    }
}
