<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Parcel;
use App\SfReimbursements;
use App\SfDisposition;
use App\Disposition;
use App\Transaction;
use DB;

/**
 * UpdateDates Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class UpdateDatesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateDates:sf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'DateUpdate ';

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
        // $dates = Parcel::select('id','sf_parcel_id')->where('sf_parcel_id','<>',NULL)->get()->all();
        // $this->line('Updating created at dates from salesforce data.'.PHP_EOL);
        // $bar = $this->output->createProgressBar(count($dates));
        // forEach($dates as $date){
        // 	$actualDate = sf_parcels::select('PropertyIDCreatedDate')->where('PropertyIDRecordID',$date->sf_parcel_id)->first();
        // 	if(isset($actualDate->PropertyIDCreatedDate)){
        // 	Parcel::where('id',$date->id)->update(['created_at' => date('Y-m-d H:i:s',strtotime($actualDate->PropertyIDCreatedDate))]);
        // 	}else{
        // 		$this->line('Cannot find matching sf parcel for id '.$date->id.PHP_EOL);
        // 	}
        // 	$bar->advance();
        // }
        //    $bar->finish();

        //    $this->line(PHP_EOL.'Updating Invoice Payment Dates');
        //    $reimbursements = '';

        //                    ///////////////////////////////////////////////////////////////////////////////
        //                    ////////////////// GET TRANSACTION INFORMATION FIRST BY GETTING INVOICES
        //                    ////////////
        //                    $invoicesToTotal = DB::table('reimbursement_invoices')->join('programs','programs.id','=','reimbursement_invoices.program_id')->join('reimbursement_purchase_orders','reimbursement_purchase_orders.id','=','po_id')->select('reimbursement_invoices.program_id','program_name','sf_program_name','reimbursement_invoices.sf_batch_id','reimbursement_invoices.id as invoice_id','po_id','rq_id')->where('reimbursement_invoices.sf_batch_id','<>',NULL)->orderBy('reimbursement_invoices.id','ASC')->get()->all();
                        


        //                    ///////////////////////////////////////////////////////////////////////////////
        //                    ////////////////// SUM TOTALS FOR ALL TOTALS PAID THAT MATCH THE
        //                    //////////// BATCH ID AND PROGRAM NAME

        //                    DB::table('transactions')->where('updated_at',NULL)->update(['updated_at'=>date('Y-m-d H:i:s',strtotime('2/22/2017'))]);
        //                    $this->line(PHP_EOL.'Creating '.count($invoicesToTotal).' Invoices');
        //                    //$invoiceBar = $this->output->createProgressBar(count($invoicesToTotal));
        //                    foreach($invoicesToTotal as $data){
        //                        ///$invoiceBar->advance();

        //                        $invoiceData = DB::table('sf_reimbursements')->select('ReimbursementCreatedDate','DatePaid')->where('ProgramProgramName','=',$data->sf_program_name)->where('BatchNumber',$data->sf_batch_id)->orderBy('DatePaid','Desc')->first();
        //                        $invoiceTotal = DB::table('sf_reimbursements')->select('TotalPaid')->where('ProgramProgramName',$data->sf_program_name)->where('BatchNumber',$data->sf_batch_id)->sum('TotalPaid');
        //                        $invoicedTotal = DB::table('sf_reimbursements')->select('TotalApproved')->where('ProgramProgramName',$data->sf_program_name)->where('BatchNumber',$data->sf_batch_id)->sum('TotalApproved');

        //                        if(isset($invoiceData->ReimbursementCreatedDate)){
        //                            // Set Invoice Date
        //                            DB::table('reimbursement_invoices')->where('id',$data->invoice_id)
        //                                ->update(['created_at'=>date('Y-m-d H:i:s',strtotime($invoiceData->ReimbursementCreatedDate))]);
        //                            DB::table('invoice_notes')->insert(['reimbursement_invoice_id'=>$data->invoice_id, 'owner_type'=>'user','owner_id'=>'2','note'=>'System generated note: The creatation date of '.$invoiceData->DatePaid.' for this invoice was determined by the date of the salesforce reimbursement record created date. Actual invoice (approval date in the legacy system) date may have been earlier or later. Please simply use this date as an estimated reference.','created_at'=>date('Y-m-d H:i:s',time())]);
        //                            // set PO date
        //                            DB::table('reimbursement_purchase_orders')->where('id',$data->po_id)
        //                                ->update(['created_at'=>date('Y-m-d H:i:s',strtotime($invoiceData->ReimbursementCreatedDate)),'status_id'=>7]);
        //                            DB::table('po_notes')->insert(['purchase_order_id'=>$data->invoice_id, 'owner_type'=>'user','owner_id'=>'2','note'=>'System generated note: Date of this Purchase Order was determined by the date of the salesforce reimbursement record created date. Actual PO (approval) date may have been earlier or later. Please simply use this date as an estimated reference.','created_at'=>date('Y-m-d H:i:s',time())]);
        //                            // set RQ Date
        //                            DB::table('reimbursement_requests')->where('id',$data->rq_id)
        //                                ->update(['created_at'=>date('Y-m-d H:i:s',strtotime($invoiceData->ReimbursementCreatedDate)),'status_id'=>7]);
        //                            DB::table('request_notes')->insert(['reimbursement_request_id'=>$data->invoice_id, 'owner_type'=>'user','owner_id'=>'2','note'=>'System generated note: Date of this Request was determined by the date of the salesforce reimbursement record created date. Actual Request date may have been earlier or later. Please simply use this date as an estimated reference.','created_at'=>date('Y-m-d H:i:s',time())]);
        //                        }else{
        //                            $this->line(PHP_EOL.'!!!!!!!!!!! Cannot read ReimbursementCreatedDate for batch id '.$data->sf_batch_id.' on invoice '.$data->invoice_id.' !!!!!!!!!!'.PHP_EOL);
        //                        }

        //                        if(isset($invoiceData->DatePaid)){
        //                            $tid = DB::table('transactions')->select('*')->where('link_to_type_id',$data->invoice_id)->where('type_id',1)->first();


        //                            if(isset($tid->updated_at)){
        //                                if(strtotime($tid->updated_at) < strtotime('March 20, 2017') || strtotime($tid->date_entered) < strtotime('10/20/14') || is_null($tid->date_entered) || $tid->date_entered == "0000-00-00"){
                                         

        //                                    DB::table('transactions')->where('id',$tid->id)->update([
        //                                            'date_entered'=>date('Y-m-d H:i:s',strtotime($invoiceData->ReimbursementCreatedDate)),
        //                                            'date_cleared'=>date('Y-m-d H:i:s',strtotime($invoiceData->DatePaid)),
        //                                            'created_at'=>date('Y-m-d H:i:s',strtotime($invoiceData->ReimbursementCreatedDate))
        //                                        ]);
                                        
        //                                    $this->line('Updated Transaction (previously updated) '.$tid->id.' with date '.date('Y-m-d H:i:s',strtotime($invoiceData->ReimbursementCreatedDate)).PHP_EOL);
        //                                 } else {
        //                                    $this->line('Skipping transaction '.$tid->id.' with a updated at date of '.$tid->updated_at.' and date entered of '.$tid->date_entered);
        //                                 }
        //                             } else if (isset($invoiceData->ReimbursementCreatedDate)){
        //                                DB::table('transactions')->where('id',$tid->id)->update([
        //                                            'date_entered'=>date('Y-m-d H:i:s',strtotime($invoiceData->ReimbursementCreatedDate)),
        //                                            'date_cleared'=>date('Y-m-d H:i:s',strtotime($invoiceData->DatePaid)),
        //                                            'created_at'=>date('Y-m-d H:i:s',strtotime($invoiceData->ReimbursementCreatedDate))
        //                                        ]);
        //                                $this->line('0000000000000000 Updated Transaction '.$tid->id.' with date '.date('Y-m-d H:i:s',strtotime($invoiceData->ReimbursementCreatedDate)).PHP_EOL);
        //                             } else {
        //                                $this->line(PHP_EOL.'!!!!!!!! TRANSACTION NOT FOUND FOR INVOICE '.$data->invoice_id.' !!!!!!!!'.PHP_EOL);
        //                             }
        //                         } else {
        //                            $this->line(PHP_EOL.'!!!!!!!! THIS INVOICE IS NOT PAID '.$data->invoice_id.' !!!!!!!!'.PHP_EOL);
        //                         }
        //                         if($invoicedTotal == $invoiceTotal){
        //                            // update all the parcels as paid status
        //                            $parcelsToUpdate = DB::table('parcels_to_reimbursement_invoices')->select('parcel_id')->where('reimbursement_invoice_id',$data->invoice_id)->get()->all();
        //                            $this->line(PHP_EOL.'Updating '.count($parcelsToUpdate).' parcels to paid status for invoice'.$data->invoice_id.PHP_EOL);
        //                            foreach($parcelsToUpdate as $ptu){
        //                                DB::table('parcels')->where('id',$ptu->parcel_id)->update(['landbank_property_status_id'=>14,'hfa_property_status_id'=>28]);
        //                            //    $p = Parcel::find($ptu->parcel_id);
                                 
        //                            }
        //                            /// set invoice status to paid
        //                            DB::table('reimbursement_invoices')->where('id',$data->invoice_id)->update(['status_id'=>6]);
        //                            $parcelsToUpdate = '';
        //                        } else {
        //                            // update all the parcels as pending payment status
        //                            $parcelsToUpdate = DB::table('parcels_to_reimbursement_invoices')->select('parcel_id')->where('reimbursement_invoice_id',$data->invoice_id)->get()->all();
        //                            $this->line(PHP_EOL.'Updating '.count($parcelsToUpdate).' parcels to invoice sent/received status for invoice'.$data->invoice_id.PHP_EOL);
        //                            foreach($parcelsToUpdate as $ptu){
        //                                DB::table('parcels')->where('id',$ptu->parcel_id)->update(['landbank_property_status_id'=>13,'hfa_property_status_id'=>27]);
        //                            //    $p = Parcel::find($ptu->parcel_id);
                                 
        //                            }
        //                            /// set invoice status to pending payment
        //                            DB::table('reimbursement_invoices')->where('id',$data->invoice_id)->update(['status_id'=>4]);
        //                            $parcelsToUpdate = '';
        //                        }
                            


        //                    }

        $invoicesToTotal = '';
        //$invoiceBar->finish();
        $this->line(PHP_EOL.PHP_EOL."FIXING DISPOSITIONS".PHP_EOL.PHP_EOL."FIXING DISPOSITIONS".PHP_EOL.PHP_EOL."FIXING DISPOSITIONS".PHP_EOL.PHP_EOL);
        $dispositions = Disposition::join('dispositions_to_invoices', 'dispositions.id', 'disposition_id')->select('disposition_id', 'disposition_invoice_id', 'dispositions.*')->get();

        foreach ($dispositions as $data) {
            /// clear out transactions
            DB::table('transactions')->where('type_id', 2)->where('link_to_type_id', $data->disposition_invoice_id)->delete();
        }
        foreach ($dispositions as $data) {
            // clear transactions
            if (isset($data->sf_parcel_id)) {
                $disposition_info = SfDisposition::select('*')->where('PropertyID', $data->sf_parcel_id)->first();
                if (count($disposition_info) < 1) {
                    $this->line(PHP_EOL.'!!!!!! NO DISPOSITION INFO FOUND USING '.$data->sf_parcel_id);
                }
                // get amounts from sf_reimbursements
                $amounts = sf_reimbursements::select('ProgramIncome', 'NetProceeds', 'RecapturedOwed', 'RecapturePaid', 'ReturnedFundsExplanation')->where('PropertyIDRecordID', $data->sf_parcel_id)->first();
                if (count($amounts)<1) {
                    $this->line(PHP_EOL.'!!!!!! NO AMOUNT INFORMATION FOUND USING '.$data->sf_parcel_id);
                }
                //dd($data,$amounts,$disposition_info);
                /// update the dates on the disposition and invoice

                $created_at = null;
                $date_submitted = null;
                $release_date = null;
                $date_approved = null;
                /// set status base
                $lbStatus = 15; // requested to hfa
                $hfaStatus = 29; // disposition requested
                $dispositionStatus = 3; // pending hfa approval
                $this->line(PHP_EOL.'Updating sf_parcel_id '.$data->sf_parcel_id.' with correct entries');
                /// determine created at date:
                $created_at = null;
                if (strtotime($disposition_info->CreatedDate) > strtotime($disposition_info->ReleaseDate) && strtotime($disposition_info->ReleaseDate) > 10) {
                    // created date is likely date paid

                    // get the previous quarter month:
                    $ReleaseDateMonth = intval(date('n', strtotime($disposition_info->ReleaseDate)));
                    $ReleaseDateYear = intval(date('Y', strtotime($disposition_info->ReleaseDate)));

                    //dd($ReleaseDateMonth,$ReleaseDateYear);
                    if ($ReleaseDateMonth > 11 && $ReleaseDateMonth < 3) {
                        if ($ReleaseDateMonth > 0) {
                            // happened in the next year
                            $ReleaseDateYear = $ReleaseDateYear - 1;
                            $created_at = date('Y-m-d H:i:s', strtotime('12/1/'.$ReleaseDateYear));
                            $this->line(PHP_EOL.'Updating sf_parcel_id '.$data->sf_parcel_id.' created at date to '.date('Y-m-d H:i:s', strtotime('12/1/'.$ReleaseDateYear)));
                        } else {
                            // happened in the same year.
                            $created_at = date('Y-m-d H:i:s', strtotime('12/1/'.$ReleaseDateYear));
                            $this->line(PHP_EOL.'Updating sf_parcel_id '.$data->sf_parcel_id.' created at date to '.date('Y-m-d H:i:s', strtotime('12/1/'.$ReleaseDateYear)));
                        }
                    } elseif ($ReleaseDateMonth > 2 && $ReleaseDateMonth < 6) {
                        $created_at = date('Y-m-d H:i:s', strtotime('3/1/'.$ReleaseDateYear));
                        $this->line(PHP_EOL.'Updating sf_parcel_id '.$data->sf_parcel_id.' created at date to '.date('Y-m-d H:i:s', strtotime('3/1/'.$ReleaseDateYear)));
                    } elseif ($ReleaseDateMonth > 5 && $ReleaseDateMonth < 9) {
                        $created_at = date('Y-m-d H:i:s', strtotime('6/1/'.$ReleaseDateYear));
                        $this->line(PHP_EOL.'Updating sf_parcel_id '.$data->sf_parcel_id.' created at date to '.date('Y-m-d H:i:s', strtotime('6/1/'.$ReleaseDateYear)));
                    } elseif ($ReleaseDateMonth > 8 && $ReleaseDateMonth < 12) {
                        $created_at = date('Y-m-d H:i:s', strtotime('9/1/'.$ReleaseDateYear));
                        $this->line(PHP_EOL.'Updating sf_parcel_id '.$data->sf_parcel_id.' created at date to '.date('Y-m-d H:i:s', strtotime('9/1/'.$ReleaseDateYear)));
                    }
                    if ($amounts->RecapturePaid == 1) {
                        $paid_date = date('Y-m-d H:i:s', strtotime($disposition_info->CreatedDate));
                    }
                } else {
                    $this->line(PHP_EOL.'Updating sf_parcel_id '.$data->sf_parcel_id.' created at date to '.date('Y-m-d H:i:s', strtotime($disposition_info->CreatedDate)));
                    $created_at = date('Y-m-d H:i:s', strtotime($disposition_info->CreatedDate));
                }

                $date_submitted = $created_at;
                $this->line(PHP_EOL.'Updating sf_parcel_id '.$data->sf_parcel_id.' submitted date to '.$created_at);

                
                /// set approved date
                if (!isset($paid_date)) {
                    //modified date is likely not the date approved, use created date
                    if ($disposition_info->Status == "Approved") {
                        $date_approved = $created_at;
                        // set status to approved
                        $lbStatus = 16;
                        $hfaStatus = 30;
                        $dispositionStatus = 7; // approved
                    } else {
                        $date_approved = null;
                    }
                } else {
                    // approved date is likely last modified
                    if ($disposition_info->Status == "Approved") {
                        $date_approved = date('Y-m-d H:i:s', strtotime($disposition_info->CreatedDate));
                        // set status to approved
                        $lbStatus = 16;
                        $hfaStatus = 30;
                        $dispositionStatus = 4; // pending payment (becuase this is attached to an invoice);
                    } else {
                        $date_approved = null;
                    }
                }

                if (is_null($date_approved) && $amounts->RecapturePaid === 0) {
                    $this->line(PHP_EOL."!!!! This is not an approved disposition - status is ".$disposition_info->Status.'. Removing it from invoice.');
                    ///// this disposition has not been approved - so it should not have items or an invoice
                    DB::table('dispositions_to_invoices')->where('disposition_id', $data->disposition_id)->delete();
                    DB::table('disposition_items')->where('disposition_id', $data->disposition_id)->delete();
                    // check if invoice is now empty
                    $dispositionsLeft = DB::table('dispositions_to_invoices')->where('disposition_invoice_id', $data->disposition_invoice_id)->count();
                    if ($dispositionsLeft < 1) {
                        // no more dispositions in this invoice - delete it
                        DB::table('disposition_invoices')->where('id', $data->disposition_invoice_id)->delete();
                    }
                }

                /// set released date
                if (!is_null($disposition_info->DateReleased)) {
                    $date_released = date('Y-m-d H:i:s', strtotime($disposition_info->DateReleased));
                    // set status to released
                    $lbStatus = 17;
                    $hfaStatus = 33;
                    $dispositionStatus = 4; // pending payment
                } else {
                    $date_released = null;
                }

                if (!isset($paid_date)) {
                    if ($amounts->RecapturePaid == 1) {
                        $paid_date = date('Y-m-d H:i:s', strtotime($disposition_info->CreatedDate));
                        $dispositionStatus = 6; // paid
                    } else {
                        $paid_date = null;
                    }
                }

                /// ensure paid status is given
                if (isset($paid_date)) {
                    if ($amounts->RecapturePaid == 1) {
                        $dispositionStatus = 6;
                    } else {
                        $paid_date = null;
                    }
                }

                /// if no recapture is owed
                if (round($amounts->RecapturedOwed, 0) == 0 && $disposition_info->status == "Approved") {
                    /// they don't owe anything so mark it as paid
                    $dispositionStatus = 6;
                }

                
                Disposition::where('id', $data->disposition_id)
                    ->update([
                        'created_at' => $created_at,
                        'date_submitted' => $date_submitted,
                        'release_date' => $release_date,
                        'date_release_requested'=>$release_date,
                        'date_approved' => $date_approved,
                        'status_id' => $dispositionStatus
                        ]);
                // get parcel id for note
                $parcel_id = Parcel::find($data->parcel_id);
                // add in transaction -- if there is one
                if (!is_null($paid_date) && !is_null($amounts->RecapturedOwed) && $amounts->RecapturedOwed > 0 && $amounts->RecapturePaid == 1) {
                    // it has been paid
                        $hfaStatus = 32; // disposition paid
                        DB::table('transactions')->insert([
                                    'account_id'=>$data->program_id,
                                    'credit_debit'=>'c',
                                    'amount'=>$amounts->RecapturedOwed,
                                    'transaction_category_id'=>6,
                                    'type_id'=>2,
                                    'link_to_type_id'=>$data->disposition_invoice_id,
                                    'status_id'=>2,
                                    'owner_id'=>$data->program_id,
                                    'owner_type'=>'program',
                                    'date_entered'=>$paid_date,
                                    'date_cleared'=>$paid_date,
                                    'created_at'=>$paid_date,
                                    'transaction_note'=>'Legacy Disposition Invoice Payment Transaction for parcel '.$parcel_id->parcel_id.' translated from Salesforce data. <br />Original note: '.$amounts->ReturnedFundsExplanation.' <br />SYSTEM NOTE: This transaction was done by creating individual invoices containing grouped dispositions that have a matching due date interpreted by either the created date or the released date (which ever was earlier, as Sales Force would change the created date to the last date the disposition was modified). Because this concept of grouping the dispositions together in a single invoice (like reimbursements) is new - payments against dispositions were done parcel by parcel prior to the upgrade to Allita Blight Manager. Thus each invoice will have multiple payments recorded against it, and may show pending payment as a status if dispositions within it are still outstanding.'
                                    ]);
                }
                // update parcel status
                Parcel::where('id', $data->parcel_id)->update([
                        'landbank_property_status_id' => $lbStatus,
                        'hfa_property_status_id' => $hfaStatus
                    ]);
            } else {
                // update as a new parcels
                Disposition::where('id', $data->disposition_id)
                    ->update([
                        'date_submitted' => $data->created_at,
                        'status' => 3
                        ]);
            } // end if legacy
        }// end for each

        /// set status of the invoices
        $dispositions = Disposition::join('dispositions_to_invoices', 'dispositions.id', 'disposition_id')->select('disposition_id', 'disposition_invoice_id', 'dispositions.*')->orderBy('disposition_invoice_id')->orderBy('created_at', 'desc')->get();
        //ordering it this way makes it so invoices are grouped together, and then the earliest parcel sets the creation date of the invoice.

        $currentInvoice = 0;
        $firstRun = 1;
        $invoiceStatus = 3; // pending hfa approval
        $invoiceTotal = 0;
        $invoiceDate = 0;
        foreach ($dispositions as $data) {
            if ($currentInvoice != $data->disposition_invoice_id) {
                /// check the status setting
                if ($firstRun != 1) {
                    // don't run this code on the first run through.
                    
                    // update the disposition invoice status
                    DB::table('disposition_invoices')->where('id', $data->disposition_invoice_id)->update(['status_id'=>$invoiceStatus, 'created_at'=>$invoiceDate]);
                    $this->line(PHP_EOL.'Updated invoice '.$data->disposition_invoice_id.' to status id '.$invoiceStatus);
                    // reset the status
                    $invoiceStatus = 3; // pending hfa approval
                    // reset invoice total
                    $invoiceTotal = 0;
                    // reset invoice amount paid
                    $invoicePaid = Transaction::where('link_to_type_id', $data->disposition_invoice_id)->where('type_id', 2)->sum('amount');
                    $invoiceDate = Transaction::select('date_entered')->where('link_to_type_id', $data->disposition_invoice_id)->where('type_id', 2)->orderBy('date_entered', 'asc')->first();
                    if (isset($invoiceDate->date_entered)) {
                        $invoiceDate = $invoiceDate->date_entered;
                    }
                    
                    // update the current invoice var
                    $currentInvoice = $data->disposition_invoice_id;
                } else {
                    $firstRun = 0;
                    $currentInvoice = $data->disposition_invoice_id;
                    $invoicePaid = Transaction::where('link_to_type_id', $data->disposition_invoice_id)->where('type_id', 2)->sum('amount');
                    $invoiceDate = Transaction::select('date_entered')->where('link_to_type_id', $data->disposition_invoice_id)->where('type_id', 2)->orderBy('date_entered', 'asc')->first();
                    if (isset($invoiceDate->date_entered)) {
                        $invoiceDate = $invoiceDate->date_entered;
                    }
                }
            }
            $invoiceTotal = $invoiceTotal + DB::table('disposition_items')->where('disposition_id', $data->disposition_id)->sum('amount');
            if ($invoicePaid > 0) {
                $invoiceStatus = 4; // pending payment
            } else {
                // this invoice has no created date yet - get one from the sf_dispositions table
                $invoiceDate = \App\SfDisposition::where('PropertyID', $data->sf_parcel_id)->select('CreatedDate')->first();
                if (isset($invoiceDate->created_at)) {
                    $invoiceDate = $invoiceDate->created_at;
                }
            }
            if (round($invoiceTotal, 2) <= round($invoicePaid, 2)) {
                // the total amount is less than or equal to the amount paid - invoice is paid.
                $invoiceStatus = 6; // pending payment
            }
        }
    }
}
