<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Auth;
use Gate;
use File;
use Storage;
use App\Programs;
use Illuminate\Http\Request;
use DB;
use App\Parcel;

/**
 * FixGreening Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class FixGreeningCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:greening';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fixes incorrect import of greening amounts ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $reimbursements = DB::table('sf_reimbursements')->join('parcels', 'sf_parcel_id', '=', 'PropertyIDRecordID')
                        ->select(
                            'parcels.id',
                            'parcels.owner_id',
                            'parcels.entity_id',
                            'parcels.account_id',
                            'GreeningCost',
                            'GreeningRequested',
                            'GreeningApproved',
                            'GreeningAdvanceOption'
                        )->distinct()->get()->all();
                             


        ///////////////////////////////////////////////////////////////////////////////
        ////////////////// FIX THE GREENING MISTAKES
        ////////////


                        
        $this->line('Correcting '.count($reimbursements).' breakout items for cost, req, po and invoice for greening where it has advance and regular greening'.PHP_EOL);
        $breakoutBar = $this->output->createProgressBar(count($reimbursements));
        $this->line(PHP_EOL.'');
        foreach ($reimbursements as $sfParcel) {
            $breakoutBar->advance();
                                

            $costItemsData = array();
            if ($sfParcel->GreeningAdvanceOption == 1) {
                // drop existing greening totals
                DB::table('cost_items')->where('parcel_id', $sfParcel->id)->where('expense_category_id', 5)->delete();
                if ($sfParcel->GreeningCost > 0) {
                    $greeningAdvanceArray = array(
                                        'breakout_type'=>3,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'expense_category_id'=>5,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->GreeningCost,
                                            'vendor_id'=>1,
                                            'description'=>'Greening Advance Requested Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                        );
                    array_push($costItemsData, $greeningAdvanceArray);
                }
            } else {
                DB::table('cost_items')->where('parcel_id', $sfParcel->id)->where('expense_category_id', 5)->delete();
                if (!is_null($sfParcel->GreeningCost)) {
                    $greeningArray = array(
                                                'breakout_type'=>1,
                                                'parcel_id'=> $sfParcel->id,
                                                'program_id'=>$sfParcel->owner_id,
                                                'entity_id'=>$sfParcel->entity_id,
                                                'account_id'=>$sfParcel->account_id,
                                                'expense_category_id'=>5,
                                                'breakout_item_status_id'=>2,
                                                'amount'=>$sfParcel->GreeningCost,
                                                'vendor_id'=>1,
                                                'description'=>'Greening Request Aggregate',
                                                'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                                );
                    array_push($costItemsData, $greeningArray);
                }
            }
            DB::table('cost_items')->insert($costItemsData);

            $parcelReqId = DB::table('parcels_to_reimbursement_requests')->select('reimbursement_request_id')->where('parcel_id', $sfParcel->id)->first();
            $requestItemsData = array();
            if (isset($parcelReqId->reimbursement_request_id)) {
                $doRequest = 1;
                $thisRequestId = $parcelReqId->reimbursement_request_id;
            } else {
                /// see if it there is a matching cost
                $costForRequest = DB::table('cost_items')->where('expense_category_id', 5)->where('parcel_id', $sfParcel->id)->count();
                if ($costForRequest > 0) {
                    $doRequest = 1;
                    $thisRequestId = null;
                } else {
                    $doRequest = 0;
                }
            }
            if ($doRequest == 1) {
                if ($sfParcel->GreeningAdvanceOption == 1) {
                    // drop existing greening totals
                    DB::table('request_items')->where('parcel_id', $sfParcel->id)->where('expense_category_id', 5)->delete();
                    if ($sfParcel->GreeningRequested > 0) {
                        $greeningAdvanceArray = array(
                                                'breakout_type'=>3,
                                                    'parcel_id'=> $sfParcel->id,
                                                    'program_id'=>$sfParcel->owner_id,
                                                    'entity_id'=>$sfParcel->entity_id,
                                                    'account_id'=>$sfParcel->account_id,
                                                    'req_id'=> $parcelReqId->reimbursement_request_id,
                                                    'expense_category_id'=>5,
                                                    'breakout_item_status_id'=>2,
                                                    'amount'=>$sfParcel->GreeningRequested,
                                                    'vendor_id'=>1,
                                                    'description'=>'Greening Advance Requested Aggregate',
                                                    'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                                );
                        array_push($requestItemsData, $greeningAdvanceArray);
                    }
                } else {
                    DB::table('request_items')->where('parcel_id', $sfParcel->id)->where('expense_category_id', 5)->delete();
                    if (!is_null($sfParcel->GreeningRequested)) {
                        $greeningArray = array(
                                                    'breakout_type'=>1,
                                                    'parcel_id'=> $sfParcel->id,
                                                    'program_id'=>$sfParcel->owner_id,
                                                    'entity_id'=>$sfParcel->entity_id,
                                                    'account_id'=>$sfParcel->account_id,
                                                    'req_id'=> $thisRequestId,
                                                    'expense_category_id'=>5,
                                                    'breakout_item_status_id'=>2,
                                                    'amount'=>$sfParcel->GreeningRequested,
                                                    'vendor_id'=>1,
                                                    'description'=>'Greening Request Aggregate',
                                                    'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                                    );
                        array_push($requestItemsData, $greeningArray);
                    }
                }
                DB::table('request_items')->insert($requestItemsData);
            } else {
                $badParcelInfo = Parcel::find($sfParcel->id);
                $this->line("PARCEL SYSTEM ID: ".$sfParcel->id." with parcel ID: ".$badParcelInfo->parcel_id." could not find a matching request id. Likely this did not have costs entered but other amounts were entered for requested approved etc. Amount requested is ".$sfParcel->GreeningRequested.". Please resolve this parcel manually.".PHP_EOL);
            }
            //TODO Determine if we need to do anything with activity logging here
            $requestItemsData = '';

            $parcelPoId = DB::table('parcels_to_purchase_orders')->select('purchase_order_id')->where('parcel_id', $sfParcel->id)->first();

            if (isset($parcelPoId)) {
                $poItemsData = array();
                                    
                // add greening in if it is there.
                if ($sfParcel->GreeningAdvanceOption == 1) {
                    DB::table('po_items')->where('parcel_id', $sfParcel->id)->where('expense_category_id', 5)->delete();
                    if ($sfParcel->GreeningApproved > 0) {
                        $greeningAdvanceArray = array(
                                            'breakout_type'=>3,
                                                'parcel_id'=> $sfParcel->id,
                                                'program_id'=>$sfParcel->owner_id,
                                                'entity_id'=>$sfParcel->entity_id,
                                                'account_id'=>$sfParcel->account_id,
                                                'po_id'=> $parcelPoId->purchase_order_id,
                                                'expense_category_id'=>5,
                                                'breakout_item_status_id'=>2,
                                                'amount'=>$sfParcel->GreeningApproved,
                                                'vendor_id'=>1,
                                                'description'=>'Greening Advance Approved Aggregate',
                                                'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            );
                        array_push($poItemsData, $greeningAdvanceArray);
                    }
                } else {
                    DB::table('po_items')->where('parcel_id', $sfParcel->id)->where('expense_category_id', 5)->delete();
                    if (!is_null($sfParcel->GreeningApproved)) {
                        $greeningArray = array(
                                                'breakout_type'=>1,
                                                'parcel_id'=> $sfParcel->id,
                                                'program_id'=>$sfParcel->owner_id,
                                                'entity_id'=>$sfParcel->entity_id,
                                                'account_id'=>$sfParcel->account_id,
                                                'po_id'=> $parcelPoId->purchase_order_id,
                                                'expense_category_id'=>5,
                                                'breakout_item_status_id'=>2,
                                                'amount'=>$sfParcel->GreeningApproved,
                                                'vendor_id'=>1,
                                                'description'=>'Greening Approved Aggregate',
                                                'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                                );
                        array_push($poItemsData, $greeningArray);
                    }
                }
                DB::table('po_items')->insert($poItemsData);
                //TODO: Determine if we need to do anything with event logging here
                $poItemsData = "";
            }

            $parcelInvId = DB::table('parcels_to_reimbursement_invoices')->select('reimbursement_invoice_id')->where('parcel_id', $sfParcel->id)->first();

            if (isset($parcelInvId)) {
                $invItemsData = array();
                if ($sfParcel->GreeningAdvanceOption == 1) {
                    DB::table('invoice_items')->where('parcel_id', $sfParcel->id)->where('expense_category_id', 5)->delete();
                    if ($sfParcel->GreeningApproved > 0) {
                        $greeningAdvanceArray = array(
                                            'breakout_type'=>3,
                                                'parcel_id'=> $sfParcel->id,
                                                'program_id'=>$sfParcel->owner_id,
                                                'entity_id'=>$sfParcel->entity_id,
                                                'account_id'=>$sfParcel->account_id,
                                                'invoice_id'=> $parcelInvId->reimbursement_invoice_id,
                                                'expense_category_id'=>5,
                                                'breakout_item_status_id'=>2,
                                                'amount'=>$sfParcel->GreeningApproved,
                                                'vendor_id'=>1,
                                                'description'=>'Greening Advance Invoiced Aggregate',
                                                'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            );
                        array_push($invItemsData, $greeningAdvanceArray);
                    }
                } else {
                    DB::table('invoice_items')->where('parcel_id', $sfParcel->id)->where('expense_category_id', 5)->delete();
                    if (!is_null($sfParcel->GreeningApproved)) {
                        $greeningArray = array(
                                                'breakout_type'=>1,
                                                'parcel_id'=> $sfParcel->id,
                                                'program_id'=>$sfParcel->owner_id,
                                                'entity_id'=>$sfParcel->entity_id,
                                                'account_id'=>$sfParcel->account_id,
                                                'invoice_id'=> $parcelInvId->reimbursement_invoice_id,
                                                'expense_category_id'=>5,
                                                'breakout_item_status_id'=>2,
                                                'amount'=>$sfParcel->GreeningApproved,
                                                'vendor_id'=>1,
                                                'description'=>'Greening Invoiced Aggregate',
                                                'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                                );
                        array_push($invItemsData, $greeningArray);
                    }
                }
                DB::table('invoice_items')->insert($invItemsData);
                $invItemsData = '';
                //   $p = Parcel::find($sfParcel->id);
            }
        } // end reimbursements for each
        $processedReimbursements = 1;
        $breakoutBar->finish();
                        
        /// put in reference ids for break out items
                    
        $requestItems = DB::table('request_items')->select('id', 'parcel_id', 'expense_category_id', 'amount')->where('expense_category_id', 5)->where('ref_id', null)->get()->all();

        $this->line(PHP_EOL.'Putting in ref_id for '.count($requestItems).' Request Items');
        $requestItemsBar = $this->output->createProgressBar(count($requestItems));
        foreach ($requestItems as $data) {
            $requestItemsBar->advance();
            /// find matching cost item
            $refId = DB::table('cost_items')
                                ->select('id')
                                ->where('parcel_id', $data->parcel_id)
                                ->where('expense_category_id', $data->expense_category_id)->first();
            if (isset($refId->id)) {
                DB::table('request_items')
                                ->where('id', $data->id)
                                ->update(
                                    ['ref_id'=> $refId->id]
                                );
            } else {
                //orphaned greening request item - delete
                DB::table('request_items')
                                ->where('id', $data->id)
                                ->delete();
                $this->line(PHP_EOL.'Found orphaned greening request_item - deleted: id '.$data->id.' in the amount of $'.$data->amount.' for parcel with system id: '.$data->parcel_id.PHP_EOL);
            }
        }
        $requestItems = '';
        $requestItemsBar->finish();

        $poItems = DB::table('po_items')->select('id', 'parcel_id', 'expense_category_id', 'amount')->where('expense_category_id', 5)->where('ref_id', null)->get()->all();

        $this->line(PHP_EOL.'Putting in ref_id for '.count($poItems).' PO Items');
        $poItemsBar = $this->output->createProgressBar(count($poItems));
        foreach ($poItems as $data) {
            $poItemsBar->advance();
            /// find matching cost item
            $refId = DB::table('request_items')
                                ->select('id')
                                ->where('parcel_id', $data->parcel_id)
                                ->where('expense_category_id', $data->expense_category_id)->first();

            if (isset($refId->id)) {
                DB::table('po_items')
                            ->where('id', $data->id)
                            ->update(
                                ['ref_id'=> $refId->id]
                            );
            } else {
                //orphaned greening request item - delete
                DB::table('po_items')
                                ->where('id', $data->id)
                                ->delete();
                $this->line(PHP_EOL.'Found orphaned greening po_item - deleted: id '.$data->id.' in the amount of $'.$data->amount.' for parcel with system id: '.$data->parcel_id.PHP_EOL);
            }
        }
        $poItems = '';
        $poItemsBar->finish();

        $invItems = DB::table('invoice_items')->select('id', 'parcel_id', 'expense_category_id', 'amount')->where('expense_category_id', 5)->where('ref_id', null)->get()->all();

        $this->line(PHP_EOL.'Putting in ref_id for '.count($invItems).' Invoice Items');
        $invoiceItemsBar = $this->output->createProgressBar(count($invItems));
        foreach ($invItems as $data) {
            /// find matching cost item
            $invoiceItemsBar->advance();
            $refId = DB::table('po_items')
                                ->select('id')
                                ->where('parcel_id', $data->parcel_id)
                                ->where('expense_category_id', $data->expense_category_id)->first();
            if (isset($refId->id)) {
                DB::table('invoice_items')
                            ->where('id', $data->id)
                            ->update(
                                ['ref_id'=> $refId->id]
                            );
            } else {
                //orphaned greening request item - delete
                DB::table('invoice_items')
                                ->where('id', $data->id)
                                ->delete();
                $this->line(PHP_EOL.'Found orphaned greening invoice_item - deleted: id '.$data->id.' in the amount of $'.$data->amount.' for parcel with system id: '.$data->parcel_id.PHP_EOL);
            }
        }
        $invItems = '';
        $invoiceItemsBar->finish();
        // */
                    
                    
        /// clear out PO Items that don't have a PO_ID
                    
                    
        $emptyPOs = DB::table('po_items')->select('*')->where('po_id', null)->orderBy('parcel_id', 'asc')->get()->all();
        $this->line(PHP_EOL.PHP_EOL.'Cleaning up '.count($emptyPOs).' empty po_items not attached to a PO'.PHP_EOL);
        //$emptyPObar = $this->output->createProgressBar(count($emptyPOs));
        foreach ($emptyPOs as $data) {
            $parcel = \App\Parcel::find($data->parcel_id);
            $poId = DB::table('parcels_to_purchase_orders')->select('*')->where('parcel_id', $data->parcel_id)->first();
            if (!isset($poId->purchase_order_id)) {
                $this->line(PHP_EOL."Parcel ".$parcel->parcel_id." with system id ".$parcel->id." had an empty po item in the amount of ".$data->amount);
                DB::table('po_items')->where('id', $data->id)->delete();
            } else {
                // there is a PO - update the record to use the PO ID
                DB::table('po_items')->where('id', $data->id)->update(['po_id'=>$poId->purchase_order_id]);
                $this->line(PHP_EOL."Parcel ".$parcel->parcel_id." with system id ".$parcel->id." was empty, but there was a PO for the parcel. Updated to be with PO ".$poId->purchase_order_id);
            }
            //$emptyPObar->advance();
        }
        //$emptyPObar->finish();
                    
        /// clear out REQ Items that don't have a RQ_ID
                    
                    
        $emptyReqs = DB::table('request_items')->select('*')->where('req_id', null)->get()->all();
        $this->line(PHP_EOL.PHP_EOL.'Cleaning up '.count($emptyReqs).' empty request_items not attached to a Request'.PHP_EOL);
        //$emptyReqsbar = $this->output->createProgressBar(count($emptyReqs));
        foreach ($emptyReqs as $data) {
            $parcel = \App\Parcel::find($data->parcel_id);
            $reqId = DB::table('parcels_to_reimbursement_requests')->select('*')->where('parcel_id', $data->parcel_id)->first();
            if (isset($reqId->reimbursement_request_id)) {
                DB::table('request_items')->where('id', $data->id)->update(['req_id'=>$reqId->reimbursement_request_id]);
                $this->line(PHP_EOL."Parcel ".$parcel->parcel_id." with system id ".$parcel->id." was empty, but there was a REQ for the parcel. Updated to be with REQ ".$reqId->reimbursement_request_id);
            } else {
                $this->line(PHP_EOL."Parcel ".$parcel->parcel_id." with system id ".$parcel->id." was empty, and is not a part of a request yet.");
            }
            //$this->line(PHP_EOL."Parcel ".$parcel->parcel_id." with system id ".$parcel->id." had an empty request in the amount of ".$data->amount);
                        //DB::table('request_items')->where('id',$data->id)->delete();
                        //$emptyReqsbar->advance();
        }
        //$emptyReqsbar->finish();
        $this->line(PHP_EOL."Updates Finsihed".PHP_EOL);
    }
}
