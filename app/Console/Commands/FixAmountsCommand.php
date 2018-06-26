<?php

namespace App\Console\Commands;

use \App\Parcel;
use \App\RequestItem;
use \App\PoItems;
use \App\InvoiceItem;
use \App\ParcelsToReimbursementRequest;
use \App\ParcelsToReimbursementInvoice;
use \App\ParcelsToPurchaseOrder;
use Illuminate\Console\Command;

/**
 * FixAmounts Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class FixAmountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:amounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        // find parcels with costs
        $parcels = Parcel::with('allCostItems', 'allRequestItems', 'allPoItems', 'allInvoiceItems', 'dispositions')->has('allCostItems')->get();

        $processBar = $this->output->createProgressBar(count($parcels));

        foreach ($parcels as $parcel) {
            $runPCheck = 0;
            if ($parcel->hasInvoiceItems()) {
                // now check for missing request amounts
                foreach ($parcel->allCostItems as $cost) {
                    //find the associated request amount
                    $reqMatch = RequestItem::where('ref_id', $cost->id)->first();
                    if (!isset($reqMatch->id)) {
                        // It did not find a match!
                        $this->line('Found a missing request amount on parcel:'.$parcel->parcel_id.PHP_EOL);
                        /// find the request for this parcel
                        $request = ParcelsToReimbursementRequest::where('parcel_id', $parcel->id)->first();
                        $newRequest = RequestItem::insertGetId([
                                                            'breakout_type'=>$cost->breakout_type,
                                                            'req_id'=>$request->reimbursement_request_id,
                                                            'parcel_id'=>$cost->parcel_id,
                                                            'account_id'=>$cost->account_id,
                                                            'program_id'=>$cost->program_id,
                                                            'entity_id'=>$cost->entity_id,
                                                            'expense_category_id'=>$cost->expense_category_id,
                                                            'amount'=>0,
                                                            'vendor_id'=>$cost->vendor_id,
                                                            'description'=>$cost->description,
                                                            'notes'=>$cost->notes,
                                                            'ref_id'=>$cost->id,
                                                            'breakout_item_status_id'=>$cost->breakout_item_status_id
                                                        ]);
                        $runPCheck = 1;
                    }
                }
                /// missing po items
                foreach ($parcel->allRequestItems as $request) {
                    //find the associated invoice amount
                    $poMatch = PoItems::where('ref_id', $request->id)->first();
                    if (!isset($poMatch->id)) {
                        // It did not find a match!
                        $this->line('Found a missing po amount on parcel:'.$parcel->parcel_id.PHP_EOL);
                        /// find the request for this parcel
                        $po = ParcelsToPurchaseOrder::where('parcel_id', $parcel->id)->first();
                        $newRequest = PoItems::insertGetId([
                                                            'breakout_type'=>$request->breakout_type,
                                                            'po_id'=>$po->purchase_order_id,
                                                            'parcel_id'=>$request->parcel_id,
                                                            'account_id'=>$request->account_id,
                                                            'program_id'=>$request->program_id,
                                                            'entity_id'=>$request->entity_id,
                                                            'expense_category_id'=>$request->expense_category_id,
                                                            'amount'=>0,
                                                            'vendor_id'=>$request->vendor_id,
                                                            'description'=>$request->description,
                                                            'notes'=>$request->notes,
                                                            'ref_id'=>$request->id,
                                                            'breakout_item_status_id'=>$request->breakout_item_status_id
                                                        ]);
                        $runPCheck = 1;
                    }
                }
                // missing invoice items
                foreach ($parcel->allPoItems as $po) {
                    //find the associated invoice amount
                    $invMatch = InvoiceItem::where('ref_id', $po->id)->first();
                    if (!isset($invMatch->id)) {
                        // It did not find a match!
                        $this->line('Found a missing invoice amount on parcel:'.$parcel->parcel_id.PHP_EOL);
                        /// find the invoice for this parcel
                        $invoice = ParcelsToReimbursementInvoice::where('parcel_id', $parcel->id)->first();
                        $newInv = InvoiceItem::insertGetId([
                                                            'breakout_type'=>$po->breakout_type,
                                                            'invoice_id'=>$invoice->reimbursement_invoice_id,
                                                            'parcel_id'=>$po->parcel_id,
                                                            'account_id'=>$po->account_id,
                                                            'program_id'=>$po->program_id,
                                                            'entity_id'=>$po->entity_id,
                                                            'expense_category_id'=>$po->expense_category_id,
                                                            'amount'=>0,
                                                            'vendor_id'=>$po->vendor_id,
                                                            'description'=>$po->description,
                                                            'notes'=>$po->notes,
                                                            'ref_id'=>$po->id,
                                                            'breakout_item_status_id'=>$po->breakout_item_status_id
                                                        ]);
                        $runPCheck = 1;
                    }
                }
            } elseif ($parcel->hasPoItems()) {
                // only insert up to the po items
                // there are po items - see if there are matches all the way forward
                // $this->line(PHP_EOL.PHP_EOL.'CHECKING PARCEL:'.$parcel->parcel_id.PHP_EOL);

                // now check for missing request amounts
                foreach ($parcel->allCostItems as $cost) {
                    //find the associated request amount
                    $reqMatch = RequestItem::where('ref_id', $cost->id)->first();
                    if (!isset($reqMatch->id)) {
                        // It did not find a match!
                        $this->line('Found a missing request amount on parcel:'.$parcel->parcel_id.PHP_EOL);
                        /// find the request for this parcel
                        $request = ParcelsToReimbursementRequest::where('parcel_id', $parcel->id)->first();
                        $newRequest = RequestItem::insertGetId([
                                                            'breakout_type'=>$cost->breakout_type,
                                                            'req_id'=>$request->reimbursement_request_id,
                                                            'parcel_id'=>$cost->parcel_id,
                                                            'account_id'=>$cost->account_id,
                                                            'program_id'=>$cost->program_id,
                                                            'entity_id'=>$cost->entity_id,
                                                            'expense_category_id'=>$cost->expense_category_id,
                                                            'amount'=>0,
                                                            'vendor_id'=>$cost->vendor_id,
                                                            'description'=>$cost->description,
                                                            'notes'=>$cost->notes,
                                                            'ref_id'=>$cost->id,
                                                            'breakout_item_status_id'=>$cost->breakout_item_status_id
                                                        ]);
                        //exit('Stopping Script to Check correction was made');
                        $runPCheck = 1;
                    }
                }
                /// missing po items
                foreach ($parcel->allRequestItems as $request) {
                    //find the associated invoice amount
                    $poMatch = PoItems::where('ref_id', $request->id)->first();
                    if (!isset($poMatch->id)) {
                        // It did not find a match!
                        $this->line('Found a missing po amount on parcel:'.$parcel->parcel_id.PHP_EOL);
                        /// find the request for this parcel
                        $po = ParcelsToPurchaseOrder::where('parcel_id', $parcel->id)->first();
                        $newRequest = PoItems::insertGetId([
                                                            'breakout_type'=>$request->breakout_type,
                                                            'po_id'=>$po->purchase_order_id,
                                                            'parcel_id'=>$request->parcel_id,
                                                            'account_id'=>$request->account_id,
                                                            'program_id'=>$request->program_id,
                                                            'entity_id'=>$request->entity_id,
                                                            'expense_category_id'=>$request->expense_category_id,
                                                            'amount'=>0,
                                                            'vendor_id'=>$request->vendor_id,
                                                            'description'=>$request->description,
                                                            'notes'=>$request->notes,
                                                            'ref_id'=>$request->id,
                                                            'breakout_item_status_id'=>$request->breakout_item_status_id
                                                        ]);
                        //exit('Stopping Script to Check correction was made'.PHP_EOL);
                        $runPCheck = 1;
                    }
                }
            } elseif ($parcel->hasRequestItems()) {
                // only insert up to the request items
                // there are request items - see if there are matches all the way forward
                //$this->line(PHP_EOL.PHP_EOL.'CHECKING PARCEL:'.$parcel->parcel_id.PHP_EOL);
                
                // now check for missing request amounts
                foreach ($parcel->allCostItems as $cost) {
                    //find the associated request amount
                    $reqMatch = RequestItem::where('ref_id', $cost->id)->first();
                    if (!isset($reqMatch->id)) {
                        // It did not find a match!
                        $this->line('Found a missing request amount on parcel:'.$parcel->parcel_id.PHP_EOL);
                        /// find the request for this parcel
                        $request = ParcelsToReimbursementRequest::where('parcel_id', $parcel->id)->first();
                        if (isset($request->reimbursement_request_id)) {
                            $requestId = $request->reimbursement_request_id;
                        } else {
                            $requestId = null;
                        }
                        $newRequest = RequestItem::insertGetId([
                                                            'breakout_type'=>$cost->breakout_type,
                                                            'req_id'=>$requestId,
                                                            'parcel_id'=>$cost->parcel_id,
                                                            'account_id'=>$cost->account_id,
                                                            'program_id'=>$cost->program_id,
                                                            'entity_id'=>$cost->entity_id,
                                                            'expense_category_id'=>$cost->expense_category_id,
                                                            'amount'=>0,
                                                            'vendor_id'=>$cost->vendor_id,
                                                            'description'=>$cost->description,
                                                            'notes'=>$cost->notes,
                                                            'ref_id'=>$cost->id,
                                                            'breakout_item_status_id'=>$cost->breakout_item_status_id
                                                        ]);
                        //exit('Stopping Script to Check correction was made');
                        $runPCheck = 1;
                    }
                }
            }
            // run parcel check

            if ($runPCheck == 1) {
                perform_all_parcel_checks($parcel);
                // find out next step and cache it in db
                guide_next_pending_step(2, $parcel->id);
            }
            if (!is_null($parcel->dispositions())) {
                $dispositions = $parcel->dispositions();
                foreach ($dispositions as $disposition) {
                    perform_all_disposition_checks($disposition, 1);
                    guide_next_pending_step(1, $parcel->id);
                    $this->line('Processing Disposition Steps'.PHP_EOL);
                }
            }
            $processBar->advance();
        }
    }
}
