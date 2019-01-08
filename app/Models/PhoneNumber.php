<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PhoneNumber extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    protected $guarded = ['id'];

    public function number() : string
    {

        $area_code = $this->area_code;
        $phone_number = $this->phone_number;
        $extension = $this->extension;

        $phone = '';

        if($area_code){
        	$phone = "(".$area_code.")";
        }

        if($phone_number){
        	$phone_number_formatted = substr_replace($phone_number, '-', 3, 0);
        	$phone = $phone." ".$phone_number_formatted;
        }

        if($extension){
        	$phone = $phone." x".$extension;
        }

        return $phone;
    }
}
