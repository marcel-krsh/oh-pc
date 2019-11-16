<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncPeople extends Model
{
    public $timestamps = true;
    protected $table = 'sync_people';
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    //
    protected $guarded = ['id'];
}
