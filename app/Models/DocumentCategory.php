<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * DocumentCategory Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class DocumentCategory extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d G:i:s.u';

    protected $guarded = ['id'];

    public function parent(){
    	if($this->parent_id !== 0){
    		return $this->hasOne('App\Models\DocumentCategory','id','parent_id');
    	} else {
    		return null;
    	}

    }


		public function scopeActive($query)
    {
        return $query->where('active', 1);
    }


}
