<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * RequestNote Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class RequestNote extends Model
{
    protected $fillable = [
        'note',
        'owner_id',
        'reimbursement_request_id'
    ];

    /**
     * Owner
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function owner() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'owner_id');
    }
}
