<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ParcelsToReimbursementInvoice Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class ParcelsToReimbursementInvoice extends Model
{
    protected $table = 'parcels_to_reimbursement_invoices';

    public $timestamps = false;

    protected $fillable = [
        'parcel_id',
        'reimbursement_invoice_id'
    ];

    /**
     * Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoice() : HasMany
    {
        return $this->hasMany(\App\Models\ReimbursementInvoice::class, 'id', 'reimbursement_invoice_id');
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
