<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProjectProgram extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    protected $guarded = ['id'];

    public function program() : HasOne
    {
        return $this->hasOne(\App\Models\Program::class, 'program_key', 'program_key');
    }
    public function status() : HasOne {
		return $this->hasOne(\App\Models\ProjectProgramStatusType::class, 'project_program_status_type_key', 'project_program_status_type_key');
	}
    public function multiple_building_status() : HasOne {
        return $this->hasOne(\App\Models\MultipleBuildingElectionType::class, 'id', 'multiple_building_election_id');
    }
}
