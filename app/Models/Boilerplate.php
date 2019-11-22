<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * AmenityHud Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Boilerplate extends Model
{
    protected $table = 'boilerplates';

    protected $fillable = [
        'name',
        'boilerplate',
        'creator_id',
        'global'
    ];

    /**
     * Creator
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function user() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'creator_id');
    }
    public function findings() : HasMany
    {
        return $this->hasMany(\App\Models\FindingTypeBoilerPlate::class, 'boilerplate_id', 'id');
    }
}
