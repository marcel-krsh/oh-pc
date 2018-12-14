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

use App\Jobs\SyncAddress;
use App\Jobs\SyncPeople;
use App\Jobs\SyncMonitoringStatusType;
use App\Jobs\SyncProjectActivity;
use App\Jobs\SyncProjectActivityType;
use App\Jobs\SyncProjectRole;
use App\Jobs\SyncProjectContactRole;
use App\Jobs\SyncOrganization;
use App\Jobs\SyncProject;
use App\Jobs\SyncAmenityType;
use App\Jobs\SyncProgram;
use App\Jobs\SyncProjectProgramStatusType;
use App\Jobs\SyncFinancialType;
use App\Jobs\SyncProgramDateType;
use App\Jobs\SyncMultipleBuildingType;
use App\Jobs\SyncPercentage;
use App\Jobs\SyncFederalMinimumSetAside;
use App\Jobs\SyncUnitStatus;
use App\Jobs\SyncUnit;
use App\Jobs\SyncUnitBedroom;
use App\Jobs\SyncHouseholdEvent;
use App\Jobs\SyncOwnerCertificationYear;
use App\Jobs\SyncHousehold;
use App\Jobs\SyncEventType;
use App\Jobs\SyncRentalAssistanceSource;
use App\Jobs\SyncRentalAssistanceType;
use App\Jobs\SyncUtilityAllowance;
use App\Jobs\SyncMonitoring;
use App\Jobs\SyncProjectAmenity;
use App\Jobs\SyncProjectFinancial;
use App\Jobs\SyncProjectProgram;
use App\Jobs\SyncUtilityAllowanceType;
use App\Jobs\SyncSpecialNeed;
use App\Jobs\SyncMonitoringMonitor;
use App\Jobs\SyncBuilding;
use App\Jobs\SyncPhoneNumber;
use App\Jobs\SyncUser;
use App\Jobs\SyncComplianceContact;
use App\Jobs\SyncPhoneNumberType;
use App\Jobs\SyncEmailAddressType;
use App\Jobs\SyncEmailAddress;
use App\Jobs\SyncBuildingAmenity;
use App\Jobs\SyncUnitAmenitie;
use App\Jobs\SyncHouseHoldSize;
use App\Jobs\SyncProjectDate;
use App\Jobs\SyncUnitIdentity;




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
