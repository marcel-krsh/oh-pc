<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Event;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * InvoiceItem Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class InvoiceItem extends Model
{
    protected $table = 'invoice_items';

    protected $fillable = [
        'breakout_type',
        'invoice_id',
        'parcel_id',
        'program_id',
        'entity_id',
        'account_id',
        'expense_category_id',
        'amount',
        'vendor_id',
        'description',
        'notes',
        'ref_id',
        'breakout_item_status_id',
        'advance'
    ];

    public static function boot()
    {
        parent::boot();

        /* @todo: convert this to an observer class */

        static::created(function ($invoice_item) {
            Event::fire('invoice_items.created', $invoice_item);
        });

        static::updated(function ($invoice_item) {
            Event::fire('invoice_items.updated', $invoice_item);
        });

        static::deleted(function ($invoice_item) {
            Event::fire('invoice_items.deleted', $invoice_item);
        });
    }

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
     * PO Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function poItem() : HasOne
    {
        return $this->hasOne(\App\Models\PoItems::class, 'id', 'ref_id');
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
}
