<?php

namespace App\Http\Controllers;

use App\Models\Parcel;
use Illuminate\Http\Request;
use App\Http\Controllers\FormsController as Form;
use App\Models\State;
use App\Models\Entity;
use App\Models\County;
use App\Models\ProgramRule;
use App\Models\Program;
use App\Models\Account;
use App\Models\TargetArea;
use App\Models\HowAcquired;
use App\Models\ParcelType;
use App\Models\PropertyStatusOption;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Auth;
use App\LogConverter;
use DB;

class MAParcelsController extends Controller
{
    public function __construct(){
        $this->allitapc();
    }

    public function parcelCreate(Form $form, $id = null)
    {
        $stateIds = State::pluck('id')->toArray();
        $stateNames = State::pluck('state_name')->toArray();
        $selectedState=array_fill(0, count($stateIds), 'false');
        $selectedState[35]='true';
        $entityIds = Entity::where('active', 1)->orderBy('entity_name', 'asc')->pluck('id')->toArray();
        $entityNames = Entity::where('active', 1)->orderBy('entity_name', 'asc')->pluck('entity_name')->toArray();
        $selectedEntity=array_fill(0, count($entityIds), 'false');
        $countyIds = County::pluck('id')->toArray();
        $countyNames = County::pluck('county_name')->toArray();
        $selectedCounty=array_fill(0, count($countyIds), 'false');
        $ruleIds = ProgramRule::where('active', 1)->orderBy('rules_name', 'asc')->pluck('id')->toArray();
        $ruleNames = ProgramRule::where('active', 1)->orderBy('rules_name', 'asc')->pluck('rules_name')->toArray();
        $selectedRule=array_fill(0, count($ruleIds), 'false');
        $programIds = Program::where('active', 1)->pluck('id')->toArray();
        $programNames = Program::where('active', 1)->pluck('program_name')->toArray();
        $selectedProgram=array_fill(0, count($programIds), 'false');
        $accountIds = Account::where('active', 1)->pluck('id')->toArray();
        $accountNames = Account::where('active', 1)->pluck('account_name')->toArray();
        $selectedAccount=array_fill(0, count($accountIds), 'false');
        $targetAreaIds = TargetArea::where('active', 1)->pluck('id')->toArray();
        $targetAreaNames = TargetArea::where('active', 1)->pluck('target_area_name')->toArray();
        $selectedTargetArea=array_fill(0, count($targetAreaIds), 'false');
        $howAcquiredIds = HowAcquired::where('active', 1)->pluck('id')->toArray();
        $howAcquiredNames = HowAcquired::where('active', 1)->pluck('how_acquired_option_name')->toArray();
        $selectedHowAcquired=array_fill(0, count($howAcquiredIds), 'false');
        //$parcelTypeIds = ParcelType::where('active',1)->pluck('id')->toArray();
        //$parcelTypeNames = ParcelType::where('active',1)->pluck('parcel_type_option_name')->toArray();
        //$selectedParcelType=array_fill(0,count($parcelTypeIds),'false');
        $hfaPropertyStatusIds = PropertyStatusOption::where('active', 1)->where('for', 'hfa')->orderBy('order', 'asc')->pluck('id')->toArray();
        $hfaPropertyStatusNames = PropertyStatusOption::where('active', 1)->where('for', 'hfa')->orderBy('order', 'asc')->pluck('option_name')->toArray();
        $selectedHFAStatus=array_fill(0, count($hfaPropertyStatusIds), 'false');

        
        $lbPropertyStatusIds = PropertyStatusOption::where('active', 1)->where('for', 'landbank')->orderBy('order', 'asc')->pluck('id')->toArray();
        $lbPropertyStatusNames = PropertyStatusOption::where('active', 1)->where('for', 'landbank')->orderBy('order', 'asc')->pluck('option_name')->toArray();
        $selectedLBStatus=array_fill(0, count($lbPropertyStatusIds), 'false');

        $radioHistoricalId = [1,0];
        $radioHistoricalNames = ['True','False'];
        $checkedHistorical = array_fill(0, count($radioHistoricalId), 'false');
        $parcel = Parcel::where('id', $id)->first();


        if (!$id) {
            $formRows['tag'] = $form->formBuilder("/parcel/store", "post", "application/x-www-form-urlencoded", "Create New Parcel", "plus-circle");
            $formRows['rows']['ele1'] = $form->text(['Parcel ID','parcel_id','','Enter parcel id','required']);
            $formRows['rows']['ele2'] = $form->selectBox(['Select Program','program_id',$programIds,$programNames,$selectedProgram,'','required']);
            $formRows['rows']['ele3'] = $form->selectBox(['Select Entity','entity_id',$entityIds,$entityNames,$selectedEntity,'','required']);
            $formRows['rows']['ele4'] = $form->selectBox(['Select Account','account_id',$accountIds,$accountNames,$selectedAccount,'','required']);
            $formRows['rows']['ele5'] = $form->text(['Street Address','street_address','','Enter parcel\'s street address','required']);
            $formRows['rows']['ele6'] = $form->text(['City','city','','Enter parcel\'s city','required']);
            $formRows['rows']['ele7'] = $form->selectBox(['Select State','state_id',$stateIds,$stateNames,$selectedState,'','required']);
            $formRows['rows']['ele8'] = $form->text(['Zip Code','zip','','Enter parcel\'s zip code','required']);
            $formRows['rows']['ele9'] = $form->selectBox(['Select County','county_id',$countyIds,$countyNames,$selectedCounty,'','required']);
            $formRows['rows']['ele10'] = $form->selectBox(['Select Target Area','target_area_id',$targetAreaIds,$targetAreaNames,$selectedTargetArea,'','required']);
            $formRows['rows']['ele11'] = $form->text(['Sale Price','sale_price','','Enter parcel\'s sale price','required']);
            $formRows['rows']['ele12'] = $form->selectBox(['How is parcel acquired','how_acquired_id',$howAcquiredIds,$howAcquiredNames,$selectedHowAcquired,'','required']);
            $formRows['rows']['ele13']= $form->textArea(['How Acquired Explanation','how_acquired_notes','','','']);
            $formRows['rows']['ele14'] = $form->radio(['Historic/Historic district','historic_significance',$radioHistoricalId,$radioHistoricalNames,$checkedHistorical,'required']);
            $formRows['rows']['ele15'] = $form->text(['Number of Units','units','','Enter the number of units this parcel has for this address.','required']);
            
            $formRows['rows']['ele16'] = $form->selectBox(['Select Program Rule','rule_id',$ruleIds,$ruleNames,$selectedRule,'','required']);
            $formRows['rows']['ele18'] = $form->selectBox(['Select LB Status','landbank_property_status_id',$lbPropertyStatusIds,$lbPropertyStatusNames,$selectedLBStatus,'','required']);
            $formRows['rows']['ele19'] = $form->selectBox(['Select HFA Status','hfa_property_status_id',$lbPropertyStatusIds,$lbPropertyStatusNames,$selectedLBStatus,'','required']);
            $formRows['rows']['ele17'] = $form->submit(['Create Parcel']);
            
            return view('pages.formtemplate', ['formRows'=>$formRows]);
        } else {
            if (Auth::user()->canEditParcels()) {
                // Michael built this using the assumption that a selected value's id would line up with the array's key --- bad bad bad...
                $radioLbValidatedIds = [0=>0, 1=>1];
                $radioLbValidatedNames = [0=>"NO", 1=>"YES"];
                $key = array_search($parcel->entity_id, $entityIds); // returns the key of the array.
                $selectedEntity[intval($key)]= 'true';

                $key = array_search($parcel->program_id, $programIds);
                $selectedProgram[intval($key)]= 'true';

                $key = array_search($parcel->account_id, $accountIds);
                $selectedAccount[$key]= 'true';

                $key = array_search($parcel->state_id, $stateIds);
                $selectedState[intval($key)]= 'true';

                $key = array_search($parcel->county_id, $countyIds);
                $selectedCounty[intval($key)]= 'true';
                
                $key = array_search($parcel->target_area_id, $targetAreaIds);
                $selectedTargetArea[intval($key)]= 'true';
                
                $key = array_search($parcel->how_acquired_id, $howAcquiredIds);
                $selectedHowAcquired[intval($key)]= 'true';
                
                // $key = array_search($parcel->parcel_type_id, $parcelTypeIds);
                // $selectedParcelType[intval($key)]= 'true';
                
                $key = array_search($parcel->landbank_property_status_id, $lbPropertyStatusIds);
                $selectedLBStatus[intval($key)]= 'true';
                
                $key = array_search($parcel->hfa_property_status_id, $hfaPropertyStatusIds);
                $selectedHFAStatus[intval($key)]= 'true';
                
                $key = array_search($parcel->program_rules_id, $ruleIds);
                $selectedRule[intval($key)]= 'true';
                

                $checkedHistorical = intval($parcel->historic_significance_or_district==0)?['false','true']:['true','false'];
                $checkedLbValidated = intval($parcel->lb_validated==0)?['true','false']:['false','true'];

                $formRows['tag'] = $form->formBuilder("/parcel/store/".$parcel->id, "post", "application/x-www-form-urlencoded", "Edit Parcel", "plus-circle");
                if (Auth::user()->entity_type == 'hfa') {
                    $formRows['rows']['ele1'] = $form->text(['Parcel ID','parcel_id',$parcel->parcel_id,'Enter parcel id','required']);
                    $formRows['rows']['ele2'] = $form->selectBox(['Select Program','program_id',$programIds,$programNames,$selectedProgram,'','required']);
                    $formRows['rows']['ele3'] = $form->selectBox(['Select Entity','entity_id',$entityIds,$entityNames,$selectedEntity,'','required']);
                    $formRows['rows']['ele4'] = $form->selectBox(['Select Account','account_id',$accountIds,$accountNames,$selectedAccount,'','required']);
                    $formRows['rows']['ele5'] = $form->text(['Street Address','street_address',$parcel->street_address,'Enter parcel\'s street address','required']);
                    $formRows['rows']['ele6'] = $form->text(['City','city',$parcel->city,'Enter parcel\'s city','required']);
                    $formRows['rows']['ele7'] = $form->selectBox(['Select State','state_id',$stateIds,$stateNames,$selectedState,'','required']);
                    $formRows['rows']['ele8'] = $form->text(['Zip Code','zip',$parcel->zip,'Enter parcel zip code','required']);
                    $formRows['rows']['ele9'] = $form->selectBox(['Select County','county_id',$countyIds,$countyNames,$selectedCounty,'','required']);
                    $formRows['rows']['ele10'] = $form->selectBox(['Select Target Area','target_area_id',$targetAreaIds,$targetAreaNames,$selectedTargetArea,'','required']);
                    $formRows['rows']['ele11'] = $form->text(['Sale Price','sale_price',$parcel->sale_price,'Enter parcel\'s sale price','required']);
                    $formRows['rows']['ele12'] = $form->selectBox(['How is parcel acquired','how_acquired_id',$howAcquiredIds,$howAcquiredNames,$selectedHowAcquired,'','required']);
                    $formRows['rows']['ele13']= $form->textArea(['How Acquired Explanation','how_acquired_notes',$parcel->how_acquired_explanation,'','']);
                    $formRows['rows']['ele14'] = $form->radio(['Historic/Historic district','historic_significance',$radioHistoricalId,$radioHistoricalNames,$checkedHistorical,'required']);
                    $formRows['rows']['ele15'] = $form->text(['Number of Units','units',$parcel->units,'Enter the number of units this parcel has for this address.','required']);
                    //$formRows['rows']['ele15'] = $form->selectBox(['Select Parcel Type','parcel_type_id',$parcelTypeIds,$parcelTypeNames,$selectedParcelType,'','required']);
                    $formRows['rows']['ele16'] = $form->selectBox(['Select Program Rule','rule_id',$ruleIds,$ruleNames,$selectedRule,'','required']);
                    $formRows['rows']['ele18'] = $form->selectBox(['Select LB Status','landbank_property_status_id',$lbPropertyStatusIds,$lbPropertyStatusNames,$selectedLBStatus,'','required']);
                    $formRows['rows']['ele19'] = $form->selectBox(['Select HFA Status','hfa_property_status_id',$hfaPropertyStatusIds,$hfaPropertyStatusNames,$selectedHFAStatus,'','required']);
                    $formRows['rows']['ele20'] = $form->radio(['Validated Parcel','lb_validated',$radioLbValidatedIds,$radioLbValidatedNames,$checkedLbValidated,'required']);
                    $formRows['rows']['ele17'] = $form->submit(['Update Parcel']);
                } elseif (Auth::user()->entity_type == 'landbank') {
                    $program = Program::where('id', $parcel->program_id)->first();
                    $countyIds = Program::where('program_name', $program->program_name)->pluck('county_id')->all();
                    $countyNames = County::whereIn('id', $countyIds)->pluck('county_name')->all();
                    $formRows['rows']['ele1'] = $form->text(['Parcel ID','parcel_id',$parcel->parcel_id,'Enter parcel id','required']);
                    $formRows['rows']['ele2'] = $form->hidden(['program_id',$parcel->program_id]);
                    $formRows['rows']['ele3'] = $form->hidden(['entity_id',$selectedEntity]);
                    $formRows['rows']['ele4'] = $form->hidden(['account_id',$selectedAccount]);
                    $formRows['rows']['ele5'] = $form->text(['Street Address','street_address',$parcel->street_address,'Enter parcel\'s street address','required']);
                    $formRows['rows']['ele6'] = $form->text(['City','city',$parcel->city,'Enter parcel\'s city','required']);
                    $formRows['rows']['ele7'] = $form->selectBox(['Select State','state_id',$stateIds,$stateNames,$selectedState,'','required']);
                    $formRows['rows']['ele8'] = $form->text(['Zip Code','zip',$parcel->zip,'Enter parcel zip code','required']);
                    $formRows['rows']['ele9'] = $form->hidden(['county_id',$selectedCounty]);
                    $formRows['rows']['ele10'] = $form->selectBox(['Select Target Area','target_area_id',$targetAreaIds,$targetAreaNames,$selectedTargetArea,'','required']);
                    $formRows['rows']['ele11'] = $form->text(['Sale Price','sale_price',$parcel->sale_price,'Enter parcel\'s sale price','required']);
                    $formRows['rows']['ele12'] = $form->selectBox(['How is parcel acquired','how_acquired_id',$howAcquiredIds,$howAcquiredNames,$selectedHowAcquired,'','required']);
                    $formRows['rows']['ele13']= $form->textArea(['How Acquired Explanation','how_acquired_notes',$parcel->how_acquired_explanation,'','']);
                    $formRows['rows']['ele14'] = $form->radio(['Historic/Historic district','historic_significance',$radioHistoricalId,$radioHistoricalNames,$checkedHistorical,'required']);
                    $formRows['rows']['ele15'] = $form->text(['Number of Units','units',$parcel->units,'Enter the number of units this parcel has for this address.','required']);
                    //$formRows['rows']['ele15'] = $form->selectBox(['Select Parcel Type','parcel_type_id',$parcelTypeIds,$parcelTypeNames,$selectedParcelType,'','required']);
                    $formRows['rows']['ele16'] = $form->hidden(['rule_id',$parcel->program_rules_id]);
                    $formRows['rows']['ele18'] = $form->hidden(['landbank_property_status_id',$parcel->landbank_property_status_id]);
                    $formRows['rows']['ele19'] = $form->hidden(['hfa_property_status_id',$parcel->hfa_property_status_id]);
                    $formRows['rows']['ele17'] = $form->submit(['Update Parcel']);
                }
                return view('pages.formtemplate', ['formRows'=>$formRows]);
            }
        }
    }

