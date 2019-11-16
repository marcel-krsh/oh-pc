<?php

namespace App\Console\Commands;

use App\Models\CachedAudit;
use Illuminate\Console\Command;

class UpdateAuditStartDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:audit_start_dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix reference back to completed_date in table that is mislabled in devco.';

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
     * @return mixed
     */
    public function handle()
    {
        $cachedAudits = CachedAudit::with('audit')->get();
        foreach ($cachedAudits as $a) {
            $this->line(PHP_EOL.PHP_EOL.'Updating start date for audit '.$a->audit_id.'.'.PHP_EOL.PHP_EOL);

            $a->inspection_schedule_date = $a->audit->completed_date;
            $a->update_cached_audit();
            $a->save();
        }
    }
}
