<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Program Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Program extends Model
{
    protected $table = 'programs';

    protected $fillable = [
        'owner_id',
        'entity_id',
        'county_id',
        'program_name',
        'owner_type'
    ];

    /**
     * Entity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity() : BelongsTo
    {
        return $this->belongsTo(\App\Models\Entity::class);
    }

    /**
     * Account
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function account() : BelongsTo
    {
        return $this->belongsTo(\App\Models\Account::class);
    }

    /**
     * County
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function county() : BelongsTo
    {
        return $this->belongsTo(\App\Models\County::class);
    }

    /**
     * Program Rule
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function programRule() : BelongsTo
    {
        return $this->belongsTo(\App\Models\ProgramRule::class, 'default_program_rules_id');
    }

    /**
     * Activate
     *
     * @return bool
     */
    public function activate() : bool
    {
        return $this->update(['active'=>1]);
    }

    /**
     * Deactivate
     *
     * @return bool
     */
    public function deactivate() : bool
    {
        return $this->update(['active'=>0]);
    }
}
