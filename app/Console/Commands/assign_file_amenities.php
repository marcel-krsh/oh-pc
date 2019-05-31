<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class assign_file_amenities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        // $projects = \App\Models\Project::get();

        // forEach($projects as $project){

        //     $project_id = $project->id;
        //     $units = $project->units();


        //     forEach($units as $unit){
        //         $building_id = $unit->building->id;
        //         $unit_id = $unit->id;
        //         $audit = $project->current_audit;
        //         if(null !== $audit){
        //             $audit_id = $audit->audit_id;
        //         } else {
        //             $audit_id = null;
        //         }
        //         $amenities = Amenity::where("file", 1 )->where('default',1)->get();

                
        //             $amenity_id = null;
        //             // $toplevel = $request->get('toplevel');

        //             $new_amenities = $amenities;

        //             //dd($project_id, $building_id, $unit_id, $amenity_id, $audit_id, $toplevel);

        //             // get current audit id using project_id
        //             // only one audit can be active at one time
        //             //$audit = CachedAudit::where("audit_id", "=", $audit_id)->orderBy('id', 'desc')->first();

        //             // if (!$audit) {
        //             //     dd("There is an error - cannot find that audit - 3854");
        //             // }

        //             //$user = Auth::user();

        //             if (null !== $audit && $new_amenities !== null) {
        //                 foreach ($new_amenities as $new_amenity) {

        //                     if ($new_amenity['auditor_id']) {
        //                         $auditor = AuditAuditor::where("user_id", "=", $new_amenity['auditor_id'])->where("audit_id", "=", $audit->audit_id)->with('user')->first();
        //                         if (!$auditor) {
        //                             dd("There is an error - this auditor doesn't seem to be assigned to this audit.");
        //                         }

        //                         $auditor_color = $auditor->user->badge_color;
        //                         $auditor_initials = $auditor->user->initials();
        //                         $auditor_name = $auditor->user->full_name();
        //                         $auditorid = $auditor->user_id;
        //                     } else {
        //                         $auditor_color = '';
        //                         $auditor_initials = '';
        //                         $auditor_name = '';
        //                         $auditorid = null;
        //                     }

        //                     // get amenity type
        //                     $amenity_type = Amenity::where("id", "=", $new_amenity['amenity_id'])->first();

        //                     // project level amenities are handled through OrderingBuilding and CachedBuilding
        //                     if ($project_id && $unit_id == '' && $building_id == '') {

        //                         $name = $amenity_type->amenity_description;

        //                         // create ProjectAmenity
        //                         // create CachedBuilding
        //                         // create AmenityInspection
        //                         // create OrderingBuilding
        //                         // load buildings

        //                         $project_amenity = new ProjectAmenity([
        //                             'project_key' => $audit->project_key,
        //                             'project_id' => $audit->project_id,
        //                             'amenity_type_key' => $amenity_type->amenity_type_key,
        //                             'amenity_id' => $amenity_type->id,
        //                             'comment' => 'manually added by ' . Auth::user()->id,
        //                         ]);
        //                         $project_amenity->save();

        //                         $cached_building = new CachedBuilding([
        //                             'building_name' => $name,
        //                             'building_id' => null,
        //                             'building_key' => null,
        //                             'audit_id' => $audit->audit_id,
        //                             'audit_key' => $audit->audit_key,
        //                             'project_id' => $audit->project_id,
        //                             'project_key' => $audit->project_key,
        //                             'lead_id' => $audit->lead_id,
        //                             'lead_key' => $audit->lead_key,
        //                             'status' => '',
        //                             'type' => $amenity_type->icon,
        //                             'type_total' => null,
        //                             'type_text' => null,
        //                             'type_text_plural' => null,
        //                             'finding_total' => 0,
        //                             'finding_file_status' => '',
        //                             'finding_nlt_status' => '',
        //                             'finding_lt_status' => '',
        //                             'finding_file_total' => 0,
        //                             'finding_file_completed' => 0,
        //                             'finding_nlt_total' => 0,
        //                             'finding_nlt_completed' => 0,
        //                             'finding_lt_total' => 0,
        //                             'finding_lt_completed' => 0,
        //                             'address' => $audit->address,
        //                             'city' => $audit->city,
        //                             'state' => $audit->state,
        //                             'zip' => $audit->zip,
        //                             'amenity_id' => $amenity_type->id,
        //                         ]);
        //                         $cached_building->save();

        //                         $amenity = new AmenityInspection([
        //                             'audit_id' => $audit->audit_id,
        //                             'project_id' => $audit->project_id,
        //                             'amenity_id' => $amenity_type->id,
        //                             'auditor_id' => $auditorid,
        //                             'cachedbuilding_id' => $cached_building->id,
        //                         ]);
        //                         $amenity->save();

        //                         $cached_building->amenity_inspection_id = $amenity->id;
        //                         $cached_building->save();

        //                         // latest ordering
        //                         $latest_ordering = OrderingBuilding::where('user_id', '=', Auth::user()->id)
        //                             ->where('audit_id', '=', $audit->audit_id)
        //                             ->orderBy('order', 'desc')
        //                             ->first()
        //                             ->order;
        //                         // save the ordering
        //                         $ordering = new OrderingBuilding([
        //                             'user_id' => Auth::user()->id,
        //                             'audit_id' => $audit->audit_id,
        //                             'building_id' => null,
        //                             'project_id' => $audit->project_id,
        //                             'amenity_id' => $amenity_type->id,
        //                             'amenity_inspection_id' => $amenity->id,
        //                             'order' => $latest_ordering + 1,
        //                         ]);
        //                         $ordering->save();

        //                         $buildings = OrderingBuilding::where('audit_id', '=', $audit->audit_id)->where('user_id', '=', Auth::user()->id)->orderBy('order', 'asc')->with('building')->get();

        //                         $data = $buildings;
                
        //     }// end for each unit
        // }// end for each project
    }// end handle
}
