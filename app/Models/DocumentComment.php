<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * DocumentComment Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class DocumentComment extends Model
{
    protected $fillable = [
        'parcel_id',
        'document_id',
        'comment'
    ];

    /**
     * Documents
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function documents() : HasMany
    {
        return $this->hasMany(\App\Models\Document::class);
    }
}
