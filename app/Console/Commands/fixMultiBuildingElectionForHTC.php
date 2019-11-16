<?php

namespace App\Console\Commands;

use App\Models\Audit;
use App\Models\ProjectProgram;
use App\Models\UnitProgram;
use Illuminate\Console\Command;

class fixMultiBuildingElectionForHTC extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix_multi_building_election_for_htc';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Some of the audits has multi building, though multiple_building_election_key=2. This script fixes that issuee';

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
        $this->line('Fixing Audits Selection Summary'.PHP_EOL);
        // return json_decode(Audit::find(7037)->selection_summary, 1);
        $project_pps = ProjectProgram::with('program.relatedGroups')->get()->groupBy('project_id'); //->groupBy('program_key');
        $fixed_audits = [];
        foreach ($project_pps as $key => $pps) {
            $project_programs = [];
            $pps = $pps->groupBy('program_key');
            $unitprograms = [];
            foreach ($pps as $key => $pp) {
                $pp = $pp->first();
                if ($pp->program) {
                    $pp_program = $pp->program;
                    // return $pp->first();
                    if (count($pp->program->relatedGroups) > 0) {
                        $program_group = $pp->program->relatedGroups->first();
                        $project_programs[$key]['program_key'] = $pp_program->program_key;
                        $project_programs[$key]['program_name'] = $pp_program->program_name;
                        $project_programs[$key]['group_id'] = $program_group->id;
                        $project_programs[$key]['group_name'] = $program_group->group_name;
                        if ($program_group->id == 7 && $pp->multiple_building_election_key == 2) {
                            $project_programs[$key]['multiple'] = 0;
                            $unitprograms = UnitProgram::where('unit_programs.project_id', '=', $pp->project_id)
                                                                    // ->join('units','units.id','unit_programs.unit_id')
                                                        //     ->join('buildings','buildings.id','units.building_id')
                                                                        ->where('program_key', $pp_program->program_key)
                                                                        ->with('unit', 'program.relatedGroups', 'unit.building', 'unit.building.address', 'unitInspected', 'project_program')
                                                                        ->get();
                        // if(count($unitprograms) > 0) {
                            // 	$audit_id = $unitprograms->first()->audit_id;
                            // 	  // return json_decode(Audit::find($audit_id)->selection_summary, 1);
                                    //  //  $unitprograms = $unitprograms->groupBy('building_id');

                                    // 	//  $total_buildings = count($unitprograms);
                                    // 	// foreach ($unitprograms as $key => $unitprogram) {
                                    // 	// 	$all_pro_programs = ProjectProgram::with('program.relatedGroups')->where('project_id', $pp->project_id)->get();
                                    // 	// 	// return $unitprogram;
                                    // 	// 	foreach ($unitprogram as $key => $up) {
                                    // 	// 		if($up->unitInspected) {
                                    // 	// 			// return $up;
                                    // 	// 		}
                                    // 	// 	}
                                    // 	// }
                            // }
                        } else {
                            $project_programs[$key]['multiple'] = 1;
                        }
                    }
                }
            }

            if (count($unitprograms) > 0) {
                $audit_id = $unitprograms->first()->audit_id;
                // See what programs are assigned to audit
                // Check if non-htc program group is assigned or not
                // Check if multiple_building_election_key=2 and only 1 HTC is assigned. If multiple HTC are assigned, make it as single
                $correcting_audit = Audit::find($audit_id);
                $selection_summary = json_decode($correcting_audit->selection_summary, 1);
                $selection_summary_programs = collect($selection_summary['programs']);
                $new_selection_summary = [];
                foreach ($project_programs as $single_program) {
                    if ($single_program['group_id'] != 7) {
                        $check_if_program_exists = $selection_summary_programs->where('group', $single_program['group_id']);
                        if (count($check_if_program_exists) == 0) {
                            // Create a new program for selection_summary ---- NOT SURE HOW TO GET ALL DATA HERE - FROM COMPLIANCE JOB??
                        } else {
                            $new_selection_summary[] = $check_if_program_exists;
                        }
                    }
                    // Check if non HTC program group exists in selection summary
                    if ($single_program['multiple'] == 0 && $single_program['group_id'] == 7) {
                        //Now check if this project has multiple HTC instead of single
                        $check_if_mul_htc_exists = $selection_summary_programs->where('group', $single_program['group_id']);
                        if (count($check_if_mul_htc_exists) > 1 && ! in_array($audit_id, $fixed_audits)) {
                            $loop = 0;
                            $required_units = 0;
                            $required_units_file = 0;
                            $totals_after_optimization = 0;
                            $totals_before_optimization = 0;
                            $totals_after_optimization_not_merged = 0;
                            foreach ($check_if_mul_htc_exists as $key => $htc_pro) {
                                if ($loop == 0) {
                                    $final_htc_program['name'] = $htc_pro['name'];
                                    $final_htc_program['pool'] = $htc_pro['pool'];
                                    $final_htc_program['group'] = $htc_pro['group'];
                                    $final_htc_program['use_limiter'] = $htc_pro['use_limiter'];
                                    $final_htc_program['program_keys'] = $htc_pro['program_keys'];
                                    // $final_htc_program['building_key'] = $htc_pro['building_key'];
                                    $final_htc_program['comments'] = [];
                                    $final_htc_program['units_after_optimization'] = [];
                                    $final_htc_program['units_before_optimization'] = [];
                                    $loop = 1;
                                }
                                $required_units = $required_units + $htc_pro['required_units'];
                                $required_units_file = $required_units_file + $htc_pro['required_units_file'];
                                $totals_after_optimization = $totals_after_optimization + $htc_pro['totals_after_optimization'];
                                $totals_before_optimization = $totals_before_optimization + $htc_pro['totals_before_optimization'];
                                $totals_after_optimization_not_merged = $totals_after_optimization_not_merged + $htc_pro['totals_after_optimization_not_merged'];
                                $final_htc_program['comments'] = array_merge($final_htc_program['comments'], $htc_pro['comments']);
                                $final_htc_program['units_after_optimization'] = array_merge($final_htc_program['units_after_optimization'], $htc_pro['units_after_optimization']);
                                $final_htc_program['units_before_optimization'] = array_merge($final_htc_program['units_before_optimization'], $htc_pro['units_before_optimization']);
                            }
                            // return $final_htc_program['comments'];
                            $final_htc_program['comments'] = array_merge(['Corrected HTC groups where multiple_building_election_key = 2'], $final_htc_program['comments']);
                            $final_htc_program['required_units'] = $required_units;
                            $final_htc_program['required_units_file'] = $required_units_file;
                            $final_htc_program['totals_after_optimization'] = $totals_after_optimization;
                            $final_htc_program['totals_before_optimization'] = $totals_before_optimization;
                            $final_htc_program['totals_after_optimization_not_merged'] = $totals_after_optimization_not_merged;
                            $new_selection_summary[] = $final_htc_program;
                            $selection_summary['programs'] = $new_selection_summary;
                            $selection_summary['old'] = $correcting_audit->selection_summary;
                            $correcting_audit->selection_summary = json_encode($selection_summary);
                            $fixed_audits = array_merge($fixed_audits, [$audit_id]);
                            $correcting_audit->save();
                            $this->line('Updated '.$audit_id);
                            // return $correcting_audit;
                        }
                    }
                }
            }
        }
        // return $fixed_audits;
    }
}
