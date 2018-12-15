<?php

namespace App\Http\Controllers;
use DB;
use DateTime;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\AuthService;
use App\Services\DevcoService;
use App\Models\AuthTracker;
use App\Models\SystemSetting;
//use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Log;

use App\Models\Address; //
use App\Models\ProjectActivity; //
use App\Models\ProjectContactRole; //
use App\Models\Organization; //
use App\Models\Project; //
use App\Models\Program; // only funding_id - which we don't sync
use App\Models\Unit; //
use App\Models\HouseholdEvent; //
use App\Models\Household; //
use App\Models\UtilityAllowance; //
use App\Models\ProjectAmenity; //
use App\Models\BuildingAmenity; //
use App\Models\UnitAmenity; //
use App\Models\ProjectFinancial; //
use App\Models\ProjectProgram; //
use App\Models\AuditAuditor; //
use App\Models\Building; 
use App\Models\PhoneNumber;
use App\Models\User;
use App\Models\ComplianceContact;
use App\Models\PhoneNumberType;
use App\Models\EmailAddressType;
use App\Models\EmailAddress;
use App\Models\HouseHoldSize;
use App\Models\ProjectDate;
use App\Models\UnitIdentity;





class SyncController extends Controller
{
    //
    public function associate($model,$lookUpModel,$associations){
        foreach($associations as $associate){
            $updates = $model::select($associate['look_up_reference'])
                        ->whereNull($associate['null_field'])
                        ->where($associate['look_up_reference'],$associate['condition_operator'],$associate['condition'])
                        ->groupBy($associate['look_up_reference'])
                        //->toSQL();
                        ->get()->all();
            //dd($updates);
            foreach ($updates as $update) {
                //lookup model
                //dd($update,$update->{$associate['look_up_reference']});
                $key = $lookUpModel::select($associate['look_up_foreign_key'])
                ->where($associate['lookup_field'],$update->{$associate['look_up_reference']})
                ->first();
                if(!is_null($key)){
                    $model::whereNull($associate['null_field'])
                        ->where(
                                $associate['look_up_reference'],
                                $update->{$associate['look_up_reference']}
                                )
                        ->update([
                                  $associate['null_field'] => $key->{$associate['look_up_foreign_key']}
                                                                    ]);
                } else {
                    Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'\'s column '.$associate['null_field'].' with foreign key of '.$update->$$associate['look_up_reference'].' and when looking for a matching value for it on column '.$associate['look_up_foreign_key'].' on the '.$associate['look_up_model'].' model.');
                    //echo date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'\'s column '.$associate['null_field'].' with foreign key of '.$update->{$associate['look_up_reference']}.' and when looking for a matching value for it on column '.$associate['look_up_foreign_key'].' on the model.<hr />';

                }

            }
        }
    }

    public function sync() {

        //////////////////////////////////////////////////
        /////// Building ID updates
        /////

        // Do clean ups:
        // BuildingContactRole::where('state','o')->update(['state'=>'OH']);

        $model = new Building;
        

        $lookUpModel = new \App\Models\Project;
        $associate = array();
        $associate[] = [
            'null_field' => 'project_id',
            'look_up_reference' => 'development_key',
            'lookup_field' => 'project_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }

        $lookUpModel = new \App\Models\BuildingStatus;
        $associate = array();
        $associate[] = [
            'null_field' => 'building_status_id',
            'look_up_reference' => 'building_status_key',
            'lookup_field' => 'building_status_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }


        //////////////////////////////////////////////////
        /////// Audit Auditors ID updates
        /////

        // Do clean ups:
        // BuildingContactRole::where('state','o')->update(['state'=>'OH']);

        $model = new AuditAuditor;
        

        $lookUpModel = new \App\Models\Audit;
        $associate = array();
        $associate[] = [
            'null_field' => 'audit_id',
            'look_up_reference' => 'monitoring_key',
            'lookup_field' => 'monitoring_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }

        $lookUpModel = new \App\Models\User;
        $associate = array();
        $associate[] = [
            'null_field' => 'user_id',
            'look_up_reference' => 'user_key',
            'lookup_field' => 'devco_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }



        //////////////////////////////////////////////////
        /////// Project Program ID updates
        /////

        // Do clean ups:
        // BuildingContactRole::where('state','o')->update(['state'=>'OH']);

        $model = new ProjectProgram;
        

        $lookUpModel = new \App\Models\Project;
        $associate = array();
        $associate[] = [
            'null_field' => 'project_id',
            'look_up_reference' => 'project_key',
            'lookup_field' => 'project_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }

        $lookUpModel = new \App\Models\Program;
        $associate = array();
        $associate[] = [
            'null_field' => 'program_id',
            'look_up_reference' => 'program_key',
            'lookup_field' => 'program_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }

        $lookUpModel = new \App\Models\ProjectProgramStatusType;
        $associate = array();
        $associate[] = [
            'null_field' => 'program_status_type_id',
            'look_up_reference' => 'project_program_status_type_key',
            'lookup_field' => 'project_program_status_type_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }

        $lookUpModel = new \App\Models\FederalSetAside;
        $associate = array();
        $associate[] = [
            'null_field' => 'federal_minimum_set_aside_id',
            'look_up_reference' => 'federal_minimum_set_aside_key',
            'lookup_field' => 'federal_minimum_set_aside_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }

        $lookUpModel = new \App\Models\MultipleBuildingElectionType;
        $associate = array();
        $associate[] = [
            'null_field' => 'multiple_building_election_id',
            'look_up_reference' => 'multiple_building_election_key',
            'lookup_field' => 'multiple_building_election_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }




        //////////////////////////////////////////////////
        /////// Project Financial ID updates
        /////

        // Do clean ups:
        // BuildingContactRole::where('state','o')->update(['state'=>'OH']);

        $model = new ProjectFinancial;
        

        $lookUpModel = new \App\Models\Project;
        $associate = array();
        $associate[] = [
            'null_field' => 'project_id',
            'look_up_reference' => 'project_key',
            'lookup_field' => 'project_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }

        $lookUpModel = new \App\Models\ProjectProgram;
        $associate = array();
        $associate[] = [
            'null_field' => 'project_program_id',
            'look_up_reference' => 'project_program_key',
            'lookup_field' => 'project_program_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }


        $lookUpModel = new \App\Models\FinancialType;
        $associate = array();
        $associate[] = [
            'null_field' => 'financial_type_id',
            'look_up_reference' => 'financial_type_key',
            'lookup_field' => 'financial_type_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }



        //////////////////////////////////////////////////
        /////// Unit Amenity ID updates
        /////

        // Do clean ups:
        // BuildingContactRole::where('state','o')->update(['state'=>'OH']);

        $model = new UnitAmenity;
        

        $lookUpModel = new \App\Models\Unit;
        $associate = array();
        $associate[] = [
            'null_field' => 'unit_id',
            'look_up_reference' => 'unit_key',
            'lookup_field' => 'unit_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }

        $lookUpModel = new \App\Models\Amenity;
        $associate = array();
        $associate[] = [
            'null_field' => 'amenity_id',
            'look_up_reference' => 'amenity_type_key',
            'lookup_field' => 'amenity_type_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }


        //////////////////////////////////////////////////
        /////// Building Amenity ID updates
        /////

        // Do clean ups:
        // BuildingContactRole::where('state','o')->update(['state'=>'OH']);

        $model = new BuildingAmenity;
        

        $lookUpModel = new \App\Models\Building;
        $associate = array();
        $associate[] = [
            'null_field' => 'building_id',
            'look_up_reference' => 'building_key',
            'lookup_field' => 'building_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }

        $lookUpModel = new \App\Models\Amenity;
        $associate = array();
        $associate[] = [
            'null_field' => 'amenity_id',
            'look_up_reference' => 'amenity_type_key',
            'lookup_field' => 'amenity_type_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }



        //////////////////////////////////////////////////
        /////// Project Amenity ID updates
        /////

        // Do clean ups:
        // ProjectContactRole::where('state','o')->update(['state'=>'OH']);

        $model = new ProjectAmenity;
        

        $lookUpModel = new \App\Models\Project;
        $associate = array();
        $associate[] = [
            'null_field' => 'project_id',
            'look_up_reference' => 'project_key',
            'lookup_field' => 'project_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }

        $lookUpModel = new \App\Models\ProjectProgram;
        $associate = array();
        $associate[] = [
            'null_field' => 'project_program_id',
            'look_up_reference' => 'project_program_key',
            'lookup_field' => 'project_program_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }

        $lookUpModel = new \App\Models\Amenity;
        $associate = array();
        $associate[] = [
            'null_field' => 'amenity_id',
            'look_up_reference' => 'amenity_type_key',
            'lookup_field' => 'amenity_type_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }


        //////////////////////////////////////////////////
        /////// Utility Allowance ID updates
        /////

        // Do clean ups:
        // ProjectContactRole::where('state','o')->update(['state'=>'OH']);

        $model = new UtilityAllowance;
        

        $lookUpModel = new \App\Models\UtilityAllowanceType;
        $associate = array();
        $associate[] = [
            'null_field' => 'utility_allowance_type_id',
            'look_up_reference' => 'utility_allowance_type_key',
            'lookup_field' => 'utility_allowance_type_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        




        //////////////////////////////////////////////////
        /////// Household ID updates
        /////

        // Do clean ups:
        // ProjectContactRole::where('state','o')->update(['state'=>'OH']);

        $model = new Household;
        

        $lookUpModel = new \App\Models\SpecialNeed;
        $associate = array();
        $associate[] = [
            'null_field' => 'special_needs_id',
            'look_up_reference' => 'special_needs_key',
            'lookup_field' => 'special_needs_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\HouseholdSize;
        $associate = array();
        $associate[] = [
            'null_field' => 'household_size_move_in_id',
            'look_up_reference' => 'household_size_move_in_key',
            'lookup_field' => 'household_size_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '10000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        


        $lookUpModel = new \App\Models\HouseholdSize;
        $associate = array();
        $associate[] = [
            'null_field' => 'household_size_id',
            'look_up_reference' => 'household_size_key',
            'lookup_field' => 'household_size_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '100000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\Unit;
        $associate = array();
        $associate[] = [
            'null_field' => 'unit_id',
            'look_up_reference' => 'unit_key',
            'lookup_field' => 'unit_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        
        $lookUpModel = new \App\Models\Project;
        $associate = array();
        $associate[] = [
            'null_field' => 'project_id',
            'look_up_reference' => 'development_key',
            'lookup_field' => 'project_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        

        

        





        //////////////////////////////////////////////////
        /////// Household Events ID updates
        /////

        // Do clean ups:
        // ProjectContactRole::where('state','o')->update(['state'=>'OH']);
        
        $model = new HouseholdEvent;
        $lookUpModel = new \App\Models\Unit;
        $associate = array();
        $associate[] = [
            'null_field' => 'unit_id',
            'look_up_reference' => 'unit_key',
            'lookup_field' => 'unit_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\OwnerCertificationYear;
        $associate = array();
        $associate[] = [
            'null_field' => 'owner_certification_year_id',
            'look_up_reference' => 'owner_certification_year_key',
            'lookup_field' => 'owner_certification_year_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\Project;
        $associate = array();
        $associate[] = [
            'null_field' => 'project_id',
            'look_up_reference' => 'project_key',
            'lookup_field' => 'project_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\Household;
        $associate = array();
        $associate[] = [
            'null_field' => 'house_hold_id',
            'look_up_reference' => 'house_hold_key',
            'lookup_field' => 'household_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        


        $lookUpModel = new \App\Models\EventType;
        $associate = array();
        $associate[] = [
            'null_field' => 'event_type_id',
            'look_up_reference' => 'event_type_key',
            'lookup_field' => 'event_type_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        


        $lookUpModel = new \App\Models\UnitStatus;
        $associate = array();
        $associate[] = [
            'null_field' => 'unit_status_id',
            'look_up_reference' => 'unit_status_key',
            'lookup_field' => 'unit_status_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\UtilityAllowance;
        $associate = array();
        $associate[] = [
            'null_field' => 'utility_allowance_id',
            'look_up_reference' => 'utility_allowance_key',
            'lookup_field' => 'utility_allowance_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\RentalAssistanceType;
        $associate = array();
        $associate[] = [
            'null_field' => 'rental_assistance_type_id',
            'look_up_reference' => 'rental_assistance_type_key',
            'lookup_field' => 'rental_assistance_type_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\RentalAssistanceSource;
        $associate = array();
        $associate[] = [
            'null_field' => 'rental_assistance_source_id',
            'look_up_reference' => 'rental_assistance_source_key',
            'lookup_field' => 'rental_assistance_source_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\UnitIdenty;
        $associate = array();
        $associate[] = [
            'null_field' => 'unit_identity_id',
            'look_up_reference' => 'unit_identity_key',
            'lookup_field' => 'unit_identity_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        


        //////////////////////////////////////////////////
        /////// Unit ID updates
        /////

        // Do clean ups:
        // ProjectContactRole::where('state','o')->update(['state'=>'OH']);
        
        $model = new Unit;
        $lookUpModel = new \App\Models\Building;
        $associate = array();
        $associate[] = [
            'null_field' => 'building_id',
            'look_up_reference' => 'building_key',
            'lookup_field' => 'building_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\UnitBedroom;
        $associate = array();
        $associate[] = [
            'null_field' => 'unit_bedroom_id',
            'look_up_reference' => 'unit_bedroom_key',
            'lookup_field' => 'unit_bedroom_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\UnitStatus;
        $associate = array();
        $associate[] = [
            'null_field' => 'unit_status_id',
            'look_up_reference' => 'unit_status_key',
            'lookup_field' => 'unit_status_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\Percentage;
        $associate = array();
        $associate[] = [
            'null_field' => 'ami_percentage_id',
            'look_up_reference' => 'ami_percentage_key',
            'lookup_field' => 'percentage_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\UnitIdentity;
        $associate = array();
        $associate[] = [
            'null_field' => 'unit_identity_id',
            'look_up_reference' => 'unit_identity_key',
            'lookup_field' => 'unit_identity_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        //////////////////////////////////////////////////
        /////// Project ID updates
        /////

        // Do clean ups:
        // ProjectContactRole::where('state','o')->update(['state'=>'OH']);
        
        $model = new Project;
        $lookUpModel = new \App\Models\Address;
        $associate = array();
        $associate[] = [
            'null_field' => 'physical_address_id',
            'look_up_reference' => 'physical_address_key',
            'lookup_field' => 'address_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\PhoneNumber;
        $associate = array();
        $associate[] = [
            'null_field' => 'default_phone_number_id',
            'look_up_reference' => 'default_phone_number_key',
            'lookup_field' => 'phone_number_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\PhoneNumber;
        $associate = array();
        $associate[] = [
            'null_field' => 'default_fax_number_id',
            'look_up_reference' => 'default_fax_number_key',
            'lookup_field' => 'phone_number_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        


        //////////////////////////////////////////////////
        /////// Organization ID updates
        /////

        // Do clean ups:
        // ProjectContactRole::where('state','o')->update(['state'=>'OH']);
        
        $model = new Organization;
        $lookUpModel = new \App\Models\Address;
        $associate = array();
        $associate[] = [
            'null_field' => 'default_address_id',
            'look_up_reference' => 'default_address_key',
            'lookup_field' => 'address_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\PhoneNumber;
        $associate = array();
        $associate[] = [
            'null_field' => 'default_phone_number_id',
            'look_up_reference' => 'default_phone_number_key',
            'lookup_field' => 'phone_number_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\PhoneNumber;
        $associate = array();
        $associate[] = [
            'null_field' => 'default_fax_number_id',
            'look_up_reference' => 'default_fax_number_key',
            'lookup_field' => 'phone_number_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\People;
        $associate = array();
        $associate[] = [
            'null_field' => 'default_contact_person_id',
            'look_up_reference' => 'default_contact_person_key',
            'lookup_field' => 'person_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\Organization;
        $associate = array();
        $associate[] = [
            'null_field' => 'parent_organization_id',
            'look_up_reference' => 'parent_organization_key',
            'lookup_field' => 'organization_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        




        //////////////////////////////////////////////////
        /////// Project Contact Roles ID updates
        /////

        // Do clean ups:
        // ProjectContactRole::where('state','o')->update(['state'=>'OH']);
        
        $model = new ProjectContactRole;
        $lookUpModel = new \App\Models\Organization;
        $associate = array();
        $associate[] = [
            'null_field' => 'organization_id',
            'look_up_reference' => 'organization_key',
            'lookup_field' => 'organization_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\ProjectProgram;
        $associate = array();
        $associate[] = [
            //columns in this table
            'null_field' => 'project_program_id',
            'look_up_reference' => 'project_program_key',
            //columns in the foreign table
            'lookup_field' => 'project_program_key',
            'look_up_foreign_key' => 'id',
            //condition against the lookup field - if one is needed.
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\Project;
        $associate = array();
        $associate[] = [
            //columns in this table
            'null_field' => 'project_id',
            'look_up_reference' => 'project_key',
            //columns in the foreign table
            'lookup_field' => 'project_key',
            'look_up_foreign_key' => 'id',
            //condition against the lookup field - if one is needed.
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\People;
        $associate = array();
        $associate[] = [
            //columns in this table
            'null_field' => 'person_id',
            'look_up_reference' => 'person_key',
            //columns in the foreign table
            'lookup_field' => 'person_key',
            'look_up_foreign_key' => 'id',
            //condition against the lookup field - if one is needed.
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\ProjectRole;
        $associate = array();
        $associate[] = [
            //columns in this table
            'null_field' => 'project_role_id',
            'look_up_reference' => 'project_role_key',
            //columns in the foreign table
            'lookup_field' => 'project_role_key',
            'look_up_foreign_key' => 'id',
            //condition against the lookup field - if one is needed.
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        


        //////////////////////////////////////////////////
        /////// Project Activities ID updates
        /////

        // Do clean ups:
        // ProjectActivity::where('state','o')->update(['state'=>'OH']);
        
        $model = new ProjectActivity;
        $lookUpModel = new \App\Models\Project;
        $associate = array();
        $associate[] = [
            'null_field' => 'project_id',
            'look_up_reference' => 'project_key',
            'lookup_field' => 'project_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\ProjectProgram;
        $associate = array();
        $associate[] = [
            //columns in this table
            'null_field' => 'project_program_id',
            'look_up_reference' => 'project_program_key',
            //columns in the foreign table
            'lookup_field' => 'project_program_key',
            'look_up_foreign_key' => 'id',
            //condition against the lookup field - if one is needed.
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        $lookUpModel = new \App\Models\ProjectActivityType;
        $associate = array();
        $associate[] = [
            'null_field' => 'project_activity_type_id',
            'look_up_reference' => 'project_activity_type_key',
            'lookup_field' => 'project_activity_type_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        

        //////////////////////////////////////////////////
        /////// Address ID update
        /////

        // do clean ups:
        Address::where('state','o')->update(['state'=>'OH']);
        Address::where('state',' O')->update(['state'=>'OH']);
        Address::where('city','Cincinnati')->where('state','')->update(['state'=>'OH']);
        Address::where('city','Youngstown')->where('state','')->update(['state'=>'OH']);
        Address::where('city','Cleveland')->where('state','')->update(['state'=>'OH']);
        Address::where('city','Columbus')->where('state','')->update(['state'=>'OH']);
        Address::where('city','Elyria')->where('state','')->update(['state'=>'OH']);
        Address::where('city','Akron')->where('state','')->update(['state'=>'OH']);
        Address::where('city','Philadelphia')->where('state','')->update(['state'=>'PA']);
        $model = new Address;
        $lookUpModel = new \App\Models\State;
        $associate = array();
        $associate[] = [
            'null_field' => 'state_id',
            'look_up_reference' => 'state',
            'lookup_field' => 'state_acronym',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => '1000000000000000000000'
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
        
    }
}
