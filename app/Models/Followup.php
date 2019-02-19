<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Followup extends Model
{
    //
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    //
    protected $guarded = ['id'];

    public function comments() : HasMany 
    {
    	return $this->hasMany(\App\Models\Comment::class, 'followup_id', 'id');
    }

    public function photos() : HasMany 
    {
    	return $this->hasMany(\App\Models\Photo::class, 'followup_id', 'id');
    }

    public function documents() : HasMany 
    {
    	return $this->hasMany(\App\Models\SyncDocuware::class, 'followup_id', 'id');
    }

    public function finding() : HasOne 
    {
    	return $this->hasOne(\App\Models\Finding::class, 'id', 'finding_id');
    }

    public function auditor() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'created_by_user_id');
    }

    public function assigned_user() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'assigned_to_user_id');
    }

    public function resolve($now = null)
    {
        if(!$now){
            $now = Carbon::now()->format('Y-m-d H:i:s');
        }

        if($this->comment_type_submitted){
            // comment_type_approved_by_user_id
            // comment_type_last_approver_at
            $this->comment_type_approved_by_user_id = Auth::user()->id;
            $this->comment_type_last_approver_at = $now;
        }elseif($this->document_type_submitted){
            $this->document_type_approved_by_user_id = Auth::user()->id;
            $this->document_type_last_approver_at = $now;

        }elseif($this->photo_type_submitted){
            $this->photo_type_approved_by_user_id = Auth::user()->id;
            $this->photo_type_last_approver_at = $now;
        }
        $this->save();
        return 1;
    }

}
