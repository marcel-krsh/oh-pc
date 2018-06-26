<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Retainage Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Retainage extends Model
{
    protected $table = 'retainages';

    protected $fillable = [
        'vendor_id',
        'expense_category_id',
        'parcel_id',
        'cost_item_id',
        'retainage_amount',
        'paid',
        'date_paid'
    ];

    /**
     * Parcel
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parcel() : HasOne
    {
        return $this->hasOne('App\Parcel', 'id', 'parcel_id');
    }

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
     * Cost Item
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function costItem() : BelongsTo
    {
        return $this->belongsTo('App\CostItem', 'cost_item_id', 'id');
    }

    /**
     * Document
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function documents() : BelongsToMany
    {
        return $this->belongsToMany('App\Document', 'document_to_retainage', 'retainage_id', 'document_id');
    }
}
