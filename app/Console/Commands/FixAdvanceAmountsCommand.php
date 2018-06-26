<?php

namespace App\Console\Commands;

use \App\CostItem;
use \App\RequestItem;
use \App\PoItems;
use \App\InvoiceItem;
use Illuminate\Console\Command;

/**
 * FixAdvanceAmounts Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class FixAdvanceAmountsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:advanceAmounts';

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
        $costs = CostItem::where('advance', 1)->get();
        foreach ($costs as $cost) {
            if ($cost->advance == 1) {
                $this->line('Cost Item '.$cost->id.' has advance = 1'.PHP_EOL);
                //make sure all associated amounts are also marked advance
                $request_item = RequestItem::where('ref_id', $cost->id)->first();
                if (isset($request_item->id)) {
                    // just apply the update.
                    $request_item->update(['advance'=>1,'breakout_type' => 3]);
                    $this->line('Updated Request Item '.$request_item->id.PHP_EOL);

                    $po_item = PoItems::where('ref_id', $request_item->id)->first();
                    if (isset($po_item->id)) {
                        //just apply the update
                        $po_item->update(['advance'=>1,'breakout_type' => 3]);
                        $this->line('Updated PO Item '.$po_item->id.PHP_EOL);
                        $invoice_item = InvoiceItem::where('ref_id', $po_item->id)->first();
                        if (isset($invoice_item->id)) {
                            $invoice_item->update(['advance'=>1,'breakout_type' => 3]);
                            $this->line('Updated Invoice Item '.$invoice_item->id.PHP_EOL);
                        }
                    }
                }
            }
        }
        $this->line(PHP_EOL);
    }
}
