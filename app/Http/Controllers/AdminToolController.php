<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Amenity;
use App\Models\AmenityHud;
use App\Models\HudInspectableArea;
use App\Models\FindingType;
use App\Models\FindingTypeBoilerplate;
use App\Models\HudFindingType;
use App\Models\DefaultFollowup;
use App\Models\Boilerplate;
use App\Models\DocumentCategory;
use App\Models\Program;
use App\Models\State;
use App\Models\County;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use App\Http\Controllers\FormsController as Form;
use Illuminate\Support\Facades\Input;
use App\LogConverter;
use \Auth;
use Session;
use App\Models\User as UserModel;
use Illuminate\Support\Facades\DB;

class AdminToolController extends Controller
{

    public function __construct(Request $request)
    {
        // $this->middleware('auth');
        //Auth::onceUsingId(2);
        //
        if (env('APP_DEBUG_NO_DEVCO') == 'true') {
            //Auth::onceUsingId(1); // TEST BRIAN
            Auth::onceUsingId(286); // TEST BRIAN
        }
    }

    /**
     * Program Create
     *
     * @param \App\Http\Controllers\FormsController $form
     * @param null                                  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // public function programCreate(Form $form, $id = null)
    // {
    //     $entityIds = Entity::where('active', 1)->orderBy('entity_name', 'asc')->pluck('id')->toArray();
    //     $entityNames = Entity::where('active', 1)->orderBy('entity_name', 'asc')->pluck('entity_name')->toArray();
    //     $selectedEntity=array_fill(0, count($entityIds), 'false');
    //     $countyIds = County::pluck('id')->toArray();
    //     $countyNames = County::pluck('county_name')->toArray();
    //     $selectedCounty=array_fill(0, count($countyIds), 'false');
    //     $ruleIds = ProgramRule::where('active', 1)->orderBy('rules_name', 'asc')->pluck('id')->toArray();
    //     $ruleNames = ProgramRule::where('active', 1)->orderBy('rules_name', 'asc')->pluck('rules_name')->toArray();
    //     $selectedRule=array_fill(0, count($ruleIds), 'false');
    //     $program = Program::where('id', $id)->first();
    //     if (!$id) {
    //         $formRows['tag'] = $form->formBuilder("/admin/program/store", "post", "application/x-www-form-urlencoded", "Create New Program", "plus-circle");
    //         $formRows['rows']['ele1'] = $form->text(['Program Name','program_name','','Enter program name','required']);
    //         $formRows['rows']['ele3'] = $form->selectBox(['Select Entity','entity_id',$entityIds,$entityNames,$selectedEntity,'','required']);
    //         $formRows['rows']['ele4'] = $form->selectBox(['Select County','county_id',$countyIds,$countyNames,$selectedCounty,'','required']);
    //         $formRows['rows']['ele5'] = $form->selectBox(['Select Rule','rule_id',$ruleIds,$ruleNames,$selectedRule,'','required']);
    //         $formRows['rows']['ele16'] = $form->submit(['Create Program']);
    //         return view('pages.formtemplate', ['formRows'=>$formRows]);
    //     } else {
    //         $entity = Entity::find($program->owner_id);
    //         $rules = ProgramRule::find($program->default_program_rules_id);
    //         $key = array_search($entity->entity_name, $entityNames);
    //         $selectedOwner[$key]= 'true';
    //         $key = array_search($entity->entity_name, $entityNames);
    //         $selectedEntity[$key]= 'true';
    //         $selectedCounty[intval($program->county_id)-1]= 'true';
    //         $key = array_search($rules->rules_name, $ruleNames);
    //         $selectedRule[$key]= 'true';
    //         // see if the entity is active
    //         if ($entity->active == 0) {
    //             $selectedEntity[] = 'true';
    //             $entityIds[] = $entity->id;
    //             $entityNames[] = "Deactivated! [".$entity->entity_name."]";
    //         }
    //         if ($rules->active == 0) {
    //             $selectedRule[] = 'true';
    //             $ruleIds[] = $rules->id;
    //             $ruleNames[] = "Deactivated! [".$rules->rules_name."]";
    //         }
    //         $formRows['tag'] = $form->formBuilder("/admin/program/store/".$program->id, "post", "application/x-www-form-urlencoded", "Edit Program", "edit");
    //         $formRows['rows']['ele1'] = $form->text(['Program Name','program_name',$program->program_name,'','required']);
    //         $formRows['rows']['ele3'] = $form->selectBox(['Select Entity','entity_id',$entityIds,$entityNames,$selectedEntity,'','required']);
    //         $formRows['rows']['ele4'] = $form->selectBox(['Select County','county_id',$countyIds,$countyNames,$selectedCounty,'','required']);
    //         $formRows['rows']['ele5'] = $form->selectBox(['Select Rule','rule_id',$ruleIds,$ruleNames,$selectedRule,'','required']);
    //         $formRows['rows']['ele16'] = $form->submit(['Update Program']);
    //         return view('pages.formtemplate', ['formRows'=>$formRows]);
    //     }
    // }

    /**
     * Get Document IDs
     *
     * @param $program_rule_id
     * @param $docType
     *
     * @return mixed
     */
    // private function getDocumentIds($program_rule_id, $docType)
    // {
    //     $requiredDocId = DB::select('select id from expense_categories WHERE LOWER(expense_category_name) LIKE ?', ['%'.$docType.'%']);

    //     $docRuleId = DocumentRule::where('program_rules_id', $program_rule_id)->where('expense_category_id', $requiredDocId[0]->id)->pluck('id');
    //     if (count($docRuleId)) {
    //         $getDocEntries = DocumentRuleEntry::where('document_rule_id', $docRuleId)->select('document_category_id')->distinct()->get();
    //         return $getDocEntries;
    //     } else {
    //         return null;
    //     }
    // }

    
    /**
     * Joined Doc Rules Expense
     *
     * current db structure uses acquisition as 2, greening as 5 and administration as 7.
     * if db structure changes, test with this and build
     *
     * @param $id
     *
     * @return mixed
     */
    // protected function joinedDocRulesExpense($id)
    // {
    //     $et_DocExp = DocumentRule::join('expense_categories', 'document_rules.expense_category_id', '=', 'expense_categories.id')
    //         ->where('document_rules.id', $id)
    //         ->select('document_rules.amount', 'expense_categories.id', 'expense_categories.expense_category_name')->get();
    //     return $et_DocExp;
    // }

    /**
     * Merge IDs
     *
     * @param $array
     *
     * @return array
     */
    // protected function mergeIds($array)
    // {
    //     $i = 0;
    //     $merged = [];
    //     if (is_array($array) && count($array)) {
    //         while ($i<count($array)) {
    //             $merged[$i]=$array[$i]['document_category_id'];
    //             $i++;
    //         }
    //     }
    //     return $merged;
    // }

    /**
     * Get Required Document IDs
     *
     * @param $program_rule_id
     *
     * @return array
     */
    // protected function getRequiredDocumentIds($program_rule_id)
    // {
    //     return $this->mergeIds($this->getRequiredDocument($program_rule_id));
    // }

    /**
     * @param $program_rule_id
     *
     * @return array
     */
    // protected function getAcquiredDocumentIds($program_rule_id)
    // {
    //     return $this->mergeIds($this->getAcquiredDocument($program_rule_id));
    // }

    /**
     * Get Pre Demo Document IDs
     *
     * @param $program_rule_id
     *
     * @return array
     */
    // protected function getPreDemoDocumentIds($program_rule_id)
    // {
    //     return $this->mergeIds($this->getPreDemoDocument($program_rule_id));
    // }

    /**
     * Get Demolition Document IDs
     *
     * @param $program_rule_id
     *
     * @return array
     */
    // protected function getDemolitionDocumentIds($program_rule_id)
    // {
    //     return $this->mergeIds($this->getDemolitionDocument($program_rule_id));
    // }

    /**
     * Get Greening Document IDs
     *
     * @param $program_rule_id
     *
     * @return array
     */
    // protected function getGreeningDocumentIds($program_rule_id)
    // {
    //     return $this->mergeIds($this->getGreeningDocument($program_rule_id));
    // }

    /**
     * Get Maintenance Document IDs
     *
     * @param $program_rule_id
     *
     * @return array
     */
    // protected function getMaintenanceDocumentIds($program_rule_id)
    // {
    //     return $this->mergeIds($this->getMaintenanceDocument($program_rule_id));
    // }

    /**
     * Get Administration Document IDs
     *
     * @param $program_rule_id
     *
     * @return array
     */
    // protected function getAdministrationDocumentIds($program_rule_id)
    // {
    //     return $this->mergeIds($this->getAdministrationDocument($program_rule_id));
    // }

    /**
     * Get Other Document IDs
     *
     * @param $program_rule_id
     *
     * @return array
     */
    // protected function getOtherDocumentIds($program_rule_id)
    // {
    //     return $this->mergeIds($this->getOtherDocument($program_rule_id));
    // }

    /**
     * Get NIP Document IDs
     * @param $program_rule_id
     *
     * @return array
     */
    // protected function getNIPDocumentIds($program_rule_id)
    // {
    //     return $this->mergeIds($this->getNIPDocument($program_rule_id));
    // }

