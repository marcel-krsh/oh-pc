<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * VendorToEntity Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class VendorToEntity extends Model
{
    protected $table = 'vendors_to_entities';

    protected $fillable = [
        'vendor_id',
        'entity_id'
    ];

    /**
     * Entity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity() : BelongsTo
    {
        return $this->belongsTo(\App\Models\Entity::class);
    }

    /**
     * Vendor
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor() : BelongsTo
    {
        return $this->belongsTo(\App\Models\Vendor::class);
    }
}
