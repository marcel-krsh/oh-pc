<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Event;

class Finding extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    //
    protected $guarded = ['id'];

    public static function boot()
    {
        parent::boot();

        static::created(function ($finding) {
            Event::fire('finding.created', $finding);
        });
    }

    public function comments() : HasMany 
    {
    	return $this->hasMany(\App\Models\Comment::class, 'finding_id', 'id');
    }

    public function boilerplates() 
    {
       $boilerplates = \DB::table('boilerplates')
            ->join('finding_type_boilerplates', 'boilerplates.id', '=', 'finding_type_boilerplates.finding_id')
            ->where('finding_type_boilerplates.finding_id',$this->id)
            ->select('boilerplates.*')->get();

        return $boilerplates;
    }

    public function photos() : HasMany 
    {
    	return $this->hasMany(\App\Models\Photo::class, 'finding_id', 'id');
    }

    public function followups() : HasMany 
    {
    	return $this->hasMany(\App\Models\Followup::class, 'finding_id', 'id');
    }

    public function has_followup_within_24h()
    {
        if( count( $this->followups()->whereDate('date_due','<=', Carbon::today()->addHours(24))->whereDate('date_due','>=',Carbon::today()) ) ){
            return 1;
        }else{
            return 0;
        }
    }

    public function has_followup_overdue()
    {
        if( count( $this->followups()->whereDate('date_due','<=', Carbon::today()) ) ){
            return 1;
        }else{
            return 0;
        }
    }


}
