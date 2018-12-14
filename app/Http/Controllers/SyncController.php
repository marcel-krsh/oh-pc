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

use App\Models\Address;
use App\Models\People;
use App\Models\MonitoringStatusType;
use App\Models\ProjectActivity;
use App\Models\ProjectActivityType;
use App\Models\ProjectRole;
use App\Models\ProjectContactRole;
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
                        ->where($associate['null_field'],$associate['condition_operator'],$associate['condition'])
                        ->groupBy($associate['look_up_reference'])
                        ->get()->all();
            foreach ($updates as $update) {
                //lookup model
                dd($update);
                $key = $lookUpModel::select($associate['look_up_foreign_key'])
                ->where($associate['lookup_field'],$update->$associate['look_up_reference'])
                ->first();
                if(!is_null($key)){
                    $model::whereNull($associate['null_field'])->where($update->$associate['look_up_reference'],$update->$associate['look_up_reference'])->update([$associate['null_field'] => $key->$associate['look_up_foreign_key']
                    ]);
                } else {
                    //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'\'s column '.$associate['null_field'].' with foreign key of '.$update->$$associate['look_up_reference'].' and when looking for a matching value for it on column '.$associate['look_up_foreign_key'].' on the '.$associate['look_up_model'].' model.');
                    echo date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'\'s column '.$associate['null_field'].' with foreign key of '.$update->$associate['look_up_reference'].' and when looking for a matching value for it on column '.$associate['look_up_foreign_key'].' on the '.$associate['look_up_model'].' model.<hr />';

                }

            }
        }
    }

    public function sync() {

        //////////////////////////////////////////////////
        /////// Address ID update
        /////
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
