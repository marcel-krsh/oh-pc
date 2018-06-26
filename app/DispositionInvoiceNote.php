<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * DispositionInvoiceNote Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class DispositionInvoiceNote extends Model
{
    protected $fillable = [
        'note',
        'owner_id',
        'disposition_invoice_id'
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
