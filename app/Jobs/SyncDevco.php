<?php

namespace App\Jobs;

// auth services to connect to API
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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

	public $timeout = 600;

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
