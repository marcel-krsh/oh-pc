<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CrrComment extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d G:i:s.u';

    protected $guarded = ['id'];

    public function user():HasOne
    {
        return $this->hasOne(\App\Models\User::class);
    }

    public function crrReport():HasOne
    {
        return $this->hasOne(\App\Models\CrrReport::class);
    }
}
