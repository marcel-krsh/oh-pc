<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Entity Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Entity extends Model
{
    protected $table = 'entities';

    protected $fillable = [
        'entity_name',
        'user_id',
        'active',
        'address1',
        'address2',
        'city',
        'state_id',
        'zip',
        'phone',
        'fax',
        'web_address',
        'email_address',
        'datatran_user',
        'datatran_password',
        'logo_link',
        'owner_type',
        'owner_id'
    ];

    /**
     * Programs
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function programs() : HasMany
    {
        return $this->hasMany(\App\Models\Program::class);
    }

    /**
     * State
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state() : BelongsTo
    {
        return $this->belongsTo(\App\Models\State::class);
    }

    /**
     * Accounts
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function accounts() : HasMany
    {
        return $this->hasMany(\App\Models\Account::class);
    }

    /**
     * Vendors
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendors() : HasMany
    {
        return $this->hasMany(\App\Models\Vendor::class);
    }

    /**
     * User
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() : BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Activate
     *
     * @return bool
     */
    public function activate()
    {
        return $this->update(['active'=>1]);
    }

    /**
     * Deactivate
     *
     * @return bool
     */
    public function deactivate()
    {
        return $this->update(['active'=>0]);
    }
}
