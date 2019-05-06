<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LocalDocumentCategory extends Model
{
    public $timestamps = true;
    //protected $dateFormat = 'Y-m-d G:i:s.u';

    protected $guarded = ['id'];

    public function document() : HasOne
    {
        return $this->hasOne(\App\Models\Document::class, 'id', 'document_id');
    }

    public function category() : HasOne
    {
        return $this->hasOne(\App\Models\DocumentCategory::class, 'id', 'document_category_id');
    }

    public function project() : HasOne
    {
        return $this->hasOne(\App\Models\Project::class, 'id', 'project_id');
    }
}
