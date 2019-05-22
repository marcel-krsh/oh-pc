<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProjectContactRole extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    

    //
    protected $guarded = ['id'];


    public function organization() : HasOne
    {
        return $this->hasOne(\App\Models\Organization::class, 'organization_key', 'organization_key');
    }

    public function person() : HasOne
    {
        return $this->hasOne(\App\Models\People::class, 'person_key', 'person_key');
    }
    public function personsProjects($person_id){
        return $this->select('project_id')->where('person_id',$person_id)->get()->all();
    }
}
