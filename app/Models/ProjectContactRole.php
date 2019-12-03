<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectContactRole extends Model
{
  use SoftDeletes;

  // public $timestamps = true;
  //protected $dateFormat = 'Y-m-d\TH:i:s.u';

  //
  protected $guarded = ['id'];
  public $timestamps = true;

    function getLastEditedAttribute($value)
    {
    	return milliseconds_mutator($value);
    }

  public function organization(): HasOne
  {
    return $this->hasOne(\App\Models\Organization::class, 'organization_key', 'organization_key');
  }

  public function person(): HasOne
  {
    return $this->hasOne(\App\Models\People::class, 'person_key', 'person_key');
  }

  public function personsProjects($person_id)
  {
    return $this->select('project_id')->where('person_id', $person_id)->get()->all();
  }

  public function projectRole()
  {
    return $this->hasOne(\App\Models\ProjectRole::class, 'id', 'project_role_id');
  }
}
