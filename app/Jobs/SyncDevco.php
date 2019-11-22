<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

// auth services to connect to API
use App\Services\AuthService;
use App\Services\DevcoService;
use App\Models\AuthTracker;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * SyncDevco Job
 *
 * @category Events
 * @license  Proprietary and confidential
 */
class SyncDevco implements ShouldQueue
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
    public $tries = 5;
    public function handle()
    {
        //
        //Log::info('Sync Job Started.');
        $time = 10;
       
            // SystemSetting::get('pcapi_access_token');
            // $addresses = DevcoService::listAddresses(1, 'january 1,2010', 1,'brian@allita.org', 'Brian Greenwood', 1, 'Server');
            // Log::info($addresses);
    }
}
