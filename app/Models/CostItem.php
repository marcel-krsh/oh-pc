<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * CostItem Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class CostItem extends Model
{
    protected $table = 'cost_items';

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
        'requested',
        'received',
        'advance',
        'advance_paid',
        'advance_paid_date'
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function requestItem() : BelongsTo
    {
        return $this->belongsTo(\App\Models\RequestItem::class, 'id', 'ref_id');
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
     * Retainage
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function retainage() : HasOne
    {
        return $this->hasOne(\App\Models\Retainage::class, 'cost_item_id', 'id');
    }

    /**
     * PO Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function poItem() : HasManyThrough
    {
        return $this->hasManyThrough(\App\Models\PoItems::class, \App\Models\RequestItem::class, 'ref_id', 'ref_id', 'id');
    }

    /**
     * Documents
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function documents() : BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Document::class, 'document_to_advance', 'cost_item_id', 'document_id');
    }
}
