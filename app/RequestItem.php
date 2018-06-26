<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * RequestItem Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class RequestItem extends Model
{
    protected $fillable = [
        'breakout_type',
        'req_id',
        'parcel_id',
        'account_id',
        'program_id',
        'entity_id',
        'expense_category_id',
        'amount',
        'vendor_id',
        'description',
        'notes',
        'ref_id',
        'breakout_item_status_id',
        'advance'
    ];

    /**
     * ExpenseCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function expenseCategory() : HasOne
    {
        return $this->hasOne('App\ExpenseCategory', 'id', 'expense_category_id');
    }

    /**
     * Vendor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function vendor() : HasOne
    {
        return $this->hasOne('App\Vendor', 'id', 'vendor_id');
    }

    /**
     * Cost Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function costItem() : HasOne
    {
        return $this->hasOne('App\CostItem', 'id', 'ref_id');
    }

    /**
     * PO Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function poItem() : BelongsTo
    {
        return $this->belongsTo('App\PoItems', 'id', 'ref_id');
    }

    /**
     * Breakout Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function breakoutStatus() : HasOne
    {
        return $this->hasOne('App\BreakoutItemsStatus', 'id', 'breakout_item_status_id');
    }
}
