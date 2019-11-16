<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserPhoneNumber extends Model
{
    public $timestamps = true;

    protected $guarded = ['id'];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function phone()
    {
        return $this->hasOne(PhoneNumber::class, 'id', 'phone_number_id');
    }

    public function scopeAllita($query)
    {
        return $query->where('devco', 0)->orWhereNull('devco');
    }

    public function phone_number_formatted(): string
    {
        $endNumber = substr($this->phone->phone_number, 0, 3).'-'.substr($this->phone->phone_number, 3, 6);
        $number = '('.$this->phone->area_code.') '.$endNumber;
        if (! is_null($this->phone->extension) && '' != $this->phone->extension) {
            $number .= ' ext:'.$this->phone->extension;
        }

        return $number;
    }

    public function phone_number_formatted_noext(): string
    {
        $endNumber = substr($this->phone->phone_number, 0, 3).'-'.substr($this->phone->phone_number, 3, 6);
        $number = $this->phone->area_code.'-'.$endNumber;

        return $number;
    }
}
