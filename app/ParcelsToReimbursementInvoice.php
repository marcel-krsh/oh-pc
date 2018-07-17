<?php

namespace App;

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
        return $this->hasMany(\App\ReimbursementInvoice::class, 'id', 'reimbursement_invoice_id');
    }

    /**
     * Parcel
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parcel() : HasMany
    {
        return $this->hasMany(\App\Parcel::class, 'id', 'parcel_id');
    }
}
