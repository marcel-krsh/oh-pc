<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Comment Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Comment extends Model
{
    protected $table = 'comments';
    //
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';


    protected $fillable = [
        'uid',
        'user_id',
        'project_id',
        'audit_id',
        'amenity_id',
        'finding_id',
        'photo_id',
        'followup_id',
        'document_id',
        'unit_id',
        'building_id',
        'comment_id',
        'recorded_date',
        'comment',
        'latitude',
        'longitude',
        'deleted'
    ];

    public function comments() : HasMany 
    {
        return $this->hasMany(\App\Models\Comment::class, 'comment_id', 'id');
    }

    public function comment() : HasOne 
    {
        return $this->hasOne(\App\Models\Comment::class, 'id', 'comment_id');
    }

    public function amenity() : HasOne 
    {
        return $this->hasOne(\App\Models\AmenityInspection::class, 'id', 'amenity_id');
    }

    public function photo() : HasOne 
    {
        return $this->hasOne(\App\Models\Photo::class, 'id', 'photo_id');
    }

    public function photos() : HasMany 
    {
        return $this->hasMany(\App\Models\Photo::class, 'comment_id', 'id');
    }

    public function document() : HasOne 
    {
        return $this->hasOne(\App\Models\Document::class, 'id', 'document_id');
    }

    public function documents() : HasMany 
    {
        return $this->hasMany(\App\Models\Document::class, 'comment_id', 'id');
    }

    public function finding() : HasOne 
    {
        return $this->hasOne(\App\Models\Finding::class, 'id', 'finding_id');
    }

    public function followup() : HasOne 
    {
        return $this->hasOne(\App\Models\Followup::class, 'id', 'followup_id');
    }

    public function user() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }

}
