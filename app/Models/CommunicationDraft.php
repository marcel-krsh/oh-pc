<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Communication Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class CommunicationDraft extends Model
{
  protected $fillable = [
    'owner_id',
    'audit_id',
    'owner_type',
    'message',
    'subject',
    'project_id',
    'finding_ids'
  ];

  use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

  protected $casts = [
    'communication_id' => 'json',
    'findings_ids' => 'array'
  ];


  /**
   * Owner
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasOne
   */
  public function owner(): HasOne
  {
    return $this->hasOne(\App\Models\User::class, 'id', 'owner_id');
  }

  /**
   * Audit
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasOne
   */
  public function audit(): HasOne
  {
    // return $this->hasOne(\App\Models\CachedAudit::class, 'id', 'audit_id');
    return $this->hasOne(\App\Models\Audit::class, 'id', 'audit_id');
  }

  /**
   * Project
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasOne
   */
  public function project(): HasOne
  {
    return $this->hasOne(\App\Models\Project::class, 'id', 'project_id');
  }

}
