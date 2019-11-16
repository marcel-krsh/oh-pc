<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * SiteVisits Model.
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class SiteVisits extends Model
{
    protected $fillable = [
        'visit_date',
        'hfa',
        'inspector_id',
        'sf_parcel_id',
        'parcel_id',
        'program_id',
        'all_structures_removed',
        'construction_debris_removed',
        'other_notes',
        'corrective_action_required',
        'retainage_released_to_contractor',
        'is_a_recap_of_maint_funds_required',
        'amount_of_maint_recapture_due',
        'was_the_property_graded_and_seeded',
        'is_there_any_signage',
        'is_grass_growing_consistently_across',
        'is_grass_mowed_weeded',
        'was_the_property_landscaped',
        'nuisance_elements_or_code_violations',
        'are_there_environmental_conditions',
        'status',
    ];

    /**
     * Pass/Fail.
     *
     * @return string
     */
    public function passFail()
    {
        $pass = 'FAIL';
        if (! is_null($this->all_structures_removed) && $this->all_structures_removed == 1) {
            $pass = 'PASS';
        } elseif ($pass != 'PASS') {
            // do nothing here - we will only show the first fail's reason.
        } else {
            $pass = 'FAIL - Structures Remain';
        }
        if (! is_null($this->construction_debris_removed) && $this->construction_debris_removed == 1 && $pass == 'PASS') {
            $pass = 'PASS';
        } elseif ($pass != 'PASS') {
            // do nothing here - we will only show the first fail's reason.
        } else {
            $pass = 'FAIL - Debris Remains';
        }
        if ((! is_null($this->corrective_action_required) && $this->corrective_action_required == 1 && $pass == 'PASS') || ($this->corrective_action_required == '' && $pass == 'PASS') || ($this->corrective_action_required == 'NULL' && $pass == 'PASS')) {
            $pass = 'PASS';
        } elseif ($pass != 'PASS') {
            // do nothing here - we will only show the first fail's reason.
        } else {
            $pass = 'FAIL - Corrective Action Required';
        }
        if ((! is_null($this->retainage_released_to_contractor) && $this->retainage_released_to_contractor == 1 && $pass == 'PASS') || (is_null($this->retainage_released_to_contractor) && $pass == 'PASS')) {
            $pass = 'PASS';
        } elseif ($pass != 'PASS') {
            // do nothing here - we will only show the first fail's reason.
        } else {
            $pass = 'FAIL - Retainage Unreleased';
        }
        if ((! is_null($this->is_a_recap_of_maint_funds_required) && $this->is_a_recap_of_maint_funds_required == 1 && $pass == 'PASS') || (is_null($this->is_a_recap_of_maint_funds_required) && $pass == 'PASS')) {
            $pass = 'PASS';
        } elseif ($pass != 'PASS') {
            // do nothing here - we will only show the first fail's reason.
        } else {
            $pass = 'FAIL - Recap Of Maintenance Owed';
        }
        if ((! is_null($this->was_the_property_graded_and_seeded) && $this->was_the_property_graded_and_seeded == 1 && $pass == 'PASS') || (is_null($this->was_the_property_graded_and_seeded) && $pass == 'PASS')) {
            $pass = 'PASS';
        } elseif ($pass != 'PASS') {
            // do nothing here - we will only show the first fail's reason.
        } else {
            $pass = 'FAIL - Property Not Graded And Seeded';
        }
        if ((! is_null($this->is_there_any_signage) && $this->is_there_any_signage == 1 && $pass == 'PASS') || (is_null($this->is_there_any_signage) && $pass == 'PASS')) {
            $pass = 'PASS';
        } elseif ($pass != 'PASS') {
            // do nothing here - we will only show the first fail's reason.
        } else {
            $pass = 'FAIL - No Signage';
        }
        if ((! is_null($this->is_grass_growing_consistently_across) && $this->is_grass_growing_consistently_across == 1 && $pass == 'PASS') || (is_null($this->is_grass_growing_consistently_across) && $pass == 'PASS')) {
            $pass = 'PASS';
        } elseif ($pass != 'PASS') {
            // do nothing here - we will only show the first fail's reason.
        } else {
            $pass = 'FAIL - Grass is Not Growing Consistently';
        }
        if ((! is_null($this->is_grass_mowed_weeded) && $this->is_grass_mowed_weeded == 1 && $pass == 'PASS') || (is_null($this->is_grass_mowed_weeded) && $pass == 'PASS')) {
            $pass = 'PASS';
        } elseif ($pass != 'PASS') {
            // do nothing here - we will only show the first fail's reason.
        } else {
            $pass = 'FAIL - Grass Has Not Been Mowed and/or Weeded';
        }
        if ((! is_null($this->was_the_property_landscaped) && $this->was_the_property_landscaped == 1 && $pass == 'PASS') || (is_null($this->was_the_property_landscaped) && $pass == 'PASS')) {
            $pass = 'PASS';
        } elseif ($pass != 'PASS') {
            // do nothing here - we will only show the first fail's reason.
        } else {
            $pass = 'FAIL - Property Not Landscaped';
        }
        if ((! is_null($this->nuisance_elements_or_code_violations) && $this->nuisance_elements_or_code_violations == 1 && $pass == 'PASS') || (is_null($this->nuisance_elements_or_code_violations) && $pass == 'PASS')) {
            $pass = 'PASS';
        } elseif ($pass != 'PASS') {
            // do nothing here - we will only show the first fail's reason.
        } else {
            $pass = 'FAIL - Nuisance or Code Violations Present';
        }
        if ((! is_null($this->are_there_environmental_conditions) && $this->are_there_environmental_conditions == 1 && $pass == 'PASS') || (is_null($this->are_there_environmental_conditions) && $pass == 'PASS')) {
            $pass = 'PASS';
        } elseif ($pass != 'PASS') {
            // do nothing here - we will only show the first fail's reason.
        } else {
            $pass = 'FAIL - Environmental Conditions Present';
        }

        return $pass;
    }

    /**
     * Parcel.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function parcel() : HasOne
    {
        return $this->hasOne(\App\Models\Parcel::class, 'id', 'parcel_id');
    }

    /**
     * Status.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function statusinfo() : HasOne
    {
        return $this->hasOne(\App\Models\VisitListStatusName::class, 'id', 'status');
    }

    /**
     * Comments.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments() : HasMany
    {
        return $this->hasMany(\App\Models\Comment::class, 'parcel_id', 'parcel_id');
    }

    /**
     * Corrections.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function corrections() : HasMany
    {
        return $this->hasMany(\App\Models\Correction::class, 'parcel_id', 'parcel_id');
    }

    /**
     * Photos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos() : HasMany
    {
        return $this->hasMany(\App\Models\Photo::class, 'parcel_id', 'parcel_id');
    }
}
