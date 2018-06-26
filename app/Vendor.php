<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Vendor Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Vendor extends Model
{
    protected $table = 'vendors';

    protected $fillable =[
        'vendor_name',
        'vendor_email',
        'vendor_phone',
        'vendor_mobile_phone',
        'vendor_fax',
        'vendor_street_address',
        'vendor_street_address2',
        'vendor_city',
        'vendor_state_id',
        'vendor_zip',
        'vendor_duns',
        'passed_sam_gov',
        'vendor_notes'
    ];

    /**
     * State
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state() : BelongsTo
    {
        return $this->belongsTo('App\State', 'vendor_state_id');
    }

    /**
     * Entity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity() : BelongsTo
    {
        return $this->belongsTo('App\Entity');
    }

    /**
     * Entities
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function entities() : BelongsToMany
    {
        return $this->belongsToMany('App\Entity');
    }

    /**
     * Cost Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function costItems()
    {
        return $this->hasMany('App\CostItem');
    }

    /**
     * Request Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requestItems() : HasMany
    {
        return $this->hasMany('App\RequestItem');
    }

    /**
     * PO Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function poItems() : HasMany
    {
        return $this->hasMany('App\PoItems');
    }

    /**
     * Invoice Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoiceItems() : HasMany
    {
        return $this->hasMany('App\InvoiceItem');
    }

    /**
     * Totals
     *
     * @param int $category
     * @param int $program
     *
     * @return array
     */
    public function totals($category = 0, $program = 0)
    {
        $total = [];
        if ($program) {
            if ($category) {
                $total['cost'] = $this->costItems()->where('expense_category_id', '=', $category)->where('program_id', '=', $program)->sum('amount') ?: 0;
                $total['request'] = $this->requestItems()->where('expense_category_id', '=', $category)->where('program_id', '=', $program)->sum('amount') ?: 0;
                $total['po'] = $this->poItems()->where('expense_category_id', '=', $category)->where('program_id', '=', $program)->sum('amount') ?: 0;
                $total['invoice'] = $this->invoiceItems()->where('expense_category_id', '=', $category)->where('program_id', '=', $program)->sum('amount') ?: 0;
            } else {
                $total['cost'] = $this->costItems()->where('program_id', '=', $program)->sum('amount') ?: 0;
                $total['request'] = $this->requestItems()->where('program_id', '=', $program)->sum('amount') ?: 0;
                $total['po'] = $this->poItems()->where('program_id', '=', $program)->sum('amount') ?: 0;
                $total['invoice'] = $this->invoiceItems()->where('program_id', '=', $program)->sum('amount') ?: 0;
            }
        } else {
            if ($category) {
                $total['cost'] = $this->costItems()->where('expense_category_id', '=', $category)->sum('amount') ?: 0;
                $total['request'] = $this->requestItems()->where('expense_category_id', '=', $category)->sum('amount') ?: 0;
                $total['po'] = $this->poItems()->where('expense_category_id', '=', $category)->sum('amount') ?: 0;
                $total['invoice'] = $this->invoiceItems()->where('expense_category_id', '=', $category)->sum('amount') ?: 0;
            } else {
                $total['cost'] = $this->costItems()->sum('amount') ?: 0;
                $total['request'] = $this->requestItems()->sum('amount') ?: 0;
                $total['po'] = $this->poItems()->sum('amount') ?: 0;
                $total['invoice'] = $this->invoiceItems()->sum('amount') ?: 0;
            }
        }
        
        return $total;
    }
}
