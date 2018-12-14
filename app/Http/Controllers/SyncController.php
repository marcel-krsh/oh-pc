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
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Log;

use App\Jobs\Address;
use App\Jobs\People;
use App\Jobs\MonitoringStatusType;
use App\Jobs\ProjectActivity;
use App\Jobs\ProjectActivityType;
use App\Jobs\ProjectRole;
use App\Jobs\ProjectContactRole;
use App\Jobs\Organization;
use App\Jobs\Project;
use App\Jobs\AmenityType;
use App\Jobs\Program;
use App\Jobs\ProjectProgramStatusType;
use App\Jobs\FinancialType;
use App\Jobs\ProgramDateType;
use App\Jobs\MultipleBuildingType;
use App\Jobs\Percentage;
use App\Jobs\FederalMinimumSetAside;
use App\Jobs\UnitStatus;
use App\Jobs\Unit;
use App\Jobs\UnitBedroom;
use App\Jobs\HouseholdEvent;
use App\Jobs\OwnerCertificationYear;
use App\Jobs\Household;
use App\Jobs\EventType;
use App\Jobs\RentalAssistanceSource;
use App\Jobs\RentalAssistanceType;
use App\Jobs\UtilityAllowance;
use App\Jobs\Monitoring;
use App\Jobs\ProjectAmenity;
use App\Jobs\ProjectFinancial;
use App\Jobs\ProjectProgram;
use App\Jobs\UtilityAllowanceType;
use App\Jobs\SpecialNeed;
use App\Jobs\MonitoringMonitor;
use App\Jobs\Building;
use App\Jobs\PhoneNumber;
use App\Jobs\User;
use App\Jobs\ComplianceContact;
use App\Jobs\PhoneNumberType;
use App\Jobs\EmailAddressType;
use App\Jobs\EmailAddress;
use App\Jobs\BuildingAmenity;
use App\Jobs\UnitAmenitie;
use App\Jobs\HouseHoldSize;
use App\Jobs\ProjectDate;
use App\Jobs\UnitIdentity;




class SyncController extends Controller
{
    //
    public function associate($model,$associations){
        foreach($associations as $associate){
            $updates = $model::whereNull($associate['null_field'])->groupBy($associate['look_up_model'])->get()->all();
            foreach ($updates as $update) {
                //lookup model
                $key = $$associate['look_up_model']::select($associate['look_up_foreign_key'])->where($associate['lookup_field'],$update->$$associate['look_up_reference'])->first();
                if(!is_null($key)){
                    $model::whereNull($associate['null_field'])->where($update->$$associate['look_up_reference'],$update->$$associate['look_up_reference'])->update([$associate['null_field'] => $key->$$associate['look_up_foreign_key']
                    ]);
                } else {
                    //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'\'s column '.$associate['null_field'].' with foreign key of '.$update->$$associate['look_up_reference'].' and when looking for a matching value for it on column '.$associate['look_up_foreign_key'].' on the '.$associate['look_up_model'].' model.');
                    echo date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'\'s column '.$associate['null_field'].' with foreign key of '.$update->$$associate['look_up_reference'].' and when looking for a matching value for it on column '.$associate['look_up_foreign_key'].' on the '.$associate['look_up_model'].' model.<hr />';

                }

            }
        }
    }

    public function sync() {

        //////////////////////////////////////////////////
        /////// Address ID update
        /////
        $model = 'Address';
        $associate = array();
        $associate[] = [
            'null_field' => 'state_id',
            'look_up_model' => 'State',
            'look_up_reference' => 'state',
            'lookup_field' => 'state_acronym',
            'look_up_foreign_key' => 'id'
        ];
        try{
            $this->associate($model,$associate);
        } catch(Exception $e){
            //Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
            echo '<strong>'.date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model.'</strong><hr>';
        }
    }
}
