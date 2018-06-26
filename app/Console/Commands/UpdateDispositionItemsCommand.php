<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\DispositionItems;
use App\DispositionsToInvoice;
use DB;

/**
 * UpdateDispositionItems Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class UpdateDispositionItemsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:disposition_items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update disposition items to include their invoice id.';

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
        DispositionItems::chunk(500, function ($dispositions) {
            $start = session('progressCount') + 1;
            $current = session('progressCount')+ count($dispositions);
            session(['progressCount' => $current]);
            $this->line(PHP_EOL.'Chunking Dispositions Checks '.$start.' through '.$current.' of '.session('dispositionTotal').PHP_EOL);
            
            $dispositionbar = $this->output->createProgressBar(count($dispositions));
            foreach ($dispositions as $data) {
                // find its invoice_id
                $invoice = DispositionsToInvoice::where('disposition_id', $data->disposition_id)->first();
                DispositionItems::where('id', $data->id)->update(['disposition_invoice_id' => $invoice->disposition_invoice_id]);
                // check if the invoice is paid in full
                $actualInvoice = \App\DispositionInvoice::find($invoice->disposition_invoice_id);
                if ($actualInvoice->balance() < .01) {
                    // mark this invoice paid
                    \App\DispositionInvoice::where('id', $invoice->disposition_invoice_id)->update(['paid'=>1]);
                }
                $dispositionbar->advance();
            }
            $dispositionbar->finish();
        });
    }
}
