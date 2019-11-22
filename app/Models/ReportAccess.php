<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReportAccess extends Model
{
  public $timestamps = true;
  //protected $dateFormat = 'Y-m-d\TH:i:s.u';

  protected $guarded = ['id'];
  protected $table   = 'report_access';

  public function project()
  {
    return $this->hasOne(\App\Models\Project::class, 'id', 'project_id');
  }

  public function user()
  {
    return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
  }

  public function scopeAllita($query)
  {
    return $query->where('devco', 0)->orWhereNull('devco');
  }
}
