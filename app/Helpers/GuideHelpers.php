<?php

use App\ApprovalRequest;
use App\CostItem;
use App\Disposition;
use App\GuideProgress;
use App\GuideStep;
use App\Mail\DispositionApprovedNotification;
use App\Parcel;
use App\ReimbursementInvoice;
use App\ReimbursementPurchaseOrders;
use App\ReimbursementRequest;
use App\Retainage;
use App\User;

/**
 * Guide Check Step.
 *
 * Check if a specific step id has been completed for an asset id (a step for disposition would get a disposition_id)
 *
 * @param null $guide_step_id
 * @param null $type_id
 *
 * @return bool
 */
function guide_check_step($guide_step_id = null, $type_id = null)
{
    if ($guide_step_id && $type_id) {
        $progress = GuideProgress::where('guide_step_id', '=', $guide_step_id)->where('type_id', '=', $type_id)->first();
        if ($progress) {
            if ($progress->completed == 1) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } else {
        return false;
    }
}

/**
 * Guide Set Progress.
 *
 * @param        $type_id
 * @param        $step_id
 * @param string $status
 * @param int    $perform_checks
 *
 * @return bool
 */
function guide_set_progress($type_id, $step_id, $status = 'completed', $perform_checks = 1)
{
    if (! $step_id) {
        return false;
    }

    $step = GuideStep::where('id', '=', $step_id)->first();
    if (! $step) {
        return false;
    }

    // run checks based on step type
    if ($perform_checks) {
        if ($step->guide_step_type_id == 1) { // disposition
            $disposition = Disposition::where('id', '=', $type_id)->first();
            if ($disposition) {
                perform_all_disposition_checks($disposition, 1);
            }
        } elseif ($step->guide_step_type_id == 2) { // parcel
            $parcel = Parcel::where('id', '=', $type_id)->first();
            if ($parcel) {
                perform_all_parcel_checks($parcel);
            }
        }
    }

    if ($status == 'completed') {
        // check if already exists
        $progress = GuideProgress::where('guide_step_id', '=', $step_id)->where('type_id', '=', $type_id)->first();
        if ($progress) {
            $progress->update([
                    'started' => 1,
                    'completed' => 1,
            ]);
        } else {
            $progress = new  GuideProgress([
                'guide_step_id' => $step_id,
                'type_id' => $type_id,
                'started' => 1,
                'completed' => 1,
            ]);
            $progress->save();
        }
    } elseif ($status == 'started') {
        $progress = GuideProgress::where('guide_step_id', '=', $step_id)->where('type_id', '=', $type_id)->first();
        if ($progress) {
            $progress->update([
                    'started' => 1,
                    'completed' => 0,
            ]);
        } else {
            $progress = new  GuideProgress([
                'guide_step_id' => $step_id,
                'type_id' => $type_id,
                'started' => 1,
                'completed' => 0,
            ]);
            $progress->save();
        }
    } else {
        return false;
    }

    if ($perform_checks) {
        if ($step->guide_step_type_id == 2) { //parcel
            $parcel = Parcel::where('id', '=', $type_id)->first();
            if ($parcel) {
                guide_next_pending_step(2, $parcel->id);
            }
        }
    }

    return true;
}

/**
 * Guide Next Pending Step.
 *
 * returns the next available step
 * $step_type_id is 1 for disposition, 2 for parcel, etc.
 * $type_id is $disposition_id, $parcel_id, etc.
 * example: dd(guide_next_pending_step(2, $parcel->id)->name);
 *
 * @param        $step_type_id
 * @param        $type_id
 * @param string $show
 *
 * @return bool|null
 */
function guide_next_pending_step($step_type_id, $type_id, $show = 'id')
{
    if (! $type_id || ! $step_type_id) {
        return false;
    }

    // special case! check for retainages and advances
    $is_parcel = 0;
    $has_retainages_or_advances = 0;

    if ($step_type_id == 2) { // parcel
        $is_parcel = 1;
        $parcel = Parcel::where('id', '=', $type_id)->first();
        if (! is_null($parcel)) {
            if (count($parcel->retainages) == 0 && count($parcel->costItemsWithAdvance) == 0) {
                // ignore steps 41, 42, 43
            } else {
                $has_retainages_or_advances = 1;
            }
        }
    }

    // get all parent steps and their children
    $steps = GuideStep::where('parent_id', '=', null)->where('guide_step_type_id', '=', $step_type_id)->with('children')->orderby('id', 'ASC')->get();

    if ($steps) {
        foreach ($steps as $step) {
            // get corresponding progress and break if doesn't exist/no completed
            if ($step->children) {
                foreach ($step->children as $substep) {
                    // special case for parcels with retainages/advances
                    if ($is_parcel && ! $has_retainages_or_advances) { // parcel without retainages/advances
                        if ($substep->id != 41 && $substep->id != 42 && $substep->id != 43) {
                            $count_existing = GuideProgress::where('guide_step_id', '=', $substep->id)->where('type_id', '=', $type_id)->where('completed', '!=', 1)->count();
                            $count_substep_progress = GuideProgress::where('guide_step_id', '=', $substep->id)->where('type_id', '=', $type_id)->count();

                            if ($count_existing || ! $count_substep_progress) { // if there are no progress at all or if there are some incomplete ones
                                /*    if(!$corresponding_progress || $corresponding_progress->completed != 1){*/
                                // we are done, this is the next step to address
                                // save it in parcel cache

                                $parcel->update([
                                    'next_step' => $substep->id,
                                ]);
                                if ($substep->landbank_property_status_id !== null) {
                                    updateStatus('parcel', $parcel, 'landbank_property_status_id', $substep->landbank_property_status_id, 0, '');
                                }
                                if ($substep->hfa_property_status_id !== null) {
                                    updateStatus('parcel', $parcel, 'hfa_property_status_id', $substep->hfa_property_status_id, 0, '');
                                }

                                return $substep;
                                break;
                            }
                        }
                    } else {
                        // $corresponding_progress = GuideProgress::where('guide_step_id','=',$substep->id)->where('type_id','=',$type_id)->orderby('id','DESC')->first();
                        // if(!$corresponding_progress || $corresponding_progress->completed != 1){
                        $count_existing = GuideProgress::where('guide_step_id', '=', $substep->id)->where('type_id', '=', $type_id)->where('completed', '!=', 1)->count();
                        $count_substep_progress = GuideProgress::where('guide_step_id', '=', $substep->id)->where('type_id', '=', $type_id)->count();
                        if ($count_existing || ! $count_substep_progress) {
                            // we are done, this is the next step to address
                            if ($is_parcel) {
                                // save next step in parcel's cache
                                $parcel->update([
                                    'next_step' => $substep->id,
                                ]);
                                if ($substep->landbank_property_status_id !== null) {
                                    updateStatus('parcel', $parcel, 'landbank_property_status_id', $substep->landbank_property_status_id, 0, '');
                                }
                                if ($substep->hfa_property_status_id !== null) {
                                    updateStatus('parcel', $parcel, 'hfa_property_status_id', $substep->hfa_property_status_id, 0, '');
                                }
                            }

                            return $substep;
                            break;
                        }
                    }
                }
            }
        }
        // we are done going through the steps, didn't find any substep that qualifies

        if ($is_parcel) {
            $parcel->update([
                'next_step' => null,
            ]);
        }

        return;
    } else {
        return;
    }
}

// --------------------------------------------------------
//
// Parcel checks
//
// --------------------------------------------------------

/**
 * Guide Parcel Validated.
 *
 * @param \App\Parcel $parcel
 *
 * @return int
 */
function guide_parcel_validated(Parcel $parcel)
{
    // in ValidationResolutions table
    // if no record, validated
    // if records, check that all have lb_resolved == 1
    if ($parcel->resolutions) {
        $all_resolved = 1;
        foreach ($parcel->resolutions as $resolution) {
            if (! $resolution->lb_resolved == 1 || ! $all_resolved) {
                $all_resolved = 0;
            }
        }
        if ($all_resolved) {
            return 1;
        } else {
            return 0;
        }
    } else {
        return 1;
    }
}

/**
 * Guide Parcel Request Sent To HFA.
 *
 * @param \App\ReimbursementPurchaseOrders $reimbursement_po
 *
 * @return int|null
 */
function guide_parcel_request_sent_to_hfa(ReimbursementPurchaseOrders $reimbursement_po)
{
    if ($reimbursement_po->created_at || $reimbursement_po->updated_at) {
        return 1;
    } else {
        return;
    }
}

/**
 * Guide Parcel Docs Added.
 *
 * @param \App\Parcel $parcel
 * @param null        $cat_id_to_check
 *
 * @return int|null
 */
function guide_parcel_docs_added(Parcel $parcel, $cat_id_to_check = null)
{
    $documents = $parcel->documents;
    if (count($documents)) {
        $all_doc_categories_found = [];
        $current_doc_categories_found = [];

        // save all categories found in all documents
        foreach ($documents as $document) {
            if ($document->categories) {
                $current_doc_categories_found = json_decode($document->categories, true); // cats used by the doc
                $all_doc_categories_found = array_merge($all_doc_categories_found, $current_doc_categories_found);
            } else {
                $current_doc_categories_found = [];
            }
        }

        // if there is a cat id, use it for the test
        if ($cat_id_to_check != null) {
            if (in_array($cat_id_to_check, $all_doc_categories_found)) {
                return 1;
            } else {
                return 0;
            }
        } else {
            // RULES TO DEFINE WHICH CATS ARE NEEDED (TBD)
            // check to make sure all required cats have been found

            // until we have the correct rules, we just check if there are any document at all
            return 1; // delete when rules are implemented
        }
    } else {
        return;
    }
}

/**
 * Guide Parcel Retainage Paid Docs Uploaded.
 *
 * @param \App\Parcel $parcel
 * @param int         $retainage_id
 *
 * @return int|null
 */
function guide_parcel_retainage_paid_docs_uploaded(Parcel $parcel, $retainage_id = 0)
{
    if ($retainage_id == 0) {
        $documents = $parcel->documents;
        if (count($documents)) {
            $all_doc_categories_found = [];
            $current_doc_categories_found = [];
            foreach ($documents as $document) {
                if ($document->categories) {
                    $current_doc_categories_found = json_decode($document->categories, true); // cats used by the doc
                    $all_doc_categories_found = array_merge($all_doc_categories_found, $current_doc_categories_found);
                } else {
                    $current_doc_categories_found = [];
                }
            }

            // 9 is for retainage payment documents
            if (in_array('9', $all_doc_categories_found)) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return;
        }
    } else {
        // make sure retainage belongs to parcel
        $retainage = Retainage::where('id', '=', $retainage_id)->where('parcel_id', '=', $parcel->id)->first();

        // get retainage's documents
        if ($retainage) {
            $documents = $retainage->documents;
            if (count($documents)) {
                $all_doc_categories_found = [];
                $current_doc_categories_found = [];
                foreach ($documents as $document) {
                    if ($document->categories) {
                        $current_doc_categories_found = json_decode($document->categories, true); // cats used by the doc
                        $all_doc_categories_found = array_merge($all_doc_categories_found, $current_doc_categories_found);
                    } else {
                        $current_doc_categories_found = [];
                    }
                }

                // 9 is for retainage payment documents
                if (in_array('9', $all_doc_categories_found)) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                return;
            }
        } else {
            return;
        }
    }
}

/**
 * Guide Parcel Advance Paid Docs Uploaded.
 *
 * @param \App\Parcel $parcel
 * @param int         $advance_id
 *
 * @return int|null
 */
function guide_parcel_advance_paid_docs_uploaded(Parcel $parcel, $advance_id = 0)
{
    if ($advance_id == 0) {
        $documents = $parcel->documents;
        if (count($documents)) {
            $all_doc_categories_found = [];
            $current_doc_categories_found = [];
            foreach ($documents as $document) {
                if ($document->categories) {
                    $current_doc_categories_found = json_decode($document->categories, true); // cats used by the doc
                    $all_doc_categories_found = array_merge($all_doc_categories_found, $current_doc_categories_found);
                } else {
                    $current_doc_categories_found = [];
                }
            }

            // 47 is for retainage payment documents
            if (in_array('47', $all_doc_categories_found)) {
                return 1;
            } else {
                return 0;
            }
        } else {
            return;
        }
    } else {
        $cost_item = CostItem::where('id', '=', $advance_id)->where('parcel_id', '=', $parcel->id)->first();

        if ($cost_item) {
            $documents = $cost_item->documents;
            if (count($documents)) {
                $all_doc_categories_found = [];
                $current_doc_categories_found = [];
                foreach ($documents as $document) {
                    if ($document->categories) {
                        $current_doc_categories_found = json_decode($document->categories, true); // cats used by the doc
                        $all_doc_categories_found = array_merge($all_doc_categories_found, $current_doc_categories_found);
                    } else {
                        $current_doc_categories_found = [];
                    }
                }

                // 47 is for retainage payment documents
                if (in_array('47', $all_doc_categories_found)) {
                    return 1;
                } else {
                    return 0;
                }
            } else {
                return;
            }
        } else {
            return;
        }
    }
}

/**
 * Guide Parcel Docs Reviewed.
 *
 * @param \App\Parcel $parcel
 *
 * @return int|null
 */
function guide_parcel_docs_reviewed(Parcel $parcel)
{
    $documents = $parcel->documents;
    if (count($documents)) {
        $all_docs_reviewed = 1;
        foreach ($documents as $document) {
            if (! ($document->approved || $document->notapproved) || ! $all_docs_reviewed) {
                $all_docs_reviewed = 0;
            }
        }
        if ($all_docs_reviewed) {
            return 1;
        } else {
            return;
        }
    } else {
        return;
    }
}

/**
 * Guide Parcel Retainage Paid Docs Reviewed.
 *
 * @param \App\Parcel $parcel
 * @param int         $retainage_id
 *
 * @return int|null
 */
function guide_parcel_retainage_paid_docs_reviewed(Parcel $parcel, $retainage_id = 0)
{
    if ($retainage_id == 0) {
        // for each retainage paid document, check if retainage has been paid, then if doc was reviewed.

        $documents = $parcel->documents;
        $all_retainage_docs_reviewed = 1;
        $retainage_doc_found = 0;

        if (count($documents)) {
            $current_doc_categories_found = [];
            foreach ($documents as $document) {
                if ($document->categories) {
                    $current_doc_categories_found = json_decode($document->categories, true); // cats used by the doc

                    // 47 is for retainage payment documents
                    if (in_array('9', $current_doc_categories_found)) {
                        $retainage_doc_found = 1;
                        // check if associated retainages have been paid
                        if ($document->retainages) {
                            $retainages_paid = 1;
                            foreach ($document->retainages as $retainage) {
                                if ($retainage->paid != 1 || ! $retainages_paid) {
                                    $retainages_paid = 0;
                                }
                            }
                        } else {
                            $retainages_paid = 0;
                        }

                        if (! ($document->approved || $document->notapproved) || ! $all_retainage_docs_reviewed || ! $retainages_paid) {
                            $all_retainage_docs_reviewed = 0;
                        }
                    }
                } else {
                    $current_doc_categories_found = [];
                }
            }

            if ($all_retainage_docs_reviewed && $retainage_doc_found) {
                return 1;
            } else {
                return;
            }
        } else {
            return;
        }
    } else {
        $retainage = Retainage::where('id', '=', $retainage_id)->where('parcel_id', '=', $parcel->id)->first();

        if ($retainage) {
            $documents = $retainage->documents;
            $all_retainage_docs_reviewed = 1;
            $retainage_doc_found = 0;

            if (count($documents)) {
                $current_doc_categories_found = [];
                foreach ($documents as $document) {
                    if ($document->categories) {
                        $current_doc_categories_found = json_decode($document->categories, true); // cats used by the doc

                        // 9 is for retainage payment documents
                        if (in_array('9', $current_doc_categories_found)) {
                            $retainage_doc_found = 1;
                            if ($retainage->paid != 1) {
                                $retainages_paid = 0;
                            } else {
                                $retainages_paid = 1;
                            }

                            if (! ($document->approved || $document->notapproved) || ! $all_retainage_docs_reviewed) {
                                $all_retainage_docs_reviewed = 0;
                            }
                        }
                    } else {
                        $current_doc_categories_found = [];
                    }
                }

                if ($all_retainage_docs_reviewed && $retainage_doc_found) {
                    return 1;
                } else {
                    return;
                }
            }
        } else {
            return;
        }
    }
}

/**
 * Guide Parcel Advance Paid Docs Reviewed.
 *
 * @param \App\Parcel $parcel
 * @param int         $advance_id
 *
 * @return int|null
 */
function guide_parcel_advance_paid_docs_reviewed(Parcel $parcel, $advance_id = 0)
{
    if ($advance_id == 0) {
        $documents = $parcel->documents;
        $all_advance_docs_reviewed = 1;
        $advance_doc_found = 0;

        if (count($documents)) {
            $current_doc_categories_found = [];
            foreach ($documents as $document) {
                if ($document->categories) {
                    $current_doc_categories_found = json_decode($document->categories, true); // cats used by the doc

                    // 9 is for advance payment documents
                    if (in_array('47', $current_doc_categories_found)) {
                        $advance_doc_found = 1;
                        // check if associated retainages have been paid
                        if ($document->retainages) {
                            $advances_paid = 1;
                            foreach ($document->advances as $advance) {
                                if ($advance->advance_paid != 1 || ! $advances_paid) {
                                    $advances_paid = 0;
                                }
                            }
                        } else {
                            $advances_paid = 0;
                        }

                        if (! ($document->approved || $document->notapproved) || ! $all_advance_docs_reviewed || ! $advances_paid) {
                            $all_advance_docs_reviewed = 0;
                        }
                    }
                } else {
                    $current_doc_categories_found = [];
                }
            }

            if ($all_advance_docs_reviewed && $advance_doc_found) {
                return 1;
            } else {
                return;
            }
        } else {
            return;
        }
    } else {
        $cost_item = CostItem::where('id', '=', $advance_id)->where('parcel_id', '=', $parcel->id)->first();

        if ($cost_item) {
            $documents = $cost_item->documents;
            $all_advance_docs_reviewed = 1;
            $advance_doc_found = 0;
            $advances_paid = 1;

            if (count($documents)) {
                $current_doc_categories_found = [];
                foreach ($documents as $document) {
                    if ($document->categories) {
                        $current_doc_categories_found = json_decode($document->categories, true); // cats used by the doc

                        // 47 is for advance payment documents
                        if (in_array('47', $current_doc_categories_found)) {
                            $advance_doc_found = 1;
                            // check if associated retainages have been paid
                            if ($cost_item->advance_paid != 1 || ! $advances_paid) {
                                $advances_paid = 0;
                            }

                            if (! ($document->approved || $document->notapproved) || ! $all_advance_docs_reviewed) {
                                $all_advance_docs_reviewed = 0;
                            }
                        }
                    } else {
                        $current_doc_categories_found = [];
                    }
                }

                if ($all_advance_docs_reviewed && $advance_doc_found) {
                    return 1;
                } else {
                    return;
                }
            }
        } else {
            return;
        }
    }
}

/**
 * Guide Parcel PO Approved.
 *
 * @param \App\ReimbursementPurchaseOrders $reimbursement_po
 *
 * @return mixed|null
 */
function guide_parcel_po_approved(ReimbursementPurchaseOrders $reimbursement_po)
{
    if ($reimbursement_po) {
        $approval_status_po = guide_approval_status(3, $reimbursement_po->id);

        return $approval_status_po['is_approved'];
    } else {
        return;
    }
}

/**
 * Guide Parcel Compliance Completed.
 *
 * @param \App\Parcel $parcel
 *
 * @return int|null
 */
function guide_parcel_compliance_completed(Parcel $parcel)
{
    // if parcel has a compliance or manual compliance, make sure it was completed
    if ($parcel->compliance || $parcel->compliance_manual) {
        if (count($parcel->compliances)) {
            $last_compliance = $parcel->compliances->first();
            if ($last_compliance->score == 'Pass') {
                return 1;
            } else {
                return;
            }
        } else {
            return;
        }
    } else {
        return 1;
    }
}

/**
 * Guide Parcel Initial PO Approval.
 *
 * @param \App\ReimbursementPurchaseOrders $reimbursement_po
 *
 * @return int|null
 */
function guide_parcel_initial_po_approval(ReimbursementPurchaseOrders $reimbursement_po)
{
    $all_po_parcel_approved = 1;
    if (count($reimbursement_po->parcels)) {
        foreach ($reimbursement_po->parcels as $po_parcel) {
            if ($po_parcel->approved_in_po != 1 || ! $all_po_parcel_approved) { // reimbursement request approved by HFA
                $all_po_parcel_approved = 0;
            }
        }

        return $all_po_parcel_approved;
    } else {
        return;
    }
}

/**
 * Guide Parcel Approved Amounts Entered.
 *
 * @param \App\Parcel $parcel
 *
 * @return int|null
 */
function guide_parcel_approved_amounts_entered(Parcel $parcel)
{
    // po items are created when the PO is created or when a value is entered in approved column
    // make sure each cost_item as a po_item to check the box
    if ($parcel->hasPoItems() && $parcel->hasCostItems()) {
        $all_cost_items_have_po_items = 1;
        foreach ($parcel->costItems() as $parcel_cost_item) {
            if ($parcel_cost_item->requestItem) {
                $corresponding_request_item = $parcel_cost_item->requestItem;
                if ($corresponding_request_item->po_item) {
                    $corresponding_po_item = $corresponding_request_item->po_item;
                    if (! $corresponding_po_item || ! $all_cost_items_have_po_items) {
                        $all_cost_items_have_po_items = 0;
                    }
                } else {
                    return;
                }
            } else {
                return;
            }
        }

        return $all_cost_items_have_po_items;
    } else {
        return;
    }
}

/**
 * Guide Parcel Request Amounts Entered.
 *
 * @param \App\Parcel $parcel
 *
 * @return int|null
 */
function guide_parcel_request_amounts_entered(Parcel $parcel)
{
    if ($parcel->hasRequestItems() && $parcel->hasCostItems()) {
        $all_cost_items_have_request_items = 1;
        foreach ($parcel->costItems() as $parcel_cost_item) {
            if ($parcel_cost_item->requestItem) {
                $corresponding_request_item = $parcel_cost_item->requestItem;
                if (! $corresponding_request_item || ! $all_cost_items_have_request_items) {
                    $all_cost_items_have_request_items = 0;
                }
            } else {
                return;
            }
        }

        return $all_cost_items_have_request_items;
    } else {
        return;
    }
}

// --------------------------------------------------------
//
// Approvals checks
//
// --------------------------------------------------------

/**
 * Guide Approvals.
 *
 * get all approvals for a specific parcel/disposition/etc
 *
 * @param $approval_type_id
 * @param $link_type_id
 *
 * @return null
 */
function guide_approvals($approval_type_id, $link_type_id)
{
    $approvals = ApprovalRequest::where('approval_type_id', '=', $approval_type_id)
                    ->where('link_type_id', '=', $link_type_id)
                    ->with('actions')
                    ->with('actions.action_type')
                    ->with('approver')
                    ->get();

    if ($approvals) {
        return $approvals;
    } else {
        return;
    }
}

/**
 * Guide Approval Status.
 *
 * checks the approval status of parcel/disposition/etc based on approval type
 * returns array [approved (bool), status_name, isapprover (bool), hasapprovals (bool), approvals]
 * status: "approved", "declined", "pending"
 *
 * @param $approval_type_id
 * @param $link_type_id
 *
 * @return array
 */
function guide_approval_status($approval_type_id, $link_type_id)
{
    $approvals = guide_approvals($approval_type_id, $link_type_id);

    $hasApprovals = 0;          // are there actions recorded?
    $isApproved = 0;            // if everyone approved
    $isApprover = 0;            // is current user an approver?
    $isDeclined = 0;            // if an approver declined, the final status is declined
    $tmp_previous_approved = 1; // used to compute full approval

    // for each approval request, check the last action
    if (count($approvals)) {
        if (! Auth::check()) {
            Auth::loginUsingId(1);
        }
        foreach ($approvals as $approval) {
            if (Auth::user()->id == $approval->user_id) {
                $isApprover = 1;
            }
            if (count($approval->actions)) {
                $action = $approval->actions->first();
                if (($action->approval_action_type_id == 1 || $action->approval_action_type_id == 5) && $tmp_previous_approved == 1) {
                    // the approval request is approved or approved by proxy and the previous actions were the same
                    $isApproved = 1;
                    $hasApprovals = 1;
                    $tmp_previous_approved = 1;
                } elseif (($action->approval_action_type_id == 1 || $action->approval_action_type_id == 5)) {
                    $hasApprovals = 1;
                    $isApproved = 0;
                    $tmp_previous_approved = 1;
                } elseif ($action->approval_action_type_id == 4) {
                    $isDeclined = 1;
                    $hasApprovals = 1;
                    $isApproved = 0;
                    $tmp_previous_approved = 0;
                } else {
                    $isApproved = 0;
                    $tmp_previous_approved = 0;
                }
            } else {
                $tmp_previous_approved = 0;
                $isApproved = 0;
            }
        }
    }

    if ($isApproved) {
        $approved = 1;
        $status_name = 'approved';
        $declined = 0;
    } elseif ($isDeclined) {
        $approved = 0;
        $status_name = 'declined';
        $declined = 1;
    } else {
        $approved = 0;
        $status_name = 'pending';
        $declined = 0;
    }

    return [
                'is_approved' => $approved,
                'is_declined' => $declined,
                'status_name' => $status_name,
                'is_approver' => $isApprover,
                'has_approvals' => $hasApprovals,
                'approvals' => $approvals,
            ];
}

/**
 * Perform All Parcel Checks.
 *
 * @param \App\Parcel $parcel
 */
function perform_all_parcel_checks(Parcel $parcel)
{
    // Reimbursement Invoice
    $reimbursement_invoice = null;
    if ($parcel->associatedInvoice) {
        if ($parcel->associatedInvoice->reimbursement_invoice_id) {
            $reimbursement_invoice_id = $parcel->associatedInvoice->reimbursement_invoice_id;
            $reimbursement_invoice = ReimbursementInvoice::where('id', '=', $reimbursement_invoice_id)->first();
        }
    }

    // PO
    $reimbursement_po = null;
    if ($parcel->associatedPo) {
        if ($parcel->associatedPo->purchase_order_id) {
            $reimbursement_po_id = $parcel->associatedPo->purchase_order_id;
            $reimbursement_po = ReimbursementPurchaseOrders::where('id', '=', $reimbursement_po_id)->first();
        }
    }

    // Request
    $reimbursement_request = null;
    if ($parcel->associatedRequest) {
        if ($parcel->associatedRequest->reimbursement_request_id) {
            $reimbursement_request_id = $parcel->associatedRequest->reimbursement_request_id;
            $reimbursement_request = ReimbursementRequest::where('id', '=', $reimbursement_request_id)->first();
        }
    }

    // remove any compliance requirement for legacy
    if (! is_null($parcel->sf_parcel_id)) {
        $parcel->update([
                'compliance' => 0,
        ]);
    }

    // has the parcel been validated by LB?
    if (guide_parcel_validated($parcel)) {
        guide_set_progress($parcel->id, 24, $status = 'completed', 0);
    } else {
        guide_set_progress($parcel->id, 24, $status = 'started', 0);
    }

    if ($parcel->hasCostItems()) {
        guide_set_progress($parcel->id, 25, $status = 'completed', 0);
    } else {
        guide_set_progress($parcel->id, 25, $status = 'started', 0);
    }

    // check if request amounts have been added
    if (guide_parcel_request_amounts_entered($parcel)) {
        guide_set_progress($parcel->id, 26, $status = 'completed', 0);
    } else {
        guide_set_progress($parcel->id, 26, $status = 'started', 0);
    }

    // now check request
    if ($reimbursement_request) {
        // added to request since request exists
        guide_set_progress($parcel->id, 29, $status = 'completed', 0);

        // LB approve request internally
        $approval_request_status = guide_approval_status(2, $reimbursement_request->id);
        if ($approval_request_status['is_approved'] || ! is_null($parcel->sf_parcel_id)) {
            guide_set_progress($parcel->id, 30, $status = 'completed', 0);
        } else {
            guide_set_progress($parcel->id, 30, $status = 'started', 0);
        }
    } else {
        guide_set_progress($parcel->id, 29, $status = 'started', 0);
    }

    // was the request submitted to HFA (if there is a PO then yes)
    if ($reimbursement_po) {
        if (guide_parcel_request_sent_to_hfa($reimbursement_po)) {
            guide_set_progress($parcel->id, 31, $status = 'completed', 0);
        } else {
            guide_set_progress($parcel->id, 31, $status = 'started', 0);
        }
    } else {
        guide_set_progress($parcel->id, 31, $status = 'started', 0);
    }

    // check documents
    // have they been reviewed
    if (guide_parcel_docs_reviewed($parcel)) {
        guide_set_progress($parcel->id, 34, $status = 'completed', 0); // documents reviewed
    } else {
        guide_set_progress($parcel->id, 34, $status = 'started', 0);
    }

    // have they been uploaded?
    if (guide_parcel_docs_added($parcel)) {
        guide_set_progress($parcel->id, 27, $status = 'completed', 0); // documents added
    } else {
        guide_set_progress($parcel->id, 27, $status = 'started', 0);
    }

    // now check PO
    if ($reimbursement_po) {
        // was compliance review completed?
        if (guide_parcel_compliance_completed($parcel)) {
            guide_set_progress($parcel->id, 37, $status = 'completed', 0);
        } else {
            guide_set_progress($parcel->id, 37, $status = 'started', 0);
        }

        // is PO approved after compliance completed?
        if (guide_parcel_po_approved($reimbursement_po)) {
            guide_set_progress($parcel->id, 38, $status = 'completed', 0);
        } else {
            guide_set_progress($parcel->id, 38, $status = 'started', 0);
        }

        // is parcel approved in PO?
        if ($parcel->approved_in_po == 1) {
            guide_set_progress($parcel->id, 55, $status = 'completed', 0); // parcel approved in PO
        } else {
            guide_set_progress($parcel->id, 55, $status = 'started', 0);
        }

        // approved amounts entered
        if (guide_parcel_approved_amounts_entered($parcel)) {
            guide_set_progress($parcel->id, 35, $status = 'completed', 0); // approved amount entered
        } else {
            guide_set_progress($parcel->id, 35, $status = 'started', 0);
        }

        // is initial PO approval completed (if all parcels in PO approved)
        if (guide_parcel_initial_po_approval($reimbursement_po)) {
            guide_set_progress($parcel->id, 36, $status = 'completed', 0); // initial PO approval
            // legacy parcel handling
            if (! is_null($parcel->sf_parcel_id)) {
                guide_set_progress($parcel->id, 30, $status = 'completed', 0); // REQ Approved assumed
                guide_set_progress($parcel->id, 33, $status = 'completed', 0); // Validated Approved assumed
                updateStatus('parcel', $parcel, 'approved_in_po', 1, 0, ''); // update the status in the parcel
                guide_set_progress($parcel->id, 55, $status = 'completed', 0); // parcel approved in PO
                guide_set_progress($parcel->id, 36, $status = 'completed', 0); // PO Initial Approved assumed
                guide_set_progress($parcel->id, 38, $status = 'completed', 0); // PO Final Approved assumed
            }
        } else {
            guide_set_progress($parcel->id, 36, $status = 'started', 0);
            // legacy parcel handling
            if (! is_null($parcel->sf_parcel_id)) {
                guide_set_progress($parcel->id, 30, $status = 'completed', 0); // REQ Approved assumed
                guide_set_progress($parcel->id, 33, $status = 'completed', 0); // Validated Approved assumed
                updateStatus('parcel', $parcel, 'approved_in_po', 1, 0, ''); // update the status in the parcel
                guide_set_progress($parcel->id, 55, $status = 'completed', 0); // parcel approved in PO
                guide_set_progress($parcel->id, 36, $status = 'completed', 0); // PO Initial Approved assumed
                guide_set_progress($parcel->id, 38, $status = 'completed', 0); // PO Final Approved assumed
            }
        }
    }

    // first check when there is an invoice already
    if ($reimbursement_invoice) {
        guide_set_progress($parcel->id, 45, $status = 'completed', 0); // invoice created from PO
        guide_set_progress($parcel->id, 39, $status = 'completed', 0); // PO sent to LB assumed

        // step 5 approvals?
        // hfa tier 1
        $approval_status_1 = guide_approval_status(8, $reimbursement_invoice->id);
        if ($approval_status_1['is_approved']) {
            guide_set_progress($parcel->id, 49, $status = 'completed', 0); // Tier 1 approved
            guide_set_progress($parcel->id, 47, $status = 'completed', 0); // Invoice sent to HFA assumed
        } else {
            guide_set_progress($parcel->id, 49, $status = 'started', 0);
        }

        // hfa tier 2
        $approval_status_2 = guide_approval_status(9, $reimbursement_invoice->id);
        if ($approval_status_2['is_approved']) {
            guide_set_progress($parcel->id, 50, $status = 'completed', 0); // Tier 2 approved
            guide_set_progress($parcel->id, 47, $status = 'completed', 0); // Invoice sent to HFA assumed
        } else {
            guide_set_progress($parcel->id, 50, $status = 'started', 0);
        }

        // hfa tier 3
        $approval_status_3 = guide_approval_status(10, $reimbursement_invoice->id);
        if ($approval_status_3['is_approved']) {
            guide_set_progress($parcel->id, 51, $status = 'completed', 0); // Tier 3 approved
            guide_set_progress($parcel->id, 47, $status = 'completed', 0); // Invoice sent to HFA assumed
        } else {
            guide_set_progress($parcel->id, 51, $status = 'started', 0);
        }

        // step 4
        // landbank approval
        $approval_status = guide_approval_status(4, $reimbursement_invoice->id);
        if ($approval_status['is_approved']) {
            guide_set_progress($parcel->id, 46, $status = 'completed', 0); // Landbank approved
        } else {
            guide_set_progress($parcel->id, 46, $status = 'started', 0);
        }

        // Is it paid?
        if ($reimbursement_invoice->status_id == 6) { // is invoice paid?
            guide_set_progress($parcel->id, 54, $status = 'completed', 0); // paid
            // make sure the parcel is also marked as paid
            $parcel = updateStatus('parcel', $parcel, 'landbank_property_status_id', 14, 0, '');
            $parcel = updateStatus('parcel', $parcel, 'hfa_property_status_id', 28, 0, '');

            guide_set_progress($parcel->id, 53, $status = 'completed', 0); // step 6 - done
            guide_set_progress($parcel->id, 47, $status = 'completed', 0); // Invoice sent to HFA assumed
            // make sure legacy parcels mark the invoice, po, and req approved as they did not go through this process.
            if (! is_null($parcel->sf_parcel_id)) {
                guide_set_progress($parcel->id, 30, $status = 'completed', 0); // REQ Approved assumed
                guide_set_progress($parcel->id, 33, $status = 'completed', 0); // Validated
                updateStatus('parcel', $parcel, 'approved_in_po', 1, 0, ''); // update the status in the parcel
                guide_set_progress($parcel->id, 55, $status = 'completed', 0); // parcel approved in PO
                guide_set_progress($parcel->id, 36, $status = 'completed', 0); // PO Initial Approved assumed
                guide_set_progress($parcel->id, 38, $status = 'completed', 0); // PO Final Approved assumed
                guide_set_progress($parcel->id, 46, $status = 'completed', 0); // Invoice Approved
                guide_set_progress($parcel->id, 49, $status = 'completed', 0); // Tier 1 assumed
                guide_set_progress($parcel->id, 50, $status = 'completed', 0); // Tier 2 assumed
                guide_set_progress($parcel->id, 51, $status = 'completed', 0); // Tier 3 assumed
            } else {
                guide_set_progress($parcel->id, 55, $status = 'completed', 0); // parcel approved in PO
                guide_set_progress($parcel->id, 36, $status = 'completed', 0); // PO Initial Approved assumed
            }
            guide_set_progress($parcel->id, 52, $status = 'completed', 0); // fiscal agent notification assumed
        } elseif ($reimbursement_invoice->status_id == 8) { // was the fiscal agent notified?
            guide_set_progress($parcel->id, 52, $status = 'completed', 0); // fiscal agent notified
            guide_set_progress($parcel->id, 47, $status = 'completed', 0); // Invoice sent to HFA assumed
        }
    } else {
        guide_set_progress($parcel->id, 45, $status = 'started', 0);
        // guide_set_progress($parcel->id, 39, $status = 'started');
    }

    // guide step checks clean-up
    // step 1
    if (guide_check_step(24, $parcel->id) &&
        guide_check_step(25, $parcel->id) &&
        guide_check_step(26, $parcel->id) &&
        guide_check_step(27, $parcel->id)) {
        guide_set_progress($parcel->id, 23, $status = 'completed', 0); // step 1 - done
    } else {
        guide_set_progress($parcel->id, 23, $status = 'started', 0);
    }
    // step 2
    if (guide_check_step(29, $parcel->id) &&
        guide_check_step(30, $parcel->id) &&
        guide_check_step(31, $parcel->id)) {
        guide_set_progress($parcel->id, 28, $status = 'completed', 0); // step 2 - done
    } else {
        guide_set_progress($parcel->id, 28, $status = 'started', 0);
    }
    // step 3
    if (guide_check_step(33, $parcel->id) &&
        guide_check_step(34, $parcel->id) &&
        guide_check_step(35, $parcel->id) &&
        guide_check_step(36, $parcel->id) &&
        guide_check_step(37, $parcel->id) &&
        guide_check_step(38, $parcel->id) &&
        guide_check_step(39, $parcel->id) &&
        guide_check_step(55, $parcel->id)) {
        guide_set_progress($parcel->id, 32, $status = 'completed', 0); // step 3 - done
    } else {
        guide_set_progress($parcel->id, 32, $status = 'started', 0);
    }
    // step 3+
    if (guide_check_step(41, $parcel->id) &&
        guide_check_step(42, $parcel->id) &&
        guide_check_step(43, $parcel->id)) {
        guide_set_progress($parcel->id, 40, $status = 'completed', 0); // step 3+ - done
    } else {
        guide_set_progress($parcel->id, 40, $status = 'started', 0);
    }
    // step 4
    if (guide_check_step(45, $parcel->id) &&
        guide_check_step(46, $parcel->id) &&
        guide_check_step(47, $parcel->id)) {
        guide_set_progress($parcel->id, 44, $status = 'completed', 0); // step 4 - done
    } else {
        guide_set_progress($parcel->id, 44, $status = 'started', 0);
    }
    // step 5
    if (guide_check_step(49, $parcel->id) &&
        guide_check_step(50, $parcel->id) &&
        guide_check_step(51, $parcel->id) &&
        guide_check_step(52, $parcel->id)) {
        guide_set_progress($parcel->id, 48, $status = 'completed', 0); // step 5 - done
    } else {
        guide_set_progress($parcel->id, 48, $status = 'started', 0);
    }
    // step 6
    if (guide_check_step(54, $parcel->id)) {
        guide_set_progress($parcel->id, 53, $status = 'completed', 0); // step 6 - done
    } else {
        guide_set_progress($parcel->id, 53, $status = 'started', 0);
    }
}

// --------------------------------------------------------
//
// Disposition checks
//
// --------------------------------------------------------

/**
 * Guide Disposition Docs Added.
 *
 * @param \App\Dispositions $disposition
 * @param null              $cat_id_to_check
 *
 * @return int|null
 */
function guide_disposition_docs_added($disposition, $cat_id_to_check = null)
{
    $parcel = $disposition->parcel;

    $documents = $parcel->documents;
    if (count($documents)) {
        $all_doc_categories_found = [];
        $current_doc_categories_found = [];

        // save all categories found in all documents
        foreach ($documents as $document) {
            if ($document->categories) {
                $current_doc_categories_found = json_decode($document->categories, true); // cats used by the doc
                $all_doc_categories_found = array_merge($all_doc_categories_found, $current_doc_categories_found);
            } else {
                $current_doc_categories_found = [];
            }
        }

        // if there is a cat id, use it for the test
        if ($cat_id_to_check != null) {
            if (in_array($cat_id_to_check, $all_doc_categories_found)) {
                return 1;
            } else {
                return 0;
            }
        } else {
            // RULES TO DEFINE WHICH CATS ARE NEEDED (TBD)
            // check to make sure all required cats have been found

            // until we have the correct rules, we just check if there are any document at all
            return 1; // delete when rules are implemented
        }
    } else {
        return;
    }
}

/**
 * Guide Disposition Docs Reviewed.
 *
 * @param \App\Dispositions $disposition
 *
 * @return int|null
 */
function guide_disposition_docs_reviewed($disposition)
{
    $parcel = $disposition->parcel;

    $documents = $parcel->documents;
    if (count($documents)) {
        $all_docs_reviewed = 1;
        foreach ($documents as $document) {
            if (! ($document->approved || $document->notapproved) || ! $all_docs_reviewed) {
                $all_docs_reviewed = 0;
            }
        }
        if ($all_docs_reviewed) {
            return 1;
        } else {
            return;
        }
    } else {
        return;
    }
}

/**
 * Perform All Disposition Checks.
 *
 * $noemails using in command line checks to prevent emails to be sent out to LB
 * example command line use: perform_all_disposition_checks($disposition, 1);
 *
 * @param \App\Dispositions $disposition
 * @param null              $noemails
 */
function perform_all_disposition_checks($disposition, $noemails = null)
{
    $parcel = $disposition->parcel;

    if ($disposition->status_id == 6) {
        guide_set_progress($disposition->id, 1, $status = 'completed', 0); // step 1 - done
        guide_set_progress($disposition->id, 6, $status = 'completed', 0); // step 2 - done
        guide_set_progress($disposition->id, 13, $status = 'completed', 0); // step 3 - done
        guide_set_progress($disposition->id, 18, $status = 'completed', 0); // step 4 - done
        guide_set_progress($disposition->id, 21, $status = 'completed', 0); // step 5 - done
        guide_set_progress($disposition->id, 22, $status = 'completed', 0); // paid

        // sale final doc uploaded?
        if (guide_parcel_docs_added($parcel, 46)) {
            guide_set_progress($disposition->id, 17, $status = 'completed', 0); // step 4 final sale document uploaded - done
        } else {
            guide_set_progress($disposition->id, 17, $status = 'started', 0);
        }
    } else {
        // Check if disposition added to an invoice (step 3, 4, 5)
        if ($disposition->invoice) {
            // disposition is added to invoice step 1 done, not sure about step 2 yet
            guide_set_progress($disposition->id, 1, $status = 'completed', 0); // step 1 - done

            // is the invoice sent to LB? (step 4 completed)
            if ($disposition->invoice->status_id == 8) { // submitted to fiscal agent
                guide_set_progress($disposition->id, 6, $status = 'completed', 0); // step 2 - done
                guide_set_progress($disposition->id, 13, $status = 'completed', 0); // step 3 - done
                guide_set_progress($disposition->id, 18, $status = 'completed', 0); // step 4 - done
            }

            // HFA approvals?
            $approval_status = guide_approval_status(12, $disposition->invoice->disposition_invoice_id); // 12 is Disposition Invoice
            if ($approval_status['is_approved']) {
                guide_set_progress($disposition->id, 19, $status = 'completed', 0); // disposition invoice approved
            } else {
                guide_set_progress($disposition->id, 19, $status = 'started', 0);
            }

            // Fiscal agent release lien?
            if ($disposition->release_date != null) {
                guide_set_progress($disposition->id, 1, $status = 'completed', 0); // step 1 - done
                guide_set_progress($disposition->id, 6, $status = 'completed', 0); // step 2 - done
                guide_set_progress($disposition->id, 14, $status = 'completed', 0); // step 3 release lien - done
            }

            // were the LB notified after approval (approved because already in invoice)?
            if (guide_check_step(10, $disposition->id) != 1) {
                if (! $noemails) {
                    // notify LB that the disposition has been approved
                    $landbankDispositionManagers = User::where('entity_id', '=', $disposition->entity_id)
                                    ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                    ->where('users_roles.role_id', '=', 12)
                                    ->where('users.active', 1) // make sure the users are active
                                    ->select('id')
                                    ->get();
                    $message_recipients_array = $landbankDispositionManagers->toArray();
                    try {
                        foreach ($message_recipients_array as $userToNotify) {
                            $current_recipient = User::where('id', '=', $userToNotify)->get()->first();
                            $emailNotification = new DispositionApprovedNotification($userToNotify, $disposition->id);
                            \Mail::to($current_recipient->email)->send($emailNotification);
                        }
                    } catch (\Illuminate\Database\QueryException $ex) {
                        dd($ex->getMessage());
                    }
                }

                guide_set_progress($disposition->id, 10, $status = 'completed', 0); // step 2 - landbank notified
            }

            // is the invoice paid?
            if ($disposition->invoice->status_id == 6) {
                guide_set_progress($disposition->id, 22, $status = 'completed', 0); // paid
                //mark invoice paid
                \App\DispositionInvoice::where('id', $disposition->invoice->id)->update(['paid'=>1]);
            } else {
                guide_set_progress($disposition->id, 22, $status = 'started', 0); // not paid
                //mark invoice unpaid
                \App\DispositionInvoice::where('id', $disposition->invoice->id)->update(['paid'=>null]);
            }
        } else {
            // disposition not yet in an invoice
            // is lien release requested?
            if ($disposition->date_release_requested != null) {
                guide_set_progress($disposition->id, 1, $status = 'completed', 0); // step 1 - done
                guide_set_progress($disposition->id, 7, $status = 'completed', 0); // step 2 calculations - done
                guide_set_progress($disposition->id, 8, $status = 'completed', 0); // step 2 supporting docs - done
                guide_set_progress($disposition->id, 9, $status = 'completed', 0); // step 2 approve request - done
                guide_set_progress($disposition->id, 10, $status = 'completed', 0); // step 2 notify lb - done
                guide_set_progress($disposition->id, 11, $status = 'completed', 0); // step 2 request lien release - done
            }

            // is the request approved?
            if ($disposition->status_id == 7) { // approved
                guide_set_progress($disposition->id, 1, $status = 'completed', 0); // step 1 - done
                guide_set_progress($disposition->id, 7, $status = 'completed', 0); // step 2 confirm calculations - done
                guide_set_progress($disposition->id, 8, $status = 'completed', 0); // step 2 supporting docs - done
                guide_set_progress($disposition->id, 9, $status = 'completed', 0); // step 2 approve request - done

                 // were the LB notified after approval?
                if (guide_check_step(10, $disposition->id) != 1) {
                    if (! $noemails) {
                        // notify LB that the disposition has been approved
                        $landbankDispositionManagers = User::where('entity_id', '=', $disposition->entity_id)
                                        ->join('users_roles', 'users.id', '=', 'users_roles.user_id')
                                        ->where('users_roles.role_id', '=', 12)
                                        ->where('users.active', 1) // make sure the users are active
                                        ->select('id')
                                        ->get();
                        $message_recipients_array = $landbankDispositionManagers->toArray();
                        try {
                            foreach ($message_recipients_array as $userToNotify) {
                                $current_recipient = User::where('id', '=', $userToNotify)->get()->first();
                                $emailNotification = new DispositionApprovedNotification($userToNotify, $disposition->id);
                                \Mail::to($current_recipient->email)->send($emailNotification);
                            }
                        } catch (\Illuminate\Database\QueryException $ex) {
                            dd($ex->getMessage());
                        }
                    }

                    guide_set_progress($disposition->id, 10, $status = 'completed', 0); // step 2 - landbank notified
                }
            }

            // are the calculations confirmed?

            // was is submitted to HFA? step 1 all done
            if ($disposition->status_id == 3) { // pending HFA approval
                guide_set_progress($disposition->id, 1, $status = 'completed', 0); // step 1 - done
            } else {
                // within step 1
                // was submitted for internal approval?
                if ($disposition->status_id == 2) {
                    guide_set_progress($disposition->id, 2, $status = 'completed', 0); // step 1 form completed - done
                    guide_set_progress($disposition->id, 3, $status = 'completed', 0); // step 1 docs uploaded - done
                    guide_set_progress($disposition->id, 4, $status = 'completed', 0); // step 1 submitted for LB approval - done
                }
            }
        }
    }

    // docs uploaded?
    if (guide_disposition_docs_added($disposition)) {
        guide_set_progress($disposition->id, 2, $status = 'completed', 0); // step 1 form completed - done
        guide_set_progress($disposition->id, 3, $status = 'completed', 0); // step 1 docs uploaded - done
    } else {
        guide_set_progress($disposition->id, 3, $status = 'started', 0); // step 1 docs uploaded - not done
    }

    // have the supporting docs been reviewed?
    if (guide_disposition_docs_reviewed($disposition)) {
        guide_set_progress($disposition->id, 8, $status = 'completed', 0); // step 2 docs reviewed - done
    } else {
        guide_set_progress($disposition->id, 8, $status = 'started', 0); // step 2 docs reviewed - not done
    }

    // sale/transfer document
    if (guide_disposition_docs_added($disposition, 46)) {
        guide_set_progress($disposition->id, 17, $status = 'completed', 0); // step 4 final sale document uploaded - done
    } else {
        guide_set_progress($disposition->id, 17, $status = 'started', 0); // step 4 final sale document uploaded - not done
    }

    // clean up parent steps if all children are completed // only if disposition isn't paid
    // Step 1
    if ($disposition->status_id != 6) {
        if (guide_check_step(2, $disposition->id) &&
            guide_check_step(3, $disposition->id) &&
            guide_check_step(4, $disposition->id) &&
            guide_check_step(5, $disposition->id)) {
            guide_set_progress($disposition->id, 1, $status = 'completed', 0); // step 1 - done
        } else {
            guide_set_progress($disposition->id, 1, $status = 'started', 0);
        }
        // Step 2
        if (guide_check_step(7, $disposition->id) &&
            guide_check_step(8, $disposition->id) &&
            guide_check_step(9, $disposition->id) &&
            guide_check_step(10, $disposition->id) &&
            guide_check_step(11, $disposition->id) &&
            guide_check_step(12, $disposition->id)) {
            guide_set_progress($disposition->id, 6, $status = 'completed', 0); // step 2 - done
        } else {
            guide_set_progress($disposition->id, 6, $status = 'started', 0);
        }
        // Step 3
        if (guide_check_step(14, $disposition->id) &&
            guide_check_step(16, $disposition->id) &&
            guide_check_step(17, $disposition->id)) {
            guide_set_progress($disposition->id, 13, $status = 'completed', 0); // step 3 - done
        } else {
            guide_set_progress($disposition->id, 13, $status = 'started', 0);
        }
        // Step 4
        if (guide_check_step(19, $disposition->id) &&
            guide_check_step(20, $disposition->id)) {
            guide_set_progress($disposition->id, 18, $status = 'completed', 0); // step 4 - done
        } else {
            guide_set_progress($disposition->id, 18, $status = 'started', 0);
        }
        // Step 5
        if (guide_check_step(22, $disposition->id)) {
            guide_set_progress($disposition->id, 21, $status = 'completed', 0); // step 5 - done
        } else {
            guide_set_progress($disposition->id, 21, $status = 'started', 0);
        }
    }
}
