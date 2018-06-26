<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * InvoiceNote Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class InvoiceNote extends Model
{
    protected $fillable = [
        'note',
        'owner_id',
        'reimbursement_invoice_id'
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
