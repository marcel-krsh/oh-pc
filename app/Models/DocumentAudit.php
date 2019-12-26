<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * DocumentCategory Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class DocumentAudit extends Model
{
	public $timestamps = true;
	//protected $dateFormat = 'Y-m-d G:i:s.u';

	protected $guarded = ['id'];

	public function audit(): HasOne
	{
		return $this->hasOne(\App\Models\Audit::class, 'id', 'audit_id');
	}

	public function document(): HasOne
	{
		return $this->hasOne(\App\Models\Document::class, 'id', 'document_id');
	}
}
