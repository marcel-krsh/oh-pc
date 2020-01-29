<?php

namespace App\Console\Commands;

use App\Models\CrrReport;
use Illuminate\Console\Command;

class FixDatesOnReports extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'reports:dates_fix';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Existing reports has no letter date and review dates. This script adds these dates.';

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
		$reports = CrrReport::where('id', '>', 4)
			->select('audit_id', 'letter_date', 'review_date', 'response_due_date', 'id', 'created_at')
			->get()->load('cached_audit');
		foreach ($reports as $key => $report) {
			if (is_null($report->letter_date)) {
				$report->letter_date = $report->created_at;
			}
			if (is_null($report->review_date)) {
				if ($report->cached_audit) {
					// return $report->cached_audit;
					$report->review_date = $report->cached_audit->inspection_schedule_date;
				}
			}
			// if (is_null($report->response_due_date)) {
			// 	$report->letter_date = $report->created_at;
			// }
			// return $report->letter_date;
			$report->save();
			// return $report;
		}
		$this->line('Completed fixing all report dates.');
	}
}
