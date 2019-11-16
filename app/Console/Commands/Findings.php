<?php

namespace App\Console\Commands;

use App\Models\CachedAudit;
use App\Models\CachedBuilding;
use App\Models\CachedUnit;
use App\Models\Finding;
use App\Models\FindingType;
use Illuminate\Console\Command;

class Findings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'finding:recount';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recounting Findings';

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
        // clear all building totals
        // clear all unit totals
        // foreach finding, add totals

        $this->info('Clearing building stats');
        CachedBuilding::whereNotNull('building_id')
            ->update([
            'finding_file_total' => 0,
            'finding_nlt_total' => 0,
            'finding_lt_total' => 0,
            'finding_total' => 0,
        ]);

        $this->info('Clearing unit stats');
        CachedUnit::whereNotNull('building_id')
            ->update([
            'finding_file_total' => 0,
            'finding_nlt_total' => 0,
            'finding_lt_total' => 0,
            'finding_total' => 0,
        ]);

        $this->info('Recounting findings');
        $findings = Finding::get();

        foreach ($findings as $finding) {
            if ($finding) {
                //dd($audit);
                // update findings totals in cached building, unit, audit as needed

                // type of finding
                $type = FindingType::where('id', '=', $finding->finding_type_id)->first()->type;

                if ($finding->building_id) {
                    $building = CachedBuilding::where('building_id', '=', $finding->building_id)->where('audit_id', '=', $finding->audit_id)->first();
                    $finding_total = $building->finding_total;

                    if ($type == 'file') {
                        $finding_file_total = $building->finding_file_total;
                        $building->finding_file_total = $finding_file_total + 1;
                        $building->finding_total = $finding_total + 1;
                        $building->save();
                    } elseif ($type == 'nlt') {
                        $finding_nlt_total = $building->finding_nlt_total;
                        $building->finding_nlt_total = $finding_nlt_total + 1;
                        $building->finding_total = $finding_total + 1;
                        $building->save();
                    } elseif ($type == 'lt') {
                        $finding_lt_total = $building->finding_lt_total;
                        $building->finding_lt_total = $finding_lt_total + 1;
                        $building->finding_total = $finding_total + 1;
                        $building->save();
                    }
                }
                if ($finding->unit_id) {
                    $unit = CachedUnit::where('unit_id', '=', $finding->unit_id)->where('audit_id', '=', $finding->audit_id)->first();
                    $finding_total = $unit->finding_total;

                    if ($type == 'file') {
                        $finding_file_total = $unit->finding_file_total;
                        $unit->finding_file_total = $finding_file_total + 1;
                        $unit->finding_total = $finding_total + 1;
                        $unit->save();
                    } elseif ($type == 'nlt') {
                        $finding_nlt_total = $unit->finding_nlt_total;
                        $unit->finding_nlt_total = $finding_nlt_total + 1;
                        $unit->finding_total = $finding_total + 1;
                        $unit->save();
                    } elseif ($type == 'lt') {
                        $finding_lt_total = $unit->finding_lt_total;
                        $unit->finding_lt_total = $finding_lt_total + 1;
                        $unit->finding_total = $finding_total + 1;
                        $unit->save();
                    }

                    // also save totals at the building level
                    $building = CachedBuilding::where('building_id', '=', $unit->building_id)->where('audit_id', '=', $finding->audit_id)->first();
                    $finding_total = $building->finding_total;

                    if ($type == 'file') {
                        $finding_file_total = $building->finding_file_total;
                        $building->finding_file_total = $finding_file_total + 1;
                        $building->finding_total = $finding_total + 1;
                        $building->save();
                    } elseif ($type == 'nlt') {
                        $finding_nlt_total = $building->finding_nlt_total;
                        $building->finding_nlt_total = $finding_nlt_total + 1;
                        $building->finding_total = $finding_total + 1;
                        $building->save();
                    } elseif ($type == 'lt') {
                        $finding_lt_total = $building->finding_lt_total;
                        $building->finding_lt_total = $finding_lt_total + 1;
                        $building->finding_total = $finding_total + 1;
                        $building->save();
                    }
                }
            }
        }
    }
}
