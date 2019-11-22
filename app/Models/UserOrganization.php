<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserOrganization extends Model
{
  public $timestamps = true;

  protected $guarded = ['id'];

  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }

  public function organization()
  {
    return $this->hasOne(Organization::class, 'id', 'organization_id');
  }

  	public function scopeAllita($query)
	  {
	    return $query->where('devco', 0)->orWhereNull('devco');
	  }

}
