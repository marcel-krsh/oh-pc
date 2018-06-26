<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\SiteVisits;
use DB;

/**
 * UpdateSiteVisits Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class UpdateSiteVisitsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateSiteVisitsCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all site visits so they are a pass fail for their question.';

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
        $siteVisits = SiteVisits::get()->all();
        $i = 0;
        foreach ($siteVisits as $visit) {
            $i++;
            /// is a recap of main funds required

            if ($visit->is_a_recap_of_maint_funds_required == 0) {
                $this->line(' Visit: '.$visit->id.' updated is recap of maint funds to pass'.PHP_EOL);
                SiteVisits::where('id', $visit->id)->update(['is_a_recap_of_maint_funds_required'=> 1]);
            }
            if ($visit->is_a_recap_of_maint_funds_required == 1) {
                $this->line(' Visit: '.$visit->id.' updated is recap of maint funds to fail'.PHP_EOL);
                SiteVisits::where('id', $visit->id)->update(['is_a_recap_of_maint_funds_required'=> 0]);
            }

            if ($visit->nuisance_elements_or_code_violations == 0) {
                $this->line(' Visit: '.$visit->id.' updated is recap of nuisance elements to pass'.PHP_EOL);
                SiteVisits::where('id', $visit->id)->update(['nuisance_elements_or_code_violations'=> 1]);
            }
            if ($visit->nuisance_elements_or_code_violations == 1) {
                $this->line(' Visit: '.$visit->id.' updated is recap of nuisance elements to fail'.PHP_EOL);
                SiteVisits::where('id', $visit->id)->update(['nuisance_elements_or_code_violations'=> 0]);
            }

            if ($visit->are_there_environmental_conditions == 0) {
                $this->line(' Visit: '.$visit->id.' updated is recap of environmental to pass'.PHP_EOL);
                SiteVisits::where('id', $visit->id)->update(['are_there_environmental_conditions'=> 1]);
            }
            if ($visit->are_there_environmental_conditions == 1) {
                $this->line(' Visit: '.$visit->id.' updated is recap of environmental to fail'.PHP_EOL);
                SiteVisits::where('id', $visit->id)->update(['are_there_environmental_conditions'=> 0]);
            }

        

            if ($visit->is_a_recap_of_maint_funds_required == 0) {
                $this->line(' Visit: '.$visit->id.' updated is recap of main funds to pass'.PHP_EOL);
                SiteVisits::where('id', $visit->id)->update(['is_a_recap_of_maint_funds_required'=> 1]);
            }
            if ($visit->is_a_recap_of_maint_funds_required == 1) {
                $this->line(' Visit: '.$visit->id.' updated is recap of main funds to fail'.PHP_EOL);
                SiteVisits::where('id', $visit->id)->update(['is_a_recap_of_maint_funds_required'=> 0]);
            }

            if ($visit->nuisance_elements_or_codeviolations == 0) {
                $this->line(' Visit: '.$visit->id.' updated is recap of main funds to pass'.PHP_EOL);
                SiteVisits::where('id', $visit->id)->update(['nuisance_elements_or_codeviolations'=> 1]);
            }
            if ($visit->nuisance_elements_or_codeviolations == 1) {
                $this->line(' Visit: '.$visit->id.' updated is recap of main funds to fail'.PHP_EOL);
                SiteVisits::where('id', $visit->id)->update(['nuisance_elements_or_codeviolations'=> 0]);
            }

            if ($visit->are_there_environmental_conditions == 0) {
                $this->line(' Visit: '.$visit->id.' updated is recap of main funds to pass'.PHP_EOL);
                SiteVisits::where('id', $visit->id)->update(['are_there_environmental_conditions'=> 1]);
            }
            if ($visit->are_there_environmental_conditions == 1) {
                $this->line(' Visit: '.$visit->id.' updated is recap of main funds to fail'.PHP_EOL);
                SiteVisits::where('id', $visit->id)->update(['are_there_environmental_conditions'=> 0]);
            }
        }
    }
}
