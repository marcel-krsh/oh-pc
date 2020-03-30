<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
	public $timestamps = true;

	public function getLastEditedAttribute($value)
	{
		return milliseconds_mutator($value);
	}

	//protected $dateFormat = 'Y-m-d\TH:i:s.u';
	//
	protected $guarded = ['id'];

	public function household(): HasOne
	{
		return $this->hasOne(\App\Models\Household::class, 'unit_id', 'id');
	}

	public function unitBedroom(): HasOne
	{
		return $this->hasOne(\App\Models\UnitBedroom::class, 'id', 'unit_bedroom_id');
	}

	public function bedroomCount(): int
	{
		return $this->unitBedroom->unit_bedroom_number;
	}

	public function building(): HasOne
	{
		return $this->hasOne(\App\Models\Building::class, 'id', 'building_id');
	}

	public function project_id(): int
	{
		return $this->building->project_id;
	}

	public function household_events(): HasMany
	{
		return $this->hasMany(\App\Models\HouseholdEvent::class, 'unit_id', 'id');
	}

	use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;
	
	public function documents(): HasMany
	{


		return $this->hasMany(\App\Models\Document::class, 'unit_ids', 'id');
	}

	public function is_market_rate(): int
	{
		if ($this->unit_identity_id == 22) {
			return true;
		} else {
			return false;
		}
	}

	public function isAssistedUnit(): bool
	{
		foreach ($this->household_events()->get() as $event) {
			if ($event->rental_assistance_amount > 0) {
				return true;
			}
		}
		return false;
	}

	public function programs(): HasMany
	{
		return $this->hasMany(\App\Models\UnitProgram::class, 'unit_id', 'id');
	}

	public function isInspectable(): bool
	{
		// TBD
		return true;
	}

	public function has_program($program_key, $audit_id): bool
	{
		if ($this->programs()->where('audit_id', '=', $audit_id)->where('program_key', '=', $program_key)->count()) {
			return true;
		} else {
			return false;
		}
	}

	public function has_program_from_array($program_key_array, $audit_id): bool
	{
		if ($this->programs()->where('audit_id', '=', $audit_id)->whereIn('program_key', $program_key_array)->count()) {
			return true;
		} else {
			return false;
		}
	}

	public function most_recent_event()
	{
		// SystemSetting::get('household_move_in_type_id')
		return $this->household_events()->orderBy('event_date', 'desc')->first();
	}
}
