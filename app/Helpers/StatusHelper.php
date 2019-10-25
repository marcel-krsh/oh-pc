<?php

/**
 * Check Status
 *
* checks parcel/po/req/invoice/disp invoice status
*
 * @param string $type
 * @param        $id
 *
 * @return int
 */
function checkStatus($type = 'parcel', $id)
{
    // not sure what an acceptable/useful output should be here? Full audit in an array?
    return 1;
}

/**
 * Update Status
 *
 * updates statuses of parcel/po/req/invoice/disp invoice and return the item
 * two ways to use: $parcel = updateStatus("parcel", $parcel, 'landbank_property_status_id', 43, 0, "If you want the model to update with the new status");
 * or updateStatus("parcel", $parcel, 'landbank_property_status_id', 43, 0, "If you want to update the data without impacting the current model");
 *
 * @param string $type       (parcel, po, request, invoice, disposition_invoice)
 * @param        $item       (should be $parcel or $request, not just its id)
 * @param        $field      (column name)
 * @param        $status     (int)
 * @param int    $withCheck
 * @param string $comment
 *
 * @return int
 */
function updateStatus($type = 'parcel', $item, $field, $status, $withCheck = 0, $comment = '')
{
    if (!Auth::check()) {
        Auth::loginUsingId(1);
    }
    switch ($type) {
        case "parcel":
        // test 67110205 landbank_property_status_id 46 hfa_property_status_id 28
            if ($item) {
                $explanation = $field.'_explanation';
                $item->update([$field => $status, $explanation => $comment]);
                // $lc = new App\LogConverter('parcel', 'update');
                // $lc->setFrom(Auth::user())->setTo($item)->setDesc('Parcel '.$item->parcel_id.' had a status updated. '.$comment.' Made visible via '.$explanation)->save();
                return $item;
            }

            break;
        case "request":
            if ($item) {
                $item->update([$field => $status]);
                // $lc = new App\LogConverter('request', 'update');
                // $lc->setFrom(Auth::user())->setTo($item)->setDesc('Reimbursement Request '.$item->id.' had a status updated. '.$comment)->save();
                return $item;
            }
            break;
        case "po":
            if ($item) {
                $item->update([$field => $status]);
                // $lc = new App\LogConverter('po', 'update');
                // $lc->setFrom(Auth::user())->setTo($item)->setDesc('Reimbursement Purchase Order '.$item->id.' had a status updated. '.$comment)->save();
                return $item;
            }
            break;
        case "invoice":
            if ($item) {
                $item->update([$field => $status]);
                // $lc = new App\LogConverter('reimbursement_invoices', 'update');
                // $lc->setFrom(Auth::user())->setTo($item)->setDesc('Reimbursement Invoice '.$item->id.' had a status updated. '.$comment)->save();
                return $item;
            }
            break;
        case "disposition_invoice":
            if ($item) {
                $item->update([$field => $status]);
                // $lc = new App\LogConverter('dispositions', 'update');
                // $lc->setFrom(Auth::user())->setTo($item)->setDesc('Disposition Invoice '.$item->id.' had a status updated. '.$comment)->save();
                return $item;
            }
            break;
        default:
    }

    return 1;
}
