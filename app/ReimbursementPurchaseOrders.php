<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * ReimbursementPurchaseOrders Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class ReimbursementPurchaseOrders extends Model
{
    protected $fillable = [
        'sf_batch_id',
        'entity_id',
        'program_id',
        'account_id',
        'status_id',
        'rq_id',
        'active'
    ];

    /**
     * Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status() : HasOne
    {
        return $this->hasOne('App\InvoiceStatus', 'id', 'status_id');
    }

    /**
     * Parcels
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function parcels() : BelongsToMany
    {
        return $this->belongsToMany('App\Parcel', 'parcels_to_purchase_orders', 'purchase_order_id', 'parcel_id');
    }

    /**
     * Entity
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function entity() : HasOne
    {
        return $this->hasOne('App\Entity', 'id', 'entity_id');
    }

    /**
     * Program
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function program() : HasOne
    {
        return $this->hasOne('App\Program', 'id', 'program_id');
    }

    /**
     * Account
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function account() : HasOne
    {
        return $this->hasOne('App\Account', 'id', 'account_id');
    }

    /**
     * Notes
     *
     * @return mixed
     */
    public function notes()
    {
        return $this->hasMany('App\PurchaseOrderNote', 'purchase_order_id', 'id')
                ->with('owner')
                ->orderBy('created_at', 'asc');
    }

    /**
     * PO Items
     *
     * @return mixed
     */
    public function poItems()
    {
        /* @todo: what should has() be instead? */
        return $this->hasMany('App\PoItems', 'po_id', 'id')
          ->has('requestItem');
    }

    /**
     * Reset Approvals
     */
    public function resetApprovals()
    {
        /* @todo: move to a service class method */
        $approvals = ApprovalRequest::whereIn('approval_type_id', [3])
                    ->where('link_type_id', '=', $this->id)
                    ->get();

        foreach ($approvals as $approval) {
            ApprovalAction::where('approval_request_id', $approval->id)->delete();
        }
    }
}
