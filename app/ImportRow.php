<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ImportRow Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class ImportRow extends Model
{
    protected $fillable = [
        'import_id',
        'table_name',
        'row_id',
        'row_updated'
    ];

    /**
     * Import
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function import() : BelongsTo
    {
        return $this->belongsTo('App\Import');
    }
}
