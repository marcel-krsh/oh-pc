<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Auth;
use Gate;
use File;
use Storage;
use App\SfParcel;
use App\Programs;
use Illuminate\Http\Request;
use App\User;
use DB;
use App\Parcel;

/**
 * ImportSF Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class ImportSFCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:sf';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports ';

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
        $bar = $this->output->createProgressBar(18);
        $bar->clear();
        $ddNote = "Trying to see null values.";
        
        $nullTargetAreas = sf_parcels::select('PropertyIDCounty', 'id')->where('PropertyIDTargetArea', null)->get()->all();

        $bar->advance();
        $this->line(PHP_EOL.'Fixing '.count($nullTargetAreas).' Null Target Areas');
        $targetAreaBar = $this->output->createProgressBar(count($nullTargetAreas));
        
        foreach ($nullTargetAreas as $data) {
            $targetAreaBar->advance();
            DB::table('sf_parcels')->where('id', $data->id)->update(['PropertyIDTargetArea'=>$data->PropertyIDCounty." NA"]);
        }
        $targetAreaBar->finish();
        unset($nullTargetAreas);

        $ddNote = "Trying to see null values.";
        $nullSalePrices = sf_parcels::select('id')->where('PropertyIDSalesPrice', null)->get()->all();
       
        $salePriceBar = $this->output->createProgressBar(count($nullSalePrices));

        $bar->advance();
        $this->line(PHP_EOL.'Fixing '.count($nullSalePrices).' Null Property Sale Prices.');
        foreach ($nullSalePrices as $data) {
            $salePriceBar->advance();

            DB::table('sf_parcels')->where('id', $data->id)->update(['PropertyIDSalesPrice'=>0]);
        }
        $salePriceBar->finish();
        
        unset($nullSalePrices);



        //ini_set('max_execution_time', 600);


        ///////////////////////////////////////////////////////////////////////////////
        ////////////////// SET I TO ZERO FOR PROCESSING.
        ////////////

        

        $i = 0;
        if (!isset($timesRun)) {
            $timesRun = 0;
        }
        $runBefore = DB::table('invoice_items')
                    ->where('ref_id', '>', 0)->count();
                    
        if ($timesRun == 0 && $runBefore == 0) {
            //$error = "Times run = ".$timesRun." and runBefore = ".$runBefore;
            //dd($error);

            $timesRun = 1;

            

                    
            ///////////////////////////////////////////////////////////////////////////////
            ////////////////// ADD DISPOSITION_DUE TO THE DISPOSITION TABLE
            ////////////


            if (Schema::hasColumn('sf_dispositions', 'disposition_due')) {
                // no need to add - it is there!
            } else {
                Schema::table('sf_dispositions', function (Blueprint $table) {
                    $table->string('disposition_due')->nullable();
                });
            }


            ////////////////////////////////////////////////////////////////////////////////
            ///////////////// ADD IN sf_program_name COLUMN

            if (Schema::hasColumn('programs', 'sf_program_name')) {
                // no need to add - it is there!
            } else {
                Schema::table('programs', function (Blueprint $table) {
                    $table->string('sf_program_name')->nullable();
                });
            }



            ///////////////////////////////////////////////////////////////////////////////
            ////////////////// CREATE DUE DATES FOR DISPOSITIONS IN SF DATA
            ////////////



            $sfDispositions = DB::table('sf_dispositions')->where('disposition_due', null)->get()->all();

                       



                    
            $bar->advance();
            $this->line(PHP_EOL.'Update '.count($sfDispositions).' Disposition NULL Due Dates.');
            $dispositionDueBar = $this->output->createProgressBar(count($sfDispositions));
            foreach ($sfDispositions as $disposition) {
                $dispositionDueBar->advance();
                if ($disposition->disposition_due == null) {
                    if (strtotime($disposition->CreatedDate) > strtotime($disposition->ReleaseDate) && strtotime($disposition->ReleaseDate) > 0) {
                        $dispostionDate = $disposition->ReleaseDate;
                    } else {
                        $dispostionDate = $disposition->CreatedDate;
                    }
                    $dispositionDate = nextQuarter($dispostionDate);

                    ///////////////////////////////////////////////////////////////////////////////
                    ////////////////// UPDATE THER RECORD WITH THE DUE DATE
                    ////////////

                    DB::table('sf_dispositions')->where('id', '=', $disposition->id)->update(['disposition_due'=>$dispositionDate]);
                }
            }
            $dispositionDueBar->finish();
                    
            $fixParcelID = DB::table('sf_site_visits')->get()->all();

            $this->line(PHP_EOL.'Fixing the Property ID on site visits');
            $siteVisitFixBar = $this->output->createProgressBar(count($fixParcelID));
            foreach ($fixParcelID as $data) {
                DB::table('sf_site_visits')->where('id', $data->id)->update(['PropertyID'=>substr($data->PropertyID, 0, 15)]);
                $siteVisitFixBar->advance();
            }
            $siteVisitFixBar->finish();

            ///////////////////////////////////////////////////////////////////////////////
            ////////////////// CLEAR MEMORY OF DISPOSTIONS
            ////////////
                    

            $sfDispositions = '';
                    

            ///////////////////////////////////////////////////////////////////////////////
            ////////////////// SEE IF WE NEED TO IMPORT PARCELS
            ////////////

            $totalSfParcels = DB::table('sf_parcels')->select('PropertyIDRecordID')->get()->all();
            $totalSfParcels = count($totalSfParcels);
            $totalAllitaParcels = DB::table('parcels')->count();

                    

            ///////////////////////////////////////////////////////////////////////////////
            ////////////////// GET PARCELS
            ////////////

            if ($totalSfParcels > $totalAllitaParcels) {
                $this->line(PHP_EOL.'Processing '.$totalSfParcels.' Parcels From Salesforce');
                $sfParcels = DB::select(DB::raw("
                                SELECT DISTINCT
                                    sf_reimbursements.PropertyIDRecordID ,
                                    sf_reimbursements.PropertyIDParcelID ,
                                    sf_reimbursements.PropertyIDPropertyName ,
                                    sf_reimbursements.ProgramProgramName ,
                                    sf_reimbursements.BatchNumber ,
                                    sf_reimbursements.ReimbursementID ,
                                    sf_reimbursements.ReimbursementCreatedDate ,
                                    sf_reimbursements.DatePaid ,
                                    sf_reimbursements.ReimbursementReimbursementName ,
                                    sf_reimbursements.GreeningAdvanceDocumented ,
                                    sf_reimbursements.GreeningAdvanceOption ,
                                    sf_reimbursements.PreDemoApproved ,
                                    sf_reimbursements.GreeningCost ,
                                    sf_reimbursements.GreeningRequested ,
                                    sf_reimbursements.GreeningApproved ,
                                    sf_reimbursements.GreeningPaid ,
                                    sf_reimbursements.PreDemoCost ,
                                    sf_reimbursements.PreDemoRequested ,
                                    sf_reimbursements.PreDemoPaid ,
                                    sf_reimbursements.MaintenanceCost ,
                                    sf_reimbursements.MaintenanceRequested ,
                                    sf_reimbursements.MaintenanceApproved ,
                                    sf_reimbursements.MaintenancePaid ,
                                    sf_reimbursements.DemolitionCost ,
                                    sf_reimbursements.DemolitionRequested ,
                                    sf_reimbursements.DemolitionApproved ,
                                    sf_reimbursements.DemolitionPaid ,
                                    sf_reimbursements.AdministrationCost ,
                                    sf_reimbursements.AdministrationRequested ,
                                    sf_reimbursements.AdministrationApproved ,
                                    sf_reimbursements.AdministrationPaid ,
                                    sf_reimbursements.AcquisitionCost ,
                                    sf_reimbursements.AcquisitionRequested ,
                                    sf_reimbursements.AcquisitionApproved ,
                                    sf_reimbursements.NIPLoanPayoffCost ,
                                    sf_reimbursements.NIPLoanPayoffRequested ,
                                    sf_reimbursements.AcquisitionPaid ,
                                    sf_reimbursements.NIPLoanPayoffApproved ,
                                    sf_reimbursements.NIPLoanPayoffPaid ,
                                    sf_reimbursements.TotalCost ,
                                    sf_reimbursements.TotalRequested ,
                                    sf_reimbursements.TotalApproved ,
                                    sf_reimbursements.TotalPaid ,
                                    sf_reimbursements.ProcessDate ,
                                    sf_reimbursements.Retainage ,
                                    sf_reimbursements.RetainagePaid ,
                                    sf_reimbursements.ReturnedFundsExplanation ,
                                    sf_reimbursements.ProgramIncome ,
                                    sf_reimbursements.NetProceeds ,
                                    sf_reimbursements.RecapturedOwed ,
                                    sf_reimbursements.RecapturePaid ,
                                    sf_parcels.PropertyIDRecordID AS PropertyIDRecordID_0 ,
                                    sf_parcels.PropertyIDCreatedDate ,
                                    sf_parcels.PropertyIDPropertyStatus ,
                                    sf_parcels.PropertyIDStatusExplanation ,
                                    sf_parcels.ProgramID ,
                                    sf_parcels.ProgramProgramName AS ProgramProgramName_0 ,
                                    sf_parcels.PropertyIDTargetArea ,
                                    sf_parcels.PropertyIDSalesPrice ,
                                    sf_parcels.PropertyIDPropertyDocuments ,
                                    sf_parcels.PropertyIDHowAcquired ,
                                    sf_parcels.PropertyIDHowAcquiredExplanation ,
                                    sf_parcels.PropertyIDLatLonLatitude ,
                                    sf_parcels.PropertyIDLatLonLongitude ,
                                    sf_parcels.PropertyIDLocationMap ,
                                    sf_parcels.PropertyIDConfidenceCode ,
                                    sf_parcels.PropertyIDOHHouseDistrict ,
                                    sf_parcels.PropertyIDOHSenateDistrict ,
                                    sf_parcels.PropertyIDUSHouseDistrict ,
                                    sf_parcels.PropertyIDGeocodeUpdatedDate ,
                                    sf_parcels.PropertyIDStreetAddress ,
                                    sf_parcels.PropertyIDCity ,
                                    sf_parcels.PropertyIDState ,
                                    sf_parcels.PropertyIDZip ,
                                    sf_parcels.PropertyIDCounty ,
                                    sf_parcels.PropertyIDUglyHouse ,
                                    sf_parcels.PropertyIDPrettyLot ,
                                    sf_parcels.PropertyIDHistoricWaiverApproved ,
                                    sf_parcels.PropertyIDHistoricSignificanceDistrict ,
                                    sf_parcels.PropertyIDDispositionType ,
                                    sf_parcels.PropertyIDWithdrawnDate ,
                                    sf_parcels.PropertyIDDisposition
                                FROM
                                    sf_parcels
                                JOIN sf_reimbursements ON sf_parcels.PropertyIDRecordID = sf_reimbursements.PropertyIDRecordID
                                "));


                $sfParcelsCount = count($sfParcels);
                //$error = "Sf Parcel Count is ".$totalSfParcels.". And the joined query has ".$sfParcelsCount;
                //dd($error,$sfParcels);

                ///////////////////////////////////////////////////////////////////////////////
                ////////////////// PROCESS PARCELS
                ////////////

                $parcelBar = $this->output->createProgressBar($sfParcelsCount);
                foreach ($sfParcels as $sfParcel) {
                    $parcelBar->advance();


                    ///////////////////////////////////////////////////////////////////////////////
                    ////////////////// GET PROGRAM ID BASED ON PROGRAM NAME
                    ////////////

                        
                    $programId = DB::table('programs')
                                ->select('id', 'entity_id', 'county_id')
                                ->where('program_name', 'like', '%'.$sfParcel->ProgramProgramName.'%')->first();

                    if (!isset($programId->id)) {
                        $error = "Was not able to find matching program name for sf parcel ".$sfParcel->PropertyIDRecordID." with Program Name ".$sfParcel->ProgramProgramName;
                        dd($error);
                    }
                    

                    




                    ///////////////////////////////////////////////////////////////////////////////
                    ////////////////// RUN QUERY TO SEE IF PARCEL IS ALREADY IN THE TABLE
                    ////////////

                        
                    $shouldWeAddRecords = DB::table('parcels')->select('id')
                                ->where('parcel_id', '=', $sfParcel->PropertyIDPropertyName)
                                ->where('program_id', '=', $programId->id)->get()->all();

                        
                    if (count($shouldWeAddRecords)>0) {
                        $this->line(PHP_EOL."Skipping Record ".$sfParcel->PropertyIDPropertyName." with program ".$sfParcel->ProgramProgramName." because it is a duplicate and has one already inserted.");
                    //dd($shouldWeAddRecords);
                    } else {
                                ///////////////////////////////////////////////////////////////////////////////
                        ////////////////// PARCEL IS NOT IN THE TABLE - CREATE INSERTION.
                        //////////// FIRST DETERMINE STATUS ID

                            
                        $statusId = DB::table('property_status_options')
                                    ->select('id')
                                    ->where('option_name', 'LIKE', '%'.$sfParcel->PropertyIDPropertyStatus.'%')
                                    ->first();


                        //dd($statusId,$sfParcel->PropertyIDPropertyStatus,$sfParcel);

                        ///////////////////////////////////////////////////////////////////////////////
                        ////////////////// MAP STATUS BASED ON INFO ON THE PARCEL
                        ////////////

                        
                         
                        // Whatever status was by sales force
                        $statusId = $statusId->id;
                        if ($statusId == 1) {
                            //Pending;
                            $hfaStatusId = 22;
                            $lbStatusId = 8;
                        } elseif ($statusId == 2) {
                            //Approved
                            $hfaStatusId = 27;
                            $lbStatusId = 13;
                        } elseif ($statusId == 3) {
                            //Withdrawn
                            $hfaStatusId = 37;
                            $lbStatusId = 3;
                        } elseif ($statusId == 4) {
                            //Declined
                            $hfaStatusId = 34;
                            $lbStatusId = 4;
                        }
                            
                            
                           



                        ///////////////////////////////////////////////////////////////////////////////
                        ////////////////// DETERMINE PARCEL'S TARGET AREA
                        ////////////

                         
                        if (!is_null($sfParcel->PropertyIDTargetArea)) {
                            $targetAreaId = DB::table('target_areas')
                                        ->select('id')
                                        ->where('target_area_name', '=', $sfParcel->PropertyIDTargetArea)
                                        ->first();
                                



                            ///////////////////////////////////////////////////////////////////////////////
                            ////////////////// IF TARGET AREA DOES NOT EXIST
                            //////////// ADD IT TO THE DATABASE

                        
                            if (count($targetAreaId) < 1) {
                                // Insert target id.
                                $targetAreaId = DB::table('target_areas')
                                                        ->insertGetId([
                                                            'county_id'=>$programId->county_id ,
                                                            'target_area_name'=>$sfParcel->PropertyIDTargetArea ,
                                                            'active'=>1
                                                        ]);
                            } else {
                                $targetAreaId = $targetAreaId->id;
                            }
                        } else {
                            $targetAreaId = null;
                        }




                        ///////////////////////////////////////////////////////////////////////////////
                        ////////////////// DETERMINE THE "HOW ACQUIRED" OPTION
                        ////////////

                        
                        if (!is_null($sfParcel->PropertyIDHowAcquired)) {
                            $howAcquiredId = DB::table('how_acquired_options')
                                        ->select('id')
                                        ->where('how_acquired_option_name', '=', $sfParcel->PropertyIDHowAcquired)
                                        ->first();
                            if (is_null($howAcquiredId)) {
                                // the option was not in our DB! Add that bad boy.
                                $howAcquiredId = DB::table('how_acquired_options')->insertGetId(['how_acquired_option_name'=>$sfParcel->PropertyIDHowAcquired]);
                            } else {
                                // it is - let's just get the id
                                $howAcquiredId = $howAcquiredId->id;
                            }
                        } else {
                            $howAcquiredId = 10;
                        }

                        ///////////////////////////////////////////////////////////////////////////////
                        ////////////////// PUT IN HOW ACQUIRED SUPPORTING NOTE
                        ////////////



                        if (is_null($sfParcel->PropertyIDHowAcquiredExplanation) || $sfParcel->PropertyIDHowAcquiredExplanation == '') {
                            $sfParcel->PropertyIDHowAcquiredExplanation = 'NA';
                        }






                        ///////////////////////////////////////////////////////////////////////////////
                        ////////////////// GET MAP INFORMATION
                        //////////// CHECK THE SF DATA TO MAKE SURE <A HREF=" AND " TARGET="_BLANK"> IS REMOVED FROM THE DATA



                        if (!is_null($sfParcel->PropertyIDLocationMap)) {
                            $mapLink = $sfParcel->PropertyIDLocationMap;
                        } else {
                            $mapLink = "NA";
                        }
             





                        ///////////////////////////////////////////////////////////////////////////////
                        ////////////////// SET THE BATCH NUMBER FOR THE FILE
                        //////////// IF IT HAS NO BATCH NUMBER - USE THE PROCESS DATE



                        if ($sfParcel->BatchNumber < 1) {
                            $sf_batch_id = strtotime($sfParcel->ProcessDate);
                        } else {
                            $sf_batch_id = $sfParcel->BatchNumber;
                        }


                        ////////////////////////////////////////////////////////////////////////////////
                        ////////////////   CHECK THAT RETAINAGE PAID IS NOT NULL
                        ///////


                        if (is_null($sfParcel->RetainagePaid)) {
                            $RetainagePaid = 0;
                        } else {
                            $RetainagePaid = $sfParcel->RetainagePaid;
                        }


                        ///////////////////////////////////////////////////////////////////////////////
                        ////////////////// INSERT THE PARCEL INTO THE DATABASE
                        ////////////
                           
                        // insert record and obtain insert Id
                        $parcelId = DB::table('parcels')->insertGetId([
                                    'created_at'=>$sfParcel->PropertyIDCreatedDate,
                                    'parcel_id'=>$sfParcel->PropertyIDPropertyName,
                                    'entity_id'=>$programId->entity_id,
                                    'status_explanation'=>$sfParcel->PropertyIDStatusExplanation,
                                    'program_id'=>$programId->id,
                                    'account_id'=>$programId->id,
                                    'owner_id'=>$programId->id,
                                    'target_area_id'=>$targetAreaId,
                                    'hfa_property_status_id'=>$hfaStatusId,
                                    'landbank_property_status_id'=>$statusId,
                                    'sale_price'=>$sfParcel->PropertyIDSalesPrice,
                                    'how_acquired_id'=>$howAcquiredId,
                                    'how_acquired_explanation'=>$sfParcel->PropertyIDHowAcquiredExplanation,
                                    'latitude'=>$sfParcel->PropertyIDLatLonLatitude,
                                    'longitude'=>$sfParcel->PropertyIDLatLonLongitude,
                                    'google_map_link'=>$mapLink,
                                    'oh_house_district'=>$sfParcel->PropertyIDOHHouseDistrict,
                                    'oh_senate_district'=>$sfParcel->PropertyIDOHSenateDistrict,
                                    'us_house_district'=>$sfParcel->PropertyIDUSHouseDistrict,
                                    'street_address'=>$sfParcel->PropertyIDStreetAddress,
                                    'city'=>$sfParcel->PropertyIDCity,
                                    'state_id'=>36,
                                    'zip'=>$sfParcel->PropertyIDZip,
                                    'county_id'=>$programId->county_id,
                                    'ugly_house'=>$sfParcel->PropertyIDUglyHouse,
                                    'pretty_lot'=>$sfParcel->PropertyIDPrettyLot,
                                    'historic_waiver_approved'=>$sfParcel->PropertyIDHistoricWaiverApproved,
                                    'historic_significance_or_district'=>$sfParcel->PropertyIDHistoricSignificanceDistrict,
                                    'withdrawn_date'=>$sfParcel->PropertyIDWithdrawnDate,
                                    'sf_batch_id'=>$sf_batch_id,
                                    'sf_parcel_id' => $sfParcel->PropertyIDRecordID,
                                    'sf_program_name'=>$sfParcel->ProgramProgramName,
                                    'retainage'=>$sfParcel->Retainage,
                                    'retainage_paid'=>$RetainagePaid,
                                    'hfa_property_status_id'=>$hfaStatusId,
                                    'landbank_property_status_id'=>$lbStatusId
                                ]);
                        $p = Parcel::find($parcelId);
                        //$this->line(PHP_EOL.'Added Parcel ID '.$p);

                        // update the sf_reimbursements with the batch number to match
                        DB::table('sf_reimbursements')->where('PropertyIDRecordID', $sfParcel->PropertyIDRecordID)->update(['BatchNumber'=>$sf_batch_id]);
                        // update the programs table with the sf_program_name
                        DB::table('programs')->where('id', $programId->id)->update(['sf_program_name'=>$sfParcel->ProgramProgramName]);
                    }
                }
                ///////////////////////////////////////////////////////////////////////////////
                ////////////////// CLEAR THE PARCEL SELECTION FROM MEMORY
                ////////////

                $sfParcels = '';
                $parcelBar->finish();
            } else {
                $this->line(PHP_EOL.'All parcels inserted... skipping parcel insert');
            }
                    

                    


                    



            ///////////////////////////////////////////////////////////////////////////////
            ////////////////// CREATE COMPLIANCES
            ////////////


            $compliances_to_create = DB::table('sf_compliances')->get()->all();
            $bar->advance();
            $this->line(PHP_EOL.'Creating '.count($compliances_to_create).' Compliances.');
            $complianceBar = $this->output->createProgressBar(count($compliances_to_create));
            foreach ($compliances_to_create as $data) {
                $complianceBar->advance();
                // check if compliance is entered already
                $checkCompliance = DB::table('compliances')->where('sf_parcel_id', $data->PropertyID)->count();
                $moreThanOneInSF = DB::table('sf_compliances')->where('PropertyID', $data->PropertyID)->count();

                if ($checkCompliance < $moreThanOneInSF) {
                    // get matching parcel system id
                    $allita_parcel_id = DB::table('parcels')->select('id')->where('sf_parcel_id', $data->PropertyID)->first();
                           
                    $programId = DB::table('programs')->select('id')->where('program_name', $data->ProgramName)->first();
                            

                    $lastNameStart = strpos($data->CreatedByFullName, " ") + 1;
                    $emailName = strtolower(substr($data->CreatedByFullName, 0, 1).substr($data->CreatedByFullName, $lastNameStart, strlen($data->CreatedByFullName)));
                    $createdByEmail = strtolower($emailName).'@ohiohome.org';
                    $createdByUserId = DB::table('users')->select('id')->where('email', $createdByEmail)->first();
                    if (!isset($createdByUserId) && !is_null($data->CreatedByFullName)) {
                        //need to add an OHFA user
                                
                        $createdByUserId = DB::table('users')->insertGetId([
                                    'name'=>$data->CreatedByFullName,
                                    'email'=>$createdByEmail,
                                    'password' => bcrypt('M0therBoard4247'),
                                    'badge_color' => 'green',
                                    'entity_id' => '1',
                                    'entity_type' => 'hfa'
                                    ]);
                        $u = User::find($createdByUserId);
                    } else {
                        $createdByUserId = $createdByUserId->id;
                    }
                    $lastNameStart = strpos($data->Analyst, " ") + 1;
                    $emailName = strtolower(substr($data->Analyst, 0, 1).substr($data->Analyst, $lastNameStart, strlen($data->Analyst)));
                    $analystEmail = strtolower($emailName).'@ohiohome.org';
                    $analystUserId = DB::table('users')->select('id')->where('email', $analystEmail)->first();
                    if (!isset($analystUserId) && !is_null($data->Analyst)) {
                        //need to add an OHFA user
                        $analystUserId = DB::table('users')->insertGetId([
                                    'name'=>$data->Analyst,
                                    'email'=>$analystEmail,
                                    'password' => bcrypt('M0therBoard4247'),
                                    'badge_color' => 'blue',
                                    'entity_id' => '1',
                                    ]);
                        $u = User::find($analystUserId);
                    } elseif (!is_null($data->Analyst)) {
                        $analystUserId = $analystUserId->id;
                    } else {
                        $analystUserId = null;
                    }


                            
                    $lastNameStart = strpos($data->Auditor, " ") + 1;
                    $emailName = substr($data->Auditor, 0, 1).substr($data->Auditor, $lastNameStart, strlen($data->Auditor));
                    $auditorEmail = strtolower($emailName).'@ohiohome.org';
                    $auditorUserId = DB::table('users')->select('id')->where('email', $auditorEmail)->first();
                    if (!isset($auditorUserId) && !is_null($data->Auditor)) {
                        //need to add an OHFA user
                                
                        $auditorUserId = DB::table('users')->insertGetId([
                                    'name'=>$data->Auditor,
                                    'email'=>$auditorEmail,
                                    'password' => bcrypt('M0therBoard4247'),
                                    'badge_color' => 'green',
                                    'entity_id' => '1',
                                    'entity_type' => 'hfa'
                                    ]);
                        $u = User::find($auditorUserId);
                    } elseif (!is_null($data->Auditor)) {
                        $auditorUserId = $auditorUserId->id;
                    } else {
                        $auditorUserId = null;
                    }
                    if (isset($allita_parcel_id->id)) {
                        DB::table('compliances')->insert([
                                'sf_parcel_id'=>$data->PropertyID,
                                'property_type_id'=>1,
                                'property_yes'=>$data->PropertyYes,
                                'property_notes'=>$data->PropertyNotes,
                                'parcel_id'=>$allita_parcel_id->id,
                                'created_at'=>$data->CreatedDate,
                                'program_id'=>$programId->id,
                                'created_by_user_id'=>$createdByUserId,
                                'analyst_id'=>$analystUserId,
                                'auditor_id'=>$auditorUserId,
                                'audit_date'=>$data->AuditDate,
                                'checklist_yes'=>$data->ChecklistYes,
                                'checklist_notes'=>$data->ChecklistNotes,
                                'consolidated_certs_pass'=>$data->ConsolidatedCertsPass,
                                'consolidated_certs_notes'=>$data->ConsolidatedCertsNotes,
                                'contractors_yes'=>$data->ContractorsYes,
                                'contractors_notes'=>$data->ContractorsNotes,
                                'environmental_yes'=>$data->EnvironmentalYes,
                                'environmental_notes'=>$data->EnvironmentalNotes,
                                'funding_limits_pass'=>$data->FundingLimitsPass,
                                'funding_limits_notes'=>$data->FundingLimitsNotes,
                                'inelligible_costs_yes'=>$data->InelligibleCostsYes,
                                'inelligible_costs_notes'=>$data->InelligibleCostsNotes,
                                'items_Reimbursed'=>$data->ItemsReimbursed,
                                'note_mortgage_pass'=>$data->NoteMortgagePass,
                                'note_mortgage_notes'=>$data->NoteMortgageNotes,
                                'payment_processing_pass'=>$data->PaymentProcessingPass,
                                'payment_processing_notes'=>$data->PaymentProcessingNotes,
                                'loan_requirements_pass'=>$data->LoanRequirementsPass,
                                'loan_requirements_notes'=>$data->LoanRequirementsNotes,
                                'photos_yes'=>$data->PhotosYes,
                                'photos_notes'=>$data->PhotosNotes,
                                'salesforce_yes'=>$data->SalesforceYes,
                                'salesforce_notes'=>$data->SalesforceNotes,
                                'right_to_demo_pass'=>$data->RighttoDemoPass,
                                'right_to_demo_notes'=>$data->RighttoDemoNotes,
                                'reimbursement_doc_pass'=>$data->ReimbursementDocPass,
                                'reimbursement_doc_notes'=>$data->ReimbursementDocNotes,
                                'target_area_yes'=>$data->TargetAreaYes,
                                'target_area_notes'=>$data->TargetAreaNotes,
                                'sdo_pass'=>$data->SDOPass,
                                'sdo_notes'=>$data->SDONotes,
                                'score'=>$data->Score,
                                'if_fail_corrected'=>$data->Iffailcorrected,
                                'property_pass'=>$data->PropertyPass,
                                'random_audit'=>$data->RandomAudit,
                                ]);
                    //$this->line(PHP_EOL.'Added Compliance for '.$allita_parcel_id->id);
                    } else {
                        $error = "Compliance Failed line 681: Cannot find a matching parcel with salesforce id ".$data->PropertyID." and ParcelId ".$data->ParcelID." for ".$data->ProgramName;
                        $this->line(PHP_EOL.$error);
                    }
                }
                $checkCompliance = '';
            }
            $compliances_to_create = '';
            $complianceBar->finish();


            ///////////////////////////////////////////////////////////////////////////////
            ////////////////// CREATE SITE VISITS
            ////////////


            $site_visits_to_create = DB::table('sf_site_visits')->get()->all();
            $bar->advance();
            $this->line(PHP_EOL.'Creating '.count($site_visits_to_create).' Site Visits.');
            $siteVisitBar = $this->output->createProgressBar(count($site_visits_to_create));
            foreach ($site_visits_to_create as $data) {
                $siteVisitBar->advance();
                // check if site visit is entered already
                $checkSiteVisit = DB::table('site_visits')->where('sf_parcel_id', $data->PropertyID)->count();
                $moreThanOneInSF = DB::table('sf_site_visits')->where('PropertyID', $data->PropertyID)->count();

                if ($checkSiteVisit < $moreThanOneInSF) {
                    // get matching parcel system id
                    $allita_parcel_id = DB::table('parcels')->select('id')->where('sf_parcel_id', $data->PropertyID)->first();
                    $entity_id = DB::table('entities')->select('id')->where('entity_name', 'like', '%'.$data->Partner.'%')->first();
                    if (!isset($entity_id->id)) {
                        $error = "Site visit entity name does not match any on file. Import aborted. Please fix data and re-do import.";
                        dd($error, $data->PropertyID, $data->Partner);
                    }
                    $lastNameStart = strpos($data->InspectorName, " ") + 1;
                    $emailName = substr($data->InspectorName, 0, 1).substr($data->InspectorName, $lastNameStart, strlen($data->InspectorName));
                    $inspectorEmail = strtolower($emailName).'@ohiohome.org';
                    $inspectedByUserId = DB::table('users')->select('id')->where('email', $inspectorEmail)->first();
                    if (!isset($inspectedByUserId) && !is_null($data->InspectorName)) {
                        //need to add an OHFA user
                                
                        $inspectedByUserId = DB::table('users')->insertGetId([
                                    'name'=>$data->InspectorName,
                                    'email'=>$inspectorEmail,
                                    'password' => bcrypt('M0therBoard4247'),
                                    'badge_color' => 'green',
                                    'entity_id' => '1',
                                    'entity_type' => 'hfa'
                                    ]);
                        $u = User::find($inspectedByUserId);
                    } elseif (!is_null($data->InspectorName)) {
                        $inspectedByUserId = $inspectedByUserId->id;
                    } else {
                        $inspectedByUserId = null;
                    }
                    //convert yes and no to boolean or null
                    switch ($data->AllStructuresRemoved) {
                        case 'Yes':
                            $alsr = 1;
                            break;
                        case 'No':
                            $alsr = 0;
                            break;
                        default:
                            $alsr = null;
                            break;
                    }
                    switch ($data->ConstructionDebrisRemoved) {
                        case 'Yes':
                            $cdr = 1;
                            break;
                        case 'No':
                            $cdr = 0;
                            break;
                        default:
                            $cdr = null;
                            break;
                    }
                    switch ($data->X10_Retainage_released_to_contractor__c) {
                        case 'Yes':
                            $rrtc = 1;
                            break;
                        case 'No':
                            $rrtc = 0;
                            break;
                        default:
                            $rrtc = null;
                            break;
                    }
                    switch ($data->X11_Is_a_recap_of_maint_funds_required__c) {
                        case 'Yes':
                            $romfr = 1;
                            break;
                        case 'No':
                            $romfr = 0;
                            break;
                        default:
                            $romfr = null;
                            break;
                    }
                    switch ($data->X12_Amount_of_maint_recapture_due__c) {
                        case 'Yes':
                            $aomrd = 1;
                            break;
                        case 'No':
                            $aomrd = 0;
                            break;
                        default:
                            $aomrd = null;
                            break;
                    }
                    switch ($data->X3_Was_the_property_graded_and_seeded__c) {
                        case 'Yes':
                            $wtpgas = 1;
                            break;
                        case 'No':
                            $wtpgas = 0;
                            break;
                        default:
                            $wtpgas = null;
                            break;
                    }
                    switch ($data->X4_Is_there_any_signage__c) {
                        case 'Yes':
                            $itas = 1;
                            break;
                        case 'No':
                            $itas = 0;
                            break;
                        default:
                            $itas = null;
                            break;
                    }
                    switch ($data->X5_Is_grass_growing_consistently_across__c) {
                        case 'Yes':
                            $iggca = 1;
                            break;
                        case 'No':
                            $iggca = 0;
                            break;
                        default:
                            $iggca = null;
                            break;
                    }
                    switch ($data->X6_Is_grass_mowed_weeded__c) {
                        case 'Yes':
                            $igmw = 1;
                            break;
                        case 'No':
                            $igmw = 0;
                            break;
                        default:
                            $igmw = null;
                            break;
                    }
                    switch ($data->X7_Was_the_property_landscaped__c) {
                        case 'Yes':
                            $wtpl = 1;
                            break;
                        case 'No':
                            $wtpl = 0;
                            break;
                        default:
                            $wtpl = null;
                            break;
                    }
                    switch ($data->X8_Nuisance_Elements_or_Code_Violations__c) {
                        case 'Yes':
                            $neocv = 1;
                            break;
                        case 'No':
                            $neocv = 0;
                            break;
                        default:
                            $neocv = null;
                            break;
                    }
                    switch ($data->X9_Are_there_Environmental_Conditions__c) {
                        case 'Yes':
                            $atec = 1;
                            break;
                        case 'No':
                            $atec = 0;
                            break;
                        default:
                            $atec = null;
                            break;
                    }
                            
                    if (!is_null($allita_parcel_id)) {
                        DB::table('site_visits')->insert([
                                'visit_date'=>$data->VisitDate,
                                'other_notes'=>$data->other_notes,
                                'corrective_action_required'=>$data->corrective_action_required,
                                'retainage_released_to_contractor'=>$rrtc,
                                'is_a_recap_of_maint_funds_required'=>$romfr,
                                'amount_of_maint_recapture_due'=>$aomrd,
                                'was_the_property_graded_and_seeded'=>$wtpgas,
                                'is_there_any_signage'=>$itas,
                                'is_grass_growing_consistently_across'=>$iggca,
                                'is_grass_mowed_weeded'=>$igmw,
                                'was_the_property_landscaped'=>$wtpl,
                                'nuisance_elements_or_code_violations'=>$neocv,
                                'are_there_environmental_conditions'=>$atec,
                                'inspector_id'=>$inspectedByUserId,
                                'sf_parcel_id'=>$data->PropertyID,
                                'parcel_id'=>$allita_parcel_id->id,
                                'entity_id'=>$entity_id->id,
                                'all_structures_removed'=>$alsr,
                                'construction_debris_removed'=>$cdr,
                                ]);
                    //$this->line(PHP_EOL.'Added Site Visit for for '.$allita_parcel_id->id);
                    } else {
                        $this->line(PHP_EOL.'Unable to create site visit for '.substr($data->PropertyID, 0, 14));
                    }
                }
            }
            $site_visits_to_create = '';
            $siteVisitBar->finish();

            ///////////////////////////////////////////////////////////////////////////////
            ////////////////// GET NEW SELECTION TO CREATE REQS, POS, INVOICES
            ////////////




            $sfParcels = DB::table('parcels')->select('owner_id', 'sf_batch_id', 'sf_program_name')->groupBy('owner_id', 'sf_batch_id', 'sf_program_name')->distinct()->get()->all();


                    
            ///////////////////////////////////////////////////////////////////////////////
            ////////////////// CREATE REQS, POS, INVOICES
            ////////////
            $bar->advance();
            $this->line(PHP_EOL.'Creating '.count($sfParcels).' Reqs, POs, and Invoices.');
            $rpiBar = $this->output->createProgressBar(count($sfParcels));
            foreach ($sfParcels as $reqPoInv) {
                $rpiBar->advance();
                $reqId = DB::table('reimbursement_requests')->insertGetId([
                                'sf_batch_id'=>$reqPoInv->sf_batch_id,
                                'entity_id'=>$reqPoInv->owner_id,
                                'program_id'=>$reqPoInv->owner_id,
                                'account_id'=>$reqPoInv->owner_id,
                                'status_id'=>1,
                                'active'=>1
                                ]);
                //$this->line(PHP_EOL.'Added Reimbursement Request for Batch '.$reqPoInv->sf_batch_id);
                //$u = User::find($analystUserId);
                        

                // check sums to determine if PO and INV items need to be created

                $checkApprovedSums = DB::table('sf_reimbursements')->where('BatchNumber', $reqPoInv->sf_batch_id)->where('ProgramProgramName', $reqPoInv->sf_program_name)->sum('TotalApproved');
                // if($reqPoInv->owner_id == 113){
                //     $error = "Checking ".$reqPoInv->sf_program_name."'s checkApprovedSums to be the value of ".$checkApprovedSums." using the lookup of program name ".$reqPoInv->sf_program_name." and sf_batch_id ".$reqPoInv->sf_batch_id;
                //     dd($error);
                // }
                if ($checkApprovedSums > 0) {
                    /// OLD METHOD /// if($reqPoInv->sf_batch_id<1000000000){
                    ///////////////////////////////////////////////////////////////////////////////
                    ////////////////// BATCH IDS WITH A NUMBER GREATER
                    //////////// HAVE NOT BEEN INVOICED
                    $poId = DB::table('reimbursement_purchase_orders')->insertGetId([
                                    'sf_batch_id'=>$reqPoInv->sf_batch_id,
                                    'entity_id'=>$reqPoInv->owner_id,
                                    'program_id'=>$reqPoInv->owner_id,
                                    'account_id'=>$reqPoInv->owner_id,
                                    'rq_id'=>$reqId,
                                    'status_id'=>1,
                                    'active'=>1
                                    ]);
                    //$this->line(PHP_EOL.'Added Purchase Order for Request '.$reqId);
                    //$po = ReimbursementPurchaseOrders::find($poId);
                    $invId = DB::table('reimbursement_invoices')->insertGetId([
                                    'sf_batch_id'=>$reqPoInv->sf_batch_id,
                                    'entity_id'=>$reqPoInv->owner_id,
                                    'program_id'=>$reqPoInv->owner_id,
                                    'account_id'=>$reqPoInv->owner_id,
                                    'po_id'=>$poId,
                                    'status_id'=>1,
                                    'active'=>1
                                    ]);
                    //$this->line(PHP_EOL.'Added Invoice for PO '.$poId);
                            //$ri = reim::find($invId);
                }


                        
                ///////////////////////////////////////////////////////////////////////////////
                ////////////////// CREATE REQS, POS, INVOICES CROSS REFERENCE
                ////////////

                $parcelsThatMatch = DB::table('parcels')->select('id', 'hfa_property_status_id', 'landbank_property_status_id')->where('sf_batch_id', $reqPoInv->sf_batch_id)->where('program_id', $reqPoInv->owner_id)->distinct()->get()->all();

                foreach ($parcelsThatMatch as $match) {
                    $tid = DB::table('parcels_to_reimbursement_requests')->insertGetId([
                                'parcel_id'=>$match->id,
                                'reimbursement_request_id'=>$reqId
                                ]);
                    //$this->line(PHP_EOL.'Added Parcel'.$match->id.' to Request '.$reqId);
                    //$ptrr = ParcelsToReimbursementRequest::find($tid);
                        
                    /// CREATE PO AND INV REF IF THEY HAVE APPROVED TOTALS
                    $checkApprovedSums = DB::table('sf_reimbursements')->where('BatchNumber', $reqPoInv->sf_batch_id)->where('ProgramProgramName', $reqPoInv->sf_program_name)->sum('TotalApproved');
                    if ($checkApprovedSums > 0) {
                        /// OLD METHOD if($reqPoInv->sf_batch_id<1000000000 && $match->hfa_property_status_id > 22){

                        ///////////////////////////////////////////////////////////////////////////////
                        ////////////////// STATUS OF APPROVED = PO AND INVOICE SHOULD EXIST
                        ////////////
                                
                        $tid = DB::table('parcels_to_purchase_orders')->insertGetId([
                                    'parcel_id'=>$match->id,
                                    'purchase_order_id'=>$poId
                                    ]);
                        //$ptpo = ParcelsToPurchaseOrder::find($tid);
                               

                        $tid = DB::table('parcels_to_reimbursement_invoices')->insertGetId([
                                    'parcel_id'=>$match->id,
                                    'reimbursement_invoice_id'=>$invId
                                    ]);
                    //$ptri = ParcelsToReimbursementInvoice::find($tid);
                    } else {
                        ///////////////////////////////////////////////////////////////////////////////
                        ////////////////// INV AND PO SHOULD NOT EXIST YET
                        //////////// REMOVE THEM
                        DB::table('reimbursement_invoices')->where('id', $invId)->delete();
                        DB::table('reimbursement_purchase_orders')->where('id', $poId)->delete();
                    }
                }


                ///////////////////////////////////////////////////////////////////////////////
                ////////////////// CLEAR MEMORY OF SELECTION
                ////////////


                $parcelsThatMatch = '';
            }
            $rpiBar->finish();







            ///////////////////////////////////////////////////////////////////////////////
            ////////////////// GET REIMBURSEMENT INFORMATION
            ////////////
            if (!isset($processedReimbursements)) {
                // use this to try and prevent double entries...
                $processedReimbursements = 0;
            }
            if ($processedReimbursements != 1) {
                $processedReimbursements = 1;
                $reimbursements = DB::table('sf_reimbursements')->join('parcels', 'sf_parcel_id', '=', 'PropertyIDRecordID')
                        ->select(
                            'parcels.id',
                            'parcels.owner_id',
                            'parcels.entity_id',
                            'parcels.account_id',
                            'AcquisitionCost',
                            'AcquisitionRequested',
                            'AcquisitionApproved',
                            'PreDemoCost',
                            'PreDemoRequested',
                            'PreDemoApproved',
                            'DemolitionCost',
                            'DemolitionRequested',
                            'DemolitionApproved',
                            'GreeningCost',
                            'GreeningRequested',
                            'GreeningApproved',
                            'GreeningAdvanceOption',
                            'MaintenanceCost',
                            'MaintenanceRequested',
                            'MaintenanceApproved',
                            'AdministrationCost',
                            'AdministrationRequested',
                            'AdministrationApproved',
                            'NIPLoanPayoffCost',
                            'NIPLoanPayoffRequested',
                            'NIPLoanPayoffApproved'
                        )->distinct()->get()->all();
                             


                ///////////////////////////////////////////////////////////////////////////////
                ////////////////// PROCESS THE COST, REQ, PO, AND INVOICE BREAK OUTS
                ////////////


                $bar->advance();
                $this->line(PHP_EOL.'Inputing '.count($reimbursements).' breakout items for cost, req, po and invoice');
                $breakoutBar = $this->output->createProgressBar(count($reimbursements));
                foreach ($reimbursements as $sfParcel) {
                    $breakoutBar->advance();
                    $timesRun = $timesRun + 1;
                    $costItemsData = [
                                        [
                                            'breakout_type'=>1,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'expense_category_id'=>9,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->NIPLoanPayoffCost,
                                            'vendor_id'=>1,
                                            'description'=>'NIP Loan Payoff Cost Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            ],
                                        [
                                            'breakout_type'=>1,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'expense_category_id'=>2,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->AcquisitionCost,
                                            'vendor_id'=>1,
                                            'description'=>'Acquisition Cost Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            ],
                                        [
                                            'breakout_type'=>1,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'expense_category_id'=>3,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->PreDemoCost,
                                            'vendor_id'=>1,
                                            'description'=>'Pre-Demo Cost Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            ],
                                        [
                                            'breakout_type'=>1,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'expense_category_id'=>4,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->DemolitionCost,
                                            'vendor_id'=>1,
                                            'description'=>'Demolition Cost Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            ],
                                        
                                        [
                                            'breakout_type'=>1,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'expense_category_id'=>6,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->MaintenanceCost,
                                            'vendor_id'=>1,
                                            'description'=>'Maintenance Cost Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            ],
                                        [
                                            'breakout_type'=>1,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'expense_category_id'=>7,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->AdministrationCost,
                                            'vendor_id'=>1,
                                            'description'=>'Administration Cost Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            ]
                                    ];

                    if ($sfParcel->GreeningAdvanceOption>0) {
                        $greeningAdvanceArray = [
                                        'breakout_type'=>3,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'expense_category_id'=>5,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->GreeningCost,
                                            'vendor_id'=>1,
                                            'description'=>'Greening Advance Cost Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                        ];
                        array_push($costItemsData, $greeningAdvanceArray);
                    } else {
                        $greeningArray = [
                                            'breakout_type'=>1,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'expense_category_id'=>5,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->GreeningCost,
                                            'vendor_id'=>1,
                                            'description'=>'Greening Cost Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            ];
                        array_push($costItemsData, $greeningArray);
                    }
                    DB::table('cost_items')->insert($costItemsData);
                    //TODO: Determine if we need to do anything with activity logging here
                    $costItemsData='';

                                
                    $parcelReqId = DB::table('parcels_to_reimbursement_requests')->select('reimbursement_request_id')->where('parcel_id', $sfParcel->id)->first();
                    $requestItemsData = [
                                        [
                                            'breakout_type'=>1,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'req_id'=> $parcelReqId->reimbursement_request_id,
                                            'expense_category_id'=>9,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->NIPLoanPayoffRequested,
                                            'vendor_id'=>1,
                                            'description'=>'NIP Loan Payoff Request Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            ],
                                        [
                                            'breakout_type'=>1,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'req_id'=> $parcelReqId->reimbursement_request_id,
                                            'expense_category_id'=>2,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->AcquisitionRequested,
                                            'vendor_id'=>1,
                                            'description'=>'Acquisition Requested Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            ],
                                        [
                                            'breakout_type'=>1,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'req_id'=> $parcelReqId->reimbursement_request_id,
                                            'expense_category_id'=>3,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->PreDemoRequested,
                                            'vendor_id'=>1,
                                            'description'=>'Pre-Demo Requested Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            ],
                                        [
                                            'breakout_type'=>1,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'req_id'=> $parcelReqId->reimbursement_request_id,
                                            'expense_category_id'=>4,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->DemolitionRequested,
                                            'vendor_id'=>1,
                                            'description'=>'Demolition Requested Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            ],
                                        [
                                            'breakout_type'=>1,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'req_id'=> $parcelReqId->reimbursement_request_id,
                                            'expense_category_id'=>6,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->MaintenanceRequested,
                                            'vendor_id'=>1,
                                            'description'=>'Maintenance Requested Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            ],
                                        [
                                            'breakout_type'=>1,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'req_id'=> $parcelReqId->reimbursement_request_id,
                                            'expense_category_id'=>7,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->AdministrationRequested,
                                            'vendor_id'=>1,
                                            'description'=>'Administration Requested Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            ]
                                    ];
                    // add greening in if it is there.
                    if ($sfParcel->GreeningAdvanceOption>0) {
                        $greeningAdvanceArray = [
                                        'breakout_type'=>3,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'req_id'=> $parcelReqId->reimbursement_request_id,
                                            'expense_category_id'=>5,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->GreeningCost,
                                            'vendor_id'=>1,
                                            'description'=>'Greening Advance Cost Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                        ];
                        array_push($requestItemsData, $greeningAdvanceArray);
                    } else {
                        $greeningArray = [
                                            'breakout_type'=>1,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'req_id'=> $parcelReqId->reimbursement_request_id,
                                            'expense_category_id'=>5,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->GreeningCost,
                                            'vendor_id'=>1,
                                            'description'=>'Greening Cost Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            ];
                        array_push($requestItemsData, $greeningArray);
                    }
                    DB::table('request_items')->insert($requestItemsData);
                    //TODO Determine if we need to do anything with activity logging here
                    $requestItemsData = '';

                    $parcelPoId = DB::table('parcels_to_purchase_orders')->select('purchase_order_id')->where('parcel_id', $sfParcel->id)->first();

                    if (isset($parcelPoId)) {
                        $poItemsData = [
                                            [
                                            'breakout_type'=>1,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'po_id'=> $parcelPoId->purchase_order_id,
                                            'expense_category_id'=>9,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->NIPLoanPayoffApproved,
                                            'vendor_id'=>1,
                                            'description'=>'NIP Loan Payoff Approved Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            ],
                                            [
                                                'breakout_type'=>1,
                                                'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                                'po_id'=> $parcelPoId->purchase_order_id,
                                                'expense_category_id'=>2,
                                                'breakout_item_status_id'=>2,
                                                'amount'=>$sfParcel->AcquisitionApproved,
                                                'vendor_id'=>1,
                                                'description'=>'Aquisition Approved Aggregate',
                                                'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                                ],
                                            [
                                                'breakout_type'=>1,
                                                'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                                'po_id'=> $parcelPoId->purchase_order_id,
                                                'expense_category_id'=>3,
                                                'breakout_item_status_id'=>2,
                                                'amount'=>$sfParcel->PreDemoApproved,
                                                'vendor_id'=>1,
                                                'description'=>'Pre-Demo Approved Aggregate',
                                                'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                                ],
                                            [
                                                'breakout_type'=>1,
                                                'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                                'po_id'=> $parcelPoId->purchase_order_id,
                                                'expense_category_id'=>4,
                                                'breakout_item_status_id'=>2,
                                                'amount'=>$sfParcel->DemolitionApproved,
                                                'vendor_id'=>1,
                                                'description'=>'Demolition Approved Aggregate',
                                                'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                                ],
                                            
                                            [
                                                'breakout_type'=>1,
                                                'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                                'po_id'=> $parcelPoId->purchase_order_id,
                                                'expense_category_id'=>6,
                                                'breakout_item_status_id'=>2,
                                                'amount'=>$sfParcel->MaintenanceApproved,
                                                'vendor_id'=>1,
                                                'description'=>'Maintenance Approved Aggregate',
                                                'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                                ],
                                            [
                                                'breakout_type'=>1,
                                                'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                                'po_id'=> $parcelPoId->purchase_order_id,
                                                'expense_category_id'=>7,
                                                'breakout_item_status_id'=>2,
                                                'amount'=>$sfParcel->AdministrationApproved,
                                                'vendor_id'=>1,
                                                'description'=>'Administration Approved Aggregate',
                                                'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                                ],
                                        ];
                        // add greening in if it is there.
                        if ($sfParcel->GreeningAdvanceOption>0) {
                            $greeningAdvanceArray = [
                                        'breakout_type'=>3,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'po_id'=> $parcelPoId->purchase_order_id,
                                            'expense_category_id'=>5,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->GreeningCost,
                                            'vendor_id'=>1,
                                            'description'=>'Greening Advance Cost Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                        ];
                            array_push($poItemsData, $greeningAdvanceArray);
                        } else {
                            $greeningArray = [
                                            'breakout_type'=>1,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'po_id'=> $parcelPoId->purchase_order_id,
                                            'expense_category_id'=>5,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->GreeningCost,
                                            'vendor_id'=>1,
                                            'description'=>'Greening Cost Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            ];
                            array_push($poItemsData, $greeningArray);
                        }
                        DB::table('po_items')->insert($poItemsData);
                        //TODO: Determine if we need to do anything with event logging here
                        $poItemsData = "";
                    }

                    $parcelInvId = DB::table('parcels_to_reimbursement_invoices')->select('reimbursement_invoice_id')->where('parcel_id', $sfParcel->id)->first();

                    if (isset($parcelPoId)) {
                        $invItemsData = [
                                            [
                                            'breakout_type'=>1,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'invoice_id'=> $parcelInvId->reimbursement_invoice_id,
                                            'expense_category_id'=>9,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->NIPLoanPayoffApproved,
                                            'vendor_id'=>1,
                                            'description'=>'NIP Loan Payoff Invoiced Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            ],
                                            [
                                                'breakout_type'=>1,
                                                'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                                'invoice_id'=> $parcelInvId->reimbursement_invoice_id,
                                                'expense_category_id'=>2,
                                                'breakout_item_status_id'=>2,
                                                'amount'=>$sfParcel->AcquisitionApproved,
                                                'vendor_id'=>1,
                                                'description'=>'Aquisition Invoiced Aggregate',
                                                'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                                ],
                                            [
                                                'breakout_type'=>1,
                                                'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                                'invoice_id'=> $parcelInvId->reimbursement_invoice_id,
                                                'expense_category_id'=>3,
                                                'breakout_item_status_id'=>2,
                                                'amount'=>$sfParcel->PreDemoApproved,
                                                'vendor_id'=>1,
                                                'description'=>'Pre-Demo Invoiced Aggregate',
                                                'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                                ],
                                            [
                                                'breakout_type'=>1,
                                                'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                                'invoice_id'=> $parcelInvId->reimbursement_invoice_id,
                                                'expense_category_id'=>4,
                                                'breakout_item_status_id'=>2,
                                                'amount'=>$sfParcel->DemolitionApproved,
                                                'vendor_id'=>1,
                                                'description'=>'Demolition Invoiced Aggregate',
                                                'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                                ],
                                            
                                            [
                                                'breakout_type'=>1,
                                                'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                                'invoice_id'=> $parcelInvId->reimbursement_invoice_id,
                                                'expense_category_id'=>6,
                                                'breakout_item_status_id'=>2,
                                                'amount'=>$sfParcel->MaintenanceApproved,
                                                'vendor_id'=>1,
                                                'description'=>'Maintenance Invoiced Aggregate',
                                                'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                                ],
                                            [
                                                'breakout_type'=>1,
                                                'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                                'invoice_id'=> $parcelInvId->reimbursement_invoice_id,
                                                'expense_category_id'=>7,
                                                'breakout_item_status_id'=>2,
                                                'amount'=>$sfParcel->AdministrationApproved,
                                                'vendor_id'=>1,
                                                'description'=>'Administration Invoiced Aggregate',
                                                'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                                ],
                                        ];
                        if ($sfParcel->GreeningAdvanceOption>0) {
                            $greeningAdvanceArray = [
                                        'breakout_type'=>3,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'invoice_id'=> $parcelInvId->reimbursement_invoice_id,
                                            'expense_category_id'=>5,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->GreeningCost,
                                            'vendor_id'=>1,
                                            'description'=>'Greening Advance Cost Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                        ];
                            array_push($invItemsData, $greeningAdvanceArray);
                        } else {
                            $greeningArray = [
                                            'breakout_type'=>1,
                                            'parcel_id'=> $sfParcel->id,
                                            'program_id'=>$sfParcel->owner_id,
                                            'entity_id'=>$sfParcel->entity_id,
                                            'account_id'=>$sfParcel->account_id,
                                            'invoice_id'=> $parcelInvId->reimbursement_invoice_id,
                                            'expense_category_id'=>5,
                                            'breakout_item_status_id'=>2,
                                            'amount'=>$sfParcel->GreeningCost,
                                            'vendor_id'=>1,
                                            'description'=>'Greening Cost Aggregate',
                                            'notes'=>'Legacy Parcel - No Break Out Available, No Dates Available.'
                                            ];
                            array_push($invItemsData, $greeningArray);
                        }
                        DB::table('invoice_items')->insert($invItemsData);
                        $invItemsData = '';
                        DB::table('parcels')->where('id', $sfParcel->id)->update(['landbank_property_status_id'=>13,'hfa_property_status_id'=>27]);
                        //   $p = Parcel::find($sfParcel->id);
                    }
                } // end reimbursements for each
                $processedReimbursements = 1;
                $breakoutBar->finish();
            } else {
                $error = "Totally ran reimbursements twice!";
                dd($error, $reimbursements);
            }
            //$reimbursementsCount = count($reimbursements);
            //dd($timesRun,$reimbursementsCount);
            // end if processed

            $reimbursements = '';

            ///////////////////////////////////////////////////////////////////////////////
            ////////////////// GET TRANSACTION INFORMATION FIRST BY GETTING INVOICES
            ////////////
            $invoicesToTotal = DB::table('reimbursement_invoices')->join('programs', 'programs.id', '=', 'program_id')->select('program_id', 'program_name', 'sf_program_name', 'sf_batch_id', 'reimbursement_invoices.id as invoice_id')->get()->all();
                        


            ///////////////////////////////////////////////////////////////////////////////
            ////////////////// SUM TOTALS FOR ALL TOTALS PAID THAT MATCH THE
            //////////// BATCH ID AND PROGRAM NAME

            $bar->advance();
            $this->line(PHP_EOL.'Creating '.count($invoicesToTotal).' Invoices');
            $invoiceBar = $this->output->createProgressBar(count($invoicesToTotal));
            foreach ($invoicesToTotal as $data) {
                $invoiceBar->advance();

                $invoiceData = DB::table('sf_reimbursements')->select('ReimbursementCreatedDate', 'DatePaid')->where('ProgramProgramName', '=', $data->sf_program_name)->where('BatchNumber', $data->sf_batch_id)->first();
                $invoiceTotal = DB::table('sf_reimbursements')->select('TotalPaid')->where('ProgramProgramName', $data->sf_program_name)->where('BatchNumber', $data->sf_batch_id)->sum('TotalPaid');

                            

                            

                if (!is_null($invoiceData->DatePaid)) {
                    ///////////////////////////////////////////////////////////////////////////////
                    ////////////////// INSERT THE TOTAL AS A TRANSACTION
                    ////////////

                    $tid = DB::table('transactions')->insertGetId([

                                        'account_id'=>$data->program_id,
                                        'credit_debit'=>'d',
                                        'amount'=>$invoiceTotal,
                                        'transaction_category_id'=>3,
                                        'type_id'=>1,
                                        'link_to_type_id'=>$data->invoice_id,
                                        'status_id'=>2,
                                        'owner_id'=>$data->program_id,
                                        'owner_type'=>'program',
                                        'date_entered'=>$invoiceData->ReimbursementCreatedDate,
                                        'date_cleared'=>$invoiceData->DatePaid,
                                        'created_at'=>$invoiceData->ReimbursementCreatedDate,
                                        'transaction_note'=>'Legacy Reimbursement Invoice Payment Transaction translated from Salesforce data by creating individual invoices for each program within each payment mass batch number recorded in salesforce. The batch number was too ambiguous to determine actual amounts paid as reimbursements to individual groups as is, and notes were too inconsistent to use as an accurate resource. Therefore I calculated the total paid for the parcels within that program\'s grouping within the batch and used the created date and paid date accordingly. Imported '.date('m/d/Y', time())
                                    ]);
                    //$t = Transaction::find($tid);
                             
                    // update all the parcels as paid status
                    $parcelsToUpdate = DB::table('parcels_to_reimbursement_invoices')->select('parcel_id')->where('reimbursement_invoice_id', $data->invoice_id)->get()->all();
                    foreach ($parcelsToUpdate as $ptu) {
                        DB::table('parcels')->where('id', $ptu->parcel_id)->update(['landbank_property_status_id'=>14,'hfa_property_status_id'=>28]);
                        //    $p = Parcel::find($ptu->parcel_id);
                    }
                    $parcelsToUpdate = '';
                }
            }

            $invoicesToTotal = '';
            $invoiceBar->finish();

                        
            ///////////////////////////////////////////////////////////////////////////////
            ////////////////// CREATE DISPOSITIONS
            ////////////
                        
            //////// ADD DISPOSITION DUE TO TABLE
            if (Schema::hasColumn('dispositions', 'disposition_due')) {
                // no need to add - it is there!
            } else {
                Schema::table('dispositions', function (Blueprint $table) {
                    $table->string('disposition_due')->nullable();
                });
            }

            $dispositionsToMake = DB::table('sf_dispositions')->join('parcels', 'PropertyID', '=', 'sf_parcel_id')
                            ->select(
                                'PropertyID',
                                'program_id',
                                'parcels.id as allita_parcel_id',
                                'DispositionExplanation',
                                'DispositionType',
                                'RetainageAmount',
                                'ReleaseDate',
                                'CreatedDate',
                                'LastModifiedDate',
                                'disposition_due'
                            )
                            ->distinct()->get()->all();

            $dtm = 0;
            $bar->advance();
            $this->line(PHP_EOL.'Creating '.count($dispositionsToMake).' Dispositions');
            $dispositionsBar = $this->output->createProgressBar(count($dispositionsToMake));
            foreach ($dispositionsToMake as $data) {
                $dispositionsBar->advance();
                $totalDispositions = count($dispositionsToMake);
                $dtm = $dtm + 1;
                /////// SET THE DISPOSITION TYPE ID
                switch ($data->DispositionType) {
                    case 'Bus/Res Dev':
                        $dispositionTypeId = 1;
                        break;
                    case 'Non-Profit':
                        $dispositionTypeId = 2;
                        break;
                    case 'Other':
                        $dispositionTypeId = 3;
                        break;
                    case 'Public Use':
                        $dispositionTypeId = 4;
                        break;
                    case 'Side Lot':
                        $dispositionTypeId = 5;
                        break;
                                
                    default:
                        $dispositionTypeId = 3;
                        break;
                }

                // Check if disposition exists
                $dipositionCheck = DB::table('dispositions')->where('parcel_id', $data->allita_parcel_id)->get()->all();

                if (!isset($dispostionCheck[0]->id)) {
                    // disposition does not exist - carry on
                    $tid = DB::table('dispositions')->insertGetId([
                                    'sf_parcel_id'=>$data->PropertyID,
                                    'entity_id'=>$data->program_id,
                                    'program_id'=>$data->program_id,
                                    'account_id'=>$data->program_id,
                                    'parcel_id'=>$data->allita_parcel_id,
                                    'disposition_type_id'=>$dispositionTypeId,
                                    'disposition_explanation'=>$data->DispositionExplanation,
                                    'release_date'=>$data->ReleaseDate,
                                    'active'=>1,
                                    'created_at'=>$data->CreatedDate,
                                    'updated_at'=>$data->LastModifiedDate,
                                    'disposition_due'=>$data->disposition_due
                                    ]);
                    //  $d = Disposition::find($tid);
                          

                    // update all the parcels as paid status
                                
                    DB::table('parcels')->where('id', $data->allita_parcel_id)->update(['landbank_property_status_id'=>15,'hfa_property_status_id'=>29]);
                    $p = Parcel::find($data->allita_parcel_id);
                } else {
                    // dd($dispositionCheck,$dispositionCheck[0]->id);
                }
            }
            $dispositionsToMake = "";
            $dispositionsBar->finish();


            ///////////////////////////////////////////////////////////////////////////////
            ////////////////// ADD DISPOSITION ITEMS TO EACH DISPOSITION
            //////////// ALL ARE MAINTENANCE

            $dispositionItemsToAdd = DB::table('dispositions')->join('sf_reimbursements', 'sf_parcel_id', '=', 'PropertyIDRecordID')
                        ->select(
                            'dispositions.id as disposition_id',
                            'parcel_id',
                            'program_id',
                            'account_id',
                            'entity_id',
                            'RecapturedOwed',
                            'ReturnedFundsExplanation',
                            'NetProceeds',
                            'ProgramIncome',
                            'dispositions.created_at'
                        )
                        ->where('RecapturedOwed', '>', 0)
                        ->distinct()->get()->all();
            $bar->advance();
            $this->line(PHP_EOL.'Adding '.count($dispositionItemsToAdd).' Items to Dispositions');
            $dispositionsItemsBar = $this->output->createProgressBar(count($dispositionItemsToAdd));
            foreach ($dispositionItemsToAdd as $data) {
                $dispositionsItemsBar->advance();
                // Due to selection limitations - let's update the disposition to contain the program income
                DB::table('dispositions')->where('dispositions.id', $data->disposition_id)->update(['program_income'=>$data->ProgramIncome]);
                // $d = Disposition::find($data->disposition_id);
                            
                // We can assume that the recaptured owed is net proceeds plus maintenance.
                $maintenanceRecapOwed = $data->RecapturedOwed - $data->NetProceeds;
                if ($maintenanceRecapOwed > 0) {
                    DB::table('disposition_items')->insert([
                                    'breakout_type'=>2,
                                    'disposition_id'=>$data->disposition_id,
                                    'parcel_id'=>$data->parcel_id,
                                    'entity_id'=>$data->entity_id,
                                    'program_id'=>$data->program_id,
                                    'account_id'=>$data->account_id,
                                    'expense_category_id'=>6,
                                    'amount'=>$maintenanceRecapOwed,
                                    'vendor_id'=>1,
                                    'description'=>'Imported Recaputre Owed $'.$data->RecapturedOwed.' minus Net Proceeds '.$data->NetProceeds.' from Salsesforce as Maintenance Recapture Owed.',
                                    'notes'=>$data->ReturnedFundsExplanation,
                                    'created_at'=>$data->created_at

                                    ]);
                }
                if ($data->NetProceeds > 0.00) {
                    DB::table('disposition_items')->insert([
                                    'breakout_type'=>2,
                                    'disposition_id'=>$data->disposition_id,
                                    'parcel_id'=>$data->parcel_id,
                                    'entity_id'=>$data->entity_id,
                                    'program_id'=>$data->program_id,
                                    'account_id'=>$data->account_id,
                                    'expense_category_id'=>8,
                                    'amount'=>$data->NetProceeds,
                                    'vendor_id'=>1,
                                    'description'=>'Imported Net Proceeds of '.$data->NetProceeds.' from Salsesforce.',
                                    'notes'=>$data->ReturnedFundsExplanation,
                                    'created_at'=>$data->created_at
                                    ]);
                }
            }
            $dispositionsItemsBar->finish();

            $dispositionItemsToAdd = '';

            ///////////////////////////////////////////////////////////////////////////////
            ////////////////// CREATE DISPOSITION INVOICES
            //////////// FIRST GET ALL THE SF DISPOSTIONS GROUPED BY PRGRAM NAME AND THEN DUE DATE
                        
            $dispositionsToInvoice = DB::table('dispositions')->select('disposition_due', 'program_id')->groupBy('program_id', 'disposition_due')->get()->all();

                        
            $bar->advance();
            $this->line(PHP_EOL.'Creating '.$dispositionsToInvoice.' Disposition Invoices');
            $dispositionsInvoiceBar = $this->output->createProgressBar(count($dispositionsToInvoice));
            foreach ($dispositionsToInvoice as $data) {
                $dispositionsInvoiceBar->advance();
                            
                $dispositionInvID = DB::table('disposition_invoices')->insertGetId([
                                                    'entity_id'=>$data->program_id,
                                                    'program_id'=>$data->program_id,
                                                    'account_id'=>$data->program_id,
                                                    'status_id'=>2,
                                                    'active'=>1,
                                                    'disposition_invoice_due'=>$data->disposition_due,
                                                    ]);

                // $d = Disposition::find($dispositionInvID);
                           
                ////// CREATE A CROSS REFERENCE FOR EVERY MATCHING DISPOSITION TO THIS INVOICE PROGRAM_ID AND DUE DATE
                $matchingDispositions = DB::table('dispositions')->select('id as disposition_id')->where('program_id', $data->program_id)->where('disposition_due', $data->disposition_due)->get()->all();

                foreach ($matchingDispositions as $disp2inv) {
                    DB::table('dispositions_to_invoices')->insert([
                                'disposition_id'=>$disp2inv->disposition_id,
                                'disposition_invoice_id'=>$dispositionInvID
                                ]);
                }
                $matchingDispositions = '';
            }

            $dispositionsInvoiceBar->finish();

            ///////////////////////////////////////////////////////////////////////////////
            ////////////////// PUT IN DISPOSITION INVOICE TRANSACTIONS
            ////////////

            // get all the disposition invoices
            $dispositionsInvoiced = DB::table('dispositions_to_invoices')->get()->all();
            // get the recapture paid status for each disposition on that invoice
            $bar->advance();
            $this->line(PHP_EOL.'Creating '.count($dispositionsInvoiced).' Disposition Invoice Transactions');
            $dispositionsInvoiceTransactionsBar = $this->output->createProgressBar(count($dispositionsInvoiced));
            foreach ($dispositionsInvoiced as $di) {
                $dispositionsInvoiceTransactionsBar->advance();
                // look up the disposition informaiton
                $disposition = DB::table('dispositions')->join('sf_reimbursements', 'dispositions.sf_parcel_id', '=', 'sf_reimbursements.PropertyIDRecordID')->where('dispositions.id', $di->disposition_invoice_id)->first();
                if ($disposition->RecapturePaid == 1) {
                    // recapture paid - insert transaction for it
                    DB::table('transactions')->insert([
                                    'account_id'=>$disposition->program_id,
                                    'credit_debit'=>'c',
                                    'amount'=>$disposition->RecapturedOwed,
                                    'transaction_category_id'=>6,
                                    'type_id'=>2,
                                    'link_to_type_id'=>$di->disposition_invoice_id,
                                    'status_id'=>2,
                                    'owner_id'=>$disposition->program_id,
                                    'owner_type'=>'program',
                                    'date_entered'=>$disposition->release_date,
                                    'date_cleared'=>$disposition->release_date,
                                    'created_at'=>$disposition->created_at,
                                    'transaction_note'=>'Legacy Disposition Invoice Payment Transaction for parcel '.$disposition->parcel_id.' translated from Salesforce data. This was done by creating individual invoices containing grouped dispositions that have a matching due date interpreted by either the created date or the released date (which ever was earlier, as Sales Force would change the created date to the last date the disposition was modified). Because this concept of grouping the dispositions together in a single invoice (like reimbursements) is new - payments against dispositions were done parcel by parcel prior to the upgrade to Allita Blight Manager. Thus each invoice will have multiple payments recorded against it, and may show pending payment as a status if dispositions within it are still outstanding. Imported '.date('m/d/Y', time())
                                    ]);
                            
                    DB::table('parcels')->where('id', $disposition->parcel_id)->update(['landbank_property_status_id'=>42,'hfa_property_status_id'=>32]);
                // $p = Parcel::find($disposition->parcel_id);
                } else {
                    DB::table('parcels')->where('id', $disposition->parcel_id)->update(['landbank_property_status_id'=>41,'hfa_property_status_id'=>31]);
                    //  $p = Parcel::find($disposition->parcel_id);
                }
            }
            $dispositionsInvoiced = '';
            $dispositionsInvoiceTransactionsBar->finish();


            $recaptureInvoices = DB::table('sf_reimbursements')->leftJoin('dispositions', 'sf_reimbursements.PropertyIDRecordID', '=', 'dispositions.sf_parcel_id')
                    ->select('ProgramProgramName')
                    ->where('disposition_due', null)->where('RecapturedOwed', '>', 0)
                    ->groupBy('ProgramProgramName')
                    ->get()->all();

            /// Should return one invoice to be made per program.
            $bar->advance();
            $this->line(PHP_EOL.'Creating '.count($recaptureInvoices).' Recapture Invoices');
            $recaptureInvoiceBar = $this->output->createProgressBar(count($recaptureInvoices));
            foreach ($recaptureInvoices as $recapInvoice) {
                $recaptureInvoiceBar->advance();
                //// GET THE PROGRAM ID FOR THIS DUDE
                $programId = DB::table('programs')->select('id', 'entity_id', 'program_name')->where('program_name', $recapInvoice->ProgramProgramName)->first();
                //// CREATE THE INVOICES FOR THE RECAPTURES
                        
                $recacptureInvoiceId = DB::table('recapture_invoices')->insertGetId([
                                                                                        
                                                                                        'entity_id'=>$programId->entity_id,
                                                                                        'program_id'=>$programId->id,
                                                                                        'account_id'=>$programId->id,
                                                                                        'status_id'=>2,
                                                                                        'recapture_due_date'=>null,
                                                                                        'active'=>1
                                                                                        ]);
                        
                //$ro = \App\RecaptureInvoice::find($recacptureInvoiceId);
                      

                //// CREATE BREAKOUT RECAPTURE ITEMS FOR THE INVOICE, AND PAIR THE RECAPTURES TO THE RECAPTURE INVOICE

                //// get recaptures for this program's invoice //
                $recapturesForInvoice = DB::table('sf_reimbursements')
                            ->leftJoin('dispositions', 'sf_reimbursements.PropertyIDRecordID', '=', 'dispositions.sf_parcel_id')
                            ->join('parcels', 'parcels.sf_parcel_id', '=', 'sf_reimbursements.PropertyIDRecordID')
                            ->select('ProgramProgramName', 'parcels.id as system_parcel_id', 'parcels.parcel_id', 'RecapturedOwed', 'RecapturePaid', 'ReturnedFundsExplanation', 'parcels.sf_parcel_id', 'parcels.program_id', 'parcels.account_id', 'parcels.entity_id')
                            ->where('disposition_due', null)->where('RecapturedOwed', '>', 0)->where('ProgramProgramName', $recapInvoice->ProgramProgramName)
                            ->distinct()->get()->all();
                foreach ($recapturesForInvoice as $data) {
                    //dd($recacptureInvoiceId,$data,$recapturesForInvoice,$recaptureInvoices);
                    DB::table('recapture_items')->insert([
                                        'breakout_type'=>2,
                                        'recapture_invoice_id'=>$recacptureInvoiceId,
                                        'parcel_id'=>$data->system_parcel_id,
                                    'entity_id'=>$data->entity_id,
                                    'program_id'=>$data->program_id,
                                    'account_id'=>$data->account_id,
                                        'expense_category_id'=>8,
                                        'amount'=>$data->RecapturedOwed,
                                        'description'=>'Legacy Recapture Item',
                                        'notes'=>'Legacy recapture item from salesforce for parcel '.$data->parcel_id.' for '.$data->ProgramProgramName.'. Unable to determine actual breakout category, date of request, or date of actual payment. '.$data->ReturnedFundsExplanation,
                                        'requested'=>null,
                                        'received'=>null,
                                ]);
                    DB::table('parcels')->where('id', $data->system_parcel_id)->update(['landbank_property_status_id'=>19,'hfa_property_status_id'=>35]);
                    //$p=Parcel::find($data->system_parcel_id);
                          
                    /// put in the paid transactions for each item:
                            
                    if ($data->RecapturePaid == 1) {
                        $tid = DB::table('transactions')->insertGetId([
                                        'account_id'=>$programId->id,
                                        'credit_debit'=>'c',
                                        'amount'=>$data->RecapturedOwed,
                                        'transaction_category_id'=>2,
                                        'type_id'=>6,
                                        'link_to_type_id'=>$recacptureInvoiceId,
                                        'status_id'=>2,
                                        'owner_id'=>$programId->id,
                                        'owner_type'=>'program',
                                        'date_entered'=>'2017-01-15 00:00:00',
                                        'date_cleared'=>'2017-01-15 00:00:00',
                                        'created_at'=>'2017-01-15 00:00:00',
                                        'transaction_note'=>'DATES ARE NOT CORRECT!! Legacy Recapture Invoice Payment Transaction translated from Salesforce data. This was done by creating individual invoices containing grouped recaptures that have a matching program. Because this concept of grouping the recaptures together in a single invoice (like reimbursements) is new - payments against recaptures were done parcel by parcel prior to the upgrade to Allita Blight Manager. Thus each invoice will have multiple payments recorded against it, and may show pending payment as a status if recaptures within it are still outstanding. Salesforce did not have any dates for when the recapture was requested or paid. Imported '.date('m/d/Y', time())
                                        ]);
                                
                        //$t = Transaction::find($tid);
                             
                        DB::table('parcels')->where('id', $data->parcel_id)->update(['landbank_property_status_id'=>20,'hfa_property_status_id'=>36]);
                        //$p = Parcel::find($data->parcel_id);
                    }
                }
                $recapturesForInvoice = '';
            }
            $recaptureInvoices = '';
            $recaptureInvoiceBar->finish();

            /// put in reference ids for break out items
                    
            $requestItems = DB::table('request_items')->select('id', 'parcel_id', 'expense_category_id')->get()->all();
            $bar->advance();
            $this->line(PHP_EOL.'Putting in '.count($requestItems).' Request Items');
            $requestItemsBar = $this->output->createProgressBar(count($requestItems));
            foreach ($requestItems as $data) {
                $requestItemsBar->advance();
                /// find matching cost item
                $refId = DB::table('cost_items')
                                ->select('id')
                                ->where('parcel_id', $data->parcel_id)
                                ->where('expense_category_id', $data->expense_category_id)->first();

                DB::table('request_items')
                            ->where('id', $data->id)
                            ->update(
                                ['ref_id'=> $refId->id]
                            );
            }
            $requestItems = '';
            $requestItemsBar->finish();

            $poItems = DB::table('po_items')->select('id', 'parcel_id', 'expense_category_id')->get()->all();
            $bar->advance();
            $this->line(PHP_EOL.'Putting in '.count($poItems).' PO Items');
            $poItemsBar = $this->output->createProgressBar(count($poItems));
            foreach ($poItems as $data) {
                $poItemsBar->advance();
                /// find matching cost item
                $refId = DB::table('request_items')
                                ->select('id')
                                ->where('parcel_id', $data->parcel_id)
                                ->where('expense_category_id', $data->expense_category_id)->first();

                DB::table('po_items')
                            ->where('id', $data->id)
                            ->update(
                                ['ref_id'=> $refId->id]
                            );
            }
            $poItems = '';
            $poItemsBar->finish();

            $invItems = DB::table('invoice_items')->select('id', 'parcel_id', 'expense_category_id')->get()->all();
            $bar->advance();
            $this->line(PHP_EOL.'Putting in '.count($invItems).' Invoice Items');
            $invoiceItemsBar = $this->output->createProgressBar(count($invItems));
            foreach ($invItems as $data) {
                /// find matching cost item
                $invoiceItemsBar->advance();
                $refId = DB::table('po_items')
                                ->select('id')
                                ->where('parcel_id', $data->parcel_id)
                                ->where('expense_category_id', $data->expense_category_id)->first();

                DB::table('invoice_items')
                            ->where('id', $data->id)
                            ->update(
                                ['ref_id'=> $refId->id]
                            );
            }
            $invItems = '';
            $invoiceItemsBar->finish();
            // */
        }




       
        
        //return redirect('/validateImport');
        /// send through the lists to correct statuses
        $bar->finish();
        //$this->info('/dashboard/request_list?fixStatus=1');
        //return '/dashboard/request_list?fixStatus=1';
    }
}
