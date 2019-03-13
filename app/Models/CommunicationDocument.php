<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * CommunicationDocument Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class CommunicationDocument extends Model
{
    protected $fillable = [
        'communication_id',
        'document_id',
        'sync_docuware_id'
    ];

    public $timestamps = false;

    /**
     * Communication
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function communication() : HasOne
    {
        return $this->hasOne(\App\Models\Communication::class);
    }

    /**
     * Document
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function document() : HasOne
    {
        return $this->hasOne(\App\Models\Document::class, 'id', 'document_id');
    }
}