    /**
     * Rule Create
     *
     * @param \App\Http\Controllers\FormsController $form
     * @param null                                  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // public function ruleCreate(Form $form, $id = null)
    // {
    //     $docIds = DocumentCategory::where('active', 1)->pluck('id')->toArray();
    //     $docNames = DocumentCategory::where('active', 1)->pluck('document_category_name')->toArray();
    //     $selectedDoc['required']=array_fill(0, count($docIds), 0);
    //     $selectedDoc['acquisition']=array_fill(0, count($docIds), 0);
    //     $selectedDoc['pre_demo']=array_fill(0, count($docIds), 0);
    //     $selectedDoc['demolition']=array_fill(0, count($docIds), 0);
    //     $selectedDoc['greening']=array_fill(0, count($docIds), 0);
    //     $selectedDoc['maintenance']=array_fill(0, count($docIds), 0);
    //     $selectedDoc['administration']=array_fill(0, count($docIds), 0);
    //     $selectedDoc['other']=array_fill(0, count($docIds), 0);
    //     $selectedDoc['nip']=array_fill(0, count($docIds), 0);
    //     $program_rule = ProgramRule::with(['documentRules','reimbursementRules'])->where('id', $id)->first();

    //     if (!$id) {
    //         $formRows['tag'] = $form->formBuilder("/admin/rule/store", "post", "application/x-www-form-urlencoded", "Create New Rule", "plus-circle");
    //         $formRows['rows']['ele1'] = $form->text(['Rule Name', 'rule_name', '', 'Enter rule name', '']);
    //         $formRows['rows']['ele2'] = $form->text(['# Maintenance Months (for the label only)', 'maintenance_recap_pro_rate', '', 'Enter number of maintenance months', '']);
    //         $formRows['rows']['ele3'] = $form->text(['Imputed Cost Per Parcel', 'imputed_cost_parcel', '', 'Enter imputed cost per parcel', '']);

    //         $formRows['rows']['ele4'] = $form->multipleText_wCheckbox(
    //             'Acquisition',
    //             [['acquisition_advance', '1', 'Acquisition advance', '',''], ['acquisition_max_advance', '', 'Acquisition max advance', ''],
    //                 ['acquisition_max', '', 'Acquisition maximum (0 = No Max)', ''], ['acquisition_min', '', 'Acquisition minimum', '']],
    //             4
    //         );
    //         $formRows['rows']['ele5'] = $form->multipleText_wCheckbox(
    //             'Pre-Demolition',
    //             [['pre_demolition_advance', '1', 'Pre demolition advance', '',''], ['pre_demolition_max_advance', '', 'Pre demolition max advance', ''],
    //                 ['pre_demolition_max', '', 'Pre demolition maximum (0 = No Max)', ''], ['pre_demolition_min', '', 'Pre demolition minimum', '']],
    //             4
    //         );
    //         $formRows['rows']['ele6'] = $form->multipleText_wCheckbox(
    //             'Demolition',
    //             [['demolition_advance', '1', 'Demolition advance', '',''], ['demolition_max_advance', '', 'Demolition max advance', ''],
    //                 ['demolition_max', '', 'Demolition maximum (0 = No Max)', ''], ['demolition_min', '', 'Demolition minimum', '']],
    //             4
    //         );
    //         $formRows['rows']['ele7'] = $form->multipleText_wCheckbox(
    //             'Greening',
    //             [['greening_advance', '1', 'Greening advance', '',''], ['greening_max_advance', '', 'Greening max advance', ''],
    //                 ['greening_max', '', 'Greening maximum (0 = No Max)', ''], ['greening_min', '', 'Greening minimum', '']],
    //             4
    //         );
    //         $formRows['rows']['ele8'] = $form->multipleText_wCheckbox(
    //             'Maintenance',
    //             [['maintenance_advance', '1', 'Maintenance advance', '',''], ['maintenance_max_advance', '', 'Maintenance max advance', ''],
    //                 ['maintenance_max', '', 'Maintenance maximum (0 = No Max)', ''], ['maintenance_min', '', 'Maintenance minimum', '']],
    //             4
    //         );
    //         $formRows['rows']['ele9'] = $form->multipleText_wCheckbox(
    //             'Administration',
    //             [['administration_advance', '1', 'Administration advance', '',''], ['administration_max_advance', '', 'Administration max advance', ''],
    //                 ['administration_max_percent', '', 'Administration maximum percent', ''], ['administration_min_percent', '', 'Administration minimum percent', '']],
    //             4
    //         );
    //         $formRows['rows']['ele10'] = $form->multipleText_wCheckbox(
    //             'Other',
    //             [['other_advance', '1', 'Other advance', '',''], ['other_max_advance', '', 'Other max advance', ''],
    //                 ['other_max', '', 'Other maximum (0 = No Max)', ''], ['other_min', '', 'Other minimum', '']],
    //             4
    //         );
    //         $formRows['rows']['ele11'] = $form->multipleText_wCheckbox(
    //             'Nip Loan PayOff',
    //             [['nip_loan_payoff_advance', '1', 'Nip loan payoff advance', '',''], ['nip_loan_payoff_max_advance', '', 'Nip loan payoff max advance', ''],
    //                 ['nip_loan_payoff_max', '', 'Nip loan payoff maximum (0 = No Max)', ''], ['nip_loan_payoff_min', '', 'Nip loan payoff minimum', '']],
    //             4
    //         );
    //         $formRows['rows']['ele12'] = $form->multipleText(
    //             [['min_units[]', '', 'Minimum units', ''], ['max_units[]', '', 'Maximum units', ''],
    //                 ['max_reimbursement[]', '', 'Max. reimbursement', '']],
    //             3
    //         );
    //         $formRows['rows']['ele13'] = $form->newDocRule13(
    //             'Required Documents',
    //             10,
    //             'Required',
    //             ['required_documents', 'options' => ['option_values' => $docIds, 'option_names' => $docNames,'selected'=>$selectedDoc['required']]]
    //         );
    //         $formRows['rows']['ele14'] = $form->newDocRule(
    //             'Acquisition Documents',
    //             2,
    //             'Acquisition',
    //             ['acquisition_amount', '', 'Enter trigger amount'],
    //             ['acquisition_documents', 'options' => ['option_values' => $docIds, 'option_names' => $docNames,'selected'=>$selectedDoc['acquisition']]]
    //         );
    //         $formRows['rows']['ele15'] = $form->newDocRule(
    //             'Pre Demolition Documents',
    //             3,
    //             'Pre Demolition',
    //             ['pre_demo_amount', '', 'Enter trigger amount'],
    //             ['pre_demo_documents', 'options' => ['option_values' => $docIds, 'option_names' => $docNames,'selected'=>$selectedDoc['pre_demo']]]
    //         );
    //         $formRows['rows']['ele16'] = $form->newDocRule(
    //             'Demolition Documents',
    //             4,
    //             'Demolition',
    //             ['demolition_amount', '', 'Enter trigger amount'],
    //             ['demolition_documents', 'options' => ['option_values' => $docIds, 'option_names' => $docNames,'selected'=>$selectedDoc['demolition']]]
    //         );
    //         $formRows['rows']['ele17'] = $form->newDocRule(
    //             'Greening Documents',
    //             5,
    //             'Greening',
    //             ['greening_amount', '', 'Enter trigger amount'],
    //             ['greening_documents', 'options' => ['option_values' => $docIds, 'option_names' => $docNames,'selected'=>$selectedDoc['greening']]]
    //         );
    //         $formRows['rows']['ele18'] = $form->newDocRule(
    //             'Maintenance Documents',
    //             6,
    //             'Maintenance',
    //             ['maintenance_amount', '', 'Enter trigger amount'],
    //             ['maintenance_documents', 'options' => ['option_values' => $docIds, 'option_names' => $docNames,'selected'=>$selectedDoc['maintenance']]]
    //         );
    //         $formRows['rows']['ele19'] = $form->newDocRule(
    //             'Administration Documents',
    //             7,
    //             'Administration',
    //             ['administration_amount', '', 'Enter trigger amount'],
    //             ['administration_documents', 'options' => ['option_values' => $docIds, 'option_names' => $docNames,'selected'=>$selectedDoc['administration']]]
    //         );
    //         $formRows['rows']['ele20'] = $form->newDocRule(
    //             'Other Documents',
    //             8,
    //             'Acquisition',
    //             ['other_amount', '', 'Enter trigger amount'],
    //             ['other_documents', 'options' => ['option_values' => $docIds, 'option_names' => $docNames,'selected'=>$selectedDoc['other']]]
    //         );
    //         $formRows['rows']['ele21'] = $form->newDocRule(
    //             'NIP Documents',
    //             9,
    //             'NIP',
    //             ['nip_amount', '', 'Enter trigger amount'],
    //             ['nip_documents', 'options' => ['option_values' => $docIds, 'option_names' => $docNames,'selected'=>$selectedDoc['nip']]]
    //         );
    //         $formRows['rows']['ele22'] = $form->submit(['Create Rule']);
    //         return view('pages.formtemplate', ['formRows' => $formRows]);
    //     } else {
    //         $amount['acquisition']="";
    //         $amount['pre_demo']="";
    //         $amount['demolition']="";
    //         $amount['greening']="";
    //         $amount['maintenance']="";
    //         $amount['administration']="";
    //         $amount['other']="";
    //         $amount['nip']="";

    //         if (array_key_exists('document_rules', (array)$program_rule)) {
    //             foreach ($program_rule->documentRules as $docRule) {
    //                 switch ($docRule->expense_category_id) {
    //                     case 2:
    //                         $amount['acquisition']=($docRule->amount)?$docRule->amount:"";
    //                         break;
    //                     case 3:
    //                         $amount['pre_demo']=($docRule->amount)?$docRule->amount:"";
    //                         break;
    //                     case 4:
    //                         $amount['demolition']=($docRule->amount)?$docRule->amount:"";
    //                         break;
    //                     case 5:
    //                         $amount['greening']=($docRule->amount)?$docRule->amount:"";
    //                         break;
    //                     case 6:
    //                         $amount['maintenance']=($docRule->amount)?$docRule->amount:"";
    //                         break;
    //                     case 7:
    //                         $amount['administration']=($docRule->amount)?$docRule->amount:"";
    //                         break;
    //                     case 8:
    //                         $amount['other']=($docRule->amount)?$docRule->amount:"";
    //                         break;
    //                     case 9:
    //                         $amount['nip']=($docRule->amount)?$docRule->amount:"";
    //                         break;
    //                 }
    //             }
    //         }

    //         //selected docs here would be the ids in the functions
    //         $selectedDoc['required']=array_fill_keys($this->getRequiredDocumentIds($id), 1);
    //         $selectedDoc['acquisition']=array_fill_keys($this->getAcquiredDocumentIds($id), 1);
    //         $selectedDoc['pre_demo']=array_fill_keys($this->getPreDemoDocumentIds($id), 1);
    //         $selectedDoc['demolition']=array_fill_keys($this->getDemolitionDocumentIds($id), 1);
    //         $selectedDoc['greening']=array_fill_keys($this->getGreeningDocumentIds($id), 1);
    //         $selectedDoc['maintenance']=array_fill_keys($this->getMaintenanceDocumentIds($id), 1);
    //         $selectedDoc['administration']=array_fill_keys($this->getAdministrationDocumentIds($id), 1);
    //         $selectedDoc['other']=array_fill_keys($this->getOtherDocumentIds($id), 1);
    //         $selectedDoc['nip']=array_fill_keys($this->getNIPDocumentIds($id), 1);

    //         $formRows['tag'] = $form->formBuilder("/admin/rule/store/".$program_rule->id, "post", "application/x-www-form-urlencoded", "Edit Rule", "plus-circle");
    //         $formRows['rows']['ele1'] = $form->text(['Rule Name', 'rule_name', $program_rule->rules_name, 'Enter rule name', '']);
    //         $formRows['rows']['ele2'] = $form->text(['# Maintenance Months (for the label only)', 'maintenance_recap_pro_rate', $program_rule->maintenance_recap_pro_rate, 'Enter number of maintenance months', '']);
    //         $formRows['rows']['ele3'] = $form->text(['Imputed Cost Per Parcel', 'imputed_cost_parcel', $program_rule->imputed_cost_per_parcel, 'Enter imputed cost per parcel', '']);

    //         $formRows['rows']['ele4'] = $form->multipleText_wCheckbox(
    //             'Acquisition',
    //             [['acquisition_advance', '1', 'Acquisition advance', '',intval($program_rule->acquisition_advance)], ['acquisition_max_advance',$program_rule->acquisition_max_advance, 'Acquisition max advance', ''],
    //                 ['acquisition_max', '', 'Acquisition maximum (0 = No Max)', ''], ['acquisition_min',$program_rule->acquisition_min, 'Acquisition minimum', '']],
    //             4
    //         );
    //         $formRows['rows']['ele5'] = $form->multipleText_wCheckbox(
    //             'Pre-Demolition',
    //             [['pre_demolition_advance', '1', 'Pre demolition advance', '',intval($program_rule->pre_demo_advance)], ['pre_demolition_max_advance', $program_rule->pre_demo_max_advance, 'Pre demolition max advance', ''],
    //                 ['pre_demolition_max', $program_rule->pre_demo_max, 'Pre demolition maximum (0 = No Max)', ''], ['pre_demolition_min',$program_rule->pre_demo_min, 'Pre demolition minimum', '']],
    //             4
    //         );
    //         $formRows['rows']['ele6'] = $form->multipleText_wCheckbox(
    //             'Demolition',
    //             [['demolition_advance', '1', 'Demolition advance', '',intval($program_rule->demolition_advance)], ['demolition_max_advance', $program_rule->demolition_max_advance, 'Demolition max advance', ''],
    //                 ['demolition_max',$program_rule->demolition_max, 'Demolition maximum (0 = No Max)', ''], ['demolition_min',$program_rule->demolition_min, 'Demolition minimum', '']],
    //             4
    //         );
    //         $formRows['rows']['ele7'] = $form->multipleText_wCheckbox(
    //             'Greening',
    //             [['greening_advance', '1', 'Greening advance', '',intval($program_rule->greening_advance)], ['greening_max_advance', $program_rule->greening_max_advance, 'Greening max advance', ''],
    //                 ['greening_max',$program_rule->greening_max, 'Greening maximum (0 = No Max)', ''], ['greening_min',$program_rule->greening_min, 'Greening minimum', '']],
    //             4
    //         );
    //         $formRows['rows']['ele8'] = $form->multipleText_wCheckbox(
    //             'Maintenance',
    //             [['maintenance_advance', '1', 'Maintenance advance', '',intval($program_rule->maintenance_advance)], ['maintenance_max_advance', $program_rule->maintenance_max_advance, 'Maintenance max advance', ''],
    //                 ['maintenance_max', $program_rule->maintenance_max, 'Maintenance maximum (0 = No Max)', ''], ['maintenance_min',$program_rule->maintenance_min, 'Maintenance minimum', '']],
    //             4
    //         );
    //         $formRows['rows']['ele9'] = $form->multipleText_wCheckbox(
    //             'Administration',
    //             [['administration_advance', '1', 'Administration advance', '',intval($program_rule->administration_advance)], ['administration_max_advance', $program_rule->administration_max_advance, 'Administration max advance', ''],
    //                 ['administration_max_percent', $program_rule->admin_max_percent, 'Administration maximum percent', ''], ['administration_min_percent',$program_rule->admin_min, 'Administration minimum percent', '']],
    //             4
    //         );
    //         $formRows['rows']['ele10'] = $form->multipleText_wCheckbox(
    //             'Other',
    //             [['other_advance', '1', 'Other advance', '',intval($program_rule->nip_loan_payoff_advance)], ['other_max_advance', $program_rule->other_advance, 'Other max advance', ''],
    //                 ['other_max', $program_rule->other_max, 'Other maximum (0 = No Max)', ''], ['other_min',$program_rule->other_min, 'Other minimum', '']],
    //             4
    //         );
    //         $formRows['rows']['ele11'] = $form->multipleText_wCheckbox(
    //             'Nip Loan PayOff',
    //             [['nip_loan_payoff_advance', '1', 'Nip loan payoff advance', '',intval($program_rule->nip_loan_payoff_advance)], ['nip_loan_payoff_max_advance', $program_rule->nip_loan_payoff_advance, 'Nip loan payoff max advance', ''],
    //                 ['nip_loan_payoff_max', $program_rule->nip_loan_payoff_max, 'Nip loan payoff maximum (0 = No Max)', ''], ['nip_loan_payoff_min',$program_rule->nip_loan_payoff_min, 'Nip loan payoff minimum', '']],
    //             4
    //         );
    //         // foreach ($program_rule->documentRules as $docRule) {
    //         //     $formRows['rows']['ele12'] = $form->multipleText(
    //         //             [['min_units', $docRule->minimum_units, 'Minimum units', ''], ['max_units',$docRule->maximum_units, 'Maximum units', ''],
    //         //                 ['max_reimbursement',$docRule->maximum_reimbursement, 'Max. reimbursement', '']],
    //         //             3
    //         //         );
    //         // }

    //         if (count($program_rule->reimbursementRules)) {
    //             $min_units_value = $program_rule->reimbursementRules[0]->minimum_units;
    //             $max_units_value = $program_rule->reimbursementRules[0]->maximum_units;
    //             $max_reimbursement_value = $program_rule->reimbursementRules[0]->maximum_reimbursement;
    //         } else {
    //             $min_units_value = '';
    //             $max_units_value = '';
    //             $max_reimbursement_value = '';
    //         }
    //         $formRows['rows']['ele12'] = $form->multipleText(
    //             [['min_units', $min_units_value, 'Minimum units', ''], ['max_units',$max_units_value, 'Maximum units', ''],
    //                 ['max_reimbursement',$max_reimbursement_value, 'Max. reimbursement', '']],
    //             3
    //         );

    //         $formRows['rows']['ele13'] = $form->newDocRule13(
    //             'Required Documents',
    //             999,
    //             'Required',
    //             ['required_documents', 'options' => ['option_values' => $docIds, 'option_names' => $docNames,'selected'=>$selectedDoc['required']]]
    //         );
    //         $formRows['rows']['ele14'] = $form->newDocRule(
    //             'Acquisition Documents',
    //             2,
    //             'Acquisition',
    //             ['acquisition_amount', $amount['acquisition'], 'Enter trigger amount'],
    //             ['acquisition_documents', 'options' => ['option_values' => $docIds, 'option_names' => $docNames,'selected'=>$selectedDoc['acquisition']]]
    //         );
    //         $formRows['rows']['ele15'] = $form->newDocRule(
    //             'Pre Demolition Documents',
    //             3,
    //             'Pre Demolition',
    //             ['pre_demo_amount',$amount['pre_demo'], 'Enter trigger amount'],
    //             ['pre_demo_documents', 'options' => ['option_values' => $docIds, 'option_names' => $docNames,'selected'=>$selectedDoc['pre_demo']]]
    //         );
    //         $formRows['rows']['ele16'] = $form->newDocRule(
    //             'Demolition Documents',
    //             4,
    //             'Demolition',
    //             ['demolition_amount', $amount['demolition'], 'Enter trigger amount'],
    //             ['demolition_documents', 'options' => ['option_values' => $docIds, 'option_names' => $docNames,'selected'=>$selectedDoc['demolition']]]
    //         );
    //         $formRows['rows']['ele17'] = $form->newDocRule(
    //             'Greening Documents',
    //             5,
    //             'Greening',
    //             ['greening_amount',$amount['greening'], 'Enter trigger amount'],
    //             ['greening_documents', 'options' => ['option_values' => $docIds, 'option_names' => $docNames,'selected'=>$selectedDoc['greening']]]
    //         );
    //         $formRows['rows']['ele18'] = $form->newDocRule(
    //             'Maintenance Documents',
    //             6,
    //             'Maintenance',
    //             ['maintenance_amount',$amount['maintenance'], 'Enter trigger amount'],
    //             ['maintenance_documents', 'options' => ['option_values' => $docIds, 'option_names' => $docNames,'selected'=>$selectedDoc['maintenance']]]
    //         );
    //         $formRows['rows']['ele19'] = $form->newDocRule(
    //             'Administration Documents',
    //             7,
    //             'Administration',
    //             ['administration_amount',$amount['administration'], 'Enter trigger amount'],
    //             ['administration_documents', 'options' => ['option_values' => $docIds, 'option_names' => $docNames,'selected'=>$selectedDoc['administration']]]
    //         );
    //         $formRows['rows']['ele20'] = $form->newDocRule(
    //             'Other Documents',
    //             8,
    //             'Acquisition',
    //             ['other_amount',$amount['other'], 'Enter trigger amount'],
    //             ['other_documents', 'options' => ['option_values' => $docIds, 'option_names' => $docNames,'selected'=>$selectedDoc['other']]]
    //         );
    //         $formRows['rows']['ele21'] = $form->newDocRule(
    //             'NIP Documents',
    //             9,
    //             'NIP',
    //             ['nip_amount',$amount['nip'], 'Enter trigger amount'],
    //             ['nip_documents', 'options' => ['option_values' => $docIds, 'option_names' => $docNames,'selected'=>$selectedDoc['nip']]]
    //         );

    //         $formRows['rows']['ele22'] = $form->submit(['Update Rule']);
    //         return view('pages.formtemplate', ['formRows' => $formRows]);
    //     }
    // }

    /**
     * Account Create
     *
     * @param \App\Http\Controllers\FormsController $form
     * @param null                                  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // public function accountCreate(Form $form, $id = null)
    // {
    //     $ownerIds = Program::pluck('id')->toArray();
    //     $ownerNames = Program::pluck('program_name')->toArray();

    //     $account = Account::where('id', $id)->first();

    //     for ($i=0; $i < count($ownerIds); $i++) {
    //         if ($ownerIds[$i] == $account->owner_id) {
    //             $selectedOwner[$i] = 'true';
    //         } else {
    //             $selectedOwner[$i] = 'false';
    //         }
    //     }

    //     if (!$id) {
    //         $formRows['tag'] = $form->formBuilder("/admin/account/store", "post", "application/x-www-form-urlencoded", "Create New Account", "plus-circle");
    //         $formRows['rows']['ele1']= $form->text(['Account Name','account_name','','Enter account name','required']);
    //         $formRows['rows']['ele2'] = $form->selectBox(['Select Program','owner_id',$ownerIds,$ownerNames,$selectedOwner,'','required']);
    //         $formRows['rows']['ele3'] = $form->submit(['Create Account']);
    //         return view('pages.formtemplate', ['formRows'=>$formRows]);
    //     } else {
    //         $formRows['tag'] = $form->formBuilder("/admin/account/store/".$account->id, "post", "application/x-www-form-urlencoded", "Edit Account", "plus-circle");
    //         $formRows['rows']['ele1']= $form->text(['Account Name','account_name',$account->account_name,'','required']);
    //         $formRows['rows']['ele2'] = $form->selectBox(['Select Owner','owner_id',$ownerIds,$ownerNames,$selectedOwner,'','required']);
    //         $formRows['rows']['ele3'] = $form->submit(['Update Account']);
    //         return view('pages.formtemplate', ['formRows'=>$formRows]);
    //     }
    // }

    /**
     * Vendor Create
     *
     * @param \App\Http\Controllers\FormsController $form
     * @param null                                  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // public function vendorCreate(Form $form, $id = null)
    // {
    //     $stateIds = State::pluck('id')->toArray();
    //     $stateNames = State::pluck('state_name')->toArray();
    //     $selectedState=array_fill(0, count($stateIds), 'false');
    //     $selectedState[35]='true';
    //     $vendor = Vendor::where('id', $id)->first();

    //     if (!$id) {
    //         $formRows['tag'] = $form->formBuilder("/admin/vendor/store", "post", "application/x-www-form-urlencoded", "Create New Vendor", "plus-circle");
    //         $formRows['rows']['ele1']= $form->text(['Vendor Name','vendor_name','','Enter vendor name','required']);
    //         $formRows['rows']['ele2']= $form->text(['Vendor Email','vendor_email','','Enter vendor email','']);
    //         $formRows['rows']['ele3']= $form->text(['Work Phone','vendor_phone','','Enter vendor phone','']);
    //         $formRows['rows']['ele4']= $form->text(['Mobile Phone','vendor_mobile','','Enter vendor mobile','']);
    //         $formRows['rows']['ele5']= $form->text(['Fax Number','vendor_fax','','Enter vendor fax','']);
    //         $formRows['rows']['ele6']= $form->text(['Street Address','vendor_street','','Enter street address','']);
    //         $formRows['rows']['ele7']= $form->text(['Street Address2','vendor_street2','','','']);
    //         $formRows['rows']['ele8']= $form->text(['City','vendor_city','','Enter vendor city','']);
    //         $formRows['rows']['ele9'] = $form->selectBox(['Select State','state_id',$stateIds,$stateNames,$selectedState,'','required']);
    //         $formRows['rows']['ele10']= $form->text(['Zip Code','vendor_zip','','Enter vendor zip','']);
    //         $formRows['rows']['ele11']= $form->text(['Duns','vendor_duns','','Enter vendor duns','']);
    //         $formRows['rows']['ele12']= $form->textArea(['Notes','vendor_notes','','','']);
    //         $formRows['rows']['ele13'] = $form->submit(['Create Vendor']);
    //         return view('pages.formtemplate', ['formRows'=>$formRows]);
    //     } else {
    //         $selectedState[intval($vendor->vendor_state_id)-1]= 'true';
    //         $formRows['tag'] = $form->formBuilder("/admin/vendor/store/".$vendor->id, "post", "application/x-www-form-urlencoded", "Edit Vendor", "plus-circle");
    //         $formRows['rows']['ele1']= $form->text(['Vendor Name','vendor_name',$vendor->vendor_name,'','required']);
    //         $formRows['rows']['ele2']= $form->text(['Vendor Email','vendor_email',$vendor->vendor_email,'','']);
    //         $formRows['rows']['ele3']= $form->text(['Work Phone','vendor_phone',$vendor->vendor_phone,'','']);
    //         $formRows['rows']['ele4']= $form->text(['Mobile Phone','vendor_mobile',$vendor->vendor_mobile_phone,'','']);
    //         $formRows['rows']['ele5']= $form->text(['Fax Number','vendor_fax',$vendor->vendor_fax,'','']);
    //         $formRows['rows']['ele6']= $form->text(['Street Address','vendor_street',$vendor->vendor_street_address,'','']);
    //         $formRows['rows']['ele7']= $form->text(['Street Address2','vendor_street2',$vendor->vendor_street_address2,'','']);
    //         $formRows['rows']['ele8']= $form->text(['City','vendor_city',$vendor->vendor_city,'','']);
    //         $formRows['rows']['ele9'] = $form->selectBox(['Select State','state_id',$stateIds,$stateNames,$selectedState,'','required']);
    //         $formRows['rows']['ele10']= $form->text(['Zip Code','vendor_zip',$vendor->vendor_zip,'','']);
    //         $formRows['rows']['ele11']= $form->text(['Duns','vendor_duns',$vendor->vendor_duns,'','']);
    //         $formRows['rows']['ele12']= $form->textArea(['Notes','vendor_notes',$vendor->vendor_notes,'','']);
    //         $formRows['rows']['ele13'] = $form->submit(['Update Vendor']);
    //         return view('pages.formtemplate', ['formRows'=>$formRows]);
    //     }
    // }

    /**
     * Target Area Create
     *
     * @param \App\Http\Controllers\FormsController $form
     * @param null                                  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // public function targetAreaCreate(Form $form, $id = null)
    // {
    //     $countyIds = County::orderBy('county_name', 'asc')->pluck('id')->toArray();
    //     $countyNames = County::orderBy('county_name', 'asc')->pluck('county_name')->toArray();
    //     $selectedCounty=array_fill(0, count($countyIds), 'false');
    //     $targetArea = TargetArea::where('id', $id)->first();

    //     if (!$id) {
    //         $formRows['tag'] = $form->formBuilder("/admin/target_area/store", "post", "application/x-www-form-urlencoded", "Create New Target Area", "plus-circle");
    //         $formRows['rows']['ele1']= $form->text(['Target Area Name','target_area_name','','Enter target area name','required']);
    //         $formRows['rows']['ele2'] = $form->selectBox(['Select County','county',$countyIds,$countyNames,$selectedCounty,'','required']);
    //         $formRows['rows']['ele3'] = $form->submit(['Create Target Area']);
    //         return view('pages.formtemplate', ['formRows'=>$formRows]);
    //     } else {
    //         $selectedCounty[intval($targetArea->county_id)-1]="true";
    //         $formRows['tag'] = $form->formBuilder("/admin/target_area/store/".$targetArea->id, "post", "application/x-www-form-urlencoded", "Edit Target Area", "plus-circle");
    //         $formRows['rows']['ele1']= $form->text(['Target Area Name','target_area_name',$targetArea->target_area_name,'','required']);
    //         $formRows['rows']['ele2'] = $form->selectBox(['Select County','county',$countyIds,$countyNames,$selectedCounty,'','required']);
    //         $formRows['rows']['ele3'] = $form->submit(['Update Target Area']);
    //         return view('pages.formtemplate', ['formRows'=>$formRows]);
    //     }
    // }

    /**
     * Document Category Create
     *
     * @param \App\Http\Controllers\FormsController $form
     * @param null                                  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function documentCategoryCreate(Form $form, $id = null)
    {
        $documentCategory = DocumentCategory::where('id', $id)->first();
        if (!$id) {
            $formRows['tag'] = $form->formBuilder("/admin/document_category/store", "post", "application/x-www-form-urlencoded", "Create New Document Category", "plus-circle");
            $formRows['rows']['ele1']= $form->text(['Document Category Name','document_category_name','','Enter document category name','required']);
            $formRows['rows']['ele2'] = $form->submit(['Create Document Category']);
            return view('formtemplate', ['formRows'=>$formRows]);
        } else {
            $formRows['tag'] = $form->formBuilder("/admin/document_category/store/".$documentCategory->id, "post", "application/x-www-form-urlencoded", "Edit Document Category", "plus-circle");
            $formRows['rows']['ele1']= $form->text(['Document Category Name','document_category_name',$documentCategory->document_category_name,'','required']);
            $formRows['rows']['ele2'] = $form->submit(['Update Document Category']);
            return view('formtemplate', ['formRows'=>$formRows]);
        }
    }

    /**
     * Expense Category Create
     *
     * @param \App\Http\Controllers\FormsController $form
     * @param null                                  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // public function expenseCategoryCreate(Form $form, $id = null)
    // {
    //     $expenseCategory = ExpenseCategory::where('id', $id)->first();
    //     if (!$id) {
    //         $formRows['tag'] = $form->formBuilder("/admin/expense_category/store", "post", "application/x-www-form-urlencoded", "Create New Expense Category", "plus-circle");
    //         $formRows['rows']['ele1']= $form->text(['Expense Category Name','expense_category_name','','Enter expense category name','required']);
    //         $formRows['rows']['ele2'] = $form->submit(['Create Expense Category']);
    //         return view('pages.formtemplate', ['formRows'=>$formRows]);
    //     } else {
    //         $formRows['tag'] = $form->formBuilder("/admin/expense_category/store/".$expenseCategory->id, "post", "application/x-www-form-urlencoded", "Edit Expense Category", "plus-circle");
    //         $formRows['rows']['ele1']= $form->text(['Expense Category Name','expense_category_name',$expenseCategory->expense_category_name,'','required']);
    //         $formRows['rows']['ele2'] = $form->submit(['Update Expense Category']);
    //         return view('pages.formtemplate', ['formRows'=>$formRows]);
    //     }
    // }

    /**
     * Country Create
     *
     * @param \App\Http\Controllers\FormsController $form
     * @param null                                  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function countyCreate(Form $form, $id = null)
    {
        $county = County::where('id', $id)->first();

        if (!$id) {
            return '<h2>No county was provided? Weird!</h2><p>Try closing and refreshing to come back and try again.</p>';
        } else {
            $formRows['tag'] = $form->formBuilder("/admin/county/store/".$county->id, "post", "application/x-www-form-urlencoded", "Edit County", "edit");
            $formRows['rows']['ele1']= $form->text(['County Name','county_name',$county->county_name,'','required']);
            $formRows['rows']['ele2'] = $form->text(['Auditor Site','auditor_site',$county->auditor_site,'','required']);
            $formRows['rows']['ele3'] = $form->submit(['Update County Information']);
            return view('pages.formtemplate', ['formRows'=>$formRows]);
        }
    }

    /**
     * Amenity Update
     *
     * @param  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function amenityCreate($id = null)
    {
        
        $amenity = Amenity::where('id', '=', $id)->first();
        $huds = HudInspectableArea::orderBy('name')->get()->all();

        if (!$amenity) {
            $amenity = null;
            return view('modals.amenity-admin-edit', compact('amenity','huds'));
        } else {
            return view('modals.amenity-admin-edit', compact('amenity','huds'));
        }
    }

    /**
     * Finding Type Create
     *
     * @param null  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function findingtypeCreate($id = null)
    {
        $finding_type = FindingType::where('id', $id)->first();

        if (!$finding_type) {
            $finding_type = null;
            $boilerplates = Boilerplate::where('global', '=', 1)->orderBy('name', 'asc')->get();

            // people who can be assigned to the follow ups
            // the audit lead, or the PM, or whoever is creating the finding (hardcoded)

            $document_categories = DocumentCategory::where('active', '=', 1)->get();
            $huds = HudInspectableArea::orderBy('name', 'asc')->get();

            return view('modals.finding-type-create', compact('finding_type', 'boilerplates', 'document_categories', 'huds'));
        } else {
            $boilerplates = Boilerplate::where('global', '=', 1)->orderBy('name', 'asc')->get();
            $document_categories = DocumentCategory::where('active', '=', 1)->get();
            $huds = HudInspectableArea::orderBy('name', 'asc')->get();

            return view('modals.finding-type-create', compact('finding_type', 'boilerplates', 'document_categories', 'huds'));
        }
    }

    /**
     * Boilerplate Create
     *
     * @param \App\Http\Controllers\FormsController $form
     * @param null                                  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function boilerplateCreate(Form $form, $id = null)
    {
        $boilerplate = Boilerplate::where('id', $id)->first();

        if (!$id) {
            $formRows['tag'] = $form->formBuilder("/admin/boilerplate/store", "post", "application/x-www-form-urlencoded", "Create New Boilerplate", "plus-circle");
            $formRows['rows']['ele1']= $form->text(['Title','name','','Enter boilerplate title','required']);
            $formRows['rows']['ele2']= $form->textArea(['Boilerplate','boilerplate','','','']);
            $formRows['rows']['ele3']= $form->checkbox(['Global','global','','','true','required']);
            $formRows['rows']['ele4'] = $form->submit(['Create Boilerplate']);
            return view('formtemplate', ['formRows'=>$formRows]);
        } else {
            $formRows['tag'] = $form->formBuilder("/admin/boilerplate/store/".$boilerplate->id, "post", "application/x-www-form-urlencoded", "Edit Boilerplate", "edit");
            $formRows['rows']['ele1']= $form->text(['Title','name',$boilerplate->name,'Enter boilerplate title','required']);
            $formRows['rows']['ele2']= $form->textArea(['Boilerplate','boilerplate',$boilerplate->boilerplate,'','required']);
            $formRows['rows']['ele3']= $form->checkbox(['Global','global',$boilerplate->global,'','true','required']);
            $formRows['rows']['ele4'] = $form->submit(['Update Boilerplate Information']);
            return view('formtemplate', ['formRows'=>$formRows]);
        }
    }

    /**
     * Hud Area Create
     *
     * @param \App\Http\Controllers\FormsController $form
     * @param null                                  $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function hudAreaCreate($id = null)
    {
        $hud = HudInspectableArea::where('id', $id)->first();

        if (!$hud) {
            $hud = null;
            $amenities = Amenity::orderBy('amenity_description', 'asc')->get();
            $findingTypes = FindingType::orderBy('name', 'asc')->get();

            return view('modals.hud-area-create', compact('hud', 'amenities','findingTypes'));
        } else {
            $amenities = Amenity::orderBy('amenity_description', 'asc')->get();
            $findingTypes = FindingType::orderBy('name', 'asc')->get();

            return view('modals.hud-area-create', compact('hud', 'amenities', 'findingTypes'));
        }
    }

    // display tabs

    public function searchOrganizations(Request $request)
    {
        if ($request->has('organizations-search')) {
            Session::put('organizations-search', $request->get('organizations-search'));
        } else {
            Session::forget('organizations-search');
        }
        return 1;
    }

    /**
     * Organizations Index
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function organizationIndex()
    {
        if (Session::has('organizations-search') && Session::get('organizations-search') != '') {
            $search = Session::get('organizations-search');
            $organizations = Organization::with(['address','person'])
                                    ->where(function ($query) use ($search) {
                                        $query->where('organization_name', 'LIKE', '%'.$search.'%');
                                    })
                                    ->orderBy('organization_name', 'asc')
                                    ->paginate(40);
        } else {
            $organizations = Organization::with(['address','person'])->orderBy('organization_name', 'asc')->paginate(40);
        }
        
        return view('admin_tabs.organizations', compact('organizations'));
    }

    /**
     * Amenities Index
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function amenityIndex()
    {
        $amenities = Amenity::orderBy('amenity_description', 'asc')->get();
        return view('admin_tabs.amenities', compact('amenities'));
    }

    /**
     * HUD INSPECTABLE AREA Index
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function hudIndex()
    {
        $huds = HudInspectableArea::orderBy('name', 'asc')->get();
        return view('admin_tabs.huds', compact('huds'));
    }

    public function searchFindingTypes(Request $request)
    {
        if ($request->has('findingtypes-search')) {
            Session::put('findingtypes-search', $request->get('findingtypes-search'));
        } else {
            Session::forget('findingtypes-search');
        }
        return 1;
    }

    /**
     * Finding Type Index
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function findingtypeIndex()
    {
        if (Session::has('findingtypes-search') && Session::get('findingtypes-search') != '') {
            $search = Session::get('findingtypes-search');
            $findingtypes = FindingType::where(function ($query) use ($search) {
                                        $query->where('name', 'LIKE', '%'.$search.'%');
            })
                                    ->orderBy('name', 'asc')
                                    ->paginate(25);
        } else {
            $findingtypes = FindingType::orderBy('name', 'asc')->paginate(25);
        }
        
        return view('admin_tabs.findingtypes', compact('findingtypes'));
    }

    /**
     * defaultfollowup Index
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function defaultfollowupIndex()
    {
        $followups = DefaultFollowup::orderBy('description', 'asc')->get();
        return view('admin_tabs.followups', compact('followups'));
    }

    /**
     * defaultfollowup Index
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function boilerplateIndex()
    {
        $boilerplates = Boilerplate::with('user')->orderBy('name', 'asc')->get();
        return view('admin_tabs.boilerplates', compact('boilerplates'));
    }

    /**
     * Program Index
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function programIndex()
    {
        $programs = Program::with(['entity','county','programRule'])->orderBy('program_name', 'asc')->get();
        return view('admin_tabs.program', compact('programs'));
    }

    /**
     * Rule Index
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // public function ruleIndex()
    // {
    //     $rules = ProgramRule::orderBy('rules_name')->get()->all();
    //     return view('admin_tabs.rule', compact('rules'));
    // }

    /**
     * Account Index
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // public function accountIndex()
    // {
    //     $accounts = Account::with(['program'])->orderBy('account_name', 'asc')->get();
    //     return view('admin_tabs.account', compact('accounts'));
    // }

    /**
     * Document Index
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function documentIndex()
    {
        $documents = DocumentCategory::orderBy('document_category_name', 'asc')->get()->all();
        return view('admin_tabs.document_categories', compact('documents'));
    }

    /**
     * Expense Index
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // public function expenseIndex()
    // {
    //     $expenses = ExpenseCategory::orderBy('expense_category_name')->where('id', '>', 1)->get()->all();
    //     return view('admin_tabs.expense_category', compact('expenses'));
    // }

    /**
     * Vendor Index
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // public function vendorIndex()
    // {
    //     $vendors = Vendor::join('states', 'states.id', '=', 'vendor_state_id')->orderBy('vendor_name', 'asc')->select('vendors.*')->get();
    //     return view('admin_tabs.vendor', compact('vendors'));
    // }

    /**
     * Target Index
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    // public function targetIndex()
    // {
    //     $targets = TargetArea::with('county')->orderBy('county_id', 'asc')->orderBy('target_area_name', 'asc')->get();
    //     return view('admin_tabs.target_area', compact('targets'));
    // }

    /**
     * County Index
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function countyIndex()
    {
        $counties = County::orderBy('county_name', 'asc')->get();
        return view('admin_tabs.counties', compact('counties'));
    }

    //store form data.

    /**
     * Entity Store
     *
     * @param \Illuminate\Http\Request $request
     * @param null                     $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    // public function entityStore(Request $request, $id = null)
    // {
    //     $this->validate($request, ['entity_name'=>'string|required',
    //             'email_address'=>'email|required',
    //             'street_address'=>'string',
    //             'street_address_2'=>'string',
    //             'city'=>'string',
    //             'zipcode'=>'string',
    //             'web_address'=>'url',
    //             'logo_link'=>'url',
    //         ]);

    //     if (!$id) {
    //         $e = Entity::create([
    //             'entity_name'=>Input::get('entity_name'),
    //             'email_address'=>Input::get('email_address'),
    //             'address1'=>Input::get('street_address'),
    //             'address2'=>Input::get('street_address_2'),
    //             'city'=>Input::get('city'),
    //             'state_id'=>Input::get('state'),
    //             'zip'=>Input::get('zipcode'),
    //             'phone'=>Input::get('phone'),
    //             'fax'=>Input::get('fax'),
    //             'datatran_user'=>Input::get('datatran_user'),
    //             'datatran_password'=>Input::get('datatran_password'),
    //             'logo_link'=>Input::get('logo_link'),
    //             'web_address'=>Input::get('web_address'),
    //             'owner_id'=>Input::get('owner'),
    //             'active'=>1,
    //             'user_id'=>Input::get('owner'),
    //         ]);
    //         if (http_response_code()==200) {
    //             $lc = new LogConverter('entity', 'create');
    //             $lc->setFrom(Auth::user())->setTo($e)->setDesc(Auth::user()->email . ' Created entity ' . $e->entity_name)->save();
    //             return response('I made an entity named '.$e->entity_name.' for you.<br /><br />I recommend making a program for them now!<hr /><a onclick="dynamicModalLoad(\'admin/program/create\')" class="uk-button uk-width-2-5@m uk-float-right">CREATE NEW PROGRAM</a>');
    //         }
    //     } else {
    //         $eold = Entity::find($id);

    //         Entity::where('id', $id)->update([
    //             'entity_name'=>Input::get('entity_name'),
    //             'email_address'=>Input::get('email_address'),
    //             'address1'=>Input::get('street_address'),
    //             'address2'=>Input::get('street_address_2'),
    //             'city'=>Input::get('city'),
    //             'state_id'=>Input::get('state'),
    //             'zip'=>Input::get('zipcode'),
    //             'phone'=>Input::get('phone'),
    //             'fax'=>Input::get('fax'),
    //             'datatran_user'=>Input::get('datatran_user'),
    //             'datatran_password'=>Input::get('datatran_password'),
    //             'logo_link'=>Input::get('logo_link'),
    //             'web_address'=>Input::get('web_address'),
    //             'owner_id'=>Input::get('owner'),
    //             'active'=>1,
    //             'user_id'=>Input::get('owner'),
    //         ]);
    //         $e = Entity::find($id);
    //         $enew = $e->toArray();
    //         $lc = new LogConverter('entity', 'update');
    //         $lc->setFrom(Auth::user())->setTo($e)->setDesc(Auth::user()->email . ' Updated Entity ' . $e->entity_name);
    //         $lc->smartAddHistory($eold, $enew);
    //         $lc->save();
    //         return response('I updated '.$eold->entity_name.' for you. <br />Whew, now I need break ;) <script>  $(\'#entities-tab\').trigger(\'click\');</script>');
    //     }
    // }

    /**
     * Program Store
     *
     * @param \Illuminate\Http\Request $request
     * @param null                     $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function programStore(Request $request, $id = null)
    {
        $entityIds = Entity::pluck('id')->toArray();
        $countyIds = County::pluck('id')->toArray();
        $ruleIds = ProgramRule::pluck('id')->toArray();
        $this->validate($request, [
            'program_name'=>'string|required',
            'entity_id'=>'in:'.implode(',', $entityIds),
            'county_id'=>'in:'.implode(',', $countyIds),
            'rule_id'=>'in:'.implode(',', $ruleIds),
        ]);
        if (!$id) {
            $p= Program::create([
                'owner_type'=>'entity',
                'owner_id'=>Input::get('entity_id'),
                'program_name'=>Input::get('program_name'),
                'entity_id'=>Input::get('entity_id'),
                'active'=>1,
                'default_program_rules_id'=>Input::get('rule_id'),
                'county_id'=>Input::get('county_id'),
            ]);
            $lc = new LogConverter('program', 'create');
            $lc->setFrom(Auth::user())->setTo($p)->setDesc(Auth::user()->email . ' Created program ' . $p->program_name)->save();
            return response('We created a program together, and now all it needs is an account! <hr /> <a onclick="dynamicModalLoad(\'admin/account/create\')" class="uk-button uk-width-2-5@m uk-float-right">CREATE NEW ACCOUNT</a>');
        } else {
            $pold = Program::find($id)->toArray();
            Program::where('id', $id)->update([
                'owner_type'=>'entity',
                'owner_id'=>Input::get('entity_id'),
                'program_name'=>Input::get('program_name'),
                'entity_id'=>Input::get('entity_id'),
                'active'=>1,
                'default_program_rules_id'=>Input::get('rule_id'),
                'county_id'=>Input::get('county_id'),
            ]);
            $p = Program::find($id);
            $pnew = $p->toArray();
            $lc = new LogConverter('program', 'update');
            $lc->setFrom(Auth::user())->setTo($p)->setDesc(Auth::user()->email . ' Updated program ' . $p->program_name);
            $lc->smartAddHistory($pold, $pnew);
            $lc->save();
            return response('I updated '.$p->program_name.' for you. We are task masters!  <script>$(\'#programs-tab\').trigger(\'click\');</script>');
        }
    }

    /**
     * Rules Validation
     *
     * @param $data
     */
    // public function rulesValidation($data)
    // {
    //     //wrap numeric values (amount and similar) into integer value and test against the validation
    //     $this->validate($data, [
    //         'rule_name'=>'string|required',
    //         'maintenance_recap_pro_rate'=>'string|required',
    //         'imputed_cost_parcel'=>'string|required',
    //         'acquisition_advance'=>'boolean',
    //         'pre_demolition_advance'=>'boolean',
    //         'demolition_advance'=>'boolean',
    //         'greening_advance'=>'boolean',
    //         'maintenance_advance'=>'boolean',
    //         'administration_advance'=>'boolean',
    //         'other_advance'=>'boolean',
    //         'nip_loan_payoff_advance'=>'boolean',
    //         'acquisition_max_advance' => 'nullable|string',
    //         'pre_demolition_max_advance' => 'nullable|string',
    //         'demolition_max_advance' => 'nullable|string',
    //         'greening_max_advance' => 'nullable|string',
    //         'maintenance_max_advance' => 'nullable|string',
    //         'administration_max_advance' => 'nullable|string',
    //         'other_max_advance' => 'nullable|string',
    //         'nip_loan_payoff_max_advance' => 'nullable|string',
    //         'acquisition_max' => 'nullable|string',
    //         'pre_demolition_max' => 'nullable|string',
    //         'demolition_max' => 'nullable|string',
    //         'greening_max' => 'nullable|string',
    //         'maintenance_max' => 'nullable|string',
    //         'administration_max_percent' => 'nullable|string',
    //         'other_max' => 'nullable|string',
    //         'nip_loan_payoff_max' => 'nullable|string',
    //         'acquisition_min' => 'required|string',
    //         'pre_demolition_min' => 'required|string',
    //         'demolition_min' => 'required|string',
    //         'greening_min' => 'required|string',
    //         'maintenance_min' => 'required|string',
    //         'administration_min_percent' => 'required|string',
    //         'other_min' => 'required|string',
    //         'nip_loan_payoff_min' => 'required|string',
    //         // reimbursement validation
    //         //            'min_units.*'=>'nullable|string',
    //         //            'max_units.*'=>'nullable|string',
    //         //            'max_reimbursement.*'=>'nullable|string',
    //         // document rules validation
    //         'required_documents.*'=>'string|nullable',
    //         'acquisition_amount'=>'string|nullable',
    //         'acquisition_documents.*'=>'string|required_with:acquisition_amount',
    //         'pre_demo_amount'=>'string|nullable',
    //         'pre_demo_documents.*'=>'string|required_with:pre_demo_amount',
    //         'demolition_amount'=>'string|nullable',
    //         'demolition_documents.*'=>'string|required_with:demolition_amount',
    //         'greening_amount'=>'string|nullable',
    //         'greening_documents.*'=>'string|required_with:greening_amount',
    //         'maintenance_amount'=>'string|nullable',
    //         'maintenance_documents.*'=>'string|required_with:maintenance_amount',
    //         'administration_amount'=>'string|nullable',
    //         'administration_documents.*'=>'string|required_with:administration_amount',
    //         'other_amount'=>'string|nullable',
    //         'other_documents.*'=>'string|required_with:other_amount',
    //         'nip_amount'=>'string|nullable',
    //         'nip_documents.*'=>'string|required_with:nip_amount'
    //     ]);
    // }

    

