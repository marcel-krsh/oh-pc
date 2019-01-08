<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Models\User;
use App\Models\SystemSetting;
use App\Models\Audit;
use App\Models\Unit;
use App\Models\Project;
use App\Models\UnitInspection;
use App\Models\Organization;
use App\Models\BuildingInspection;
use App\Models\ProjectContactRole;
use App\Models\CachedAudit;
use App\Models\Program;
use App\Services\DevcoService;
use App\Models\UnitProgram;
use Illuminate\Support\Facades\Redis;
use Auth;

class AuditsEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @return void
     */


    public $user;

    public function __construct(User $user)
    {
        $this->user = $user;
        if (env('APP_DEBUG_NO_DEVCO') == 'true') {
           // Auth::onceUsingId(1); // TEST BRIAN
            Auth::onceUsingId(286); // TEST
            $this->user = Auth::user();
        }
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('audits');
    }

    // public function auditCreated(Audit $audit)
    // {
    //     // check the monitoring_status_type_key for 4,5 or 6
    //     // that means we have to create CachedAudit row if doesn't exist (shouldn't, since it is a creation)
    //     if ($audit) {
    //         if (in_array($audit->monitoring_status_type_key, [4,5,6])) {
    //             if (!CachedAudit::where('audit_id', '=', $audit->id)->count()) {
    //                 //if($this->fetchAuditUnits($audit)){             // first get units
    //                 if (1) {
    //                     // run the selection process 10 times and keep the best one
    //                     $best_run = null;
    //                     $best_total = null;
    //                     $overlap = null;
    //                     $project = null;
    //                     $organization_id = null;

    //                     for ($i=0; $i<3; $i++) {
    //                         $summary = $this->selectionProcess($audit);
    //                         if (count($summary[0]['grouped']) < $best_total || $best_run == null) {
    //                             $best_run = $summary[0];
    //                             $overlap = $summary[1];
    //                             $project = $summary[2];
    //                             $organization_id = $summary[3];
    //                             $best_total = count($summary[0]['grouped']);
    //                         }
    //                     }

    //                     // save all units selected in selection table
    //                     if ($best_run) {
    //                         $group_id = 1;

    //                         foreach ($best_run['programs'] as $program) {
    //                             $unit_keys = $program['units_after_optimization'];

    //                             $units = Unit::whereIn('unit_key', $unit_keys)->get();

    //                             foreach ($units as $unit) {
    //                                 if (in_array($unit->unit_key, $overlap)) {
    //                                     $has_overlap = 1;
    //                                 } else {
    //                                     $has_overlap = 0;
    //                                 }

    //                                 $program_keys = explode(',', $program['program_keys']);

    //                                 foreach ($unit->programs as $unit_program) {
    //                                     if (in_array($unit_program->program_key, $program_keys)) {
    //                                         $u = new UnitInspection([
    //                                             'group' => $program['name'],
    //                                             'group_id' => $group_id,
    //                                             'unit_id' => $unit->id,
    //                                             'unit_key' => $unit->unit_key,
    //                                             'building_id' => $unit->building_id,
    //                                             'building_key' => $unit->building_key,
    //                                             'audit_id' => $audit->id,
    //                                             'audit_key' => $audit->monitoring_key,
    //                                             'project_id' => $project->id,
    //                                             'project_key' => $project->project_key,
    //                                             'program_key' => $unit_program->program_key,
    //                                             'pm_organization_id' => $organization_id,
    //                                             'has_overlap' => $has_overlap
    //                                         ]);
    //                                         $u->save();
    //                                     }
    //                                 }
    //                             }
    //                             $group_id = $group_id + 1;
    //                         }
    //                     }
                        
    //                     $this->createNewCachedAudit($audit, $best_run);    // finally create the audit
    //                 }
    //             }
    //         }
    //     }
    // }

    // public function auditUpdated(Audit $audit)
    // {
    //     // check the monitoring_status_type_key for 4,5 or 6
    //     // check if audit already exists in cachedaudits if not create it
    //     // createNewCachedAudit($audit);
    // }

    // public function fetchAuditUnits(Audit $audit)
    // {
    //     UnitProgram::where('audit_id', $audit->id)->delete();
        
    //     $apiConnect = new DevcoService();
    //     // paths to the info we need: dd($audit, $audit->project, $audit->project->buildings);

    //     // Get all the units we need to get programs for:

    //     $buildings = $audit->project->buildings;
    //     if (!is_null($buildings)) {
    //     //Process each building
    //         foreach ($buildings as $building) {
    //             //Get the building's units
    //             $buildingUnits = $building->units;

    //             if (!is_null($buildingUnits)) {
    //             // Process each unit
    //                 foreach ($buildingUnits as $unit) {
    //                     // Get the unit's current program designation from DevCo
    //                     try {
    //                         $unitProgramData = $apiConnect->getUnitPrograms($unit->unit_key, 1, 'admin@allita.org', 'Updating Unit Program Data', 1, 'Server');
    //                         $unitProgramData = json_decode($unitProgramData, true);
    //                         //dd($unitProgramData['data']);
    //                         //dd($unitProgramData['data'][0]['attributes']['programKey']);
    //                         foreach ($unitProgramData['data'] as $unitProgram) {
    //                            //dd('Unit Program Id - '.$unitProgram['attributes']['programKey']);

    //                             $program = Program::where('program_key', $unitProgram['attributes']['programKey'])->first();
    //                             if (!is_null($program)) {
    //                                 UnitProgram::insert([
    //                                     'unit_key'      =>  $unit->unit_key,
    //                                     'unit_id'       =>  $unit->id,
    //                                     'program_key'   =>  $program->program_key,
    //                                     'program_id'    =>  $program->id,
    //                                     'audit_id'      =>  $audit->id,
    //                                     'monitoring_key'=>  $audit->monitoring_key,
    //                                     'created_at'    =>  date("Y-m-d g:h:i", time()),
    //                                     'updated_at'    =>  date("Y-m-d g:h:i", time())
    //                                 ]);
    //                             } else {
    //                                 Log::info('Unable to find program with key of '.$unitProgram['attributes']['programKey'].' on unit_key'.$unit->unit_key.' for audit'.$audit->monitoring_key);
    //                             }
    //                         }
    //                     } catch (Exception $e) {
    //                         Log::info('Unable to get the unit programs on unit_key'.$unit->unit_key.' for audit'.$audit->monitoring_key);
    //                     }
    //                 }
    //             }
    //         }

    //         return 1;
    //     }
    // }

    // public function adjustedLimit($n)
    // {
    //     // based on $n units, return the corresponding adjusted sample size
    //     switch (true) {
    //         case ($n >= 1 && $n <=4):
    //             return $n;
    //         break;
    //         case ($n == 5 || $n == 6):
    //             return 5;
    //         break;
    //         case ($n == 7):
    //             return 6;
    //         break;
    //         case ($n == 8 || $n == 9):
    //             return 7;
    //         break;
    //         case ($n == 10 || $n == 11):
    //             return 8;
    //         break;
    //         case ($n == 12 || $n == 13):
    //             return 9;
    //         break;
    //         case ($n >= 14 && $n <= 16):
    //             return 10;
    //         break;
    //         case ($n >= 17 && $n <= 18):
    //             return 11;
    //         break;
    //         case ($n >= 19 && $n <= 21):
    //             return 12;
    //         break;
    //         case ($n >= 22 && $n <= 25):
    //             return 13;
    //         break;
    //         case ($n >= 26 && $n <= 29):
    //             return 14;
    //         break;
    //         case ($n >= 30 && $n <= 34):
    //             return 15;
    //         break;
    //         case ($n >= 35 && $n <= 40):
    //             return 16;
    //         break;
    //         case ($n >= 41 && $n <= 47):
    //             return 17;
    //         break;
    //         case ($n >= 48 && $n <= 56):
    //             return 18;
    //         break;
    //         case ($n >= 57 && $n <= 67):
    //             return 19;
    //         break;
    //         case ($n >= 68 && $n <= 81):
    //             return 20;
    //         break;
    //         case ($n >= 82 && $n <= 101):
    //             return 21;
    //         break;
    //         case ($n >= 102 && $n <= 130):
    //             return 22;
    //         break;
    //         case ($n >= 131 && $n <= 175):
    //             return 23;
    //         break;
    //         case ($n >= 176 && $n <= 257):
    //             return 24;
    //         break;
    //         case ($n >= 258 && $n <= 449):
    //             return 25;
    //         break;
    //         case ($n >= 450 && $n <= 1461):
    //             return 26;
    //         break;
    //         case ($n >= 1462):
    //             return 27;
    //         break;
    //         default:
    //             return 0;
    //     }
    // }

    // public function randomSelection($units, $percentage = 20, $min = 0)
    // {
    //     if (count($units)) {
    //         $total = count($units);

    //         $needed = ceil($total * $percentage / 100);

    //         if ($min > $total) {
    //             $min = $total;
    //         }
    //         if ($needed < $min) {
    //             $needed = $min;
    //         }

    //         $output = [];

    //         foreach (array_rand($units, $needed) as $id) {
    //             $output[] = $units[$id];
    //         }

    //         return $output;
    //     } else {
    //         return [];
    //     }
    // }

    // public function combineOptimize($selection)
    // {
    //     //dd($selection);
    //     // $adjusted_units_count = $this->adjustedLimit(count($units_selected)); dd($adjusted_units_count);
    //     // array_slice($input, 0, 3)
    //     // only applies to the first and the last set

    //     $summary = []; // for stats
    //     $output = []; // for units

    //     // create empty array to store ids and priorities
        
    //     //for each set, run intersect
    //     //for each intersect result increase priority in the id
    //     //once all intersects are done, reorder each set by priority
    //     //make the limited selection
    //     //combine and fetch all units
    //     //store units for each program id
    //     //and create stats
    //     //
        
    //     $priority = [];

    //     // run the intersects
    //     $array_to_compare = [];
    //     $array_to_compare_with = [];
    //     $intersect = [];

    //     for ($i=0; $i < count($selection); $i++) {
    //         $array_to_compare = $selection[$i]['units'];

    //         for ($j=0; $j < count($selection); $j++) {
    //             if ($i != $j) {
    //                 $array_to_compare_with = $selection[$j]['units'];
                    
    //                 $intersects = array_intersect($array_to_compare, $array_to_compare_with);

    //                 foreach ($intersects as $intersect) {
    //                     if (array_key_exists($intersect, $priority)) {
    //                         $priority[$intersect] = $priority[$intersect]+1;
    //                     } else {
    //                         $priority[$intersect] = 1;
    //                     }
    //                 }
    //             }
    //         }
    //     }

    //    // now we have unit_keys in a priority table
    //     arsort($priority);

    //     for ($i=0; $i < count($selection); $i++) {
    //         $summary['programs'][$i]['name'] = $selection[$i]['program_name'];
    //         $summary['programs'][$i]['group'] = $i + 1;
    //         $summary['programs'][$i]['pool'] = $selection[$i]['pool'];
    //         $summary['programs'][$i]['program_keys'] = $selection[$i]['program_ids'];
    //         $summary['programs'][$i]['totals_before_optimization'] = $selection[$i]['totals'];
    //         $summary['programs'][$i]['units_before_optimization'] = $selection[$i]['units'];
    //         $summary['programs'][$i]['use_limiter'] = $selection[$i]['use_limiter'];
    //         $summary['programs'][$i]['comments'] = $selection[$i]['comments'];

    //         $tmp_selection = []; // used to store selection as we go through the priorities
    //         $tmp_program_output = []; // used to store the units selected for this program set

    //         if ($selection[$i]['use_limiter'] == 1) {
    //             $needed = $this->adjustedLimit(count($selection[$i]['units']));

    //             foreach ($priority as $p => $val) {
    //                 if (in_array($p, $selection[$i]['units']) && count($tmp_selection) < $needed) {
    //                     $tmp_selection[] = $p;
    //                 }
    //             }

    //             // check if we need more
    //             if (count($tmp_selection) < $needed) {
    //                 for ($j=0; $j<count($selection[$i]['units']); $j++) {
    //                     if (!in_array($selection[$i]['units'][$j], $tmp_selection) && count($tmp_selection) < $needed) {
    //                         $tmp_selection[] = $selection[$i]['units'][$j];
    //                     }
    //                 }
    //             }

    //             $tmp_program_output = $tmp_selection;
    //             $output = array_merge($output, $tmp_selection);
    //         } else {
    //             $tmp_program_output = $selection[$i]['units'];
    //             $output = array_merge($output, $selection[$i]['units']);
    //         }

    //           $summary['programs'][$i]['totals_after_optimization'] = count($tmp_program_output);
    //           $summary['programs'][$i]['units_after_optimization'] = $tmp_program_output;
    //     }

    //     //dd(array_unique($output), $output);

    //     $summary['ungrouped'] = $output;
    //     $summary['grouped'] = array_unique($output);

    //     return $summary;
    // }

    // public function selectionProcess(Audit $audit)
    // {
    //     // is the project processing all the buildings together? or do we have a combination of grouped buildings and single buildings?
    //     if ($audit->development_key) {
    //         $project = Project::where('project_key', '=', $audit->development_key)->with('programs')->first();
    //     } else {
    //         return "Error, this audit isn't associated with a project somehow...";
    //     }

    //     if (!$project->programs) {
    //         return "Error, this project doesn't have a program.";
    //     }

    //     $total_buildings = $project->total_building_count;
    //     $total_units = $project->total_unit_count;

    //     $pm_contact = ProjectContactRole::where('project_key', '=', $audit->development_key)
    //                             ->where('project_role_key', '=', 21)
    //                             ->with('organization')
    //                             ->first();

    //     $organization_id = null;
    //     if ($pm_contact) {
    //         if ($pm_contact->organization) {
    //             $organization_id = $pm_contact->organization->id;
    //         }
    //     }
        
    //     // save all buildings in building_inspection table
    //     $buildings = $project->buildings;

    //     // remove any data
    //     BuildingInspection::where('audit_id', '=', $audit->id)->delete();

    //     if ($buildings) {
    //         foreach ($buildings as $building) {
    //             if ($building->address) {
    //                 $address = $building->address->line_1;
    //                 $city = $building->address->city;
    //                 $state = $building->address->state;
    //                 $zip = $building->address->zip;
    //             } else {
    //                 $address = '';
    //                 $city = '';
    //                 $state = '';
    //                 $zip = '';
    //             }

    //             $b = new BuildingInspection([
    //                 'building_id' => $building->id,
    //                 'building_key' => $building->building_key,
    //                 'building_name' => $building->building_name,
    //                 'address' => $address,
    //                 'city' => $city,
    //                 'state' => $state,
    //                 'zip' => $zip,
    //                 'audit_id' => $audit->id,
    //                 'audit_key' => $audit->monitoring_key,
    //                 'project_id' => $project->id,
    //                 'project_key' => $project->project_key,
    //                 'pm_organization_id' => $organization_id,
    //                 'auditors' => null,
    //                 'nlt_count' => 0,
    //                 'lt_count' => 0,
    //                 'followup_count' => 0,
    //                 'complete' => 0,
    //                 'submitted_date_time' => null
    //             ]);
    //             $b->save();
    //         }
    //     }

    //     $selection = [];


    //     //
    //     //
    //     // 1 - FAF || NSP || TCE || RTCAP || 811 units
    //     // total for all those programs combined
    //     //
    //     //
        

    //     $comments = [];

    //     $program_bundle_ids = explode(',', SystemSetting::get('program_bundle'));
    //     $program_bundle_names = Program::whereIn('program_key', $program_bundle_ids)->get()->pluck('program_name')->toArray();
    //     $program_bundle_names = implode(',', $program_bundle_names);

    //     $comments[] = 'Pool of units chosen using audit id '.$audit->id.' and a list of programs: '.$program_bundle_names;
        
    //     $units = Unit::whereHas('programs', function ($query) use ($audit, $program_bundle_ids) {
    //                         $query->where('audit_id', '=', $audit->id);
    //                         $query->whereIn('program_key', $program_bundle_ids);
    //     })->get();

    //     $total = count($units);
    //     $comments[] = 'Total units in the pool is '.count($units);

    //     $program_htc_ids = explode(',', SystemSetting::get('program_htc'));
    //     $program_htc_names = Program::whereIn('program_key', $program_htc_ids)->get()->pluck('program_name')->toArray();
    //     $program_htc_names = implode(',', $program_htc_names);

    //     $program_htc_overlap = array_intersect($program_htc_ids, $program_bundle_ids);
    //     $program_htc_overlap_names = Program::whereIn('program_key', $program_htc_overlap)->get()->pluck('program_name')->toArray(); // 30001,30043
    //     $program_htc_overlap_names = implode(',', $program_htc_overlap_names);
    //     $comments[] = 'Identified the program keys that have HTC funding: '.$program_htc_overlap_names;

    //     $has_htc_funding = 0;
    //     foreach ($units as $unit) {
    //         foreach ($unit->programs as $unit_program) {
    //             if (in_array($unit_program->program_key, $program_htc_overlap)) {
    //                 $has_htc_funding = 1;
    //                 $comments[] = 'The unit key '.$unit->unit_key.' belongs to a program with HTC funding '.$unit_program->program_name;
    //             }
    //         }
    //     }

    //     if (!$has_htc_funding) {
    //         $comments[] = 'By checking each unit and associated programs with HTC funding, we determined that no HTC funding exists for this pool';
    //         $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 20);
    //         $comments[] = '20% of the pool is randomly selected. Total selected: '.count($units_selected);
    //     } else {
    //         $comments[] = 'By checking each unit and associated programs with HTC funding, we determined that there is HTC funding for this pool';

    //         // check in project_program->first_year_award_claimed date for the 15 year test
        
    //         $first_year = null;

    //         // look at HTC programs, get the most recent year for the check
    //         $comments[] = 'Going through the HTC programs, we look for the most recent year in the first_year_award_claimed field.';
    //         foreach ($project->programs as $program) {
    //             if (in_array($program->program_key, $program_htc_overlap)) {
    //                 if ($first_year == null || $first_year < $program->first_year_award_claimed) {
    //                     $first_year = $program->first_year_award_claimed;
    //                     $comments[] = 'Program key '.$program->program_key.' has the year '.$program->first_year_award_claimed.'.';
    //                 }
    //             }
    //         }

    //         if (idate("Y")-15 > $first_year && $first_year != null) {
    //             $first_fifteen_years = 0;
    //             $comments[] = 'Based on the year, we determined that the program is not within the first 15 years.';
    //         } else {
    //             $first_fifteen_years = 1;
    //             $comments[] = 'Based on the year, we determined that the program is within the first 15 years.';
    //         }
            
    //         if ($first_fifteen_years) {
    //             // check project for least purchase
    //             $leaseProgramKeys = explode(',', SystemSetting::get('lease_purchase'));
    //             $comments[] = 'Check if the programs associated with the project correspond to lease purchase using program keys: '.SystemSetting::get('lease_purchase').'.';
    //             foreach ($project->programs as $program) {
    //                 if (in_array($program->program_key, $leaseProgramKeys)) {
    //                     $isLeasePurchase = 1;
    //                     $comments[] = 'A program key '.$program->program_key.' confirms that this is a lease purchase.';
    //                 } else {
    //                     $isLeasePurchase = 0;
    //                 }
    //             }


    //             if ($isLeasePurchase) {
    //                 $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 20);
    //                 $comments[] = '20% of the pool is randomly selected. Total selected: '.count($units_selected);
    //             } else {
    //                 $is_multi_building_project = 0;

    //                 // eventually we will also be checking for building grouping...

    //                 // for each of the current programs+project, check if multiple_building_election_key is 2 for multi building project
    //                 $comments[] = 'Going through each program to determine if the project is a multi building project by looking for multiple_building_election_key=2.';
    //                 foreach ($project->programs as $program) {
    //                     if (in_array($program->program_key, $program_bundle_ids)) {
    //                         if ($program->multiple_building_election_key == 2) {
    //                             $is_multi_building_project = 1;
    //                             $comments[] = 'Program key '.$program->program_key.' showed that the project is a multi building project.';
    //                         }
    //                     }
    //                 }

    //                 if ($is_multi_building_project) {
    //                     $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 20);
    //                     $comments[] = '20% of the pool is randomly selected. Total selected: '.count($units_selected);
    //                 } else {
    //                     $comments[] = 'The project is not a multi building project.';
    //                     // group units by building, then proceed with the random selection
    //                     // create a new list of units based on building and project key
    //                     $units_selected = [];
    //                     foreach ($buildings as $building) {
    //                         $new_building_selection = $this->randomSelection($building->units->pluck('unit_key')->toArray(), 20);
    //                         $units_selected = array_merge($units_selected, $new_building_selection);
    //                         $comments[] = '20% of building key '.$building->building_key.' is randomly selected. Total selected: '.count($new_building_selection).'.';
    //                     }
    //                 }
    //             }
    //         } else {
    //             $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 20);
    //             $comments[] = '20% of the pool is randomly selected. Total selected: '.count($units_selected);
    //         }
    //     }
        
    //     $selection[] = [
    //         "program_name" => "FAF NSP TCE RTCAP 811",
    //         "program_ids" => SystemSetting::get('program_bundle'),
    //         "pool" => count($units),
    //         "units" => $units_selected,
    //         "totals" => count($units_selected),
    //         "use_limiter" => $has_htc_funding, // used to trigger limiter
    //         "comments" => $comments
    //     ];


    //     //
    //     //
    //     // 2 - 811 units
    //     // 100% selection
    //     // for units with 811 funding
    //     //
    //     //
        

    //     $program_811_ids = explode(',', SystemSetting::get('program_811'));
    //     $program_811_names = Program::whereIn('program_key', $program_811_ids)->get()->pluck('program_name')->toArray();
    //     $program_811_names = implode(',', $program_811_names);
    //     $comments = [];
    //     $units = Unit::whereHas('programs', function ($query) use ($audit, $program_811_ids) {
    //                         $query->where('audit_id', '=', $audit->id);
    //                         $query->whereIn('program_key', $program_811_ids);
    //     })->get();
    //     $units_selected = $units->pluck('unit_key')->toArray();

    //     $comments[] = 'Pool of units chosen among units belonging to programs associated with this audit id '.$audit->id.'. Programs: '.$program_811_names;
    //     $comments[] = 'Total units in the pool is '.count($units);
    //     $comments[] = '100% of units selected:'.count($units_selected);

    //     $selection[] = [
    //         "program_name" => "811",
    //         "program_ids" => SystemSetting::get('program_811'),
    //         "pool" => count($units),
    //         "units" => $units_selected,
    //         "totals" => count($units_selected),
    //         "use_limiter" => 0,
    //         "comments" => $comments
    //     ];


    //     //
    //     //
    //     // 3 - Medicaid units
    //     // 100% selection
    //     //
    //     //
        

    //     $program_medicaid_ids = explode(',', SystemSetting::get('program_medicaid'));
    //     $program_medicaid_names = Program::whereIn('program_key', $program_medicaid_ids)->get()->pluck('program_name')->toArray();
    //     $program_medicaid_names = implode(',', $program_medicaid_names);
    //     $comments = [];
    //     $units = Unit::whereHas('programs', function ($query) use ($audit, $program_medicaid_ids) {
    //                         $query->where('audit_id', '=', $audit->id);
    //                         $query->whereIn('program_key', $program_medicaid_ids);
    //     })->get();
    //     $units_selected = $units->pluck('unit_key')->toArray();

    //     $comments[] = 'Pool of units chosen among units belonging to programs associated with this audit id '.$audit->id.'. Programs: '.$program_medicaid_names;
    //     $comments[] = 'Total units in the pool is '.count($units);
    //     $comments[] = '100% of units selected:'.count($units_selected);

    //     $selection[] = [
    //         "program_name" => "Medicaid",
    //         "program_ids" => SystemSetting::get('program_medicaid'),
    //         "pool" => count($units),
    //         "units" => $units_selected,
    //         "totals" => count($units_selected),
    //         "use_limiter" => 0,
    //         "comments" => $comments
    //     ];


    //     //
    //     //
    //     // 4 - HOME
    //     //
    //     //
        

    //     $program_home_ids = explode(',', SystemSetting::get('program_home'));
    //     $program_home_names = Program::whereIn('program_key', $program_home_ids)->get()->pluck('program_name')->toArray();
    //     $program_home_names = implode(',', $program_home_names);
    //     $comments = [];

    //     $comments[] = 'Pool of units chosen among units belonging to programs associated with this audit id '.$audit->id.'. Programs: '.$program_home_names;

    //     $total_units_with_program = Unit::whereHas('programs', function ($query) use ($audit) {
    //                         $query->where('audit_id', '=', $audit->id);
    //     })->count();

    //     $units = Unit::whereHas('programs', function ($query) use ($audit, $program_home_ids) {
    //                         $query->where('audit_id', '=', $audit->id);
    //                         $query->whereIn('program_key', $program_home_ids);
    //     })->get();
        
    //     $total_units = count($units);

    //     $program_htc_overlap = array_intersect($program_htc_ids, $program_home_ids);
    //     $program_htc_overlap_names = Program::whereIn('program_key', $program_htc_overlap)->get()->pluck('program_name')->toArray();
    //     $program_htc_overlap_names = implode(',', $program_htc_overlap_names);

    //     $units_selected = [];
    //     $htc_units_subset_for_all = [];
    //     $htc_units_subset = [];
    //     $units_to_check_for_overlap = [];
        
    //     $comments[] = 'Total units with HOME funding is '.$total_units;
    //     $comments[] = 'Total units in the project with a program is '.$total_units_with_program;
 
    //     if (count($units) <= 4) {
    //         $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 100);
    //         $comments[] = 'Because there are less than 4 HOME units, the selection is 100%. Total selected: '.count($units_selected);
    //     } else {
    //         if (ceil($total_units/2) >= ceil($total_units_with_program/5)) {
    //             $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, ceil($total_units/2));

    //             $comments[] = 'Because there are more than 4 units and because 20% of project units is smaller than 50% of HOME units, the total selected is '.ceil($total_units/2);
    //         } else {
    //             $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, ceil($total_units_with_program/5));
    //             $comments[] = 'Because there are more than 4 units and because 20% of project units is greater than 50% of HOME units, the total selected is '.ceil($total_units_with_program/5);
    //         }
    //     }

    //     foreach ($units_selected as $unit_selected) {
    //         $has_htc_funding = 0;

    //         $comments[] = 'Checking if HTC funding applies to this unit '.$unit_selected.' by cross checking with HTC programs: '.$program_htc_overlap_names;
            
    //         // if units have HTC funding add to subset
    //         $unit = Unit::where('unit_key', '=', $unit_selected)->first();
    //         foreach ($unit->programs as $unit_program) {
    //             if (in_array($unit_program->program_key, $program_htc_overlap)) {
    //                 $has_htc_funding = 1;
    //                 $comments[] = 'The unit key '.$unit_selected.' belongs to a program with HTC funding '.$unit_program->program_name;
    //             }
    //         }
    //         if ($has_htc_funding) {
    //             $comments[] = 'We determined that there was HTC funding for this unit. The unit was added to the HTC subset.';
    //             $htc_units_subset[] = $unit_selected;
    //         }
    //     }

    //     $htc_units_subset_for_home = $htc_units_subset;
    //     $units_to_check_for_overlap = array_merge($units_to_check_for_overlap, $units_selected);

    //     $selection[] = [
    //         "program_name" => "HOME",
    //         "program_ids" => SystemSetting::get('program_home'),
    //         "pool" => count($units),
    //         "units" => $units_selected,
    //         "totals" => count($units_selected),
    //         'htc_subset' => $htc_units_subset,
    //         "use_limiter" => 0,
    //         "comments" => $comments
    //     ];


    //     //
    //     //
    //     // 5 - OHTF
    //     //
    //     //
        

    //     $program_ohtf_ids = explode(',', SystemSetting::get('program_ohtf'));
    //     $program_ohtf_names = Program::whereIn('program_key', $program_ohtf_ids)->get()->pluck('program_name')->toArray();
    //     $program_ohtf_names = implode(',', $program_ohtf_names);
    //     $comments = [];

    //     $comments[] = 'Pool of units chosen among units belonging to programs associated with this audit id '.$audit->id.'. Programs: '.$program_ohtf_names;

    //     // total units with programs already computed in HOME
    //     // $total_units_with_program

    //     $units = Unit::whereHas('programs', function ($query) use ($audit, $program_ohtf_ids) {
    //                         $query->where('audit_id', '=', $audit->id);
    //                         $query->whereIn('program_key', $program_ohtf_ids);
    //     })->get();

    //     $total_units = count($units);

    //     $program_htc_overlap = array_intersect($program_htc_ids, $program_ohtf_ids);
    //     $program_htc_overlap_names = Program::whereIn('program_key', $program_htc_overlap)->get()->pluck('program_name')->toArray();
    //     $program_htc_overlap_names = implode(',', $program_htc_overlap_names);

    //     $units_selected = [];
    //     $htc_units_subset = [];

    //     $comments[] = 'Total units with OHTF funding is '.$total_units;
    //     $comments[] = 'Total units in the project with a program is '.$total_units_with_program;

    //     if (count($units) <= 4) {
    //         $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 100);
    //         $comments[] = 'Because there are less than 4 OHTF units, the selection is 100%. Total selected: '.count($units_selected);
    //     } else {
    //         if (ceil($total_units/2) >= ceil($total_units_with_program/5)) {
    //              $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, ceil($total_units/2));
    //              $comments[] = 'Because there are more than 4 units and because 20% of project units is smaller than 50% of OHTF units, the total selected is '.ceil($total_units/2);
    //         } else {
    //             $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, ceil($total_units_with_program/5));
    //             $comments[] = 'Because there are more than 4 units and because 20% of project units is greater than 50% of OHTF units, the total selected is '.ceil($total_units_with_program/5);
    //         }
    //     }

    //     foreach ($units_selected as $unit_selected) {
    //         $has_htc_funding = 0;

    //         $comments[] = 'Checking if HTC funding applies to this unit '.$unit_selected->unit_key.' by cross checking with HTC programs: '.$program_htc_overlap_names;

    //         // if units have HTC funding add to subset
    //         foreach ($unit_selected->programs as $unit_program) {
    //             if (in_array($unit_program->program_key, $program_htc_overlap)) {
    //                 $has_htc_funding = 1;
    //                 $comments[] = 'The unit key '.$unit_selected->unit_key.' belongs to a program with HTC funding '.$unit_program->program_name;
    //             }
    //         }
    //         if ($has_htc_funding) {
    //             $htc_units_subset = array_merge($htc_units_subset, $unit_selected);
    //             $comments[] = 'We determined that there was HTC funding for this unit. The unit was added to the HTC subset.';
    //         }
    //     }

    //     $htc_units_subset_for_ohtf = $htc_units_subset;
    //     $units_to_check_for_overlap = array_merge($units_to_check_for_overlap, $units_selected);

    //     $selection[] = [
    //         "program_name" => "OHTF",
    //         "program_ids" => SystemSetting::get('program_ohtf'),
    //         "pool" => count($units),
    //         "units" => $units_selected,
    //         "totals" => count($units_selected),
    //         'htc_subset' => $htc_units_subset,
    //         "use_limiter" => 0,
    //         "comments" => $comments
    //     ];


    //     //
    //     //
    //     // 6 - NHTF
    //     //
    //     //
        

    //     $program_nhtf_ids = explode(',', SystemSetting::get('program_nhtf'));
    //     $program_nhtf_names = Program::whereIn('program_key', $program_nhtf_ids)->get()->pluck('program_name')->toArray();
    //     $program_nhtf_names = implode(',', $program_nhtf_names);
    //     $comments = [];

    //     $comments[] = 'Pool of units chosen among units belonging to programs associated with this audit id '.$audit->id.'. Programs: '.$program_nhtf_names;

    //     $units = Unit::whereHas('programs', function ($query) use ($audit, $program_nhtf_ids) {
    //                         $query->where('audit_id', '=', $audit->id);
    //                         $query->whereIn('program_key', $program_nhtf_ids);
    //     })->get();

    //     $program_htc_overlap = array_intersect($program_htc_ids, $program_nhtf_ids);
    //     $program_htc_overlap_names = Program::whereIn('program_key', $program_htc_overlap)->get()->pluck('program_name')->toArray();
    //     $program_htc_overlap_names = implode(',', $program_htc_overlap_names);

    //     $units_selected = [];
    //     $htc_units_subset = [];
        
    //     $total_units = count($units);

    //     $comments[] = 'Total units with NHTF funding is '.$total_units;
    //     $comments[] = 'Total units in the project with a program is '.$total_units_with_program;

    //     if (count($units) <= 4) {
    //         $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 100);
    //         $comments[] = 'Because there are less than 4 NHTF units, the selection is 100%. Total selected: '.count($units_selected);
    //     } else {
    //         if (ceil($total_units/2) >= ceil($total_units_with_program/5)) {
    //              $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, ceil($total_units/2));
    //              $comments[] = 'Because there are more than 4 units and because 20% of project units is smaller than 50% of NHTF units, the total selected is '.ceil($total_units/2);
    //         } else {
    //             $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, ceil($total_units_with_program/5));
    //             $comments[] = 'Because there are more than 4 units and because 20% of project units is greater than 50% of NHTF units, the total selected is '.ceil($total_units_with_program/5);
    //         }
    //     }

    //     foreach ($units_selected as $unit_selected) {
    //         $has_htc_funding = 0;

    //         $comments[] = 'Checking if HTC funding applies to this unit '.$unit_selected->unit_key.' by cross checking with HTC programs: '.$program_htc_overlap_names;

    //         // if units have HTC funding add to subset
    //         $unit = Unit::where('unit_key', '=', $unit_selected)->first();
    //         foreach ($unit_selected->programs as $unit_program) {
    //             if (in_array($unit_program->program_key, $program_htc_overlap)) {
    //                 $has_htc_funding = 1;
    //                 $comments[] = 'The unit key '.$unit_selected.' belongs to a program with HTC funding '.$unit_program->program_name;
    //             }
    //         }
    //         if ($has_htc_funding) {
    //             $comments[] = 'We determined that there was HTC funding for this unit. The unit was added to the HTC subset.';
    //             $htc_units_subset[] = $unit_selected;
    //         }
    //     }

    //     $htc_units_subset_for_nhtf = $htc_units_subset;
    //     $units_to_check_for_overlap = array_merge($units_to_check_for_overlap, $units_selected);

    //     $selection[] = [
    //         "program_name" => "NHTF",
    //         "program_ids" => SystemSetting::get('program_nhtf'),
    //         "pool" => count($units),
    //         "units" => $units_selected,
    //         "totals" => count($units_selected),
    //         'htc_subset' => $htc_units_subset,
    //         "use_limiter" => 0,
    //         "comments" => $comments
    //     ];

    //     // check for HOME, OHTF, NHTF overlap and send to analyst
    //     // overlap contains the keys of units
    //     $overlap = [];
    //     for ($i=0; $i<count($units_to_check_for_overlap); $i++) {
    //         for ($j=0; $j<count($units_to_check_for_overlap); $j++) {
    //             if ($units_to_check_for_overlap[$i] == $units_to_check_for_overlap[$j] && $i != $j && !in_array($units_to_check_for_overlap[$i], $overlap)) {
    //                 $overlap[] = $units_to_check_for_overlap[$i];
    //             }
    //         }
    //     }


    //     //
    //     //
    //     // 7 - HTC
    //     // get totals of all units HTC and select all units without NHTF. OHTF and HOME
    //     // check in project_program->first_year_award_claimed date for the 15 year test
    //     // after 15 years: 20% of total
    //     // $program_htc_ids = SystemSetting::get('program_htc'); // already loaded
    //     //
    //     //

    //     $comments = [];

    //     // total HTC funded units (71)
    //     $all_htc_units = Unit::whereHas('programs', function ($query) use ($audit, $program_htc_ids) {
    //                         $query->where('audit_id', '=', $audit->id);
    //                         $query->whereIn('program_key', $program_htc_ids);
    //     })->get();

    //     $total_htc_units = count($all_htc_units);

    //     $comments[] = 'The total of HTC units is '.$total_htc_units.'.';

    //     // HTC without HOME, OHTF, NHTF
    //     $program_htc_only_ids = array_diff($program_htc_ids, $program_home_ids, $program_ohtf_ids, $program_nhtf_ids);

    //     $program_htc_only_names = Program::whereIn('program_key', $program_htc_only_ids)->get()->pluck('program_name')->toArray();
    //     $program_htc_only_names = implode(',', $program_htc_only_names);

    //     $comments[] = 'Pool of units chosen among units belonging to HTC programs associated with this audit id '.$audit->id.' excluding HOME, OHTF and NHTF. Programs: '.$program_htc_only_names;

    //     $units = [];
    //     foreach ($all_htc_units as $all_htc_unit) {
    //         $do_not_add = 0;
    //         foreach ($all_htc_unit->programs as $all_htc_unit_program) {
    //             if (in_array($all_htc_unit_program->program_key, $program_home_ids) ||
    //                 in_array($all_htc_unit_program->program_key, $program_ohtf_ids) ||
    //                 in_array($all_htc_unit_program->program_key, $program_nhtf_ids)) {
    //                 $do_not_add = 1;
    //             }
    //         }

    //         if (!$do_not_add) {
    //             $units[] = $all_htc_unit->unit_key;
    //         }
    //     }

    //     $comments[] = 'The total of HTC units excluding HOME, OHTF and NHTF is '.count($units).'.';

    //     // check in project_program->first_year_award_claimed date for the 15 year test
        
    //     // how many units do we need in the selection accounting for the ones added from HOME, OHTF, NHTF
        
    //     $htc_units_subset = array_merge($htc_units_subset_for_home, $htc_units_subset_for_ohtf, $htc_units_subset_for_nhtf);

    //     if (ceil($total_htc_units/5) >= count($htc_units_subset)) {
    //         $number_of_htc_units_needed = ceil($total_htc_units/5) - count($htc_units_subset);
    //         $comments[] = 'The number of HTC units that still need to be selected is 20% of the total number of HTC units minus the number of HTC units already selected with HOME, OHTF and NHTF: '.$number_of_htc_units_needed.'.';
    //     } else {
    //         $number_of_htc_units_needed = 0;
    //         $comments[] = 'There are enough HTC units in the HOME, OHTF and NHTF, no need to select more.';
    //     }

    //     $units_selected = [];

    //     // only proceed with selection if needed
    //     if ($number_of_htc_units_needed > 0 && count($units) > 0) {
    //         $first_year = null;

    //         // look at HTC programs, get the most recent year for the check
    //         $comments[] = 'Going through the HTC programs, we look for the most recent year in the first_year_award_claimed field.';
    //         foreach ($project->programs as $program) {
    //             if (in_array($program->program_key, $program_htc_only_ids)) {
    //                 if ($first_year == null || $first_year < $program->first_year_award_claimed) {
    //                     $first_year = $program->first_year_award_claimed;
    //                     $comments[] = 'Program key '.$program->program_key.' has the year '.$program->first_year_award_claimed.'.';
    //                 }
    //             }
    //         }

    //         if (idate("Y")-15 > $first_year && $first_year != null) {
    //             $first_fifteen_years = 0;
    //             $comments[] = 'Based on the year, we determined that the program is not within the first 15 years.';
    //         } else {
    //             $first_fifteen_years = 1;
    //             $comments[] = 'Based on the year, we determined that the program is within the first 15 years.';
    //         }
            
    //         if ($first_fifteen_years) {
    //             // check project for least purchase
    //             $leaseProgramKeys = explode(',', SystemSetting::get('lease_purchase'));
    //             $comments[] = 'Check if the programs associated with the project correspond to lease purchase using program keys: '.SystemSetting::get('lease_purchase').'.';
    //             foreach ($project->programs as $program) {
    //                 if (in_array($program->program_key, $leaseProgramKeys)) {
    //                     $isLeasePurchase = 1;
    //                     $comments[] = 'A program key '.$program->program_key.' confirms that this is a lease purchase.';
    //                 } else {
    //                     $isLeasePurchase = 0;
    //                 }
    //             }

    //             if ($isLeasePurchase) {
    //                 $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, $number_of_htc_units_needed);
    //                 $comments[] = 'It is a lease purchase. Total selected: '.count($units_selected);
    //             } else {
    //                 $is_multi_building_project = 0;
    //                 $comments[] = 'It is not a lease purchase.';
    //                 // for each of the current programs+project, check if multiple_building_election_key is 2 for multi building project
    //                 $comments[] = 'Going through each program to determine if the project is a multi building project by looking for multiple_building_election_key=2.';
    //                 foreach ($project->programs as $program) {
    //                     if (in_array($program->program_key, $program_bundle_ids)) {
    //                         if ($program->multiple_building_election_key == 2) {
    //                             $is_multi_building_project = 1;
    //                             $comments[] = 'Program key '.$program->program_key.' showed that the project is a multi building project.';
    //                         }
    //                     }
    //                 }

    //                 if ($is_multi_building_project) {
    //                     $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, $number_of_htc_units_needed);
    //                     $comments[] = 'The project is a multi building project. Total selected: '.count($units_selected);
    //                 } else {
    //                     $comments[] = 'The project is not a multi building project.';
    //                     // group units by building, then proceed with the random selection
    //                     // create a new list of units based on building and project key
    //                     $units_selected = [];
    //                     foreach ($buildings as $building) {
    //                         if ($building->units) {
    //                             // get total of HTC funded units for that building
    //                             $total_htc_units_for_building = Unit::where('building_key', '=', $building->building_key)
    //                                             ->whereHas('programs', function ($query) use ($audit, $program_htc_ids) {
    //                                                 $query->where('audit_id', '=', $audit->id);
    //                                                 $query->whereIn('program_key', $program_htc_ids);
    //                                             })->count();
    //                             $comments[] = 'The total of HTC units for building key '.$building->building_key.' is '.$total_htc_units_for_building.'.';

    //                             // get total of HTC units that overlap with HOME, OHTF and NHTF in that particular building
    //                             // units with HTC, not in subset

    //                             $total_htc_units_with_overlap_for_building = Unit::where('building_key', '=', $building->building_key)
    //                                             ->whereHas('programs', function ($query) use ($audit, $program_htc_ids) {
    //                                                 $query->where('audit_id', '=', $audit->id);
    //                                                 $query->whereIn('program_key', $program_htc_ids);
    //                                             })->count();
                                
    //                             // get all HTC units that do not overlap with HOME, OHTF and NHTF in that building
    //                             $htc_units_without_overlap = Unit::where('building_key', '=', $building->building_key)
    //                                             ->whereHas('programs', function ($query) use ($audit, $program_htc_ids, $program_home_ids, $program_ohtf_ids, $program_nhtf_ids) {
    //                                                 $query->where('audit_id', '=', $audit->id);
    //                                                 $query->whereIn('program_key', $program_htc_ids);
    //                                                 $query->whereNotIn('program_key', $program_home_ids);
    //                                                 $query->whereNotIn('program_key', $program_ohtf_ids);
    //                                                 $query->whereNotIn('program_key', $program_nhtf_ids);
    //                                             })->get();

    //                             $total_htc_units_without_overlap = count($htc_units_without_overlap);

    //                             $new_building_selection = $this->randomSelection($building->units->pluck('unit_key')->toArray(), 0, $number_of_htc_units_needed);
    //                             $units_selected = array_merge($units_selected, $new_building_selection);
    //                             $comments[] = 'Randomly selected units in building '.$building->building_key.'. Total selected: '.count($new_building_selection).'.';
    //                         }
    //                     }
    //                 }
    //             }
    //         } else {
    //             $units_selected = $this->randomSelection($units->pluck('unit_key')->toArray(), 0, $number_of_htc_units_needed);
    //             $comments[] = 'Total selected: '.count($units_selected);
    //         }
    //     }

    //     $units_selected = array_merge($units_selected, $htc_units_subset_for_home, $htc_units_subset_for_ohtf, $htc_units_subset_for_nhtf);

    //     $selection[] = [
    //         "program_name" => "HTC",
    //         "program_ids" => SystemSetting::get('program_htc'),
    //         "pool" => count($units),
    //         "units" => $units_selected,
    //         "totals" => count($units_selected),
    //         "use_limiter" => 1,
    //         "comments" => $comments
    //     ];

    //     // combineOptimize returns an array [units, summary]
    //     $optimized_selection = $this->combineOptimize($selection);

    //     return [$optimized_selection, $overlap, $project, $organization_id];
    // }

    // public function createNewCachedAudit(Audit $audit, $summary = null)
    // {
    //     // create cached audit
    //     //
        
    //     $project_id = null;
    //     $project_ref = '';
    //     $project_name = null;
    //     $total_buildings = 0;
    //     $lead = null;
    //     $lead_json = '{ "id": null, "name": "", "initials": "", "color": "", "status": "" }';

    //     // project address
    //     $address = '';
    //     $city = '';
    //     $state = '';
    //     $zip = '';


    //     if ($audit->user_key) {
    //         $lead_user = User::where('devco_key', '=', $audit->user_key)->first();
    //         if ($lead_user) {
    //             $lead = $lead_user->id;
    //             $words = explode(" ", $lead_user->name);
    //             $initials = "";
    //             foreach ($words as $w) {
    //                 $initials .= $w[0];
    //             }
    //             $initials = substr($initials, 0, 2); // keep the first two letters only

    //             $data = [
    //                 "id" => $lead_user->id,
    //                 "name" => $lead_user->name,
    //                 "initials" => $initials,
    //                 "color" => $lead_user->badge_color,
    //                 "status" => ""
    //             ];
    //             $lead_json = json_encode($data);
    //         }
    //     }

    //     if ($audit->development_key) {
    //         $project = Project::where('project_key', '=', $audit->development_key)->with('address')->first();
    //         if ($project) {
    //             $project_id = $project->id;
    //             $project_ref = $project->project_key;
    //             $project_name = $project->project_name;
    //             $total_buildings = $project->total_building_count;

    //             if ($project->address) {
    //                 $address = $project->address->line_1;
    //                 $city = $project->address->city;
    //                 $state = $project->address->state;
    //                 $zip = $project->address->zip;
    //             }
    //         }
    //     }

    //     // in project_roles
    //     // primary owner: project_role_key = 20, id = 98
    //     // primary manager: project_role_key = 21, id = 161
    //     $pm_name = '';
    //     $pm_contact = ProjectContactRole::where('project_key', '=', $audit->development_key)
    //                             ->where('project_role_key', '=', 21)
    //                             ->with('organization.address')
    //                             ->first();

    //     if ($pm_contact) {
    //         if ($pm_contact->organization) {
    //             $pm_name = $pm_contact->organization->organization_name;
    //         }
    //     }
    //     if ($pm_name == '') {
    //         if ($pm_contact->person) {
    //             $pm_name = $pm_contact->person->first_name." ".$pm_contact->person->last_name;
    //         }
    //     }

    //     // if no organization put contact name under the project name

    //     // build amenities array using amenity_inspections table

    //     // total items is the total number of units added during the selection process
    //     $total_items = count($summary['grouped']); // TBD

    //     // save summary
    //     $audit->selection_summary = json_encode($summary);
    //     $audit->save();

    //     $cached_audit = new CachedAudit([
    //             'audit_id' => $audit->id,
    //             'audit_key' => $audit->monitoring_key,
    //             'project_id' => $project_id,
    //             'project_key' => $audit->development_key,
    //             'project_ref' => $project_ref,
    //             'status' => '',
    //             'lead' => $lead,
    //             'lead_json' => $lead_json,
    //             'title' => $project_name,
    //             'pm' => $pm_name,
    //             'address' => $address,
    //             'city' => $city,
    //             'state' => $state,
    //             'zip' => $zip,
    //             'total_buildings' => $total_buildings,
    //             'inspection_icon' => 'a-mobile-repeat',
    //             'inspection_status' => 'action-needed', // no scheduled date in array yet, that's why
    //             'inspection_status_text' => 'AUDIT NEEDS SCHEDULING',
    //             'inspection_schedule_text' => 'CLICK TO SCHEDULE AUDIT',
    //             'inspectable_items' => 0,
    //             'total_items' => $total_items,
    //             'audit_compliance_icon' => 'a-circle-checked',
    //             'audit_compliance_status' => 'ok-actionable',
    //             'audit_compliance_status_text' => 'AUDIT COMPLIANT',
    //             'followup_status' => '',
    //             'followup_status_text' => 'NO FOLLOWUPS',
    //             'file_audit_icon' => 'a-folder',
    //             'file_audit_status' => '',
    //             'file_audit_status_text' => 'CLICK TO ADD A FINDING',
    //             'nlt_audit_icon' => 'a-booboo',
    //             'nlt_audit_status' => '',
    //             'nlt_audit_status_text' => 'CLICK TO ADD A FINDING',
    //             'lt_audit_icon' => 'a-skull',
    //             'lt_audit_status' => '',
    //             'lt_audit_status_text' => 'CLICK TO ADD A FINDING',
    //             'smoke_audit_icon' => 'a-flames',
    //             'smoke_audit_status' => '',
    //             'smoke_audit_status_text' => 'CLICK TO ADD A FINDING',
    //             'auditor_status_icon' => 'a-avatar-fail',
    //             'auditor_status' => 'action-required',
    //             'auditor_status_text' => 'ASSIGN AUDITORS',
    //             'message_status_icon' => 'a-envelope-4',
    //             'message_status' => '',
    //             'message_status_text' => '',
    //             'document_status_icon' => 'a-files',
    //             'document_status' => '',
    //             'document_status_text' => 'DOCUMENT STATUS',
    //             'history_status_icon' => 'a-person-clock',
    //             'history_status' => '',
    //             'history_status_text' => 'NO/VIEW HISTORY',
    //             'step_status_icon' => 'a-home-question',
    //             'step_status' => 'no-action',
    //             'step_status_text' => 'REVIEW AND ASSIGN INSPECTABLE AREAS',
    //             //'amenities_json' => json_encode($amenities)
    //         ]);
    //         $cached_audit->save();

    //     // $data = [
    //     //     'event' => 'NewMessage',
    //     //     'data' => [
    //     //         'stats_communication_total' => $stats_communication_total
    //     //     ]
    //     // ];

    //     // Redis::publish('communications', json_encode($data));
    // }
}
