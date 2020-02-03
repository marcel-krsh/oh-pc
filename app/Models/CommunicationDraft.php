<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Communication Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class CommunicationDraft extends Model
{
	use SoftDeletes;

	protected $fillable = [
		'owner_id',
		'audit_id',
		'owner_type',
		'message',
		'subject',
		'project_id',
		'finding_ids',
	];

	protected $casts = [
		'communication_id' => 'json',
		'findings_ids' => 'array',
	];

	/**
	 * Owner
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function owner(): HasOne
	{
		return $this->hasOne(\App\Models\User::class, 'id', 'owner_id');
	}

	/**
	 * Audit
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function audit(): HasOne
	{
		// return $this->hasOne(\App\Models\CachedAudit::class, 'id', 'audit_id');
		return $this->hasOne(\App\Models\Audit::class, 'id', 'audit_id');
	}

	/**
	 * Project
	 *
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function project(): HasOne
	{
		return $this->hasOne(\App\Models\Project::class, 'id', 'project_id');
	}

	public function getAttachedDocuments()
	{
		$document_json = $this->documents;
		if (!is_null($document_json)) {
			$document_array = json_decode($this->documents, true);
			$document_ids = [];
			foreach ($document_ids as $key => $document_id) {
				array_push($document_ids, $document_id[0]);
				// if($document) {
				// 	$categories = LocalDocumentCategory::where('document_id', $document->id)->get();
				// }
			}
			return $document = Document::whereIn('id', $document_ids)->get();
		} else {
			return [];
		}
	}

	public function getSelectedDocuments()
	{
		$document_json = $this->selected_documents;
		if (!is_null($document_json)) {
			$document_array = json_decode($this->selected_documents, true);
			$document_ids = [];
			foreach ($document_array as $key => $document_id) {
				array_push($document_ids, $document_id[0]);
				// if($document) {
				// 	$categories = LocalDocumentCategory::where('document_id', $document->id)->get();
				// }
			}
			// return $document_ids;
			return $document = Document::whereIn('id', $document_ids)->get();
		} else {
			return [];
		}
	}
}
