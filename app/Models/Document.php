<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * Document Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Document extends Model
{
    protected $table = 'documents';

    protected $guarded = ['id'];

    protected $casts = [
	    'findings_ids' => 'array'
	  ];

    public function finding() : HasOne
    {
        return $this->hasOne(\App\Models\Finding::class, 'id', 'finding_id');
    }

    public function auditor() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }

    public function comments() : HasMany
    {
        return $this->hasMany(\App\Models\Comment::class, 'document_id', 'id')->orderBy('id','asc');
    }

    // public function photos() : HasMany
    // {
    //     return $this->hasMany(\App\Models\Photo::class, 'document_id', 'id')->orderBy('id','asc');
    // }

    public function followup() : HasOne
    {
        return $this->hasOne(\App\Models\Followup::class, 'id', 'followup_id');
    }

    public function photo() : HasOne
    {
        return $this->hasOne(\App\Models\Photo::class, 'id', 'photo_id');
    }

    public function comment() : HasMany
    {
        return $this->hasMany(\App\Models\Comment::class, 'id', 'comment_id');
    }

    public function document_categories()
    {
        $local_categories = \App\Models\LocalDocumentCategory::where('document_id', '=', $this->id)->pluck('document_category_id')->toArray();
        if($local_categories){
            return \App\Models\DocumentCategory::whereIn('id', $local_categories)->get();
        }else{
            return null;
        }

    }

    // OLD METHODS.
    // VERIFY THAT WE NEED THEM.




    /**
     * Retainages
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    // public function retainages() : BelongsToMany
    // {
    //     return $this->belongsToMany(\App\Models\Retainage::class, 'document_to_retainage', 'document_id', 'retainage_id');
    // }

    /**
     * Advances
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    // public function advances() : BelongsToMany
    // {
    //     return $this->belongsToMany(\App\Models\CostItem::class, 'document_to_advance', 'document_id', 'cost_item_id');
    // }

    /**
     * Approve Categories
     *
     * Used to approve document's categories, for example all approvals upload:
     * $document->approve_categories([15, 30, 31, 32, 33, 34, 35, 36, 37, 39])
     *
     * @param null $cat_array
     *
     * @return mixed
     */

    public function approve_categories($cat_array = null)
    {
        if (is_array($cat_array)) {
            // get current approval array (category ids that had their document approved)
            if ($this->approved) {
                $current_approval_array = json_decode($this->approved, true);
            } else {
                $current_approval_array = [];
            }

            // get current 'notapproval' array (category ids that had their document approved)
            if ($this->notapproved) {
                $current_notapproval_array = json_decode($this->notapproved, true);
            } else {
                $current_notapproval_array = [];
            }

            if ($this->categories) {
                $current_category_array = json_decode($this->categories, true);
            } else {
                $current_category_array = [];
            }

            foreach ($cat_array as $catid) {
                if (in_array($catid, $current_category_array)) {
                    // if already "notapproved", remove from notapproved array
                    if (in_array($catid, $current_notapproval_array)) {
                        unset($current_notapproval_array[array_search($catid, $current_notapproval_array)]);
                        $current_notapproval_array = array_values($current_notapproval_array);
                        $notapproval = json_encode($current_notapproval_array);

                        $this->update([
                            'notapproved' => $notapproval,
                        ]);
                    }

                    // if not already approved, add to approved array
                    if (!in_array($catid, $current_approval_array)) {
                        $current_approval_array[] = $catid;
                        $approval = json_encode($current_approval_array);

                        $this->update([
                            'approved' => $approval,
                        ]);
                    }
                }
            }

            return 1;
        }
    }

    public function assigned_categories()
		{
		    return $this->belongsToMany('App\Models\DocumentCategory', 'local_document_categories', 'document_id', 'document_category_id')->where('parent_id','<>',0);
		}

}
