<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;

use Illuminate\Database\Eloquent\Model;

class CrrPart extends Model
{
    //
    public function crr_part_type() : HasOne
    {
        return $this->hasOne(CrrPartType::class, 'id', 'crr_part_type_id');
    }
}
