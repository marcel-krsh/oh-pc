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
use App\Models\Organization; 
use App\Models\Project;
use App\Models\AmenityType;
use App\Models\Program;
use App\Models\ProjectProgramStatusType;
use App\Models\FinancialType;
use App\Models\ProgramDateType;
use App\Models\MultipleBuildingType;
use App\Models\Percentage;
use App\Models\FederalMinimumSetAside;
use App\Models\UnitStatus;
use App\Models\Unit;
use App\Models\UnitBedroom;
use App\Models\HouseholdEvent;
use App\Models\OwnerCertificationYear;
use App\Models\Household;
use App\Models\EventType;
use App\Models\RentalAssistanceSource;
use App\Models\RentalAssistanceType;
use App\Models\UtilityAllowance;
use App\Models\Monitoring;
use App\Models\ProjectAmenity;
use App\Models\ProjectFinancial;
use App\Models\ProjectProgram;
use App\Models\UtilityAllowanceType;
use App\Models\SpecialNeed;
use App\Models\MonitoringMonitor;
use App\Models\Building;
use App\Models\PhoneNumber;
use App\Models\User;
use App\Models\ComplianceContact;
use App\Models\PhoneNumberType;
use App\Models\EmailAddressType;
use App\Models\EmailAddress;
use App\Models\BuildingAmenity;
use App\Models\UnitAmenitie;
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
                    //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'\'s column '.$associate['null_field'].' with foreign key of '.$update->$$associate['look_up_reference'].' and when looking for a matching value for it on column '.$associate['look_up_foreign_key'].' on the '.$associate['look_up_model'].' model.');
                    echo date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'\'s column '.$associate['null_field'].' with foreign key of '.$update->{$associate['look_up_reference']}.' and when looking for a matching value for it on column '.$associate['look_up_foreign_key'].' on the model.<hr />';

                }

            }
        }
    }

    public function sync() {

        
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
            'condition' => ' '
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
            'condition' => ' '
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
            'lookup_field' => 'fax_number_key',
            'look_up_foreign_key' => 'id',
            'condition_operator' => '!=',
            'condition' => ' '
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
            'condition' => ' '
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
            'condition' => ' '
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
            'condition' => ' '
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
            'condition' => ' '
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
            'condition' => ' '
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
            'condition' => ' '
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
            'condition' => ' '
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
            'condition' => ' '
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
            'condition' => ' '
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
            'condition' => ' '
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
            'condition' => ' '
        ];
        try{
            $this->associate($model,$lookUpModel,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
    }
}
