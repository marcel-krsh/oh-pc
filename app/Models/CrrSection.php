<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;

class CrrSection extends Model
{
    //
    // public $timestamps = false;

    public function parts(): HasMany{

        return $this->hasMany('App\Models\CrrPart')
        ->join('crr_part_orders','crr_parts.id','=','crr_part_orders.crr_part_id')
        ->orderBy('order','asc')
        ;
    }
}
