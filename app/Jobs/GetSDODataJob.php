<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * GetSDOData Job
 *
 * @category Events
 * @license  Proprietary and confidential
 */
class GetSDODataJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     */
    public function handle()
    {
        //&exportResults=true
        //https://savethedreamohio.gov/admin/main/list?zzz=0&SortBy=&SortByDirection=DESC&DateAgingFrom=1%2F1%2F1900+12%3A00%3A00+AM&DateAgingThrough=1%2F1%2F2099+12%3A00%3A00+AM&AdvancedapplicantApplyDateLow=01%2F01%2F1900&AdvancedapplicantApplyDateHigh=01%2F01%2F2099&exportResults=true
    }
}
