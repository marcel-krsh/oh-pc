<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Parcel;

/**
 * FindOrphans Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class FindOrphansCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'find:orphans';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find and fix orphan req, po, invoice items ';

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
        $reqs = \DB::table('reimbursement_requests')->select('id as req_id')->get()->all();
        $this->line('Finding items that should not have req_ids or other items.'.PHP_EOL);
        $bar = $this->output->createProgressBar(count($reqs));
        foreach ($reqs as $req) {
            //get all the parcels_ids by getting all the req_items with this req_id
            $parcels = \DB::table('request_items')->select('parcel_id')->where('req_id', $req->req_id)->groupBy('parcel_id')->get()->all();
            //// now check that the parcel_id is in the parcels to requests table
            foreach ($parcels as $parcel) {
                $parcelCheck = \DB::table('parcels_to_reimbursement_requests')->where('parcel_id', $parcel->parcel_id)->count();
                if ($parcelCheck < 1) {
                    // the parcel is NOT in the list - we need to delete all the po and invoice items, and then make sure its request amounts have null for their req_id
                    $this->line(PHP_EOL.'Parcel with system id '.$parcel->parcel_id.' is not in request '.$req->req_id.'. Removing po, inv items if there are any, and setting request items to null, then updating its hfa status to unsubmitted.'.PHP_EOL);
                    \DB::table('po_items')->where('parcel_id', $parcel->parcel_id)->delete();
                    \DB::table('invoice_items')->where('parcel_id', $parcel->parcel_id)->delete();
                    \DB::table('request_items')->where('parcel_id', $parcel->parcel_id)->update(['req_id'=>null]);
                    \DB::table('parcels')->where('id', $parcel->parcel_id)->update(['hfa_property_status_id'=>39]);
                }
            }
            $bar->advance();
        }
        $bar->finish();
        
        $reqs = \DB::table('reimbursement_purchase_orders')->select('id as req_id')->get()->all();
        $this->line(PHP_EOL.PHP_EOL.'Finding items that should not have po_ids or other items.'.PHP_EOL);
        $poBar = $this->output->createProgressBar(count($reqs));
        foreach ($reqs as $req) {
            //get all the parcels_ids by getting all the req_items with this req_id
            $parcels = \DB::table('po_items')->select('parcel_id')->where('po_id', $req->req_id)->groupBy('parcel_id')->get()->all();
            //// now check that the parcel_id is in the parcels to requests table
            foreach ($parcels as $parcel) {
                $parcelCheck = \DB::table('parcels_to_purchase_orders')->where('parcel_id', $parcel->parcel_id)->count();
                if ($parcelCheck < 1) {
                    // the parcel is NOT in the list - we need to delete all the po and invoice items, and then make sure its request amounts have null for their req_id
                    $this->line(PHP_EOL.'Parcel with system id '.$parcel->parcel_id.' is not in po '.$req->req_id.'. Removing po, inv items if there are any, and setting request items to null, then updating its hfa status to unsubmitted.'.PHP_EOL);
                    \DB::table('parcels_to_reimbursement_requests')->where('parcel_id', $parcel->parcel_id)->delete();
                    \DB::table('po_items')->where('parcel_id', $parcel->parcel_id)->delete();
                    \DB::table('invoice_items')->where('parcel_id', $parcel->parcel_id)->delete();
                    \DB::table('request_items')->where('parcel_id', $parcel->parcel_id)->update(['req_id'=>null]);
                    \DB::table('parcels')->where('id', $parcel->parcel_id)->update(['hfa_property_status_id'=>39, 'landbank_property_status_id'=>7]);
                }
            }
            $poBar->advance();
        }
        $poBar->finish();
        
        $this->line(PHP_EOL.PHP_EOL.'Finding and deleting PO items that do not have a po_id');
        
        $nullPos = \DB::table('po_items')->select('*')->where('po_id', null)->get()->all();
        
        if (count($nullPos)>0) {
            $this->line(PHP_EOL.'Found '.count($nullPos).' PO Items without a po_id.');
            foreach ($nullPos as $data) {
                $parcelInfo = Parcel::find($data->parcel_id);
                \DB::table('po_items')->where('id', $data->id)->delete();
                $this->line(PHP_EOL.'==> DELETED PO ITEM FOR PARCEL '.$parcelInfo->parcel_id.' with system id '.$parcelInfo->id);
            }
        }
        
        $this->line(PHP_EOL.PHP_EOL.'Finding and deleting Invoice items that do not have a invoice_id');
        $nullInvs = \DB::table('invoice_items')->select('*')->where('invoice_id', null)->get()->all();
        
        if (count($nullInvs)>0) {
            $this->line(PHP_EOL.'Found '.count($nullPos).' Invoice Items without a invoice_id.');
            foreach ($nullInvs as $data) {
                $parcelInfo = Parcel::find($data->parcel_id);
                \DB::table('invoice_items')->where('id', $data->id)->delete();
                $this->line(PHP_EOL.'==> DELETED INVOICE ITEM FOR PARCEL '.$parcelInfo->parcel_id.' with system id '.$parcelInfo->id);
            }
        }
        $this->line(PHP_EOL.PHP_EOL.'Finding parcels that have a mismatched number of items.');
        $parcels = Parcel::select('parcel_id', 'id')->where('id', '>', 0)->get()->all();
        $count = 0;
        foreach ($parcels as $parcel) {
            /// get counts of each set of items for this parcel
            $costs = \DB::table('cost_items')->where('parcel_id', $parcel->id)->count();
            $reqs = \DB::table('request_items')->where('parcel_id', $parcel->id)->count();
            $pos = \DB::table('po_items')->where('parcel_id', $parcel->id)->count();
            $invs = \DB::table('invoice_items')->where('parcel_id', $parcel->id)->count();
            if (($costs != $reqs && $reqs != 0 && $pos != 0 && $costs < $reqs) || ($costs != $pos && $pos != 0) || ($costs != $invs && $invs != 0)) {
                // there are some blanks out there!!! ahhhhh!
                $count++;
                $this->line($count.' | Parcel '.$parcel->parcel_id.' with system id '.$parcel->id.' has C:'.$costs.' R:'.$reqs.' P:'.$pos.' I:'.$invs);
            }
        }
    }
}
