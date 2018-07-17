<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * DocumentRuleEntry Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class DocumentRuleEntry extends Model
{
    protected $table = 'document_rule_entries';

    protected $fillable = [
        'document_category_id',
        'document_rule_id'
    ];

    /**
     * Document Rule
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function documentRule()
    {
        return $this->belongsTo(\App\DocumentRule::class);
    }
}
