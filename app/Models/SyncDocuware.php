<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SyncDocuware extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    

    //
    protected $guarded = ['id'];

    public function comments() : HasMany 
    {
        return $this->hasMany(App\Models\Comment::class, 'comment_id', 'id');
    }
   

    
}
