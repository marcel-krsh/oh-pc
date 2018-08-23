<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * DocumentRule Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class DocumentRule extends Model
{
    protected $table = 'document_rules';

    protected $fillable = [
        'amount',
        'program_rules_id',
        'expense_category_id'
    ];

    /**
     * Program Rules
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function programRules() : BelongsTo
    {
        return $this->belongsTo(\App\Models\ProgramRule::class);
    }

    /**
     * Document Rule Entries
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documentRuleEntries() : HasMany
    {
        return $this->hasMany(\App\Models\DocumentRuleEntry::class);
    }
}
