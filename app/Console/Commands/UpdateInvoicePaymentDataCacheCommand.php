<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\ReimbursementInvoice;
use App\RecaptureInvoice;
use App\DispositionInvoice;
use DB;

/**
 * UpdateInvoicePaymentDataCache Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class UpdateInvoicePaymentDataCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:invoice_cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update invoice payment cache.';

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
        session(['invoiceTotal' => ReimbursementInvoice::count()]);
        $this->line('Running reimburesment invoice updates on '.session('invoiceTotal').' invoices.'.PHP_EOL);
        session(['progressCount' => 0 ]);
        ReimbursementInvoice::chunk(50, function ($invoices) {
            $start = session('progressCount') + 1;
            $current = session('progressCount')+ count($invoices);
            session(['progressCount' => $current]);
            $this->line(PHP_EOL.'Chunking invoice updates '.$start.' through '.$current.' of '.session('invoiceTotal').PHP_EOL);
            
            $parcelbar = $this->output->createProgressBar(count($invoices));
            foreach ($invoices as $data) {
                $data->updatePaymentDetails();

                // Update steps based on current status of items
                $parcelbar->advance();
            }
            $parcelbar->finish();
        });
        session(['invoiceTotal' => DispositionInvoice::count()]);
        $this->line('Running disposition invoice updates on '.session('invoiceTotal').' invoices.'.PHP_EOL);
        session(['progressCount' => 0 ]);
        DispositionInvoice::chunk(50, function ($invoices) {
            $start = session('progressCount') + 1;
            $current = session('progressCount')+ count($invoices);
            session(['progressCount' => $current]);
            $this->line(PHP_EOL.'Chunking invoice updates '.$start.' through '.$current.' of '.session('invoiceTotal').PHP_EOL);
            
            $parcelbar = $this->output->createProgressBar(count($invoices));
            foreach ($invoices as $data) {
                $data->updatePaymentDetails();

                // Update steps based on current status of items
                $parcelbar->advance();
            }
            $parcelbar->finish();
        });

        session(['invoiceTotal' => RecaptureInvoice::count()]);
        $this->line('Running recapture invoice updates on '.session('invoiceTotal').' invoices.'.PHP_EOL);
        session(['progressCount' => 0 ]);
        RecaptureInvoice::chunk(50, function ($invoices) {
            $start = session('progressCount') + 1;
            $current = session('progressCount')+ count($invoices);
            session(['progressCount' => $current]);
            $this->line(PHP_EOL.'Chunking invoice updates '.$start.' through '.$current.' of '.session('invoiceTotal').PHP_EOL);
            
            $parcelbar = $this->output->createProgressBar(count($invoices));
            foreach ($invoices as $data) {
                $data->updatePaymentDetails();

                // Update steps based on current status of items
                $parcelbar->advance();
            }
            $parcelbar->finish();
        });
    }
}
