<?php

namespace App;

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
        return $this->hasOne(\App\User::class, 'id', 'user_id');
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
                $item =  \App\Disposition::where("id", "=", $this->type_id)->first();
                $item_type = 'disposition';
                $item_link = '/dispositions/'.$item->parcel_id.'/'.$item->id;
                $item_link_parcel = '/viewparcel/'.$item->parcel_id;
                break;
            case "communications":
                $item =  \App\Communication::where("id", "=", $this->type_id)->first();
                $item_type = 'communication';
                $item_link = '/dispositions/'.$item->parcel_id.'/'.$item->id;
                $item_link_parcel = '/viewparcel/'.$item->parcel_id;
                break;
            case "file":
                $item =  \App\Document::where("id", "=", $this->type_id)->first();
                $item_type = 'file';
                if ($item && $item->id && $item->parcel_id) {
                    $item_link = route('documents.downloadDocument', [$item->parcel_id, $item->id]);
                }
                if ($item && $item->parcel_id) {
                    $item_link_parcel = '/viewparcel/'.$item->parcel_id;
                }
                break;
            case "users":
                $item =  \App\User::where("id", "=", $this->type_id)->first();
                $item_type = 'user';
                $item_link = null;
                $item_link_parcel = null;
                break;
            case "disposition_invoices":
                $item =  \App\DispositionInvoice::where("id", "=", $this->type_id)->first();
                $item_type = null;
                $item_link = null;
                $item_link_parcel = null;
                break;
            case "reimbursement_invoices":
                $item =  \App\ReimbursementInvoice::where("id", "=", $this->type_id)->first();
                $item_type = 'invoice';
                $item_link = '/invoices/'.$item->id;
                $item_link_parcel = null;
                break;
            case "reimbursement_purchase_orders":
                $item =  \App\ReimbursementPurchaseOrders::where("id", "=", $this->type_id)->first();
                $item_type = 'po';
                $item_link = '/po/'.$item->id;
                $item_link_parcel = null;
                break;
            case "reimbursement_requests":
                $item =  \App\ReimbursementRequest::where("id", "=", $this->type_id)->first();
                $item_type = 'request';
                $item_link = '/requests/'.$item->id;
                $item_link_parcel = null;
                break;
            case "admin":
                $item =  \App\User::where("id", "=", $this->type_id)->first();
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
