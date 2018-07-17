<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ParcelsToPurchaseOrder Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class ParcelsToPurchaseOrder extends Model
{
    protected $table = 'parcels_to_purchase_orders';

    public $timestamps = false;

    protected $fillable = [
        'parcel_id',
        'purchase_order_id'
    ];

    /**
     * PO
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function po() : HasMany
    {
        return $this->hasMany(\App\ReimbursementPurchaseOrders::class, 'id', 'purchase_order_id');
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
