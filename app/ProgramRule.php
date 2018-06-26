<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * ProgramRule Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class ProgramRule extends Model
{
    protected $table = 'program_rules';

    protected $fillable = [
        'hfa',
        'rules_name',
        'maintenance_recap_pro_rate',
        'imputed_cost_per_parcel',
        'acquisition_advance',
        'pre_demo_advance',
        'demolition_advance',
        'greening_advance',
        'maintenance_advance',
        'administration_advance',
        'other_advance',
        'nip_loan_payoff_advance',
        'acquisition_max_advance',
        'pre_demo_max_advance',
        'demolition_max_advance',
        'greening_max_advance',
        'maintenance_max_advance',
        'administration_max_advance',
        'other_max_advance',
        'nip_loan_payoff_max_advance',
        'acquisition_max',
        'pre_demo_max',
        'demolition_max',
        'greening_max',
        'maintenance_max',
        'admin_max_percent',
        'other_max',
        'nip_loan_payoff_max',
        'acquisition_min',
        'pre_demo_min',
        'demolition_min',
        'greening_min',
        'maintenance_min',
        'admin_min',
        'other_min',
        'nip_loan_payoff_min'
    ];

    /**
     * Document Rules
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documentRules() : HasMany
    {
        return $this->hasMany('App\DocumentRule', 'program_rules_id');
    }

    /**
     * Reimbursement Rules
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reimbursementRules() : HasMany
    {
        return $this->hasMany('App\ReimbursementRule', 'program_rules_id');
    }
}
