<?php

namespace App\Console\Commands;

use App\Models\Organization;
use Illuminate\Console\Command;
use App\Models\SyncOrganization;

class FixOrganizationNullKeys extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'fix:organization_null_keys';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Fixes the null keys in organization table by fetching related values from sync_organizations tables';

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
		$total = Organization::whereNull('organization_key')->get()->count();
		$this->line($total . ' records found. ');
		if ($total > 0) {
			$selection = $this->ask('Do you want to update the records for these organizations(yes or no):');
			$selection = strtolower($selection);
			if ($selection == 'yes') {
				$progressBar = $this->output->createProgressBar($total);
				$progressBar->setProgressCharacter("\xf0\x9f\x8c\x80");
				$chunk = 20;
				$progress = $chunk;
				$organizations = Organization::whereNull('organization_key')->chunk($chunk, function ($orgs) use ($progressBar, $progress) {
					$progressBar->advance($progress);
					foreach ($orgs as $key => $org) {
						$sync_organization = SyncOrganization::where('allita_id', $org->id)->first();
						if ($sync_organization) {
							$org->organization_key = $sync_organization->organization_key;
							$org->default_address_key = $sync_organization->default_address_key;
							$org->default_phone_number_key = $sync_organization->default_phone_number_key;
							$org->default_fax_number_key = $sync_organization->default_fax_number_key;
							$org->default_contact_person_key = $sync_organization->default_contact_person_key;
							$org->fed_id_number = $sync_organization->fed_id_number;
							$org->save();
						} else {
							$this->line('Looks like there is no sync record found for this organization: ID => ' . $org->id . ' NAME => ' . $org->organization_name);
						}
					}
				});
			} else {
				$this->line('Cancelled');
			}
		}
	}
}
