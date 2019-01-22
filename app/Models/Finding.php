<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Finding extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    //
    protected $guarded = ['id'];

    public function comments() : HasMany 
    {
    	return $this->hasMany(App\Models\Comment::class, 'finding_id', 'id');
    }

    public function photos() : HasMany 
    {
    	return $this->hasMany(App\Models\Photo::class, 'finding_id', 'id');
    }

    public function followups() : HasMany 
    {
    	return $this->hasMany(App\Models\Followup::class, 'finding_in', 'id');
    }


}
