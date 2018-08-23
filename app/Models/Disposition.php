<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Dispositions Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Disposition extends Model
{
    protected $fillable = [
        'created_at',
        'entity_id',
        'parcel_id',
        'program_id',
        'account_id',
        'disposition_type_id',
        'release_date',
        'active',
        'program_income',
        'disposition_due',
        'transaction_cost',
        'special_circumstance',
        'special_circumstance_id',
        'full_description',
        'status_id',
        'permanent_parcel_id',
        'public_use_political',
        'public_use_community',
        'public_use_oneyear',
        'public_use_facility',
        'nonprofit_taxexempt',
        'nonprofit_community',
        'nonprofit_oneyear',
        'nonprofit_newuse',
        'dev_fmv',
        'dev_oneyear',
        'dev_newuse',
        'dev_purchaseag',
        'dev_taxescurrent',
        'dev_nofc',
        'hfa_calc_income',
        'hfa_calc_trans_cost',
        'hfa_calc_maintenance_total',
        'hfa_calc_monthly_rate',
        'hfa_calc_months',
        'hfa_calc_maintenance_due',
        'hfa_calc_demo_cost',
        'hfa_calc_epi',
        'hfa_calc_payback',
        'hfa_calc_gain',
        'legal_description_in_documents',
        'description_use_in_documents',
        'date_release_requested',
        'hfa_calc_months_prepaid'
    ];

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
     * Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status() : HasOne
    {
        return $this->hasOne(\App\Models\InvoiceStatus::class, 'id', 'status_id');
    }

    /**
     * Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function type() : HasOne
    {
        return $this->hasOne(\App\Models\DispositionType::class, 'id', 'disposition_type_id');
    }

    /**
     * Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function invoice() : HasOne
    {
        return $this->hasOne(\App\Models\DispositionsToInvoice::class, 'disposition_id', 'id');
    }

    /**
     * Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items() : HasMany
    {
        return $this->hasMany(\App\Models\DispositionItems::class, 'disposition_id', 'id');
    }

    /**
     * Total
     *
     * @return mixed
     */
    public function total()
    {
        return $this->items()->sum('amount');
    }

    /**
     * Entity
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function entity() : HasOne
    {
        return $this->hasOne(\App\Models\Entity::class, 'id', 'entity_id');
    }

    /**
     * Program
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function program() : HasOne
    {
        return $this->hasOne(\App\Models\Program::class, 'id', 'program_id');
    }
}
