<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Event;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * RecaptureItem Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class RecaptureItem extends Model
{
    protected $table = 'recapture_items';

    protected $fillable = [
        'breakout_type',
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
        'recapture_invoice_id'
    ];

    public static function boot()
    {
        parent::boot();

        /* @todo: move to observer */

        static::created(function ($reapture_item) {
            Event::fire('reapture_items.created', $reapture_item);
        });

        static::updated(function ($reapture_item) {
            Event::fire('reapture_items.updated', $reapture_item);
        });

        static::deleted(function ($reapture_item) {
            Event::fire('reapture_items.deleted', $reapture_item);
        });
    }

    /**
     * Breakout Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function item() : HasOne
    {
        return $this->hasOne(\App\InvoiceItem::class, 'id', 'ref_id');
    }

    /**
     * Expense Category
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function expenseCategory() : HasOne
    {
        return $this->hasOne(\App\ExpenseCategory::class, 'id', 'expense_category_id');
    }

    /**
     * Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function invoice() : HasOne
    {
        return $this->hasOne(\App\RecaptureInvoice::class, 'id', 'recapture_invoice_id');
    }

    /**
     * Program
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function program() : HasOne
    {
        return $this->hasOne(\App\Program::class, 'id', 'program_id');
    }

    /**
     * Parcel
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parcel() : HasOne
    {
        return $this->hasOne(\App\Parcel::class, 'id', 'parcel_id');
    }
}
