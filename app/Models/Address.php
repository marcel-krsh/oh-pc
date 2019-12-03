<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    public $timestamps = true;

    function getLastEditedAttribute($value)
    {
    	return milliseconds_mutator($value);
    }
    //protected $dateFormat = 'Y-m-d G:i:s.u';

    protected $guarded = ['id'];

    public function formatted_address($unit_name = null)
    {
    	$address = '';

        if($unit_name){
            if($this->line_1){
                $address = $this->line_1;
            }
            if($this->line_2){
                $address = $address . ", " . $this->line_2;
            }
            $address = $address . " # " . $unit_name;
            if($this->city){
                $address = $address . "<br />" . $this->city. " ".$this->state. " " . $this->zip;
            }
        }else{
            if($this->line_1){
                $address = $this->line_1;
            }
            if($this->line_2){
                $address = $address . "<br />" . $this->line_2;
            }
            if($this->city){
                $address = $address . "<br />" . $this->city. " ".$this->state. " " . $this->zip;
            }
        }



    	return $address;
    }

    public function basic_address()
    {
        return $this->line_1." ".$this->city." ".$this->state." ".$this->zip;
    }
}
