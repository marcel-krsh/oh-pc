<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\AuthService;
use App\Services\DevcoService;
use App\Models\AuthTracker;
use App\Models\SystemSetting;
use App\Models\User;
use DB;
use DateTime;
use Illuminate\Support\Facades\Hash;
use App\Models\Audit;
use Event;
use Log;

class CreateTestAuditJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $audit;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Audit $audit)
    {
        $this->audit = $audit;
    }
    public $tries = 5;
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (is_null($this->audit)) {
             Log::info('Did not receive audit ');
        } else {
            Log::info('Creating a test event for audit id'.$this->audit->id);
        }
        try {
            Event::listen('audit.created', $this->audit);
        } catch (Exception $e) {
            Log::info('Unable to fire event '.$e);
        }
    }
}
