<?php

namespace App\Models;

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
        return $this->hasOne(\App\Models\ExpenseCategory::class, 'id', 'expense_category_id');
    }

    /**
     * Vendor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function vendor() : HasOne
    {
        return $this->hasOne(\App\Models\Vendor::class, 'id', 'vendor_id');
    }

    /**
     * Request Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function requestItem() : HasOne
    {
        return $this->hasOne(\App\Models\RequestItem::class, 'id', 'ref_id');
    }

    /**
     * Invoice Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoiceItem()
    {
        return $this->belongsTo(\App\Models\InvoiceItem::class, 'id', 'ref_id');
    }

    /**
     * Breakout Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function breakoutStatus() : HasOne
    {
        return $this->hasOne(\App\Models\BreakoutItemsStatus::class, 'id', 'breakout_item_status_id');
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
