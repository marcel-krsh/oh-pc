<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * HistoricEmail Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class HistoricEmail extends Model
{
    protected $table = 'historic_emails';

    protected $fillable = [
        'user_id',
        'type',
        'type_id',
        'subject',
        'body'
    ];

    /**
     * Recipient
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function recipient() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'user_id');
    }

    /**
     * Type Info
     *
     * @return array
     */
    public function typeInfo() : array
    {
        $item = null;
        $item_type = null;
        $item_link = null;
        $item_link_parcel = null;

        switch ($this->type) {
            case "dispositions":
                $item =  \App\Models\Disposition::where("id", "=", $this->type_id)->first();
                $item_type = 'disposition';
                $item_link = '/dispositions/'.$item->parcel_id.'/'.$item->id;
                $item_link_parcel = '/viewparcel/'.$item->parcel_id;
                break;
            case "communications":
                $item =  \App\Models\Communication::where("id", "=", $this->type_id)->first();
                $item_type = 'communication';
                $item_link = '/dispositions/'.$item->parcel_id.'/'.$item->id;
                $item_link_parcel = '/viewparcel/'.$item->parcel_id;
                break;
            case "file":
                $item =  \App\Models\Document::where("id", "=", $this->type_id)->first();
                $item_type = 'file';
                if ($item && $item->id && $item->parcel_id) {
                    $item_link = route('documents.downloadDocument', [$item->parcel_id, $item->id]);
                }
                if ($item && $item->parcel_id) {
                    $item_link_parcel = '/viewparcel/'.$item->parcel_id;
                }
                break;
            case "users":
                $item =  \App\Models\User::where("id", "=", $this->type_id)->first();
                $item_type = 'user';
                $item_link = null;
                $item_link_parcel = null;
                break;
            case "disposition_invoices":
                $item =  \App\Models\DispositionInvoice::where("id", "=", $this->type_id)->first();
                $item_type = null;
                $item_link = null;
                $item_link_parcel = null;
                break;
            case "reimbursement_invoices":
                $item =  \App\Models\ReimbursementInvoice::where("id", "=", $this->type_id)->first();
                $item_type = 'invoice';
                $item_link = '/invoices/'.$item->id;
                $item_link_parcel = null;
                break;
            case "reimbursement_purchase_orders":
                $item =  \App\Models\ReimbursementPurchaseOrders::where("id", "=", $this->type_id)->first();
                $item_type = 'po';
                $item_link = '/po/'.$item->id;
                $item_link_parcel = null;
                break;
            case "reimbursement_requests":
                $item =  \App\Models\ReimbursementRequest::where("id", "=", $this->type_id)->first();
                $item_type = 'request';
                $item_link = '/requests/'.$item->id;
                $item_link_parcel = null;
                break;
            case "admin":
                $item =  \App\Models\User::where("id", "=", $this->type_id)->first();
                $item_type = 'user';
                $item_link = null;
                $item_link_parcel = null;
                break;
            default:
        }

        // we need the item, the item type, the item link
        return [$item, $item_type, $item_link, $item_link_parcel];
    }
}