    public function parcelStore(Request $request, $id = null)
    {
        $this->validate($request, [
            'parcel_id'=>'required|string',
            'program_id'=>'required|numeric',
            'entity_id'=>'required|numeric',
            'account_id'=>'required|numeric',
            'street_address'=>'required|string',
            'city'=>'required|string',
            'state_id'=>'required|numeric',
            'zip'=>'required|string',
            'county_id'=>'required|numeric',
            'target_area_id'=>'required|numeric',
            'sale_price'=>'required|string',
            'how_acquired_id'=>'required|numeric',
            'historic_significance'=>'required',
            'units'=>'required|numeric',
            'rule_id'=>'required|numeric',
            'hfa_property_status_id'=>'required|numeric',
            'landbank_property_status_id'=>'required|numeric'
        ]);


        if (!$id) {
            $parcel = Parcel::create([
                'parcel_id'=>Input::get('parcel_id'),
                'program_id'=>Input::get('program_id'),
                'owner_id'=>Input::get('program_id'),
                'entity_id'=>Input::get('entity_id'),
                'account_id'=>Input::get('account_id'),
                'street_address'=>Input::get('street_address'),
                'city'=>Input::get('city'),
                'state_id'=>Input::get('state_id'),
                'zip'=>Input::get('zip'),
                'county_id'=>Input::get('county_id'),
                'target_area_id'=>Input::get('target_area_id'),
                'sale_price'=>Input::get('sale_price'),
                'how_acquired_id'=>Input::get('how_acquired_id'),
                'how_acquired_explanation'=>Input::get('how_acquired_notes'),
                'historic_significance_or_district'=>Input::get('historic_significance'),
                'units'=>Input::get('units'),
                'program_rules_id'=>Input::get('rule_id'),
            ]);
            if (http_response_code()==200) {
                $lc = new LogConverter('parcel', 'create');
                $lc->setFrom(Auth::user())->setTo($parcel)->setDesc(Auth::user()->email . ' Created parcel ' . $parcel->parcel_id)->save();
                return response('Parcel: '.$parcel->parcel_id.' successfully created.
                    <script>
                    $(\'.uk-modal-close-default\').trigger(\'click\');
                    window.location=\'dashboard?tab=5\';
                    </script>
                ');
            }
        } else {
            $parcelold = Parcel::find($id);
            $lbValidated = 1;
            $message = "";
            
            if ($parcelold->lb_validated == 0) {
                // it was never validated - keep the validation set to 0
                $lbValidated = 0;
                $message = " It appears that you still need to validate this parcel, you should see a button at the top just above the parcel details to do that.";
            }
            if (!is_null(Input::get('lb_validated'))) {
                $lbValidated = Input::get('lb_validated');
                if (Input::get('lb_validated') == 1) {
                    $message = "";
                } else {
                    $message = "Please be sure to run the validation on this property. You should see a button at the top just above the parcel details to do that.";
                }
            }
            /// check to see if they are changing the address
            if ($parcelold->street_address != Input::get('street_address')) {
                $lbValidated = 0;
            }
            if ($parcelold->city != Input::get('city')) {
                $lbValidated = 0;
            }
            if ($parcelold->state_id != Input::get('state_id')) {
                $lbValidated = 0;
            }
            if ($parcelold->zip != Input::get('zip')) {
                $lbValidated = 0;
            }
            if ($parcelold->county_id != Input::get('county_id')) {
                $lbValidated = 0;
            }
            if ($lbValidated == 0 && $message == "") {
                $message = " It appears that you may need to revalidate this parcel because the address changed. You should see a button at the top just above the parcel details to do that.";
            }


            if (!is_null($parcelold->sf_parcel_id) && $lbValidated == 0) {
                // this is a legacy parcel - create a import for it.
                // check that there is not already an import for this
                $existingImport = DB::table('import_rows')->where('row_id', $parcelold->id)->where('table_name', 'parcels')->count();
                if ($existingImport < 1) {
                    // no import for it
                    /// create import
                    $import_id = DB::table('imports')->insertGetId([
                        'created_at'=> $parcelold->created_at,
                        'updated_at'=> date('Y-m-d H:i:s', time()),
                        'user_id'=>Auth::user()->id,
                        'entity_id'=>$parcelold->entity_id,
                        'program_id'=>$parcelold->program_id,
                        'account_id'=>$parcelold->account_id,
                        'original_file'=>"/not_a_file_import",
                        'validated'=>0
                        ]);
                    DB::table('import_rows')->insert([
                        'created_at'=> $parcelold->created_at,
                        'updated_at'=> date('Y-m-d H:i:s', time()),
                        'table_name'=> 'parcels',
                        'import_id'=> $import_id,
                        'row_id'=>$parcelold->id
                        ]);
                }
            }

            Parcel::where('id', $id)->update([
                'parcel_id'=>Input::get('parcel_id'),
                'program_id'=>Input::get('program_id'),
                'owner_id'=>Input::get('program_id'),
                'entity_id'=>Input::get('entity_id'),
                'account_id'=>Input::get('account_id'),
                'street_address'=>Input::get('street_address'),
                'city'=>Input::get('city'),
                'state_id'=>Input::get('state_id'),
                'zip'=>Input::get('zip'),
                'county_id'=>Input::get('county_id'),
                'target_area_id'=>Input::get('target_area_id'),
                'sale_price'=>Input::get('sale_price'),
                'how_acquired_id'=>Input::get('how_acquired_id'),
                'how_acquired_explanation'=>Input::get('how_acquired_notes'),
                'historic_significance_or_district'=>Input::get('historic_significance'),
                'units'=>Input::get('units'),
                'program_rules_id'=>Input::get('rule_id'),
                'lb_validated'=>$lbValidated,
                'hfa_property_status_id'=>Input::get('hfa_property_status_id'),
                'landbank_property_status_id'=>Input::get('landbank_property_status_id')
            ]);
            $p = Parcel::find($id);

            
            $parcelold = $parcelold->toArray();
            $parcelnew = $p->toArray();
            $lc = new LogConverter('parcel', 'update');
            $lc->setFrom(Auth::user())->setTo($p)->setDesc(Auth::user() . ' Updated parcel area ' . $p->parcel_id);
            $lc->smartAddHistory($parcelold, $parcelnew);
            $lc->save();
            return response('I updated the parcel for you.'.$message.'<script>
                    
                    $(\'#parcel-subtab-1\').trigger(\'click\');
                    </script>
            ');
        }
    }
}
