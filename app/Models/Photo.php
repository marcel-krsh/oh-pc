<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Photo Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Photo extends Model
{
    protected $table = 'photos';

    protected $fillable = [
        'uid',
        'project_id',
        'user_id',
        'recorded_date',
        'audit_id',
        'notes',
        'latitude',
        'longitude',
        'correction_id',
        'comment_id',
        'deleted'
    ];

    public function photos() : HasMany 
    {
        return $this->hasMany(App\Models\Photo::class, 'photo_id', 'id');
    }

    public function photo() : HasOne 
    {
        return $this->hasOne(App\Models\Photo::class, 'id', 'photo_id');
    }

    public function document() : HasOne 
    {
        return $this->hasOne(App\Models\SyncDocuware::class, 'id', 'document_id');
    }

    public function finding() : HasOne 
    {
        return $this->hasOne(App\Models\Finding::class, 'id', 'finding_id');
    }

    public function followup() : HasOne 
    {
        return $this->hasOne(App\Models\Followup::class, 'id', 'followup_id');
    }

    public function comment() : HasOne 
    {
        return $this->hasOne(App\Models\Followup::class, 'id', 'followup_id');
    }
}
