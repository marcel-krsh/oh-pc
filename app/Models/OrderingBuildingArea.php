<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderingBuildingArea extends Model
{
    protected $table = 'ordering_building_area';

    public $timestamps = false;
    
    protected $fillable = [
        'user_id',
        'audit_id',
        'building_id',
        'area_id',
        'order'
    ];
}
