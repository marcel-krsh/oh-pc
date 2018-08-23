<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Event;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * DispositionItems Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class DispositionItems extends Model
{
    protected $table = 'disposition_items';

    protected $fillable = [
        'breakout_type',
        'disposition_id',
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
        'disposition_invoice_id'
    ];

    public static function boot()
    {
        parent::boot();

        /* @todo: convert these to an observer class */

        static::created(function ($disposition_item) {
            Event::fire('disposition_items.created', $disposition_item);
        });

        static::updated(function ($disposition_item) {
            Event::fire('disposition_items.updated', $disposition_item);
        });

        static::deleted(function ($disposition_item) {
            Event::fire('disposition_items.deleted', $disposition_item);
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
     * Parcel
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parcel() : HasOne
    {
        return $this->hasOne(\App\Models\Parcel::class, 'id', 'parcel_id');
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