    /**
     * Document Category Store
     *
     * @param \Illuminate\Http\Request $request
     * @param null                     $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function documentCategoryStore(Request $request, $id = null)
    {
        $this->validate($request, [
           'document_category_name'=> 'string|required'
        ]);
        if (!$id) {
            $d = DocumentCategory::create([
                'document_category_name' => Input::get('document_category_name')
            ]);
            // $lc = new LogConverter('documentcategory', 'create');
            // $lc->setFrom(Auth::user())->setTo($d)->setDesc(Auth::user()->email . ' Created Document Category ' . $d->document_category_name)->save();
            return response('I created the document category. I stored it. I love it.');
        } else {
            $dold = DocumentCategory::find($id)->toArray();
            DocumentCategory::where('id', $id)->update([
                'document_category_name' => Input::get('document_category_name')
            ]);
            $d = DocumentCategory::find($id);
            $dnew = $d->toArray();
            // $lc = new LogConverter('documentcategory', 'update');
            // $lc->setFrom(Auth::user())->setTo($d)->setDesc(Auth::user()->email . ' Updated Document Category ' . $d->document_category_name);
            // $lc->smartAddHistory($dold, $dnew);
            // $lc->save();
            return response('I updated your document category. That was fun! What else do you have for me?');
        }
    }

    /**
     * Boilerplate Store
     *
     * @param \Illuminate\Http\Request $request
     * @param null                     $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function boilerplateStore(Request $request, $id = null)
    {
        $this->validate($request, [
           'name'=> 'string|required'
        ]);
        if (!$id) {
            $d = Boilerplate::create([
                'name' => Input::get('name'),
                'boilerplate' => Input::get('boilerplate'),
                'global' => (array_key_exists('global', $inputs)) ? 1 : 0,
                'creator_id' => Auth::user()->id
            ]);
            // $lc = new LogConverter('documentcategory', 'create');
            // $lc->setFrom(Auth::user())->setTo($d)->setDesc(Auth::user()->email . ' Created Document Category ' . $d->document_category_name)->save();
            return response('<h2>Success</h2><p>I created the boilerplate.</p>');
        } else {
            $dold = Boilerplate::find($id)->toArray();
            Boilerplate::where('id', $id)->update([
                'name' => Input::get('name'),
                'boilerplate' => Input::get('boilerplate'),
                'global' => (array_key_exists('global', $inputs)) ? 1 : 0,
            ]);
            $d = Boilerplate::find($id);
            $dnew = $d->toArray();
            // $lc = new LogConverter('documentcategory', 'update');
            // $lc->setFrom(Auth::user())->setTo($d)->setDesc(Auth::user()->email . ' Updated Document Category ' . $d->document_category_name);
            // $lc->smartAddHistory($dold, $dnew);
            // $lc->save();
            return response('I updated your boilerplate. That was fun! What else do you have for me?');
        }
    }

    /**
     * HUD Area Store
     *
     * @param \Illuminate\Http\Request $request
     * @param null                     $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function hudAreaStore(Request $request, $id = null)
    {
        $inputs = $request->get('inputs');
        $amenities = json_decode($request->get('amenities'), true);
        $findingTypes = json_decode($request->get('findingTypes'), true);
        //dd($findingTypes,$amenities);
        $amenities = $amenities['items'];
        $findingTypes = $findingTypes['items'];



        if (!$id) {
            $hud = HudInspectableArea::create([
                'name' => $inputs['name'],
                'site' => (array_key_exists('site', $inputs)) ? 1 : 0,
                'building_system' => (array_key_exists('building_system', $inputs)) ? 1 : 0,
                'building_exterior' => (array_key_exists('global', $inputs)) ? 1 : 0,
                'common_area' => (array_key_exists('global', $inputs)) ? 1 : 0,
                'unit' =>(array_key_exists('global', $inputs)) ? 1 : 0,
                'file' =>(array_key_exists('global', $inputs)) ? 1 : 0


            ]);

            // add amenities
            if (count($amenities)) {
                //add in the update
                foreach ($amenities as $amenity) {
                    AmenityHud::create([
                        'hud_inspectable_area_id' => $hud->id,
                        'amenity_id' => $amenity['id']
                    ]);
                }
            }

            // add finding types
            if (count($findingTypes)) {
                foreach ($findingTypes as $findingType) {
                    HudFindingType::create([
                        'hud_inspectable_area_id' => $hud->id,
                        'finding_type_id' => $findingType['id']
                    ]);
                }
            }

            // $lc = new LogConverter('documentcategory', 'create');
            // $lc->setFrom(Auth::user())->setTo($d)->setDesc(Auth::user()->email . ' Created Document Category ' . $d->document_category_name)->save();
            return response('<h2>Success!</h2><p>I created the HUD area.</p>');
        } else {
            $hud = HudInspectableArea::where('id', '=', $id)->first();
            
            if ($hud) {
                $hud->update([
                    'name' => $inputs['name']
                ]);
                $hud->touch(); // ensure timestamps are updated

                // remove amenities
                AmenityHud::where('hud_inspectable_area_id', '=', $hud->id)->delete();

                // add amenities
                if (count($amenities)) {
                    foreach ($amenities as $amenity) {
                        AmenityHud::create([
                            'hud_inspectable_area_id' => $hud->id,
                            'amenity_id' => $amenity['id']
                        ]);
                    }
                }

                // remove finding types
                HudFindingType::where('hud_inspectable_area_id', '=', $hud->id)->delete();

                // add finding types
                if (count($findingTypes)) {
                    foreach ($findingTypes as $findingType) {
                        HudFindingType::create([
                            'hud_inspectable_area_id' => $hud->id,
                            'finding_type_id' => $findingType['id']
                        ]);
                    }
                }

                return response('<h2>Success!</h2><p>I updated your HUD area.</p>');
            } else {
                return response('<h2>Problem...</h2><p>I cannot find that record.</p>');
            }

            // $lc = new LogConverter('documentcategory', 'update');
            // $lc->setFrom(Auth::user())->setTo($d)->setDesc(Auth::user()->email . ' Updated Document Category ' . $d->document_category_name);
            // $lc->smartAddHistory($dold, $dnew);
            // $lc->save();
        }
    }

    /**
     * Amenity Store
     *
     * @param \Illuminate\Http\Request $request
     * @param null                     $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function amenityStore(Request $request, $id=null)
    {
            $inputs = $request->get('inputs');

            $project = (array_key_exists('project', $inputs)) ? 1 : 0;
            $buildingExterior = (array_key_exists('building_exterior', $inputs)) ? 1 : 0;
            $buildingSystem = (array_key_exists('building_system', $inputs)) ? 1 : 0;
            $commonArea = (array_key_exists('common_area', $inputs)) ? 1 : 0;
            $file = (array_key_exists('file', $inputs)) ? 1 : 0;
            $unit = (array_key_exists('unit', $inputs)) ? 1 : 0;
            $unitDefault = (array_key_exists('unit_default', $inputs)) ? 1 : 0;
            $buildingDefault = (array_key_exists('building_default', $inputs)) ? 1 : 0;
            $projectDefault = (array_key_exists('project_default', $inputs)) ? 1 : 0;
            $inspectable = (array_key_exists('inspectable', $inputs)) ? 1 : 0;
            $huds = json_decode($request->get('huds'), true);
            $huds = $huds['items'];

        if (!$id) {
            $amenity = Amenity::create([
                    'amenity_description' => $inputs['amenity_description'],
                    'project' => $project,
                    'building_exterior' => $buildingExterior,
                    'building_system' => $buildingSystem,
                    'common_area' => $commonArea,
                    'file' => $file,
                    'unit' => $unit,
                    'unit_default' => $unitDefault,
                    'building_default' => $buildingDefault,
                    'project_default' => $projectDefault,
                    'inspectable' => $inspectable,
                    'policy' => $inputs['policy'],
                    'time_to_complete' => $inputs['time'],
                    'icon' => $inputs['icon']
                ]);

                // add huds
                if (count($huds)) {
                    foreach ($huds as $hud) {
                        AmenityHud::create([
                            'amenity_id' => $amenity->id,
                            'hud_inspectable_area_id' => $hud['id']
                        ]);
                    }
                }

                return response('<h2>Success!</h2><p>I created the amenity.</p>');
             

        }else{
            $amenity = Amenity::where('id', '=', $id)->first();
           
            if ($amenity) {
                $amenity->update([
                    'amenity_description' => $inputs['amenity_description'],
                    'project' => $project,
                    'building_exterior' => $buildingExterior,
                    'building_system' => $buildingSystem,
                    'common_area' => $commonArea,
                    'file' => $file,
                    'unit' => $unit,
                    'unit_default' => $unitDefault,
                    'building_default' => $buildingDefault,
                    'project_default' => $projectDefault,
                    'inspectable' => $inspectable,
                    'policy' => $inputs['policy'],
                    'time_to_complete' => $inputs['time'],
                    'icon' => $inputs['icon']
                ]);
                // remove huds
                AmenityHud::where('amenity_id', '=', $amenity->id)->delete();

                // add huds
                if (count($huds)) {
                    foreach ($huds as $hud) {
                        //dd($hud,$amenity->id);
                        AmenityHud::create([
                            'amenity_id' => $amenity->id,
                            'hud_inspectable_area_id' => $hud['id']
                        ]);
                    }
                }

                return response('I updated the amenity. That was fun! What else do you have for me?');
            } else {
                return response('I cannot find that record.');
            }
        }

        // $lc = new LogConverter('documentcategory', 'update');
        // $lc->setFrom(Auth::user())->setTo($d)->setDesc(Auth::user()->email . ' Updated Document Category ' . $d->document_category_name);
        // $lc->smartAddHistory($dold, $dnew);
        // $lc->save();
    }

    /**
     * Finding Type Store
     *
     * @param \Illuminate\Http\Request $request
     * @param null                     $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function findingtypeStore(Request $request, $id = null)
    {
        $inputs = $request->get('inputs');
        $boilerplates = json_decode($request->get('boilerplates'), true);
        $boilerplates = $boilerplates['items'];
        $huds = json_decode($request->get('huds'), true);
        $huds = $huds['items'];
        $followups = json_decode($request->get('followups'), true);
        $followups = $followups['items'];

        if (!$id) {
            $f = FindingType::create([
                'name' => $inputs['name'],
                'nominal_item_weight' => $inputs['nominal_item_weight'],
                'criticality' => $inputs['criticality'],
                'one' => (array_key_exists('one', $inputs)) ? 1 : 0,
                'two' => (array_key_exists('two', $inputs)) ? 1 : 0,
                'three' => (array_key_exists('three', $inputs)) ? 1 : 0,
                'type' => $inputs['type'],
                'building_exterior' => (array_key_exists('building_exterior', $inputs)) ? 1 : 0,
                'building_system' => (array_key_exists('building_system', $inputs)) ? 1 : 0,
                'site' => (array_key_exists('site', $inputs)) ? 1 : 0,
                'common_area' => (array_key_exists('common_area', $inputs)) ? 1 : 0,
                'unit' => (array_key_exists('unit', $inputs)) ? 1 : 0,
                'file' => (array_key_exists('file', $inputs)) ? 1 : 0
            ]);

            // add boilerplates
            if (count($boilerplates)) {
                foreach ($boilerplates as $boilerplate) {
                    FindingTypeBoilerplate::create([
                        'finding_type_id' => $f->id,
                        'boilerplate_id' => $boilerplate['id']
                    ]);
                }
            }

            // add huds
            if (count($huds)) {
                foreach ($huds as $hud) {
                    HudFindingType::create([
                        'finding_type_id' => $f->id,
                        'hud_inspectable_area_id' => $hud['id']
                    ]);
                }
            }
            
            // add followups
            if (count($followups)) {
                foreach ($followups as $followup) {
                    DefaultFollowup::create([
                        'finding_type_id' => $f->id,
                        'description' => $followup['description'],
                        'quantity' => $followup['number'],
                        'duration' => $followup['duration'],
                        'assignment' => $followup['assignment'],
                        'reply' => $followup['reply'],
                        'photo' => $followup['photo'],
                        'doc' => $followup['doc'],
                        'doc_categories' => json_encode($followup['cats'])
                    ]);
                }
            }

            // $lc = new LogConverter('documentcategory', 'create');
            // $lc->setFrom(Auth::user())->setTo($d)->setDesc(Auth::user()->email . ' Created Document Category ' . $d->document_category_name)->save();
            return response('I created the finding type. I stored it. I love it.');
        } else {
            $finding_type = FindingType::where('id', '=', $id)->first();

            if ($finding_type) {
                $finding_type->update([
                    'name' => $inputs['name'],
                    'nominal_item_weight' => $inputs['nominal_item_weight'],
                    'criticality' => $inputs['criticality'],
                    'one' => (array_key_exists('one', $inputs)) ? 1 : 0,
                    'two' => (array_key_exists('two', $inputs)) ? 1 : 0,
                    'three' => (array_key_exists('three', $inputs)) ? 1 : 0,
                    'type' => $inputs['type'],
                    'building_exterior' => (array_key_exists('building_exterior', $inputs)) ? 1 : 0,
                    'building_system' => (array_key_exists('building_system', $inputs)) ? 1 : 0,
                    'site' => (array_key_exists('site', $inputs)) ? 1 : 0,
                    'common_area' => (array_key_exists('common_area', $inputs)) ? 1 : 0,
                    'unit' => (array_key_exists('unit', $inputs)) ? 1 : 0,
                    'file' => (array_key_exists('file', $inputs)) ? 1 : 0
                ]);

                // remove boilerplates
                // remove followups
                
                FindingTypeBoilerplate::where('finding_type_id', '=', $finding_type->id)->delete();
                HudFindingType::where('finding_type_id', '=', $finding_type->id)->delete();
                DefaultFollowup::where('finding_type_id', '=', $finding_type->id)->delete();

                // add boilerplates
                if (count($boilerplates)) {
                    foreach ($boilerplates as $boilerplate) {
                        FindingTypeBoilerplate::create([
                            'finding_type_id' => $finding_type->id,
                            'boilerplate_id' => $boilerplate['id']
                        ]);
                    }
                }

                // add huds
                if (count($huds)) {
                    foreach ($huds as $hud) {
                        HudFindingType::create([
                            'finding_type_id' => $finding_type->id,
                            'hud_inspectable_area_id' => $hud['id']
                        ]);
                    }
                }
                
                // add followups
                if (count($followups)) {
                    foreach ($followups as $followup) {
                        DefaultFollowup::create([
                            'finding_type_id' => $finding_type->id,
                            'description' => $followup['description'],
                            'quantity' => $followup['number'],
                            'duration' => $followup['duration'],
                            'assignment' => $followup['assignment'],
                            'reply' => $followup['reply'],
                            'photo' => $followup['photo'],
                            'doc' => $followup['doc'],
                            'doc_categories' => json_encode($followup['cats'])
                        ]);
                    }
                }

                return response('<h2>Success!</h2><p>I updated the finding type.</p>');
            } else {
                return response('<h2>Problem...</h2><p>I am sorry, but I cannot find that record.</p>');
            }
        }
    }

    /**
     * Expense Category Store
     *
     * @param \Illuminate\Http\Request $request
     * @param null                     $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    // public function expenseCategoryStore(Request $request, $id = null)
    // {
    //     $this->validate($request, [
    //         'expense_category_name'=>'string|required'
    //     ]);
    //     if (!$id) {
    //         $e = ExpenseCategory::create([
    //             'expense_category_name' => Input::get('expense_category_name')
    //         ]);
    //         $lc = new LogConverter('expensecategory', 'create');
    //         $lc->setFrom(Auth::user())->setTo($e)->setDesc(Auth::user()->email . ' Created expense category ' . $e->expense_category_name)->save();
    //         return response('I did it. I made it exactly as you asked. What should we do next?');
    //     } else {
    //         $eold = ExpenseCategory::find($id)->toArray();
    //         ExpenseCategory::where('id', $id)->update([
    //             'expense_category_name' => Input::get('expense_category_name')
    //         ]);
    //         $e = ExpenseCategory::find($id);
    //         $enew = $e->toArray();
    //         $lc = new LogConverter('expensecategory', 'update');
    //         $lc->setFrom(Auth::user())->setTo($e)->setDesc(Auth::user()->email . ' Updated expense category ' . $e->expense_category_name);
    //         $lc->smartAddHistory($eold, $enew);
    //         $lc->save();
    //         return response('Consider that expense category updated. What is next?');
    //     }
    // }

    /**
     * Vendor Store
     *
     * @param \Illuminate\Http\Request $request
     * @param null                     $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    // public function vendorStore(Request $request, $id = null)
    // {
    //     $this->validate($request, [
    //         'vendor_name'=>'string|required',
    //         'vendor_email'=>'email',
    //         'vendor_phone'=>'string',
    //         'vendor_mobile'=>'string',
    //         'vendor_fax'=>'string',
    //         'vendor_street'=>'string',
    //         'vendor_street2'=>'string',
    //         'vendor_city'=>'string',
    //         'vendor_zip'=>'string',
    //         'vendor_duns'=>'string',
    //         'vendor_notes'=>'string'
    //     ]);
    //     if (!$id) {
    //         $v = Vendor::create([
    //             'vendor_name'=> Input::get('vendor_name'),
    //             'vendor_email'=>Input::get('vendor_email'),
    //             'vendor_phone'=>Input::get('vendor_phone'),
    //             'vendor_mobile_phone'=> Input::get('vendor_mobile'),
    //             'vendor_fax'=>Input::get('vendor_fax'),
    //             'vendor_street_address'=>Input::get('vendor_street'),
    //             'vendor_street_address2'=>Input::get('vendor_street2'),
    //             'vendor_city'=>Input::get('vendor_city'),
    //             'vendor_state_id'=>Input::get('state_id'),
    //             'vendor_zip'=>Input::get('vendor_zip'),
    //             'vendor_duns'=>Input::get('vendor_duns'),
    //             'vendor_notes'=>Input::get('vendor_notes')
    //         ]);
    //         $lc = new LogConverter('vendor', 'create');
    //         $lc->setFrom(Auth::user())->setTo($v)->setDesc(Auth::user()->email . ' Created Vendor' . $v->vendor_name)->save();

    //         return response('I created your vendor. Let us put them to work now!');
    //     } else {
    //         $vold = Vendor::find($id)->toArray();
    //         Vendor::where('id', $id)->update([
    //             'vendor_name'=> Input::get('vendor_name'),
    //             'vendor_email'=>Input::get('vendor_email'),
    //             'vendor_phone'=>Input::get('vendor_phone'),
    //             'vendor_mobile_phone'=> Input::get('vendor_mobile'),
    //             'vendor_fax'=>Input::get('vendor_fax'),
    //             'vendor_street_address'=>Input::get('vendor_street'),
    //             'vendor_street_address2'=>Input::get('vendor_street2'),
    //             'vendor_city'=>Input::get('vendor_city'),
    //             'vendor_state_id'=>Input::get('state_id'),
    //             'vendor_zip'=>Input::get('vendor_zip'),
    //             'vendor_duns'=>Input::get('vendor_duns'),
    //             'vendor_notes'=>Input::get('vendor_notes')
    //         ]);
    //         $v = Vendor::find($id);
    //         $vnew = $v->toArray();
    //         $lc = new LogConverter('vendor', 'update');
    //         $lc->setFrom(Auth::user())->setTo($v)->setDesc(Auth::user() . ' Updated vendor ' . $v->vendor_name);
    //         $lc->smartAddHistory($vold, $vnew);
    //         $lc->save();
    //         return response('I successfully updated your vendor. Let us do a little dance.');
    //     }
    // }

    /**
     * Target Area Store
     *
     * @param \Illuminate\Http\Request $request
     * @param null                     $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    // public function targetAreaStore(Request $request, $id = null)
    // {
    //     $countyIds = County::pluck('id')->toArray();
    //     $this->validate($request, [
    //         'target_area_name'=>'string|required',
    //         'county'=>'in:'.implode(',', $countyIds),
    //     ]);
    //     if (!$id) {
    //         $t = TargetArea::create([
    //             'county_id'=>Input::get('county'),
    //             'target_area_name'=>Input::get('target_area_name')
    //         ]);
    //         $lc = new LogConverter('target', 'create');
    //         $lc->setFrom(Auth::user())->setTo($t)->setDesc(Auth::user()->email . ' Created Target' . $t->target_area_name)->save();
    //         return response('I\'ve added '.Input::get('target_area_name').' to the list of available target areas.');
    //     } else {
    //         $told = TargetArea::find($id)->toArray();
    //         TargetArea::where('id', $id)->update([
    //             'county_id'=>Input::get('county'),
    //             'target_area_name'=>Input::get('target_area_name')
    //         ]);
    //         $t = TargetArea::find($id);
    //         $tnew = $t->toArray();
    //         $lc = new LogConverter('target', 'update');
    //         $lc->setFrom(Auth::user())->setTo($t)->setDesc(Auth::user() . ' Updated target area ' . $t->target_area_name);
    //         $lc->smartAddHistory($told, $tnew);
    //         $lc->save();
    //         return response('I updated the target area successfully. You\'re welcome ;) <script>$(\'#target-areas-tab\').trigger(\'click\');</script>');
    //     }
    // }

    /**
     * County Store
     *
     * @param \Illuminate\Http\Request $request
     * @param null                     $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    // public function countyStore(Request $request, $id = null)
    // {
    //     if (!$id) {
    //         return response('Oops - no county was selected? That\'s weird.');
    //     } else {
    //         $told = County::find($id)->toArray();
    //         DB::table('counties')->where('id', $id)->update([
    //             'county_name'=>Input::get('county_name'),
    //             'auditor_site'=>Input::get('auditor_site')
    //         ]);
    //         $t = TargetArea::find($id);
    //         $tnew = $t->toArray();
    //         $lc = new LogConverter('county', 'update');
    //         $lc->setFrom(Auth::user())->setTo($t)->setDesc(Auth::user() . ' Updated county information for ' . $t->county_name);
    //         $lc->smartAddHistory($told, $tnew);
    //         $lc->save();
    //         return response('I updated the county area successfully. You\'re welcome ;) <script>$(\'#counties-tab\').trigger(\'click\');</script>');
    //     }
    // }

    /**
     * Deactivate Tools
     *
     * @param $type
     * @param $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    // public function deactivateTools($type, $id)
    // {
    //     switch ($type) {
    //         case "entity":
    //             $e = Entity::find($id);
    //              Entity::where('id', $id)->update(['active'=>0]);
    //             $lc = new LogConverter('entity', 'deactivate');
    //             $lc->setFrom(Auth::user())->setTo($e)->setDesc(Auth::user()->email . ' deactivated entity ' . $e->entity_name.' as well as its programs, accounts, and users.')->save();
    //             Program::where('owner_id', $id)->update(['active'=>0]);
    //             Account::where('entity_id', $id)->update(['active'=>0]);
    //             User::where('entity_id', $id)->update(['active'=>0]);
    //             return response($e->entity_name.' has been deactivated. The users belonging to this entity can no longer access the site. I have also deactivated their users, programs, and accounts. Their programs are no longer available for landbank users to register as members through the registration screen. It will continue to show up in reports if it has parcels assigned to it.');
    //         break;
    //         case "program":
    //             $p = Program::find($id);
    //             Program::where('id', $id)->update(['active'=>0]);
    //             Account::where('owner_id', $id)->update(['active'=>0]);
    //             $lc = new LogConverter('program', 'deactivate');
    //             $lc->setFrom(Auth::user())->setTo($p)->setDesc(Auth::user()->email . ' deactivated program ' . $p->program_name)->save();
    //             return response('I have deactivated the program and its associated account. Users can no longer see the account in the registration list. If it has parcels or transactions associated with it, it will still appear in reports.');
    //         break;
    //         case "rule":
    //             $pr = ProgramRule::find($id);
    //             $p = Program::where('default_program_rules_id', $id)->where('active', 1)->count();
    //             if ($p > 0) {
    //                 if ($p > 1) {
    //                     $plural = "s";
    //                 } else {
    //                     $plural = "";
    //                 }
    //                 return "I'm sorry, I cannot deactivate $pr->rules_name because it is still being used by $p program$plural. Please reassign those program$plural to use a differnt rule before deactivating.";
    //             } else {
    //                 ProgramRule::where('id', $id)->update(['active'=>0]);
    //                 $lc = new LogConverter('programrule', 'deactivate');
    //                 $lc->setFrom(Auth::user())->setTo($pr)->setDesc(Auth::user()->email . ' deactivated program rule')->save();
    //                 return response($pr->rules_name.' has been deactivated and will not be used going forward. Parcels that used this rule previously will still show this rule unless they are reassigned to another. Inactive programs are allowed to have this rule associated with them because they cannot be used.');
    //             }
    //             break;
    //         case "account":
    //             $a = Account::find($id);
    //             $p = Program::find($a->owner_id);
    //             Account::where('id', $id)->update(['active'=>0]);
    //             $lc = new LogConverter('account', 'deactivate');
    //             $lc->setFrom(Auth::user())->setTo($a)->setDesc(Auth::user()->email . ' deactivated account ' . $a->account_name)->save();
    //             return response($a->account_name.' has been deactivated and cannot accept any new Transactions. However, if it has transactions, it will still show up in accounting until all its transactions have been moved to a different account. Please be sure to create a new account for '.$p->program_name.', otherwise it will not be able to accept reimbursements.');
    //         break;
    //         case "vendor":
    //             Vendor::where('id', $id)->update(['active'=>0]);
    //             $v = Vendor::find($id);
    //             $lc = new LogConverter('vendor', 'deactivate');
    //             $lc->setFrom(Auth::user())->setTo($v)->setDesc(Auth::user()->email . ' deactivated vendor ' . $v->vendor_name)->save();
    //             return response($v->vendor_name.' is no longer available for new expense reimbursements.');
    //         break;
    //         case "target":
    //             TargetArea::where('id', $id)->update(['active'=>0]);
    //             //TODO: add event logging
    //             $ta = TargetArea::find($id);
    //             return response('The '.$ta->target_area_name.' target area is no longer available for new parcels. Old parcels assigned to it will still show it; thus it will still show up in the parcels list until all parcels assigned to it have been reassigned.');
    //         break;
    //         case "document":
    //             $dc = DocumentCategory::find($id);
    //             DocumentCategory::where('id', $id)->update(['active'=>0]);
    //             $lc = new LogConverter('documentcategory', 'deactivate');
    //             $lc->setFrom(Auth::user())->setTo($dc)->setDesc(Auth::user()->email . ' deactivated decument category ' . $dc->document_category_name)->save();
    //             return response('I deactivated '.$dc->document_category_name.' and it will no longer be available. Please note that old documents uploaded under it will still show on their respective tabs.');
    //         break;
    //         case "expense":
    //             $ec = ExpenseCategory::find($id);
    //             ExpenseCategory::where('id', $id)->update(['active'=>0]);
    //             $lc = new LogConverter('expensecategory', 'deactivate');
    //             $lc->setFrom(Auth::user())->setTo($ec)->setDesc(Auth::user()->email . ' deactivated expense category ' . $ec->expense_category_name)->save();
    //             return response('I deactivated the expense category. Note that it will still show on parcels that used it. Account totals will also show it parenthetically if they had any expenses within it.');
    //         break;
    //     }
    // }

    /**
     * Activate Tools
     *
     * @param $type
     * @param $id
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    // public function activateTools($type, $id)
    // {
    //     switch ($type) {
    //         case "entity":
    //             $e = Entity::find($id);
    //             Entity::where('id', $id)->update(['active'=>1]);
    //             $lc = new LogConverter('entity', 'activate');
    //             $lc->setFrom(Auth::user())->setTo($e)->setDesc(Auth::user()->email . ' activated entity ' . $e->entity_name)->save();
    //             return response('I activated '.$e->entity_name.' for you. You may want to check and make sure it has an active program, account, and user.');
    //         break;
    //         case "program":
    //             Program::where('id', $id)->update(['active'=>1]);
    //             $p = Program::find($id);
    //             $lc = new LogConverter('program', 'activate');
    //             $lc->setFrom(Auth::user())->setTo($p)->setDesc(Auth::user()->email . ' activated program ' . $p->program_name)->save();
    //             return response('I activated '.$p->program_name.' for you. What\'s next?');
    //         break;
    //         case "rule":
    //             ProgramRule::where('id', $id)->update(['active'=>1]);
    //             $pr = ProgramRule::find($id);
    //             $lc = new LogConverter('programrule', 'activate');
    //             $lc->setFrom(Auth::user())->setTo($pr)->setDesc(Auth::user()->email . ' activated program rule')->save();
    //             return response('Rule is activated');
    //         break;
    //         case "account":
    //             Account::where('id', $id)->update(['active'=>1]);
    //             $a = Account::find($id);
    //             $lc = new LogConverter('account', 'activate');
    //             $lc->setFrom(Auth::user())->setTo($a)->setDesc(Auth::user()->email . ' activated account ' . $a->account_name)->save();
    //             return response('Account is activated');
    //         break;
    //         case "vendor":
    //             Vendor::where('id', $id)->update(['active'=>1]);
    //             $v = Vendor::find($id);
    //             $lc = new LogConverter('vendor', 'activate');
    //             $lc->setFrom(Auth::user())->setTo($v)->setDesc(Auth::user()->email . ' activated vendor ' . $v->vendor_name)->save();
    //             return response('Vendor is activated');
    //         break;
    //         case "target":
    //             TargetArea::where('id', $id)->update(['active'=>1]);
    //             //TODO: Add event logging
    //             return response('Target area is activated');
    //         break;
    //         case "document":
    //             DocumentCategory::where('id', $id)->update(['active'=>1]);
    //             $dc = DocumentCategory::find($id);
    //             $lc = new LogConverter('documentcategory', 'activate');
    //             $lc->setFrom(Auth::user())->setTo($dc)->setDesc(Auth::user()->email . 'activated document category ' . $dc->document_category_name)->save();
    //             return response('Document category is activated');
    //         break;
    //         case "expense":
    //             ExpenseCategory::where('id', $id)->update(['active'=>1]);
    //             $ec = ExpenseCategory::find($id);
    //             $lc = new LogConverter('expensecategory', 'activate');
    //             $lc->setFrom(Auth::user())->setTo($ec)->setDesc(Auth::user()->email . ' activated expense category ' . $ec->expense_category_name)->save();
    //             return response('Expense category is activated');
    //         break;
    //     }
    // }
}
