<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProgramGroup extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d\TH:i:s.u';

    protected $guarded = ['id'];

    public function program() : HasOne
    {
        return $this->hasOne(\App\Models\Program::class, 'program_key', 'program_key');
    }
}
