<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Document Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Document extends Model
{
    protected $table = 'documents';

    protected $fillable = [
        'id',
        'user_id',

        // should be a pivot table
        //'categories', // json array

        // should be fields on the pivot table
        //'approved',
        //'notapproved',

        'comment', // textarea

        // polymorphic instead
        //'parcel_id',
        'documentable_id',
        'documentable_type',

        'provider_project_number',
        'provider_document_class',
        'provider_document_description',
        'provider_document_date',
        'provider_retention_schedule_code',
        'provider_notes',
        'provider_full_text',
        'provider_file_name',
        'provider_file_extension',
        'provider_file_size'
        'provider_page_count',
        'provider_created_at',
        'provider_updated_at',

        'created_at',
        'updated_at',
        'deleted_at'
    ];











    // OLD METHODS.
    // VERIFY THAT WE NEED THEM.


    /**
     * Retainages
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function retainages() : BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Retainage::class, 'document_to_retainage', 'document_id', 'retainage_id');
    }

    /**
     * Advances
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function advances() : BelongsToMany
    {
        return $this->belongsToMany(\App\Models\CostItem::class, 'document_to_advance', 'document_id', 'cost_item_id');
    }

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
}
