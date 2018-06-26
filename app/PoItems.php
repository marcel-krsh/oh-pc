<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * PoItems Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class PoItems extends Model
{
    protected $fillable = [
        'breakout_type',
        'po_id',
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
     * Expense Category
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
     * Request Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function requestItem() : HasOne
    {
        return $this->hasOne('App\RequestItem', 'id', 'ref_id');
    }

    /**
     * Invoice Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoiceItem()
    {
        return $this->belongsTo('App\InvoiceItem', 'id', 'ref_id');
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

    /**
     * Has Request Item
     *
     * @return bool
     */
    public function hasRequestItem()
    {
        return (bool) $this->requestItem()->count();
    }
}
