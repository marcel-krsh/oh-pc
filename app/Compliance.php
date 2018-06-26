<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Compliance Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Compliance extends Model
{
    use SoftDeletes;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'sf_parcel_id',
        'property_type_id',
        'property_yes',
        'property_notes',
        'parcel_id',
        'program_id',
        'created_by_user_id',
        'analyst_id',
        'auditor_id',
        'audit_date',
        'checklist_yes',
        'checklist_notes',
        'consolidated_certs_pass',
        'consolidated_certs_notes',
        'contractors_yes',
        'contractors_notes',
        'environmental_yes',
        'environmental_notes',
        'funding_limits_pass',
        'funding_limits_notes',
        'inelligible_costs_yes',
        'inelligible_costs_notes',
        'items_Reimbursed',
        'note_mortgage_pass',
        'note_mortgage_notes',
        'payment_processing_pass',
        'payment_processing_notes',
        'loan_requirements_pass',
        'loan_requirements_notes',
        'photos_yes',
        'photos_notes',
        'salesforce_yes',
        'salesforce_notes',
        'right_to_demo_pass',
        'right_to_demo_notes',
        'reimbursement_doc_pass',
        'reimbursement_doc_notes',
        'target_area_yes',
        'target_area_notes',
        'sdo_pass',
        'sdo_notes',
        'score',
        'if_fail_corrected',
        'property_pass',
        'property_pass_notes',
        'random_audit',
        'parcel_hfa_status_id'
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
     * Property Type
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function propertyType() : HasOne
    {
        return $this->hasOne('App\ParcelType', 'id', 'property_type_id');
    }

    /**
     * Program
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function program() : HasOne
    {
        return $this->hasOne('App\Program', 'id', 'program_id');
    }

    /**
     * Creator
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function creator() : HasOne
    {
        return $this->hasOne('App\User', 'id', 'created_by_user_id');
    }

    /**
     * Analyst
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function analyst() : HasOne
    {
        return $this->hasOne('App\User', 'id', 'analyst_id');
    }

    /**
     * Auditor
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function auditor() : HasOne
    {
        return $this->hasOne('App\User', 'id', 'auditor_id');
    }
}
