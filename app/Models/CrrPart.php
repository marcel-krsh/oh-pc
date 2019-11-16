<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CrrPart extends Model
{
    //
    public function crr_part_type() : HasOne
    {
        return $this->hasOne(CrrPartType::class, 'id', 'crr_part_type_id');
    }
}
