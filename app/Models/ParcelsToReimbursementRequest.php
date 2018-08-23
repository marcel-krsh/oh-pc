<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ParcelsToReimbursementRequest Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class ParcelsToReimbursementRequest extends Model
{
    protected $table = 'parcels_to_reimbursement_requests';

    public $timestamps = false;

    protected $fillable = [
        'parcel_id',
        'reimbursement_request_id'
    ];

    /**
     * Request
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function request() : HasMany
    {
        return $this->hasMany(\App\Models\ReimbursementRequest::class, 'id', 'reimbursement_request_id');
    }

    /**
     * Parcel
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parcel() : HasMany
    {
        return $this->hasMany(\App\Models\Parcel::class, 'id', 'parcel_id');
    }
}
