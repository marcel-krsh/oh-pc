<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Import Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Import extends Model
{
    protected $fillable = [
        'user_id',
        'entity_id',
        'account_id',
        'program_id',
        'original_file'
    ];

    /**
     * Import Rows
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function importRows() : HasMany
    {
        return $this->hasMany(\App\Models\ImportRow::class);
    }

    /**
     * Imported By
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function imported_by() : BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }
}
