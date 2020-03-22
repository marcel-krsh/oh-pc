<?php

namespace App\Models;

use Event;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Finding extends Model
{

	public $timestamps = true;
	//protected $dateFormat = 'Y-m-d\TH:i:s.u';

	//
	protected $guarded = ['id'];

	public static function boot()
	{
		parent::boot();

		static::created(function ($finding) {
			Event::dispatch('finding.created', $finding);
		});
	}

	public function comments(): HasMany
	{
		return $this->hasMany(\App\Models\Comment::class, 'finding_id', 'id')->whereNULL('photo_id')->orderBy('id', 'asc');
	}

	use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

	public function document()
	{
		return $this->hasManyJson('App\Models\Document', 'finding_ids');
	}

	// public function documents(): HasMany
	// {
	// 	return $this->hasMany(\App\Models\Document::class, 'finding_id', 'id')->orderBy('id', 'asc');
	// }

	public function project(): HasOne
	{
		return $this->hasOne(\App\Models\Project::class, 'id', 'project_id');
	}

	public function unit(): HasOne
	{
		return $this->hasOne(\App\Models\Unit::class, 'id', 'unit_id');
	}

	public function building(): HasOne
	{
		return $this->hasOne(\App\Models\Building::class, 'id', 'building_id');
	}

	public function audit(): HasOne
	{
		return $this->hasOne(\App\Models\Audit::class, 'id', 'audit_id');
	}

	public function audit_plain(): HasOne
	{
		return $this->hasOne(\App\Models\Audit::class, 'id', 'audit_id')
			->select('id', 'project_id', 'start_date', 'completed_date', 'person_id', 'monitoring_status_type_key', 'last_edited', 'created_at', 'updated_at');
	}

	public function amenity_inspection(): HasOne
	{
		return $this->hasOne(\App\Models\AmenityInspection::class, 'id', 'amenity_inspection_id');
	}

	public function amenity(): HasOne
	{
		return $this->hasOne(\App\Models\Amenity::class, 'id', 'amenity_id');
	}

	public function finding_type(): HasOne
	{
		return $this->hasOne(\App\Models\FindingType::class, 'id', 'finding_type_id');
	}

	public function level_description()
	{
		if ($this->level == 1) {
			return $this->finding_type()->first()->one_description;
		} elseif ($this->level == 2) {
			return $this->finding_type()->first()->two_description;
		} elseif ($this->level == 3) {
			return $this->finding_type()->first()->three_description;
		}
	}

	public function finding_types()
	{
		// list all finding_types related to the amenity
		// if the amenity is a unit - we show the finding types for unit
		// if it is a building amenity, we show be, bs, and ca
		// if it is a project amenity we show site and ca

		$amenity = $this->amenity;
		if ($amenity->unit) {
			return FindingType::where('unit', '=', 1)->orderBy('name', 'asc')->get();
		} elseif ($amenity->building_system || $amenity->building_exterior) {
			return FindingType::where('building_exterior', '=', 1)->orwhere('building_system', '=', 1)->orwhere('common_area', '=', 1)->orderBy('name', 'asc')->get();
		} elseif ($amenity->project) {
			return FindingType::where('site', '=', 1)->orwhere('common_area', '=', 1)->orderBy('name', 'asc')->get();
		} elseif ($amenity->file) {
			return FindingType::where('file', '=', 1)->orderBy('name', 'asc')->get();
		}

		return null;
	}

	public function auditor(): HasOne
	{
		return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
	}

	public function boilerplates()
	{
		$boilerplates = \DB::table('boilerplates')
			->join('finding_type_boilerplates', 'boilerplates.id', '=', 'finding_type_boilerplates.finding_id')
			->where('finding_type_boilerplates.finding_id', $this->id)
			->select('boilerplates.*')->get();

		return $boilerplates;
	}

	public function photos(): HasMany
	{
		return $this->hasMany(\App\Models\Photo::class, 'finding_id', 'id')->orderBy('id', 'asc');
	}

	public function followups(): HasMany
	{
		return $this->hasMany(\App\Models\Followup::class, 'finding_id', 'id')->orderBy('id', 'asc');
	}

	public function has_followup_within_24h()
	{
		if (count($this->followups()->whereDate('date_due', '<=', Carbon::today()->addHours(24))->whereDate('date_due', '>=', Carbon::today()))) {
			return 1;
		} else {
			return 0;
		}
	}

	public function has_followup_overdue()
	{
		if (count($this->followups()->whereDate('date_due', '<=', Carbon::today()))) {
			return 1;
		} else {
			return 0;
		}
	}

	public function icon()
	{
		$type = $this->finding_type->type;

		if ($type == "nlt") {
			return "a-booboo";
		} elseif ($type == "lt") {
			return "a-skull";
		} elseif ($type == "file") {
			return "a-folder";
		}

		return '';
	}

	public function is_current_audit()
	{

		// current project's audit
		$current_audit = $this->project->lastAudit();

		if ($current_audit->id == $this->audit_id) {
			return 1;
		} else {
			return 0;
		}
	}
}
