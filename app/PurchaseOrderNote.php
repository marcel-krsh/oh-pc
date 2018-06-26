<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * PurchaseOrderNote Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class PurchaseOrderNote extends Model
{
    protected $table = 'po_notes';

    protected $fillable = [
        'note',
        'owner_id',
        'purchase_order_id'
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
