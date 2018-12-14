<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\AuthService;
use App\Services\DevcoService;
use App\Models\AuthTracker;
use App\Models\SystemSetting;
use App\Models\User;
use DB;
use DateTime;
use Illuminate\Support\Facades\Hash;


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

class SyncIdssJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
    public $tries = 5;
    /**
     * Execute the job.
     *
     * @return void
     */
    public function associate($model,$associate){
        $updates = $$model::whereNull('state_id')->groupBy('state')->get()->all();
        foreach ($updates as $update) {
            //look up id value

        }


        $update = $$model::where()
    }
    public function handle()
    {
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
            associate($model,$associate);
        } catch(Exception $e){
            Log::info(date('m/d/Y H:i:s ::',time()).'Failed associating keys for '.$model);
        }

        

        
    }
}
