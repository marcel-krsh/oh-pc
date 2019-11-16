<?php

namespace App\Http\Controllers;

use App\Mail\EmailCreateNewUser;
use App\Models\Account;
use App\Models\Address;
use App\Models\Audit;
use App\Models\Communication;
use App\Models\CommunicationRecipient;
use App\Models\EmailAddress;
use App\Models\EmailAddressType;
use App\Models\Entity;
use App\Models\FindingType;
use App\Models\GuideStep;
use App\Models\HistoricEmail;
use App\Models\Jobs\ParcelsExportJob;
use App\Models\Organization;
use App\Models\Parcel;
use App\Models\People;
use App\Models\PhoneNumber;
use App\Models\PhoneNumberType;
use App\Models\Program;
use App\Models\Project;
use App\Models\ProjectContactRole;
use App\Models\Report;
use App\Models\ReportAccess;
use App\Models\Role;
use App\Models\State;
use App\Models\User;
use App\Models\UserRole;
use Auth;
use Excel;
use Gate;
use Illuminate\Http\Request;
use Image;
use Redirect;
use Session;
use Validator;
use \DB;

ini_set('max_execution_time', 600);
class PagesController extends Controller
{
  public function __construct()
  {
    if (env('APP_ENV') == 'local') {
      Auth::onceUsingId(env('USER_ID_IMPERSONATION'));
    }
  }

  public function changeLog(Request $request)
  {
    if (!Auth::check()) {
      return redirect()->to('login');
    } else {
      return view('pages.change-log');
    }
  }

  public function codes(Request $request)
  {
    if ($request->get('code')) {
      $codeId = intval($request->get('code'));
    } else {
      $codeId = null;
    }
    $codes = FindingType::get();
    return view('crr.reac-codes', compact('codeId', 'codes'));
  }

  public function resetTokens()
  {
    // SystemSetting::where('key','pcapi_access_token')->delete();
    // SystemSetting::where('key','pcapi_access_token_expires')->delete();
    // SystemSetting::where('key','pcapi_refresh_token')->delete();
    // $newTokens = SystemSetting::get();
    // dd($newTokens);
  }

  public function imageGen($image)
  {
    $img = Image::canvas(800, 800, '#ccc');
    $img->text($image, 400, 400, function ($font) {
      $font->file(base_path('storage/fonts/SourceSansPro-Light.ttf'));
      $font->size(80);
      $font->color('#fdf6e3');
      $font->align('center');
      $font->valign('center');
      $font->angle(45);
    });

    return $img->response('jpg');
  }

  public function export()
  {
    if (Auth::user()->entity_type == 'hfa') {
      $requestor = Auth::user();

      // Save report request in database
      $new_report = new Report([
        'type'            => "export_parcels",
        'folder'          => null,
        'filename'        => null,
        'pending_request' => 1,
        'user_id'         => $requestor->id,
      ]);
      $new_report->save();

      $job = new ParcelsExportJob($requestor, $new_report->id);
      dispatch($job);

      return Redirect::route('reports.listparcels')->with('systemMessage', 'Your export is being processed. An email will be sent when the file is ready to download.');
      //return redirect()->back()->with('systemMessage','Parcels export is being processed. An email will be sent when the file is ready to download.');
    } else {
      return "<script>alert('Sorry, you do not have permission to do this');</script>";
    }
  }

  public function login()
  {
    return view('auth.login');
  }

  public function stats()
  {
    if (Gate::allows('view-all-parcels')) {
      $tuser = Auth::user();
      // $lc    = new LogConverter('user', 'viewstats');
      // $lc->setDesc($tuser->email . ' Viewed stats')->setFrom($tuser)->setTo($tuser)->save();
      if (Auth::user()->entity_type == "hfa") {
        $entityTypeOperator = "like";
        $entityTypeValue    = "'%%'";
      } else {
        $entityTypeOperator = "=";
        $entityTypeValue    = Auth::user()->entity_id;
      }
      $stats = DB::select(DB::raw("
                SELECT p.program_name,
               p.id as program_id,
               p.entity_id,
               p.active,
               pc.*,
               ts.*,
               tc.*,
               tr.*,
               tp.*,
               ti.*
               -- disposition_items.*,
               -- recapture_items.*


        FROM programs p

        INNER JOIN accounts a
            ON p.entity_id = a.entity_id


        INNER JOIN
        (
            SELECT a.id AS parcels_account_id,
                   COUNT( pc.account_id ) AS Total_Parcels,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 1 THEN 1 END) AS LB__Pending,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 2 THEN 1 END) AS LB__Approved_HFA,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 3 THEN 1 END) AS LB__Withdrawn_By_HFA,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 4 THEN 1 END) AS LB__Declined_By_HFA,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 5 THEN 1 END) AS LB__InProcess_With_LB,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 6 THEN 1 END) AS LB__Ready_For_Signature_In_LB,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 7 THEN 1 END) AS LB__Ready_For_Submission_To_HFA,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 8 THEN 1 END) AS LB__Requested_Reimbursement,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 9 THEN 1 END) AS LB__Corrections_Requested_By_HFA,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 10 THEN 1 END) AS LB__Reimbursement_Approved_By_HFA,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 11 THEN 1 END) AS LB__Reimbursement_Declined_By_HFA,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 12 THEN 1 END) AS LB__Reimbursement_Withdrawn,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 13 THEN 1 END) AS LB__Invoiced_To_HFA,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 14 THEN 1 END) AS LB__Paid_By_HFA,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 15 THEN 1 END) AS LB__Disposition_Requested_To_HFA,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 16 THEN 1 END) AS LB__Disposition_Approved_By_HFA,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 17 THEN 1 END) AS LB__Disposition_Released_By_HFA,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 18 THEN 1 END) AS LB__Disposition_Declined_By_HFA,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 19 THEN 1 END) AS LB__Repayment_Required_From_HFA,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 20 THEN 1 END) AS LB__Repayment_Paid_To_HFA,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 41 THEN pc.account_id END) AS LB__Disposition_Invoice_Due_To_HFA,
                   COUNT(CASE WHEN pc.landbank_property_status_id = 42 THEN pc.account_id END) AS LB__Dispostion_Paid_To_HFA,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 21 THEN 1 END) AS HFA__Compliance_Review,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 22 THEN 1 END) AS HFA__Processing,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 23 THEN 1 END) AS HFA__Corrections_Requested_To_LB,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 24 THEN 1 END) AS HFA__Ready_For_Signators_In_HFA,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 25 THEN 1 END) AS HFA__Reimbursement_Denied_To_LB,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 26 THEN 1 END) AS HFA__Reimbursement_Approved_To_LB,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 27 THEN 1 END) AS HFA__Invoice_Received_From_LB,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 28 THEN 1 END) AS HFA__Paid_Reimbursement,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 29 THEN 1 END) AS HFA__Disposition_Requested_By_LB,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 30 THEN 1 END) AS HFA__Disposition_Approved_To_LB,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 31 THEN 1 END) AS HFA__Disposition_Invoiced_To_LB,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 32 THEN 1 END) AS HFA__Disposition_Paid_By_LB,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 33 THEN 1 END) AS HFA__Disposition_Released_To_LB,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 34 THEN 1 END) AS HFA__Repayment_Required_To_LB,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 35 THEN pc.account_id END) AS HFA__Repayment_Invoiced_To_LB,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 36 THEN pc.account_id END) AS HFA__Repayment_Received_From_LB,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 37 THEN pc.account_id END) AS HFA__Withdrawn_To_LB,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 38 THEN pc.account_id END) AS HFA__Unsubmitted,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 39 THEN pc.account_id END) AS HFA__Declined_To_LB,
                   COUNT(CASE WHEN pc.hfa_property_status_id = 40 THEN pc.account_id END) AS HFA__PO_Sent_To_LB


            FROM accounts a
            LEFT JOIN parcels pc
                ON a.id = pc.account_id
            GROUP BY a.id
        ) pc
            ON a.id = pc.parcels_account_id

        -- LEFT JOIN (
        --   SELECT disposition_items.program_id,
        --   SUM(disposition_items.amount) as Disposition_Total
        --   FROM disposition_items
        --   GROUP BY disposition_items.program_id
        -- ) disposition_items
        -- ON a.id = disposition_items.program_id



        -- LEFT JOIN (
        --   SELECT recapture_items.program_id,
        --   SUM(recapture_items.amount) as Recapture_Total
        --   FROM recapture_items
        --   GROUP BY recapture_items.program_id
        -- ) recapture_items
        -- ON a.id = recapture_items.program_id


        INNER JOIN
        (
            SELECT a.id AS transactions_account_id,
                   SUM(CASE WHEN ts.transaction_category_id = 1 THEN ts.amount ELSE 0 END) AS Deposits_Made,
                   SUM(CASE WHEN ts.transaction_category_id = 3 THEN ts.amount ELSE 0 END) AS Reimbursements_Paid,
                   SUM(CASE WHEN ts.transaction_category_id = 2 THEN ts.amount ELSE 0 END) AS Recaptures_Received,
                   SUM(CASE WHEN ts.transaction_category_id = 6 THEN ts.amount ELSE 0 END) AS Dispositions_Received,
                   SUM(CASE WHEN ts.transaction_category_id = 4 THEN ts.amount ELSE 0 END) AS Transfers_Made,
                   SUM(CASE WHEN ts.transaction_category_id = 5 THEN ts.amount ELSE 0 END) AS Line_Of_Credit


            FROM accounts a
            LEFT JOIN transactions ts
                ON a.id = ts.account_id
            GROUP BY a.id
        ) ts
            ON a.id = ts.transactions_account_id

        INNER JOIN
        (
            SELECT a.id AS cost_account_id,
                   SUM(CASE WHEN c.expense_category_id = 9 THEN c.amount ELSE 0 END) AS NIP_Loan_Cost,
                   SUM(CASE WHEN c.expense_category_id = 2 THEN c.amount ELSE 0 END) AS Acquisition_Cost,
                   SUM(CASE WHEN c.expense_category_id = 3 THEN c.amount ELSE 0 END) AS PreDemo_Cost,
                   SUM(CASE WHEN c.expense_category_id = 4 THEN c.amount ELSE 0 END) AS Demolition_Cost,
                   SUM(CASE WHEN c.expense_category_id = 5 THEN c.amount ELSE 0 END) AS Greening_Cost,
                   SUM(CASE WHEN c.expense_category_id = 6 THEN c.amount ELSE 0 END) AS Maintenance_Cost,
                   SUM(CASE WHEN c.expense_category_id = 7 THEN c.amount ELSE 0 END) AS Administration_Cost,
                   SUM(CASE WHEN c.expense_category_id = 8 THEN c.amount ELSE 0 END) AS Other_Cost,
                   AVG(CASE WHEN c.expense_category_id = 9 THEN c.amount ELSE 0 END) AS NIP_Loan_Cost_Average,
                   AVG(CASE WHEN c.expense_category_id = 2 THEN c.amount ELSE 0 END) AS Acquisition_Cost_Average,
                   AVG(CASE WHEN c.expense_category_id = 3 THEN c.amount ELSE 0 END) AS PreDemo_Cost_Average,
                   AVG(CASE WHEN c.expense_category_id = 4 THEN c.amount ELSE 0 END) AS Demolition_Cost_Average,
                   AVG(CASE WHEN c.expense_category_id = 5 THEN c.amount ELSE 0 END) AS Greening_Cost_Average,
                   AVG(CASE WHEN c.expense_category_id = 6 THEN c.amount ELSE 0 END) AS Maintenance_Cost_Average,
                   AVG(CASE WHEN c.expense_category_id = 7 THEN c.amount ELSE 0 END) AS Administration_Cost_Average,
                   AVG(CASE WHEN c.expense_category_id = 8 THEN c.amount ELSE 0 END) AS Other_Cost_Average,
               COALESCE(SUM(c.amount),0) AS Total_Cost
            FROM accounts a
            LEFT JOIN cost_items c
                ON a.id = c.account_id
            GROUP BY a.id
        ) tc
            ON a.id = tc.cost_account_id

        INNER JOIN
        (
            SELECT a.id AS request_account_id,
                   SUM(CASE WHEN r.expense_category_id = 9 THEN r.amount ELSE 0 END) AS NIP_Loan_Requested,
                   SUM(CASE WHEN r.expense_category_id = 2 THEN r.amount ELSE 0 END) AS Acquisition_Requested,
                   SUM(CASE WHEN r.expense_category_id = 3 THEN r.amount ELSE 0 END) AS PreDemo_Requested,
                   SUM(CASE WHEN r.expense_category_id = 4 THEN r.amount ELSE 0 END) AS Demolition_Requested,
                   SUM(CASE WHEN r.expense_category_id = 5 THEN r.amount ELSE 0 END) AS Greening_Requested,
                   SUM(CASE WHEN r.expense_category_id = 6 THEN r.amount ELSE 0 END) AS Maintenance_Requested,
                   SUM(CASE WHEN r.expense_category_id = 7 THEN r.amount ELSE 0 END) AS Administration_Requested,
                   SUM(CASE WHEN r.expense_category_id = 8 THEN r.amount ELSE 0 END) AS Other_Requested,
               COALESCE(SUM(r.amount),0) AS Total_Requested
            FROM accounts a
            LEFT JOIN request_items r
                ON a.id = r.account_id
            GROUP BY a.id
        ) tr
            ON a.id = tr.request_account_id

        INNER JOIN
        (
            SELECT a.id AS po_account_id,
                   SUM(CASE WHEN po.expense_category_id = 9 THEN po.amount ELSE 0 END) AS NIP_Loan_Approved,
                   SUM(CASE WHEN po.expense_category_id = 2 THEN po.amount ELSE 0 END) AS Acquisition_Approved,
                   SUM(CASE WHEN po.expense_category_id = 3 THEN po.amount ELSE 0 END) AS PreDemo_Approved,
                   SUM(CASE WHEN po.expense_category_id = 4 THEN po.amount ELSE 0 END) AS Demolition_Approved,
                   SUM(CASE WHEN po.expense_category_id = 5 THEN po.amount ELSE 0 END) AS Greening_Approved,
                   SUM(CASE WHEN po.expense_category_id = 6 THEN po.amount ELSE 0 END) AS Maintenance_Approved,
                   SUM(CASE WHEN po.expense_category_id = 7 THEN po.amount ELSE 0 END) AS Administration_Approved,
                   SUM(CASE WHEN po.expense_category_id = 8 THEN po.amount ELSE 0 END) AS Other_Approved,
               COALESCE(SUM(po.amount),0) AS Total_Approved
            FROM accounts a
            LEFT JOIN po_items po
                ON a.id = po.account_id
            GROUP BY a.id
        ) tp
            ON a.id = tp.po_account_id

        INNER JOIN
        (
            SELECT a.id AS inv_account_id,
                   SUM(CASE WHEN inv.expense_category_id = 9 THEN inv.amount ELSE 0 END) AS NIP_Loan_Invoiced,
                   SUM(CASE WHEN inv.expense_category_id = 2 THEN inv.amount ELSE 0 END) AS Acquisition_Invoiced,
                   SUM(CASE WHEN inv.expense_category_id = 3 THEN inv.amount ELSE 0 END) AS PreDemo_Invoiced,
                   SUM(CASE WHEN inv.expense_category_id = 4 THEN inv.amount ELSE 0 END) AS Demolition_Invoiced,
                   SUM(CASE WHEN inv.expense_category_id = 5 THEN inv.amount ELSE 0 END) AS Greening_Invoiced,
                   SUM(CASE WHEN inv.expense_category_id = 6 THEN inv.amount ELSE 0 END) AS Maintenance_Invoiced,
                   SUM(CASE WHEN inv.expense_category_id = 7 THEN inv.amount ELSE 0 END) AS Administration_Invoiced,
                   SUM(CASE WHEN inv.expense_category_id = 8 THEN inv.amount ELSE 0 END) AS Other_Invoiced,
               COALESCE(SUM(inv.amount),0) AS Total_Invoiced
            FROM accounts a
            LEFT JOIN invoice_items inv
                ON a.id = inv.account_id
            GROUP BY a.id
        ) ti
            ON a.id = ti.inv_account_id
            WHERE p.id <> 1
            AND p.entity_id $entityTypeOperator $entityTypeValue
            ORDER BY p.program_name
                        "));

      $sumStatData = [];

      foreach ($stats as $k => $subArray) {
        foreach ($subArray as $id => $value) {
          if (is_numeric($value)) {
            array_key_exists($id, $sumStatData) ? $sumStatData[$id] += $value : $sumStatData[$id] = $value;
          }
        }
      }
      //dd($sumStatData);

      return view('pages.stats', compact('stats', 'sumStatData'));
    } else {
      $tuser = Auth::user();
      // $lc    = new LogConverter('user', 'unauthorized viewstats');
      // $lc->setDesc($tuser->email . ' attempted to view stats')->setFrom($tuser)->setTo($tuser)->save();
      return 'Sorry you do not have access to the dashboard.';
    }
  }

  public function dashboard(Request $request)
  {
    //if (Gate::allows('view-all-parcels')) {
    $sentEmailTo = "NA";
    // if(Auth::user()->show_how_to == 1 && session('shownHowTo') < 1) {
    //   $showHowTo = 1;
    //   session(['shownHowTo' => 1]);
    // } else {
    //   $showHowTo = 0;
    // }
    $showHowTo = 0;

    if ($request->query('tab') >= 1) {
      $tab       = "dash-subtab-" . intval($request->query('tab'));
      $showHowTo = 2;
    } else {
      // default tab to load
      $tab = "dash-subtab-10";
    }

    if ($request->query('parcelsListFilter') > 0) {
      session(['parcels_status_filter' => intval($request->query('parcelsListFilter'))]);
      $showHowTo = 2;
    } else {
      $parcelsListFilter = 0;
    }
    if ($request->query('hfaParcelsListFilter') > 0) {
      session(['hfa_parcels_status_filter' => intval($request->query('hfaParcelsListFilter'))]);
      $showHowTo = 2;
    } else {
      $hfaParcelsListFilter = 0;
    }

    if ($request->query('parcelsListFilter') == 7) {
      // Give instruction on how to submit for reimbursement.
      $showHowTo = 3;
    }
    if ($request->query('parcelsListFilter') == 6) {
      // Give instruction on how to approve for reimbursement.
      $showHowTo = 4;
    }
    if ($request->query('parcelsListFilter') == 9) {
      // Give instruction on how to correct parcels for hfa.
      $showHowTo = 5;
    }
    if ($request->query('parcelsListFilter') == 47) {
      // Give instruction on how to correct parcels internally.
      $showHowTo = 6;
    }
    if ($request->query('parcelsListFilter') == 10) {
      // Give instruction on how to invoice parcels.
      $showHowTo = 7;
    }
    if ($request->query('parcelsListFilter') == 11) {
      // Give instruction on steps to take for a declined reimbursement.
      $showHowTo = 8;
    }
    if ($request->query('posStatusFilter') == 2) {
      // Give instruction on steps to take for a approved POs.
      $showHowTo = 9;
    }
    if ($request->query('requestsStatusFilter') == 3) {
      // Give instruction on steps to take for a declined Requests.
      $showHowTo = 10;
    }

    //// load the sitevisit tab instead
    $routed = \Route::getFacadeRoot()->current()->uri();
    if ("site_visit_manager" == $routed) {
      // Give instruction on steps to take for a approved POs.
      $loadDetailTab = 2;
    } else {
      $loadDetailTab = 1;
    }

    $current_user          = Auth::user();
    $unseen_communications = CommunicationRecipient::where('user_id', $current_user->id)
      ->where('seen', '0')
      ->groupBy('communication_id', 'id', 'seen', 'user_id', 'created_at', 'updated_at')
      ->with('communication')
      ->with('communication.owner')
      ->get();

    if (count($unseen_communications)) {
      foreach ($unseen_communications as $unseen_communication) {
        $unseen_communication->summary = strlen($unseen_communication->communication->message) > 400 ? substr($unseen_communication->communication->message, 0, 20) . "..." : $unseen_communication->communication->message;
      }
    } else {
      $unseen_communications = [];
    }

    $filter['lbFilters']  = DB::table('property_status_options')->where('for', 'landbank')->where('active', '1')->orderBy('order', 'asc')->get();
    $filter['hfaFilters'] = DB::table('property_status_options')->where('for', 'hfa')->where('active', '1')->orderBy('order', 'asc')->get();
    return view('pages.dashboard', compact('showHowTo', 'tab', 'parcelsListFilter', 'sentEmailTo', 'unseen_communications', 'filter', 'loadDetailTab'));
    // } else {
    //     $error = "Sorry you do not have access to the dashboard.";
    //     $message = "";
    //     $type = "danger";
    //     if (Auth::user()){
    //         if (Auth::user()->active == 0) {
    //             if (Auth::user()->validate_all !=1) {
    //                 $message = "It doesn\'t appear your user has been activated yet. If you just registered they may not have reviewed your request just yet. If it has been awhile, please contact your admin directly to activate your user account.";
    //             } else {
    //                 $message = "Your account is under review by one of my admins. You will be notified when it is activated.";
    //             }
    //         }
    //     }
    //     return view('pages.error', compact('error', 'message', 'type'));
    // }
  }

  public function map(Request $request)
  {
    if (Gate::allows('view-all-parcels')) {
      if (1 == $request->sdo) {
        $points = DB::table('sdo_parcels')->select('latitude', 'longitude')->get()->all();
      } elseif (Auth::user()->entity_type == "hfa") {
        $points = Parcel::select('latitude', 'longitude')->get()->all();
      } else {
        $points = Parcel::select('latitude', 'longitude')->where('entity_id', Auth::user()->entity_id)->get()->all();
      }

      return view('pages.map', compact('points', 'request'));
    } else {
      return 'Sorry you do not have access to the map.';
    }
  }

  public function parcelList(Request $request)
  {
    if (app('env') == 'local') {
      app('debugbar')->disable();
    }
    if (Gate::allows('view-all-parcels')) {
      // determine if they are OHFA or not
      if (Auth::user()->entity_id != 1) {
        // create values for a where clause
        $where_entity_id          = Auth::user()->entity_id;
        $where_entity_id_operator = '=';
      } else {
        // they are OHFA - see them all
        $where_entity_id          = 0;
        $where_entity_id_operator = '>';
      }

      // Build out the query and store it
      // start with sorting

      /// The sorting column
      $sortedBy = $request->query('parcels_sort_by');
      /// Retain the original value submitted through the query
      if (strlen($sortedBy) > 0) {
        // update the sort by
        session(['parcels_sorted_by_query' => $sortedBy]);
        $parcels_sorted_by_query = $request->session()->get('parcels_sorted_by_query');
      } elseif (!is_null($request->session()->get('parcels_sorted_by_query'))) {
        // use the session value

        $parcels_sorted_by_query = $request->session()->get('parcels_sorted_by_query');
      } else {
        // set the default
        session(['parcels_sorted_by_query' => '12']);
        $parcels_sorted_by_query = $request->session()->get('parcels_sorted_by_query');
      }
      //dd($parcels_sorted_by_query);

      /// If a new sort has been provided
      // Rebuild the query

      if (!is_null($sortedBy)) {
        switch ($request->query('parcels_asc_desc')) {
          case '1':
            # code...
            session(['parcels_asc_desc' => 'desc']);
            $parcelsAscDesc = $request->session()->get('parcels_asc_desc');
            session(['parcels_asc_desc_opposite' => ""]);
            $parcelsAscDescOpposite = $request->session()->get('parcels_asc_desc_opposite');
            break;

          default:
            session(['parcels_asc_desc' => 'asc']);
            $parcelsAscDesc = $request->session()->get('parcels_asc_desc');
            session(['parcels_asc_desc_opposite' => 1]);
            $parcelsAscDescOpposite = $request->session()->get('parcels_asc_desc_opposite');
            break;
        }
        switch ($sortedBy) {
          case '1':
            # parcel id
            session(['parcels_sort_by' => 'parcels.parcel_id']);
            $parcelsSortBy = $request->session()->get('parcels_sort_by');
            break;
          case '2':
            # Address street
            session(['parcels_sort_by' => 'parcels.street_address']);
            $parcelsSortBy = $request->session()->get('parcels_sort_by');
            break;
          case '3':
            # Address city
            session(['parcels_sort_by' => 'parcels.city']);
            $parcelsSortBy = $request->session()->get('parcels_sort_by');
            break;
          case '4':
            # Address state
            session(['parcels_sort_by' => 'parcels.state_id']);
            $parcelsSortBy = $request->session()->get('parcels_sort_by');
            break;
          case '5':
            # Address zip
            session(['parcels_sort_by' => 'parcels.zip']);
            $parcelsSortBy = $request->session()->get('parcels_sort_by');
            break;
          case '6':
            # program
            session(['parcels_sort_by' => 'program_name']);
            $parcelsSortBy = $request->session()->get('parcels_sort_by');
            break;
          case '7':
            # Cost
            session(['parcels_sort_by' => 'cost_total']);
            $parcelsSortBy = $request->session()->get('parcels_sort_by');
            break;
          case '8':
            #  Requested
            session(['parcels_sort_by' => 'requested_total']);
            $parcelsSortBy = $request->session()->get('parcels_sort_by');
            break;
          case '9':
            #  Approved
            session(['parcels_sort_by' => 'approved_total']);
            $parcelsSortBy = $request->session()->get('parcels_sort_by');
            break;
          case '10':
            #  Paid
            session(['parcels_sort_by' => 'invoiced_total']);
            $parcelsSortBy = $request->session()->get('parcels_sort_by');
            break;
          case '11':
            #  Paid
            session(['parcels_sort_by' => 'property_status_options.option_name']);
            $parcelsSortBy = $request->session()->get('parcels_sort_by');
            break;
          case '12':
            #  Date
            session(['parcels_sort_by' => 'parcels.created_at']);
            $parcelsSortBy = $request->session()->get('parcels_sort_by');
            break;

          case '13':
            #  HFA Status
            session(['parcels_sort_by' => 'hfa_property_status_options.option_name']);
            $parcelsSortBy = $request->session()->get('parcels_sort_by');
            break;
          case '14':
            #  Target Area
            session(['parcels_sort_by' => 'target_areas.target_area_name']);
            $parcelsSortBy = $request->session()->get('parcels_sort_by');
            break;
          case '15':
            #  Completed Step
            session(['parcels_sort_by' => 'guide_steps.name']);
            $parcelsSortBy = $request->session()->get('parcels_sort_by');
            break;
          case '16':
            #  Not Completed Step
            session(['parcels_sort_by' => 'guide_steps.name']);
            $parcelsSortBy = $request->session()->get('parcels_sort_by');
            break;
          case '17':
            #  Next Step
            session(['parcels_sort_by' => 'guide_steps.name']);
            $parcelsSortBy = $request->session()->get('parcels_sort_by');
            break;
          default:
            # code...
            session(['parcels_sort_by' => 'parcels.created_at']);
            $parcelsSortBy = $request->session()->get('parcels_sort_by');
            break;
        }
      } elseif (is_null($request->session()->get('parcels_sort_by'))) {
        // no values in the session - then store in simpler variables.
        session(['parcels_sort_by' => 'parcels.created_at']);
        $parcelsSortBy = $request->session()->get('parcels_sort_by');
        session(['parcels_asc_desc' => 'asc']);
        $parcelsAscDesc = $request->session()->get('parcels_asc_desc');
        session(['parcels_asc_desc_opposite' => '1']);
        $parcelsAscDescOpposite = $request->session()->get('parcels_asc_desc_opposite');
      } else {
        // use values in the session
        $parcelsSortBy          = $request->session()->get('parcels_sort_by');
        $parcelsAscDesc         = $request->session()->get('parcels_asc_desc');
        $parcelsAscDescOpposite = $request->session()->get('parcels_asc_desc_opposite');
      }

      // Check if there is a Program Filter Provided
      if (is_numeric($request->query('parcels_program_filter'))) {
        //Update the session
        session(['parcels_program_filter' => $request->query('parcels_program_filter')]);
        $parcelsProgramFilter = $request->session()->get('parcels_program_filter');
        session(['parcels_program_filter_operator' => '=']);
        $parcelsProgramFilterOperator = $request->session()->get('parcels_program_filter_operator');
        // clear the target area filter on program filter change
        session(['target_area_filter' => '%%']);
        session(['target_area_filter_operator' => 'LIKE']);
      } elseif (is_null($request->session()->get('parcels_program_filter')) || $request->query('parcels_program_filter') == 'ALL') {
        // There is no Program Filter in the Session
        session(['parcels_program_filter' => '%%']);
        $parcelsProgramFilter = $request->session()->get('parcels_program_filter');
        session(['parcels_program_filter_operator' => 'LIKE']);
        $parcelsProgramFilterOperator = $request->session()->get('parcels_program_filter_operator');
      } else {
        // use values in the session
        $parcelsProgramFilter         = $request->session()->get('parcels_program_filter');
        $parcelsProgramFilterOperator = $request->session()->get('parcels_program_filter_operator');
      }

      // Check if there is a lb status filter
      if (is_numeric($request->query('parcels_status_filter'))) {
        //Update the session
        session(['parcels_status_filter' => $request->query('parcels_status_filter')]);
        $parcelsStatusFilter = $request->session()->get('parcels_status_filter');
        session(['parcels_status_filter_operator' => '=']);
        $parcelsStatusFilterOperator = $request->session()->get('parcels_status_filter_operator');
      } elseif (is_null($request->session()->get('parcels_status_filter')) || $request->query('parcels_status_filter') == 'ALL') {
        // There is no Program Filter in the Session
        session(['parcels_status_filter' => '%%']);
        $parcelsStatusFilter = $request->session()->get('parcels_status_filter');
        session(['parcels_status_filter_operator' => 'LIKE']);
        $parcelsStatusFilterOperator = $request->session()->get('parcels_status_filter_operator');
      } else {
        // use values in the session
        $parcelsStatusFilter = $request->session()->get('parcels_status_filter');
        if ($request->session()->get('parcels_status_filter_operator') == null) {
          session(['parcels_status_filter_operator' => '=']);
        }
        $parcelsStatusFilterOperator = $request->session()->get('parcels_status_filter_operator');
      }

      // Check if there is a hfa status filter
      if (is_numeric($request->query('hfa_parcels_status_filter'))) {
        //Update the session
        session(['hfa_parcels_status_filter' => $request->query('hfa_parcels_status_filter')]);
        $hfaParcelsStatusFilter = $request->session()->get('hfa_parcels_status_filter');
        if ($request->session()->get('hfa_parcels_status_filter_operator') == null) {
          session(['hfa_parcels_status_filter_operator' => '=']);
        }
        $hfaParcelsStatusFilterOperator = $request->session()->get('hfa_parcels_status_filter_operator');
      } elseif (is_null($request->session()->get('hfa_parcels_status_filter')) || $request->query('hfa_parcels_status_filter') == 'ALL') {
        // There is no Program Filter in the Session
        session(['hfa_parcels_status_filter' => '%%']);
        $hfaParcelsStatusFilter = $request->session()->get('hfa_parcels_status_filter');
        session(['hfa_parcels_status_filter_operator' => 'LIKE']);
        $hfaParcelsStatusFilterOperator = $request->session()->get('hfa_parcels_status_filter_operator');
      } else {
        // use values in the session
        $hfaParcelsStatusFilter = $request->session()->get('hfa_parcels_status_filter');
        if ($request->session()->get('hfa_parcels_status_filter_operator') == null) {
          session(['hfa_parcels_status_filter_operator' => '=']);
        }
        $hfaParcelsStatusFilterOperator = $request->session()->get('hfa_parcels_status_filter_operator');
      }

      // Check if there is a target area filter
      if (is_numeric($request->query('target_area_filter'))) {
        //Update the session
        session(['target_area_filter' => $request->query('target_area_filter')]);
        $targetAreaFilter = $request->session()->get('target_area_filter');
        if ($request->session()->get('target_area_filter_operator') == null) {
          session(['target_area_filter_operator' => '=']);
        }
        $targetAreaFilterOperator = $request->session()->get('target_area_filter_operator');
      } elseif (is_null($request->session()->get('target_area_filter')) || $request->query('target_area_filter') == 'ALL') {
        // There is no Program Filter in the Session
        session(['target_area_filter' => '%%']);
        $targetAreaFilter = $request->session()->get('target_area_filter');
        session(['target_area_filter_operator' => 'LIKE']);
        $targetAreaFilterOperator = $request->session()->get('target_area_filter_operator');
      } else {
        // use values in the session
        $targetAreaFilter = $request->session()->get('target_area_filter');
        if ($request->session()->get('target_area_filter_operator') == null) {
          session(['target_area_filter_operator' => '=']);
        }
        $targetAreaFilterOperator = $request->session()->get('target_area_filter_operator');
      }

      // NEXT STEP FILTER
      if (is_numeric($request->query('parcels_next_filter'))) {
        //Update the session
        session(['parcels_next_filter' => $request->query('parcels_next_filter')]);
        $parcelsNextFilter = $request->session()->get('parcels_next_filter');
        if ($request->session()->get('parcels_next_filter_operator') == null) {
          session(['parcels_next_filter_operator' => '=']);
        }
        $parcelsNextFilterOperator = $request->session()->get('parcels_next_filter_operator');
      } elseif (is_null($request->session()->get('parcels_next_filter')) || $request->query('parcels_next_filter') == 'ALL') {
        // There is no Next Step Filter in the Session
        session(['parcels_next_filter' => '%%']);
        $parcelsNextFilter = $request->session()->get('parcels_next_filter');
        session(['parcels_next_filter_operator' => 'LIKE']);
        $parcelsNextFilterOperator = $request->session()->get('parcels_next_filter_operator');
      } else {
        // use values in the session
        $parcelsNextFilter = $request->session()->get('parcels_next_filter');
        if ($request->session()->get('parcels_next_filter_operator') == null) {
          session(['parcels_next_filter_operator' => '=']);
        }
        $parcelsNextFilterOperator = $request->session()->get('parcels_next_filter_operator');
      }
      /*
      // STEP COMPLETED FILTER
      if (is_numeric($request->query('step_completed_filter'))) {
      //Update the session
      session(['step_completed_filter' => $request->query('step_completed_filter')]);
      $stepCompletedFilter = $request->session()->get('step_completed_filter');
      if ($request->session()->get('step_completed_filter_operator') == null) {
      session(['step_completed_filter_operator' => '=']);
      }
      $stepCompletedFilterOperator = $request->session()->get('step_completed_filter_operator');
      } elseif (is_null($request->session()->get('step_completed_filter')) || $request->query('step_completed_filter') == 'ALL') {
      session(['step_completed_filter' => '0']);
      $stepCompletedFilter = $request->session()->get('step_completed_filter');
      session(['step_completed_filter_operator' => 'LIKE']);
      $stepCompletedFilterOperator = $request->session()->get('step_completed_filter_operator');
      } else {
      // use values in the session
      $stepCompletedFilter = $request->session()->get('step_completed_filter');
      if ($request->session()->get('step_completed_filter_operator') == null) {
      session(['step_completed_filter_operator' => '=']);
      }
      $stepCompletedFilterOperator = $request->session()->get('step_completed_filter_operator');
      }

      // STEP NOT COMPLETED FILTER
      if (is_numeric($request->query('step_not_completed_filter'))) {
      //Update the session
      session(['step_not_completed_filter' => $request->query('step_not_completed_filter')]);
      $stepNotCompletedFilter = $request->session()->get('step_not_completed_filter');
      if ($request->session()->get('step_not_completed_filter_operator') == null) {
      session(['step_not_completed_filter_operator' => '=']);
      }
      $stepNotCompletedFilterOperator = $request->session()->get('step_not_completed_filter_operator');
      } elseif (is_null($request->session()->get('step_not_completed_filter')) || $request->query('step_not_completed_filter') == 'ALL') {
      session(['step_not_completed_filter' => '0']);
      $stepNotCompletedFilter = $request->session()->get('step_not_completed_filter');
      session(['step_not_completed_filter_operator' => 'LIKE']);
      $stepNotCompletedFilterOperator = $request->session()->get('step_not_completed_filter_operator');
      } else {
      // use values in the session
      $stepNotCompletedFilter = $request->session()->get('step_not_completed_filter');
      if ($request->session()->get('step_not_completed_filter_operator') == null) {
      session(['step_not_completed_filter_operator' => '=']);
      }
      $stepNotCompletedFilterOperator = $request->session()->get('step_not_completed_filter_operator');
      }
       */
      // RETAINAGE FILTER
      if (is_numeric($request->query('retainage_filter'))) {
        //Update the session
        session(['retainage_filter' => $request->query('retainage_filter')]);
        $retainageFilter = $request->session()->get('retainage_filter');
      } elseif (is_null($request->session()->get('retainage_filter')) || $request->query('retainage_filter') == 'ALL') {
        // There is no Next Step Filter in the Session
        session(['retainage_filter' => '%%']);
        $retainageFilter = $request->session()->get('retainage_filter');
      } else {
        // use values in the session
        $retainageFilter = $request->session()->get('retainage_filter');
      }

      // Translate the number to actual retainage status to check
      // Query 1 is a whereHas
      // Query 2 is a whereDoesntHave
      if (1 == $retainageFilter) {
        // unpaid retainages only
        $retainageQuery1         = 'unpaidRetainages';
        $retainageFilterOperator = '>';
        $retainageFilterValue    = '0';
        $retainageFiltered       = 'UNPAID RETAINAGE';
      }
      if (2 == $retainageFilter) {
        // paid retainages only
        $retainageQuery1         = 'paidRetainages';
        $retainageFilterOperator = '>';
        $retainageFilterValue    = '0';
        $retainageFiltered       = 'PAID RETAINAGE';
      }
      if (3 == $retainageFilter) {
        // no retainages only
        $retainageQuery1         = 'retainages';
        $retainageFilterOperator = '=';
        $retainageFilterValue    = '0';
        $retainageFiltered       = 'NO RETAINAGE';
      }
      if ('%%' == $retainageFilter) {
        // all parcels
        $retainageQuery1         = 'state';
        $retainageFilterOperator = '=';
        $retainageFilterValue    = '1';
        $retainageFiltered       = null;
      }

      // ADVANCE PAYMENT FILTER
      if (is_numeric($request->query('advance_filter'))) {
        //Update the session
        session(['advance_filter' => $request->query('advance_filter')]);
        $advanceFilter = $request->session()->get('advance_filter');
      } elseif (is_null($request->session()->get('advance_filter')) || $request->query('advance_filter') == 'ALL') {
        // There is no Next Step Filter in the Session
        session(['advance_filter' => '%%']);
        $advanceFilter = $request->session()->get('advance_filter');
      } else {
        // use values in the session
        $advanceFilter = $request->session()->get('advance_filter');
      }

      // Translate the number to actual retainage status to check
      // Query 1 is a whereHas
      // Query 2 is a whereDoesntHave
      if (1 == $advanceFilter) {
        // unpaid advances only
        $advanceQuery1         = 'unpaidAdvanceItems';
        $advanceFilterOperator = '>';
        $advanceFilterValue    = '0';
        $advanceFiltered       = 'UNPAID ADVANCE';
      }
      if (2 == $advanceFilter) {
        // paid advances only
        $advanceQuery1         = 'paidAdvanceItems';
        $advanceFilterOperator = '>';
        $advanceFilterValue    = '0';
        $advanceFiltered       = 'PAID ADVANCE';
      }
      if (3 == $advanceFilter) {
        // no advances only
        $advanceQuery1         = 'advanceItems';
        $advanceFilterOperator = '=';
        $advanceFilterValue    = '0';
        $advanceFiltered       = 'NO ADVANCE';
      }
      if ('%%' == $advanceFilter) {
        // all parcels
        $advanceQuery1         = 'state';
        $advanceFilterOperator = '=';
        $advanceFilterValue    = '1';
        $advanceFiltered       = null;
      }

      // DISPOSITION FILTER
      if (is_numeric($request->query('disposition_filter'))) {
        //Update the session
        session(['disposition_filter' => $request->query('disposition_filter')]);
        $dispositionFilter = $request->session()->get('disposition_filter');
      } elseif (is_null($request->session()->get('disposition_filter')) || $request->query('disposition_filter') == 'ALL') {
        // There is no Next Step Filter in the Session
        session(['disposition_filter' => '%%']);
        $dispositionFilter = $request->session()->get('disposition_filter');
      } else {
        // use values in the session
        $dispositionFilter = $request->session()->get('disposition_filter');
      }

      // Translate the number to actual disposition status to check
      // Query 1 is a whereHas
      // Query 2 is a whereDoesntHave
      if (1 == $dispositionFilter) {
        // draft
        $dispositionQuery1         = 'draftDispositions';
        $dispositionFilterOperator = '>';
        $dispositionFilterValue    = '0';
        $dispositionFiltered       = 'Draft dispositions';
      }
      if (2 == $dispositionFilter) {
        // pending lb approval
        $dispositionQuery1         = 'pendingLbApprovalDispositions';
        $dispositionFilterOperator = '>';
        $dispositionFilterValue    = '0';
        $dispositionFiltered       = 'Dispositions Pending LB Approval';
      }
      if (3 == $dispositionFilter) {
        // pending hfa approval
        $dispositionQuery1         = 'pendingHfaApprovalDispositions';
        $dispositionFilterOperator = '>';
        $dispositionFilterValue    = '0';
        $dispositionFiltered       = 'Dispositions Pending HFA Approval';
      }
      if (4 == $dispositionFilter) {
        // pending payment
        $dispositionQuery1         = 'pendingPaymentDispositions';
        $dispositionFilterOperator = '>';
        $dispositionFilterValue    = '0';
        $dispositionFiltered       = 'Dispositions Pending Payment';
      }
      if (5 == $dispositionFilter) {
        // declined
        $dispositionQuery1         = 'declinedDispositions';
        $dispositionFilterOperator = '>';
        $dispositionFilterValue    = '0';
        $dispositionFiltered       = 'Declined Dispositions';
      }
      if (6 == $dispositionFilter) {
        // pending hfa approval
        $dispositionQuery1         = 'paidDispositions';
        $dispositionFilterOperator = '>';
        $dispositionFilterValue    = '0';
        $dispositionFiltered       = 'Paid Dispositions';
      }
      if (7 == $dispositionFilter) {
        // Approved Dispositions
        $dispositionQuery1         = 'approvedDispositions';
        $dispositionFilterOperator = '>';
        $dispositionFilterValue    = '0';
        $dispositionFiltered       = 'Approved Dispositions';
      }
      if (8 == $dispositionFilter) {
        // submitted to fiscal agent
        $dispositionQuery1         = 'dispositions_submitted_to_fiscal_agent';
        $dispositionFilterOperator = '>';
        $dispositionFilterValue    = '0';
        $dispositionFiltered       = 'Submitted to Fiscal Agent';
      }
      if (9 == $dispositionFilter) {
        // has dispostitons
        $dispositionQuery1         = 'dispositions';
        $dispositionFilterOperator = '>';
        $dispositionFilterValue    = '0';
        $dispositionFiltered       = 'Has disposition(s)';
      }
      if (10 == $dispositionFilter) {
        // release requested
        $dispositionQuery1         = 'dispositions';
        $dispositionFilterOperator = '=';
        $dispositionFilterValue    = '0';
        $dispositionFiltered       = 'No Dispositions';
      }
      if (11 == $dispositionFilter) {
        // release requested
        $dispositionQuery1         = 'releaseRequestedDispositions';
        $dispositionFilterOperator = '>';
        $dispositionFilterValue    = '0';
        $dispositionFiltered       = 'Release Requested Dispositions';
      }
      if (12 == $dispositionFilter) {
        // released
        $dispositionQuery1         = 'releasedDispositions';
        $dispositionFilterOperator = '>';
        $dispositionFilterValue    = '0';
        $dispositionFiltered       = 'Released Dispositions';
      }

      if ('%%' == $dispositionFilter) {
        // all parcels
        $dispositionQuery1         = 'state';
        $dispositionFilterOperator = '=';
        $dispositionFilterValue    = '1';
        $dispositionFiltered       = null;
      }

      // SET THE FILTER BADGE FOR STATUS

      $statusFiltered           = null;
      $hfaStatusFiltered        = null;
      $parcelNextFiltered       = null;
      $targetAreaFiltered       = null;
      $programFiltered          = null;
      $stepCompletedFiltered    = null;
      $stepNotCompletedFiltered = null;
      // $advanceFiltered is set in the query config
      // $reatainageFiltered is set in the query config
      // $dispositionFiltered is set in the query config

      // Insert other Filters here

      $currentUser = Auth::user();

      if ($request->query('export') != 1) {
        // BASE INFO FOR PAGE - DON'T NEED IT FOR THE EXPORT.
        // Programs
        if (Auth::user()->entity_type == "hfa") {
          $programs = Parcel::join('programs', 'parcels.program_id', '=', 'programs.id')->select('programs.program_name', 'programs.id')->groupBy('programs.id', 'programs.program_name')->orderBy('programs.program_name')->get();
        }

        // Guide Steps
        if (Auth::user()->entity_type == "landbank") {
          $nextSteps = \App\Models\GuideStep::with('isNextStep')->join('guide_step_types', 'guide_steps.guide_step_type_id', 'guide_step_types.id')->select('guide_steps.*', 'guide_step_types.name as type_name')->whereNull('hidden_from_lb')->orderBy('guide_step_type_id', 'desc')->orderBy('order', 'asc')->get();
        } else {
          $nextSteps = \App\Models\GuideStep::with('isNextStep')->join('guide_step_types', 'guide_steps.guide_step_type_id', 'guide_step_types.id')->select('guide_steps.*', 'guide_step_types.name as type_name')->orderBy('guide_step_type_id', 'desc')->orderBy('order', 'asc')->get();
        }

        // Target Areas
        if (Auth::user()->entity_type == "landbank") {
          $county = \App\Models\Program::select('county_id')->where('entity_id', Auth::User()->entity_id)->first();

          $targetAreas = \App\Models\TargetArea::join('programs', 'programs.county_id', 'target_areas.county_id')->select('target_area_name', 'program_name', 'target_areas.id')->where('target_areas.county_id', $county->county_id)->where('target_areas.active', 1)->orderBy('program_name', 'asc')->orderBy('target_area_name', 'asc')->get();
        } elseif ("%%" != $parcelsProgramFilter) {
          $targetAreas = \App\Models\TargetArea::join('programs', 'programs.county_id', 'target_areas.county_id')->where('target_areas.active', 1)->where('programs.id', $parcelsProgramFilter)->select('target_area_name', 'program_name', 'target_areas.id')->orderBy('program_name', 'asc')->orderBy('target_area_name', 'asc')->get();
        } else {
          $targetAreas = \App\Models\TargetArea::join('programs', 'programs.county_id', 'target_areas.county_id')->where('target_areas.active', 1)->select('target_area_name', 'program_name', 'target_areas.id')->orderBy('program_name', 'asc')->orderBy('target_area_name', 'asc')->get();
        }

        $statuses = Parcel::join('property_status_options', 'parcels.landbank_property_status_id', '=', 'property_status_options.id')->select('property_status_options.option_name', 'property_status_options.id')->groupBy('property_status_options.id', 'property_status_options.option_name')->orderBy('order')->get();

        $hfaStatuses = Parcel::join('property_status_options', 'parcels.hfa_property_status_id', '=', 'property_status_options.id')->select('property_status_options.option_name', 'property_status_options.id')->groupBy('property_status_options.id', 'property_status_options.option_name')->orderBy('order')->get('order');

        $dispositionStatuses = \App\Models\InvoiceStatus::select('id', 'invoice_status_name')->orderBy('invoice_status_name')->get();

        $i = 0;
      }

      // Check if they are not a HFA or if there is a filter applied - we require hfa members to apply at least one filter.

      // Fix an issue where somewhere the status is getting set incorrectly - likely on the import screen as it transitions to the validate:
      if (39 == $parcelsStatusFilter) {
        $parcelsStatusFilter = "%%";
      }
      if ("%%" != $advanceFilter || "%%" != $targetAreaFilter || "%%" != $dispositionFilter || "%%" != $retainageFilter || "%%" != $parcelsNextFilter || "%%" != $parcelsStatusFilter || "%%" != $hfaParcelsStatusFilter ||
        Auth::user()->entity_type == "hfa" ||
        Auth::user()->entity_type == "landbank" || "LIKE" != $parcelsProgramFilterOperator) {
        if ('property_status_options.option_name' == $parcelsSortBy) {
          $joinTable    = 'property_status_options';
          $joinOnColumn = 'parcels.landbank_property_status_id';
          $joinToColumn = 'property_status_options.id';

          // if export
          if ($request->query('export') == 1) {
            $parcelsSortBy = 'lb_status.option_name';
          }
        } elseif ('hfa_property_status_options.option_name' == $parcelsSortBy) {
          $joinTable     = 'property_status_options';
          $joinOnColumn  = 'parcels.hfa_property_status_id';
          $joinToColumn  = 'property_status_options.id';
          $parcelsSortBy = 'property_status_options.option_name';

          // if export
          if ($request->query('export') == 1) {
            $parcelsSortBy = 'hfa_status.option_name';
          }
        } elseif ('target_areas.target_area_name' == $parcelsSortBy) {
          $joinTable    = 'target_areas';
          $joinOnColumn = 'parcels.target_area_id';
          $joinToColumn = 'target_areas.id';
        } elseif ('program_name' == $parcelsSortBy) {
          $joinTable    = 'programs';
          $joinOnColumn = 'parcels.program_id';
          $joinToColumn = 'programs.id';
        } elseif ('guide_steps.name' == $parcelsSortBy) {
          $joinTable    = 'guide_steps';
          $joinOnColumn = 'parcels.next_step';
          $joinToColumn = 'guide_steps.id';
        } else {
          // default join
          $joinTable    = 'guide_steps';
          $joinOnColumn = 'parcels.next_step';
          $joinToColumn = 'guide_steps.id';
        }

        if ($request->query('export') != 1) {
          $parcel_query = Parcel::with('targetArea', 'county', 'state', 'entity', 'importId', 'program', 'landbankPropertyStatus', 'hfaPropertyStatus', 'importId.import.imported_by', 'documents', 'retainages', 'unpaidRetainages', 'dispositions', 'dispositions.status', 'paidRetainages', 'dispositionsSubmittedToFiscalAgent', 'approvedDispositions', 'paidDispositions', 'declinedDispositions', 'pendingPaymentDispositions', 'pendingHfaApprovalDispositions', 'pendingLbApprovalDispositions', 'draftDispositions', 'releasedDispositions', 'releaseRequestedDispositions', 'advanceItems', 'paidAdvanceItems', 'unpaidAdvanceItems')
            ->where('parcels.program_id', $parcelsProgramFilterOperator, $parcelsProgramFilter)
            ->where('parcels.landbank_property_status_id', $parcelsStatusFilterOperator, $parcelsStatusFilter)
            ->where('parcels.hfa_property_status_id', $hfaParcelsStatusFilterOperator, $hfaParcelsStatusFilter)
            ->where('parcels.entity_id', $where_entity_id_operator, $where_entity_id)
            ->where('parcels.next_step', $parcelsNextFilterOperator, $parcelsNextFilter)
            ->where('parcels.target_area_id', $targetAreaFilterOperator, $targetAreaFilter);

          // if($stepCompletedFilter > 0 || $stepNotCompletedFilter > 0){
          //     if($stepCompletedFilter > 0 && $stepNotCompletedFilter > 0){
          //         $parcel_query->leftjoin('guide_progress', 'parcels.id', 'guide_progress.type_id');
          //         $parcel_query->where(function($query) use($stepCompletedFilter) {
          //                         $query->where('guide_progress.completed','=',1)
          //                             ->where('guide_progress.guide_step_id', '=', $stepCompletedFilter);
          //                     });
          //         $parcel_query->leftjoin('guide_progress as progress2', 'parcels.id', 'progress2.type_id');
          //         $parcel_query->where(function($query) use($stepNotCompletedFilter) {
          //                         $query->where('progress2.completed','=',0)
          //                             ->where('progress2.guide_step_id', '=', $stepNotCompletedFilter);
          //                     });
          //     }
          //     elseif($stepCompletedFilter > 0){
          //         $parcel_query->join('guide_progress', function ($join) use($stepCompletedFilter, $stepNotCompletedFilter) {
          //             $join->on('guide_progress.type_id', '=', 'parcels.id')
          //                  ->where('guide_progress.completed','=',1)
          //                  ->where('guide_progress.guide_step_id', '=', $stepCompletedFilter);
          //         });
          //     }elseif($stepNotCompletedFilter > 0){
          //         $parcel_query->leftjoin('guide_progress', function ($join) use($stepCompletedFilter, $stepNotCompletedFilter) {
          //             $join->on('guide_progress.type_id', '=', 'parcels.id')
          //                  ->where('guide_progress.completed','=',0)
          //                  ->where('guide_progress.guide_step_id', '=', $stepNotCompletedFilter);
          //         });
          //     }
          //     $parcel_query->join('guide_steps as steps', 'guide_progress.guide_step_id','steps.id')
          //                 ->where('steps.guide_step_type_id','=','2');

          // }

          //->doesntHave($retainageQuery2)
          $parcel_query->has($advanceQuery1, $advanceFilterOperator, $advanceFilterValue)
            ->has($retainageQuery1, $retainageFilterOperator, $retainageFilterValue)
            ->has($dispositionQuery1, $dispositionFilterOperator, $dispositionFilterValue)
            ->join($joinTable, $joinOnColumn, '=', $joinToColumn)
            ->orderBy($parcelsSortBy, $parcelsAscDesc)
            ->select('parcels.*');

          $parcels = $parcel_query->simplePaginate(100);

          //$totalParcels =  $parcel_query->count(); // this causes problems with infinite scroll data
          $totalParcels = Parcel::

            with('retainages', 'unpaidRetainages', 'paidRetainages', 'dispositionsSubmittedToFiscalAgent', 'approvedDispositions', 'paidDispositions', 'declinedDispositions', 'pendingPaymentDispositions', 'pendingHfaApprovalDispositions', 'pendingLbApprovalDispositions', 'draftDispositions', 'releasedDispositions', 'releaseRequestedDispositions', 'advanceItems', 'paidAdvanceItems', 'unpaidAdvanceItems')
            ->select('parcels.id as parcel_system_id')
            ->has($advanceQuery1, $advanceFilterOperator, $advanceFilterValue)
            ->where('parcels.target_area_id', $targetAreaFilterOperator, $targetAreaFilter)
            ->where('parcels.program_id', $parcelsProgramFilterOperator, $parcelsProgramFilter)
            ->where('parcels.landbank_property_status_id', $parcelsStatusFilterOperator, $parcelsStatusFilter)
            ->where('parcels.hfa_property_status_id', $hfaParcelsStatusFilterOperator, $hfaParcelsStatusFilter)
            ->where('parcels.entity_id', $where_entity_id_operator, $where_entity_id)
            ->where('next_step', $parcelsNextFilterOperator, $parcelsNextFilter)
            ->has($retainageQuery1, $retainageFilterOperator, $retainageFilterValue)
            ->has($dispositionQuery1, $dispositionFilterOperator, $dispositionFilterValue)
            ->count();

          $steps = GuideStep::where('guide_step_type_id', '=', 2)->get();

          return view('parcels.index', compact('i', 'parcels', 'totalParcels', 'currentUser', 'parcels_sorted_by_query', 'parcelsAscDesc', 'parcelsAscDescOpposite', 'programs', 'statuses', 'parcelsProgramFilter', 'parcelsStatusFilter', 'programFiltered', 'statusFiltered', 'hfaStatuses', 'hfaParcelsStatusFilter', 'hfaStatusFiltered', 'parcelsProgramFilterOperator', 'parcelsNextFilter', 'parcelsNextFilterOperator', 'nextSteps', 'retainageFiltered', 'dispositionFiltered', 'dispositionFilter', 'dispositionStatuses', 'retainageFilter', 'advanceFilter', 'advanceFiltered', 'targetAreaFilter', 'targetAreaFiltered', 'targetAreas', 'steps')); //, 'stepCompletedFilter', 'stepNotCompletedFilter'
        } elseif ($request->query('export') == 1) {
          if ($request->query('export_paid_only') == 1) {
            $paid1a    = 'reimbursement_invoices.reimbursement_balance';
            $paid1Eval = '<';
            $paid1b    = .01;

            $paid2a    = 'reimbursement_invoices.reimbursement_last_payment_cleared_date';
            $paid2Eval = '>';
            $paid2b    = '1969-01-11';
          } elseif ($request->query('export_partially_paid_only') == 1) {
            $paid1a    = 'reimbursement_invoices.reimbursement_balance';
            $paid1Eval = '>';
            $paid1b    = 0;

            $paid2a    = 'reimbursement_invoices.reimbursement_last_payment_cleared_date';
            $paid2Eval = '>';
            $paid2b    = '1969-01-11';
          } else {
            $paid1a    = 'parcels.id';
            $paid1Eval = '>';
            $paid1b    = 0;

            $paid2a    = 'parcels.id';
            $paid2Eval = '>';
            $paid2b    = '0';
          }

          $parcels = Parcel::
            //with('retainages','unpaidRetainages','dispositions','dispositions.status','paidRetainages','dispositionsSubmittedToFiscalAgent', 'approvedDispositions','paidDispositions','declinedDispositions','pendingPaymentDispositions','pendingHfaApprovalDispositions','pendingLbApprovalDispositions','draftDispositions', 'releasedDispositions', 'releaseRequestedDispositions','advanceItems','paidAdvanceItems','unpaidAdvanceItems')
            join('programs', 'parcels.program_id', 'programs.id')
            ->join('counties', 'parcels.county_id', 'counties.id')
            ->join('states', 'parcels.state_id', 'states.id')
            ->join('entities', 'parcels.entity_id', 'entities.id')
            ->join('target_areas', 'parcels.target_area_id', 'target_areas.id')
            ->join('how_acquired_options', 'parcels.how_acquired_id', 'how_acquired_options.id')
            ->join('parcel_type_options', 'parcels.parcel_type_id', 'parcel_type_options.id')
            ->join('property_status_options as lb_status', 'parcels.landbank_property_status_id', 'lb_status.id')
            ->join('property_status_options as hfa_status', 'parcels.hfa_property_status_id', 'hfa_status.id')
            ->join('guide_steps', 'parcels.next_step', 'guide_steps.id')
          /// https://laracasts.com/discuss/channels/laravel/query-builder-and-sum
            ->leftJoin(DB::raw('(SELECT cost_items.parcel_id as the_parcel_id1,
		                                    COALESCE(SUM(cost_items.amount),0) AS Total_Costs,
		                                      (SELECT COALESCE(SUM(cost_items.amount) , 0) FROM cost_items Where cost_items.expense_category_id = 2 and cost_items.parcel_id = the_parcel_id1) AS Acquisition_Cost,
		                                      (SELECT COALESCE(SUM(cost_items.amount) , 0)  FROM cost_items Where cost_items.expense_category_id = 9 and cost_items.parcel_id = the_parcel_id1) AS NIP_Loan_Payoff_Cost ,
		                                      (SELECT COALESCE(SUM(cost_items.amount) , 0) FROM cost_items Where cost_items.expense_category_id = 3 and cost_items.parcel_id = the_parcel_id1) AS PreDemo_Cost   ,
		                                      (SELECT COALESCE(SUM(cost_items.amount) , 0)  FROM cost_items Where cost_items.expense_category_id = 4 and cost_items.parcel_id = the_parcel_id1) AS Demo_Cost  ,
		                                      (SELECT COALESCE(SUM(cost_items.amount) , 0)    FROM cost_items Where cost_items.expense_category_id = 5 AND cost_items.breakout_type = 3  and cost_items.parcel_id = the_parcel_id1) AS Greening_Advance_Cost,
		                                      (SELECT COALESCE(SUM(cost_items.amount) , 0)    FROM cost_items Where cost_items.expense_category_id = 5 AND cost_items.breakout_type = 1  and cost_items.parcel_id = the_parcel_id1) AS Greening_Cost,
		                                      (SELECT COALESCE(SUM(cost_items.amount) , 0)    FROM cost_items Where cost_items.expense_category_id = 6 and cost_items.parcel_id = the_parcel_id1) AS Maintenance_Cost,
		                                      (SELECT COALESCE(SUM(cost_items.amount) , 0)    FROM cost_items Where cost_items.expense_category_id = 7 and cost_items.parcel_id = the_parcel_id1) AS Admin_Cost,
		                                      (SELECT COALESCE(SUM(cost_items.amount) , 0)    FROM cost_items Where cost_items.expense_category_id = 8 and cost_items.parcel_id = the_parcel_id1) AS Other_Cost

		                                    FROM cost_items GROUP BY the_parcel_id1) AS cost_items'), 'parcels.id', '=', 'the_parcel_id1')

            ->leftJoin(DB::raw('(SELECT request_items.parcel_id as the_parcel_id2, request_items.req_id,
		                                    COALESCE(SUM(request_items.amount),0) AS Total_Requested,

		                                      (SELECT COALESCE(SUM(request_items.amount) , 0) FROM request_items Where request_items.expense_category_id = 2 and request_items.parcel_id = the_parcel_id2) AS Acquisition_Requested,
		                                      (SELECT COALESCE(SUM(request_items.amount) , 0)  FROM request_items Where request_items.expense_category_id = 9 and request_items.parcel_id = the_parcel_id2) AS NIP_Loan_Payoff_requested ,
		                                      (SELECT COALESCE(SUM(request_items.amount) , 0) FROM request_items Where request_items.expense_category_id = 3 and request_items.parcel_id = the_parcel_id2) AS PreDemo_requested   ,
		                                      (SELECT COALESCE(SUM(request_items.amount) , 0)  FROM request_items Where request_items.expense_category_id = 4 and request_items.parcel_id = the_parcel_id2) AS Demo_requested  ,
		                                      (SELECT COALESCE(SUM(request_items.amount) , 0)    FROM request_items Where request_items.expense_category_id = 5 AND request_items.breakout_type = 3  and request_items.parcel_id = the_parcel_id2) AS Greening_Advance_Requested,
		                                      (SELECT COALESCE(SUM(request_items.amount) , 0)    FROM request_items Where request_items.expense_category_id = 5 AND request_items.breakout_type = 1  and request_items.parcel_id = the_parcel_id2) AS Greening_Requested,
		                                      (SELECT COALESCE(SUM(request_items.amount) , 0)    FROM request_items Where request_items.expense_category_id = 6 and request_items.parcel_id = the_parcel_id2) AS Maintenance_Requested,
		                                      (SELECT COALESCE(SUM(request_items.amount) , 0)    FROM request_items Where request_items.expense_category_id = 7 and request_items.parcel_id = the_parcel_id2) AS Admin_Requested,
		                                      (SELECT COALESCE(SUM(request_items.amount) , 0)    FROM request_items Where request_items.expense_category_id = 8 and request_items.parcel_id = the_parcel_id2) AS Other_Requested

		                                    FROM request_items GROUP BY the_parcel_id2) AS request_items'), 'parcels.id', '=', 'the_parcel_id2')

            ->leftJoin(DB::raw('(SELECT po_items.parcel_id as the_parcel_id3,
		                                    COALESCE(SUM(po_items.amount),0) AS Total_Approved,

		                                      (SELECT COALESCE(SUM(po_items.amount) , 0) FROM po_items Where po_items.expense_category_id = 2 and po_items.parcel_id = the_parcel_id3) AS Acquisition_Approved,
		                                      (SELECT COALESCE(SUM(po_items.amount) , 0)  FROM po_items Where po_items.expense_category_id = 9 and po_items.parcel_id = the_parcel_id3) AS NIP_Loan_Payoff_Approved ,
		                                      (SELECT COALESCE(SUM(po_items.amount) , 0) FROM po_items Where po_items.expense_category_id = 3 and po_items.parcel_id = the_parcel_id3) AS PreDemo_Approved   ,
		                                      (SELECT COALESCE(SUM(po_items.amount) , 0)  FROM po_items Where po_items.expense_category_id = 4 and po_items.parcel_id = the_parcel_id3) AS Demo_Approved  ,
		                                      (SELECT COALESCE(SUM(po_items.amount) , 0)    FROM po_items Where po_items.expense_category_id = 5 AND po_items.breakout_type = 3  and po_items.parcel_id = the_parcel_id3) AS Greening_Advance_Approved,
		                                      (SELECT COALESCE(SUM(po_items.amount) , 0)    FROM po_items Where po_items.expense_category_id = 5 AND po_items.breakout_type = 1  and po_items.parcel_id = the_parcel_id3) AS Greening_Approved,
		                                      (SELECT COALESCE(SUM(po_items.amount) , 0)    FROM po_items Where po_items.expense_category_id = 6 and po_items.parcel_id = the_parcel_id3) AS Maintenance_Approved,
		                                      (SELECT COALESCE(SUM(po_items.amount) , 0)    FROM po_items Where po_items.expense_category_id = 7 and po_items.parcel_id = the_parcel_id3) AS Admin_Approved,
		                                      (SELECT COALESCE(SUM(po_items.amount) , 0)    FROM po_items Where po_items.expense_category_id = 8 and po_items.parcel_id = the_parcel_id3) AS Other_Approved
		                                    FROM po_items GROUP BY the_parcel_id3) AS po_items'), 'parcels.id', '=', 'the_parcel_id3')

            ->leftJoin(DB::raw('(SELECT invoice_items.parcel_id as the_parcel_id4, invoice_items.invoice_id,
		                                    COALESCE(SUM(invoice_items.amount),0) AS Total_Invoiced,

		                                      (SELECT COALESCE(SUM(invoice_items.amount) , 0) FROM invoice_items Where invoice_items.expense_category_id = 2 and invoice_items.parcel_id = the_parcel_id4) AS Acquisition_Invoiced,
		                                      (SELECT COALESCE(SUM(invoice_items.amount) , 0)  FROM invoice_items Where invoice_items.expense_category_id = 9 and invoice_items.parcel_id = the_parcel_id4) AS NIP_Loan_Payoff_Invoiced ,
		                                      (SELECT COALESCE(SUM(invoice_items.amount) , 0) FROM invoice_items Where invoice_items.expense_category_id = 3 and invoice_items.parcel_id = the_parcel_id4) AS PreDemo_Invoiced   ,
		                                      (SELECT COALESCE(SUM(invoice_items.amount) , 0)  FROM invoice_items Where invoice_items.expense_category_id = 4 and invoice_items.parcel_id = the_parcel_id4) AS Demo_Invoiced  ,
		                                      (SELECT COALESCE(SUM(invoice_items.amount) , 0)    FROM invoice_items Where invoice_items.expense_category_id = 5 AND invoice_items.breakout_type = 3  and invoice_items.parcel_id = the_parcel_id4) AS Greening_Advance_Invoiced,
		                                      (SELECT COALESCE(SUM(invoice_items.amount) , 0)    FROM invoice_items Where invoice_items.expense_category_id = 5 AND invoice_items.breakout_type = 1  and invoice_items.parcel_id = the_parcel_id4) AS Greening_Invoiced,
		                                      (SELECT COALESCE(SUM(invoice_items.amount) , 0)    FROM invoice_items Where invoice_items.expense_category_id = 6 and invoice_items.parcel_id = the_parcel_id4) AS Maintenance_Invoiced,
		                                      (SELECT COALESCE(SUM(invoice_items.amount) , 0)    FROM invoice_items Where invoice_items.expense_category_id = 7 and invoice_items.parcel_id = the_parcel_id4) AS Admin_Invoiced,
		                                      (SELECT COALESCE(SUM(invoice_items.amount) , 0)    FROM invoice_items Where invoice_items.expense_category_id = 8 and invoice_items.parcel_id = the_parcel_id4) AS Other_Invoiced
		                                    FROM invoice_items GROUP BY the_parcel_id4) AS invoice_items'), 'parcels.id', '=', 'the_parcel_id4')

            ->leftJoin(DB::raw('(SELECT disposition_items.parcel_id, disposition_items.disposition_invoice_id, COALESCE(SUM(disposition_items.amount),0) AS Total_Disposition_Owed FROM disposition_items GROUP BY disposition_items.parcel_id) AS disposition_items'), 'parcels.id', '=', 'disposition_items.parcel_id')

            ->leftJoin(DB::raw('(SELECT recapture_items.parcel_id, recapture_items.recapture_invoice_id, COALESCE(SUM(recapture_items.amount),0) AS Total_Recapture_Owed FROM recapture_items GROUP BY recapture_items.parcel_id) AS recapture_items'), 'parcels.id', '=', 'recapture_items.parcel_id')

            ->leftJoin('reimbursement_invoices', 'reimbursement_invoices.id', 'invoice_items.invoice_id')

            ->leftJoin('disposition_invoices', 'disposition_invoices.id', 'disposition_items.disposition_invoice_id')

            ->leftJoin('recapture_invoices', 'recapture_invoices.id', 'recapture_items.recapture_invoice_id')

            ->leftJoin('invoice_statuses', 'reimbursement_invoices.status_id', 'invoice_statuses.id')
            ->leftJoin('invoice_statuses as recapture_invoice_statuses', 'recapture_invoices.status_id', 'invoice_statuses.id')
            ->leftJoin('invoice_statuses as disposition_invoice_statuses', 'disposition_invoices.status_id', 'invoice_statuses.id')
            ->leftJoin('dispositions', 'parcels.id', 'dispositions.parcel_id')
            ->leftJoin('disposition_types', 'dispositions.disposition_type_id', 'disposition_types.id')
            ->leftJoin('invoice_statuses as disposition_statuses', 'dispositions.status_id', 'disposition_statuses.id')
            ->select(

              'parcels.id as System_ID',
              'parcels.parcel_id as Parcel_ID',
              'parcels.created_at as Imported_Date',
              'reimbursement_invoices.reimbursement_last_payment_cleared_date as Last_Payment_To_LB_Cleared',
              'disposition_invoices.disposition_last_payment_cleared_date as Last_Disposition_Payment_Cleared',
              'recapture_invoices.recapture_last_payment_cleared_date as Last_Recapture_Payment_Cleared',
              'parcel_type_options.parcel_type_option_name as Parcel_Type',
              'units as Units',
              'programs.program_name as Program_Name',
              'entities.entity_name as Entity',
              'parcels.street_address as Street_Address',
              'parcels.city as City',
              'states.state_name as State',
              'parcels.zip as Zip',
              'counties.county_name as County',
              'target_area_name as Target_Area',
              'oh_house_district as OH_House_District',
              'oh_senate_district as OH_Senate_District',
              'us_house_district as US_House_District',
              'latitude as Latitude',
              'longitude as Longitude',
              'google_map_link as Google_Maps',
              'sale_price as Sale_Price',
              'how_acquired_options.how_acquired_option_name as How_Acquired',
              'how_acquired_explanation as Explanation',
              'historic_significance_or_district as Historic',
              'historic_waiver_approved as Historic_Approved',
              'ugly_house as Use_As_Before',
              'pretty_lot as Use_As_After',
              'lb_status.option_name as LB_Status',
              'hfa_status.option_name as HFA_Status',
              'compliance as Requires_Random_Compliance',
              'compliance_manual as Has_Manual_Compliance',
              'compliance_score as Compliance_Score',
              'approved_in_po as Approved_For_PO',
              'declined_in_po as Declined_For_PO',
              'Total_Costs',
              'Acquisition_Cost',
              'NIP_Loan_Payoff_Cost',
              'PreDemo_Cost',
              'Demo_Cost',
              'Greening_Advance_Cost',
              'Greening_Cost',
              'Maintenance_Cost',
              'Admin_Cost',
              'Other_Cost',
              'Total_Requested',
              'req_id as Req_Number',
              'Acquisition_Requested',
              'NIP_Loan_Payoff_Requested',
              'PreDemo_Requested',
              'Demo_Requested',
              'Greening_Advance_Requested',
              'Greening_Requested',
              'Maintenance_Requested',
              'Admin_Requested',
              'Other_Requested',
              'Total_Approved',
              'reimbursement_invoices.po_id as PO_Number',
              'Acquisition_Approved',
              'NIP_Loan_Payoff_Approved',
              'PreDemo_Approved',
              'Demo_Approved',
              'Greening_Advance_Approved',
              'Greening_Approved',
              'Maintenance_Approved',
              'Admin_Approved',
              'Other_Approved',
              'Total_Invoiced',
              'reimbursement_invoices.id as Invoice_Number',
              'Acquisition_Invoiced',
              'NIP_Loan_Payoff_Invoiced',
              'PreDemo_Invoiced',
              'Demo_Invoiced',
              'Greening_Advance_Invoiced',
              'Greening_Invoiced',
              'Maintenance_Invoiced',
              'Admin_Invoiced',
              'Other_Invoiced',
              'invoice_statuses.invoice_status_name as Invoice_Status',
              'reimbursement_invoices.reimbursement_total_amount as Total_Amount_On_Full_Reimbursement_Invoice',
              'reimbursement_invoices.reimbursement_total_paid as Total_Paid_On_Full_Reimbursement_Invoice',
              'reimbursement_invoices.reimbursement_balance as Balance_On_Full_Reimbursement_Invoice',
              'disposition_invoices.id as Disposition_ID',
              'disposition_types.disposition_type_name as Disposition_Type',
              'disposition_statuses.invoice_status_name as Disposition_Status',
              'date_submitted as Date_Disposition_Submitted',
              'date_approved as Date_Disposition_Approved',
              'date_release_requested as Date_Lien_Release_Requested_to_Fiscal_Agent',
              'dispositions.release_date as Lien_Released_Date',
              'dispositions.created_at as Date_Disposition_Started_by_LB',
              'dispositions.updated_at as Date_Disposition_Last_Updated',
              'special_circumstance as Disposition_Special_Circumstance',
              'full_description as Disposition_Full_Description',
              'legal_description_in_documents as Disposition_Description_Uploaded',
              'permanent_parcel_id as Disposition_Permanent_Parcel_Id',
              'public_use_political as Disposition_Public_Use_Political_Subdivision',
              'public_use_community as Disposition_Public_Use_Community_Benefit',
              'public_use_oneyear as Disposition_Public_Use_Construction_Operation_One_Year',
              'public_use_facility as Disposition_Public_Use_Facility',
              'nonprofit_taxexempt as Disposition_Nonprofit_Tax_Exempt',
              'nonprofit_community as Disposition_Nonprofit_Community_Use',
              'nonprofit_oneyear as Disposition_Nonprofit_Construction_Operation_One_Year',
              'nonprofit_newuse as Disposition_Nonprofit_Zoned_for_New_Use',
              'dev_fmv as Disposition_Bus-Res_FMV',
              'dev_oneyear as Disposition_Bus-Res_Construction_Operation_One_Year',
              'dev_newuse as Disposition_Bus-Res_Zoned_for_New_Use',
              'dev_purchaseag as Disposition_Bus-Res_Purchase_Agreement',
              'dev_taxescurrent as Disposition_Bus-Res_Taxes_Current',
              'dev_nofc as Disposition_Bus-Res_No_FC',
              'program_income as Disposition_Program_Income_Submitted',
              'transaction_cost as Disposition_Transaction_Cost_Submitted',
              'hfa_calc_income as Disposition_Calculated_Income',
              'hfa_calc_trans_cost as Disposition_Calculated_Transaction_Cost',
              'hfa_calc_maintenance_total as Disposition_Actual_Maintenance_Paid_to_LB',
              'hfa_calc_months_prepaid as Disposition_Months_Prepaid',
              'hfa_calc_monthly_rate as Disposition_Maintenance_Monthly_Rate',
              'hfa_calc_months as Disposition_Calculated_Months_Maintained',
              'hfa_calc_maintenance_due as Disposition_Maintenance_Owed_Back',
              'hfa_calc_demo_cost as Disposition_Actual_Demo_Cost_Paid_to_LB',
              'hfa_calc_epi as Disposition_Eligible_Property_Income',
              'hfa_calc_gain as Disposition_Total_Capital_Gain_for_LB',
              'hfa_calc_payback as Disposition_Total_Recapture_Owed',
              'disposition_invoices.id as Disposition_Invoice_ID',
              'disposition_invoice_statuses.invoice_status_name as Disposition_Invoice_Status',
              'disposition_invoices.disposition_total_amount as Total_Amount_On_Full_Disposition_Invoice',
              'disposition_invoices.disposition_total_paid as Total_Paid_On_Full_Disposition_Invoice',
              'disposition_invoices.disposition_balance as Balance_On_Full_Disposition_Invoice',
              'recapture_invoices.id as Recapture_Invoice_ID',
              'recapture_invoices.recapture_total_amount as Total_Amount_On_Full_Recapture_Invoice',
              'recapture_invoices.recapture_total_paid as Total_Paid_On_Full_Recapture_Invoice',
              'recapture_invoices.recapture_balance as Balance_On_Full_Recapture_Invoice',
              'Total_Recapture_Owed as Total_Parcel_Recapture_Amount'
            )
            ->where($paid1a, $paid1Eval, $paid1b)
            ->where($paid2a, $paid2Eval, $paid2b)
            ->where('parcels.program_id', $parcelsProgramFilterOperator, $parcelsProgramFilter)
            ->where('parcels.landbank_property_status_id', $parcelsStatusFilterOperator, $parcelsStatusFilter)
            ->where('parcels.hfa_property_status_id', $hfaParcelsStatusFilterOperator, $hfaParcelsStatusFilter)
            ->where('parcels.entity_id', $where_entity_id_operator, $where_entity_id)
            ->where('parcels.next_step', $parcelsNextFilterOperator, $parcelsNextFilter)
            ->where('parcels.target_area_id', $targetAreaFilterOperator, $targetAreaFilter)
            ->has($advanceQuery1, $advanceFilterOperator, $advanceFilterValue)
            ->has($retainageQuery1, $retainageFilterOperator, $retainageFilterValue)
            ->has($dispositionQuery1, $dispositionFilterOperator, $dispositionFilterValue)
            ->groupBy('parcels.id')
            ->orderBy($parcelsSortBy, $parcelsAscDesc)
            ->get();

          Excel::create('Parcels Export by ' . Auth::User()->name . ' ' . date('m-d-Y g-i-s a', time()), function ($excel) use ($parcels) {
            $excel->sheet('Export', function ($sheet) use ($parcels) {
              $sheet->fromArray($parcels);

              //$sheet->setAutoFilter();
              $sheet->row(1, function ($row) {
                // call cell manipulation methods
                $row->setBackground('#005186');
                $row->setFontSize(15);
                $row->setFontColor('#ffffff');
              });
              $sheet->freezeFirstRow(1);
            });
          })->export('xls');
        }
      } else {
        $parcels      = null;
        $totalParcels = 0;
        return view('parcels.index', compact('i', 'parcels', 'totalParcels', 'currentUser', 'parcels_sorted_by_query', 'parcelsAscDesc', 'parcelsAscDescOpposite', 'programs', 'statuses', 'parcelsProgramFilter', 'parcelsStatusFilter', 'programFiltered', 'statusFiltered', 'hfaStatuses', 'hfaParcelsStatusFilter', 'hfaStatusFiltered', 'parcelsProgramFilterOperator', 'parcelsNextFilter', 'parcelsNextFilterOperator', 'nextSteps', 'retainageFiltered', 'dispositionFiltered', 'dispositionFilter', 'dispositionStatuses', 'retainageFilter', 'advanceFilter', 'advanceFiltered', 'targetAreaFilter', 'targetAreaFiltered', 'targetAreas'));
      }
    } else {
      return 'Sorry you do not have access to the parcel list.';
    }
  }

  public function exportPaidParcels(Request $request)
  {
    // check roles
    if (Auth::user()->id >= 3) {
      return "Are you sure you are allowed do this?";
    }

    // start job
    $requestor = Auth::user();

    // Save report request in database
    $new_report = new Report([
      'type'            => "export_paid_parcels",
      'folder'          => null,
      'filename'        => null,
      'pending_request' => 1,
      'user_id'         => $requestor->id,
    ]);
    $new_report->save();

    $job = new ParcelsExportJob($requestor, $new_report->id, 'paid');
    dispatch($job);

    // $lc = new LogConverter('user', 'export paid parcels requested');
    // $lc->setDesc($requestor->email . ' requested a report (export paid parcels)')->setFrom($requestor)->setTo($requestor)->save();

    return 1;
  }

  public function activityLogs()
  {
    //TODO: Create gate for this
    return view('dashboard.activitylog');
  }

  public function userList()
  {
    if (Auth::user()->canManageUsers()) {
      //$parcels = Parcel::limit(100)->orderBy('county_id', 'asc')->get();
      //$totalParcels = Parcel::count();
      // $totalUsers = \App\Models\User::count();
      if (Auth::user()->entity_id == 1) {
        // They are OHFA - get all users.
        $myUsers = DB::table('users')->join('entities', 'users.entity_id', '=', 'entities.id')->select('users.id', 'users.name', 'users.email', 'users.badge_color', 'entities.entity_name', 'entities.id as entity_id', 'users.active', 'users.api_token')->orderBy('entities.entity_name', 'asc')->orderBy('users.name', 'asc')->get()->all();
      } else {
        $myUsers = DB::table('users')->join('entities', 'users.entity_id', '=', 'entities.id')->select('users.id', 'users.name', 'users.email', 'users.badge_color', 'entities.entity_name', 'entities.id as entity_id', 'users.active', 'users.api_token')->where('users.entity_id', '=', Auth::user()->entity_id)->orderBy('users.name', 'asc')->get()->all();
      }
      $totalUsers = count($myUsers);
      $tuser      = Auth::user();
      // $lc         = new LogConverter('user', 'viewusers');
      // $lc->setDesc($tuser->email . ' Viewed user list')->setFrom($tuser)->setTo($tuser)->save();
      return view('dashboard.users', compact('myUsers', 'totalUsers'));
    } else {
      $tuser = Auth::user();
      // $lc    = new LogConverter('user', 'unauthorized viewusers');
      // $lc->setDesc($tuser->email . ' Attempted to view user list')->setFrom($tuser)->setTo($tuser)->save();
      return 'Sorry you do not have access to this page.';
    }
  }

  public function userShow($userId)
  {
    if (Auth::user()->canManageUsers()) {
      // $parcels = Parcel::limit(100)->orderBy('county_id', 'asc')->get();
      // $totalParcels = Parcel::count();
      // $totalUsers = \App\Models\User::count();
      $editUser = \App\Models\User::with('roles')->find($userId);

      if (Auth::user('entity_type', 'hfa')) {
        $entities = \App\Models\Entity::where('active', 1)->orderBy('entity_name', 'ASC')->get();
      } else {
        $entities = \App\Models\Entity::where('entity_id', Auth::user()->entity_id)->orderBy('entity_name', 'ASC')->get();
      }

      $hfa_roles = Role::where('role_parent_id', '=', 1)->orderBy('role_name', 'ASC')->where('active', '=', 1)->get();
      $lb_roles  = Role::where('role_parent_id', '=', 2)->orderBy('role_name', 'ASC')->where('active', '=', 1)->get();
      //$roles = Role::orderBy('role_parent_id','asc')->get()->all();
      /*
      if(Auth::user()->entity_id == 1){
      // They are OHFA - get all users.
      $myUsers = DB::table('users')->join('entities', 'users.entity_id', '=', 'entities.id')->select('users.id','users.name','users.email','users.badge_color','entities.entity_name','entities.id as entity_id')->orderBy('entities.entity_name', 'asc')->get()->all();
      } else {
      $myUsers = DB::table('users')->join('entities', 'users.entity_id', '=', 'entities.id')->select('users.id','users.name','users.email','users.badge_color','entities.entity_name','entities.id as entity_id')->where('users.entity_id','=', Auth::user()->entity_id)->orderBy('entities.entity_name', 'asc')->get()->all();
      }
       */
      $tuser = Auth::user();
      // $lc    = new LogConverter('user', 'viewuser');
      // $lc->setDesc($tuser->email . ' Viewed user ' . $editUser->email)->setFrom($tuser)->setTo($editUser)->save();
      return view('modals.user', compact('editUser', 'lb_roles', 'hfa_roles', 'entities'));
    } else {
      $tuser = Auth::user();
      // $lc    = new LogConverter('user', 'unauthorized viewuser');
      // $lc->setDesc($tuser->email . ' Attempted to view user ' . $editUser->email)->setFrom($tuser)->setTo($editUser)->save();
      return 'Sorry you do not have access to view this user.';
    }
  }

  public function userEdit(Request $request, $userId)
  {
    if (Auth::user()->canManageUsers()) {
      $userParams = [];
      $editUser   = \App\Models\User::find($userId);
      if ($request->email == $editUser->email) {
        $validator = Validator::make($request->all(), [
          'name'     => 'required|max:255',
          'password' => 'sometimes|min:6|confirmed',
        ]);
      } else {
        $validator = Validator::make($request->all(), [
          'name'     => 'required|max:255',
          'email'    => 'required|email|max:255|unique:users',
          'password' => 'min:6|confirmed',
        ]);
      }

      if ($validator->fails()) {
        $message = '';
        foreach ($validator->errors()->all() as $error_message) {
          $message = $error_message . "<br />" . $message;
        }
        $msg = ['message' => $message, 'status' => 0];
        return json_encode($msg);
      }

      $roles = $request->get('role');
      $editUser->roles()->detach();
      // $lc         = new LogConverter('user', 'edituser');
      $addedRoles = [];
      if (isset($roles)) {
        foreach ($roles as $rolekey => $rolevalue) {
          $role = Role::find($rolekey);
          array_push($addedRoles, $role->role_name);
          // $lc->addRole($role->role_name);
          $editUser->roles()->save($role);
        }
      }
      $userParams['roles'] = $addedRoles;
      $tuser               = Auth::user();
      // $lc->setDesc($tuser->email . ' edited user ' . $editUser->email)->setFrom($tuser)->setTo($editUser);
      // $lc->save();
      $newToken = $editUser->api_token;

      if (is_null($newToken)) {
        $newToken = "NOT SET.";
      }

      if (isset($request->api_token_reset)) {
        $newToken = str_random(60);
      }

      if (strlen($request->password) > 0) {
        // store a password update too
        $editUser->update([
          'name'        => $request->name,
          'email'       => $request->email,
          'badge_color' => $request->badge_color,
          'entity_id'   => $request->entity_id,
          'password'    => bcrypt($request->password),
          'api_token'   => $newToken,
        ]);
      } else {
        $editUser->update([
          'name'        => $request->name,
          'email'       => $request->email,
          'entity_id'   => $request->entity_id,
          'badge_color' => $request->badge_color,
          'api_token'   => $newToken,
        ]);
      }
      if (isset($request->entity_type)) {
        $editUser->update([
          'entity_type' => $request->entity_type,
        ]);
      }
      // NEW TOKEN LOGIC

      $tuser = Auth::user();
      // $lc    = new LogConverter('user', 'edituser');
      // $lc->setDesc($tuser->email . ' edited user ' . $editUser->email)->setFrom($tuser)->setTo($editUser)->save();
      $msg = ['message' => 'I updated ' . $request->name . ' successfully. Their API Token is : ' . $newToken, 'status' => 1];
      return json_encode($msg);
    } else {
      $tuser = Auth::user();
      // $lc    = new LogConverter('user', 'unauthorized edituser');
      // $lc->setDesc($tuser->email . ' attempted to edit user ' . $editUser->email)->setFrom($tuser)->setTo($editUser)->save();
      $msg = ['message' => 'Sorry, but you do not have access to edit users.', 'status' => 0];
      return json_encode($msg);
    }
  }

  public function userActivate($userId)
  {
    $editUser = \App\Models\User::find($userId);
    $editUser->activate();
    $tuser = Auth::user();
    // $lc    = new LogConverter('user', 'activateuser');
    // $lc->setDesc($tuser->email . ' Activated user ' . $editUser->email)->setFrom($tuser)->setTo($editUser)->save();
    $msg = ['message' => 'Successfully activated user', 'status' => 1];
    return json_encode($msg);
  }

  public function userQuickActivate($userId, Request $request)
  {
    $editUser     = User::find($userId);
    $canAuthorize = 0;
    if (is_null($editUser)) {
      $message = "<h2>USER HAS BEEN DELETED!</h2><p>This user had already been deleted by another admin.</p>";
      $error   = "Looks like this user has been deleted! This was likely done by another admin who did not approve the user. You can view their actions in the overall site history.";
      $type    = "danger";
      return view('pages.error', compact('error', 'message', 'type'));
    } else {
      $entity       = \App\Models\Entity::find($editUser->entity_id);
      $canAuthorize = DB::table('users_roles')->where('user_id', Auth::user()->id)->where('role_id', 5)->count();
      if ((
        Auth::user()->id == $entity->owner_id
        || (1 == $editUser->validate_all && $canAuthorize > 0)
      )
        && $request->query('t') == $editUser->email_token
        && $editUser->tries < 3
        && 0 == $editUser->active && strlen($editUser->email_token) > 0
      ) {
        $editUser->activate();
        $tuser = Auth::user();
        // $lc    = new LogConverter('user', 'activate');
        // $lc->setDesc($tuser->email . ' Activated user ' . $editUser->email)->setFrom($tuser)->setTo($editUser)->save();
        if (1 == $editUser->validate_all) {
          //Activate program and entity
          $entityToActivate = DB::table('entities')->select('*')->where('owner_id', $editUser->id)->first();
          DB::table('entities')->where('id', $entityToActivate->id)->update(['active' => 1]);
          $tuser = Auth::user();
          $e     = Entity::find($entityToActivate->id);
          // $lc    = new LogConverter('entity', 'activate');
          // $lc->setDesc($tuser->email . ' Activated Entity' . $e->entity_name)->setFrom($tuser)->setTo($e)->save();
          DB::table('programs')->where('owner_id', $entityToActivate->id)->update(['active' => 1]);
          // Create and account for the program
          $programToGetAccount = DB::table('programs')->join('counties', 'county_id', '=', 'counties.id')->select('*')->where('programs.owner_id', $entityToActivate->id)->first();
          DB::table('accounts')->insert([
            'account_name'    => "Blight " . $programToGetAccount->county_name,
            'entity_id'       => $entityToActivate->id,
            'owner_id'        => $programToGetAccount->id,
            'account_type_id' => 1,
            'active'          => 1,
          ]);
          $a  = Account::where('account_name', "Blight " . $programToGetAccount->county_name)->first();
          // $lc = new LogConverter('account', 'create');
          // $lc->setFrom(Auth::user())->setTo($a)->setDesc(Auth::user()->email . ' Created account ' . $a->account_name);
          // $lc->addProperty('owner_id', $entityToActivate->owner_id);
          // $lc->addProperty('entity_id', $entityToActivate->id);
          // $lc->save();
        }
        $editUser->resetTries();
        $editUser->update(['email_token' => ""]);
        // email user to let them know they have been approved.
        session(['userId' => $editUser->id]);
        $emailAccountApproval = new \App\Models\Mail\EmailAccountApproval($editUser);
        \Mail::to($editUser->email)->send($emailAccountApproval);
        session(['systemMessage' => 'Successfully Activated User', 'hideHowTo' => 1, 'editUserRoles' => $editUser->id]);
        return redirect('/dashboard?tab=7');
      } elseif (1 == $editUser->active) {
        $ownerFirstNameEnd = strpos($editUser->name, " ");
        $ownerFirstName    = substr($editUser->name, 0, $ownerFirstNameEnd);
        $message           = "<h2>ACTIVATED!</h2><p>$ownerFirstName had already been activated though.</p>";
        $error             = "Looks like $ownerFirstName has already been activated! Thanks for doing your part.";
        $type              = "success";
        return view('pages.error', compact('error', 'message', 'type'));
      } else {
        $error = "Sorry you don't have permission to modify that user.";
        $editUser->incrementTries();
        $message = "Looks like you may have a bad link or perhaps the entire link didn't make it through? You can try again, however too many attempts will lock out the quick activation for this user.";
        $type    = "danger";
        if ($editUser->tries > 2) {
          $message = "This user cannot be activated using the quick link. Please go to the users tab from the dashboard and activate them there."; //" Entity ID:".$editUser->entity_id." to User Entity ID ".Auth::user()->entity_id." whose ownership id = ".$entity->owner_id.". With the token of ".$request->query('t')." versus ".$editUser->email_token;
        }
        $tuser = Auth::user();
        // $lc    = new LogConverter('user', 'unauthorized activate');
        // $lc->setDesc($tuser->email . ' Attempted to activate user ' . $editUser->email)->setFrom($tuser)->setTo($editUser)->save();
        return view('pages.error', compact('error', 'message', 'type'));
      }
    }
  }

  public function userdeactivate($userId)
  {
    if (Auth::user()->canManageUsers()) {
      // $lc       = new LogConverter('user', 'deactivate');
      $editUser = \App\Models\User::find($userId);
      $editUser->deactivate();
      $tuser = Auth::user();
      // $lc    = new LogConverter('user', 'deactivate');
      // $lc->setDesc($tuser->email . ' Deactivated user ' . $editUser->email)->setFrom($tuser)->setTo($editUser)->save();
      $msg = ['message' => 'Successfully deactivated user', 'status' => 1];
      // $lc->setDesc($editUser->email . ' Deactivated')->setFrom(Auth::user())->setTo($editUser)->save();
      return json_encode($msg);
    } else {
      $msg = ['message' => 'Sorry, you don\'t have permission to do that.', 'status' => 1];
    }
  }

  public function userQuickDelete($userId, Request $request)
  {
    $editUser     = User::find($userId);
    $canAuthorize = 0;

    if (is_null($editUser)) {
      $message = "<h2>USER HAS BEEN DELETED!</h2><p>This user had already been deleted by another admin.</p>";
      $error   = "Looks like this user has already been deleted! Thanks for doing your part.";
      $type    = "success";
      return view('pages.error', compact('error', 'message', 'type'));
    } else {
      $entity       = \App\Models\Entity::find($editUser->entity_id);
      $canAuthorize = DB::table('users_roles')->where('user_id', Auth::user()->id)->where('role_id', 5)->count();
      if ((
        Auth::user()->id == $entity->owner_id
        || (1 == $editUser->validate_all && $canAuthorize > 0)
      )
        && $request->query('t') == $editUser->email_token
        && $editUser->tries < 3
        && 0 == $editUser->active && strlen($editUser->email_token) > 0) {
        /// They are allowed to do this.
        /// Make sure this user CAN be deleted.
        $compliances = DB::table('compliances')
          ->where('created_by_user_id', $editUser->id)
          ->orWhere('analyst_id', $editUser->id)
          ->orWhere('auditor_id', $editUser->id)
          ->count();
        $documents = DB::table('documents')
          ->where('user_id', $editUser->id)
          ->count();
        $entities = DB::table('entities')
          ->where('user_id', $editUser->id)
          ->orWhere('owner_id', $editUser->id)
          ->count();
        $imports = DB::table('imports')
          ->where('user_id', $editUser->id)
          ->count();
        $notes = DB::table('notes')->where('owner_id', $editUser->id)
          ->count();
        $siteVisits = DB::table('site_visits')
          ->where('inspector_id', $editUser->id)
          ->count();

        if (($compliances + $documents + $entities + $imports + $notes + $siteVisits > 0 && 1 != $editUser->validate_all) || ($compliances + $documents + $imports + $notes + $siteVisits > 0 && 1 == $editUser->validate_all)) {
          // User is used elsewhere - cannot delete.
          $ownerFirstNameEnd = strpos($editUser->name, " ");
          $ownerFirstName    = substr($editUser->name, 0, $ownerFirstNameEnd);
          if (1 == $editUser->active) {
            //deactivate user
            $editUser->deactivate();
            // $lc = new LogConverter('user', 'deactivate');
            // $lc->setDesc($editUser->email . ' Deactivated')->setFrom(Auth::user())->setTo($editUser)->save();
            $editUser->update(['email_token' => ""]);
            if (1 == $editUser->validate_all) {
              //Delete program and entity
              $entityToActivate = DB::table('entities')->select('id')->where('owner_id', $editUser->id)->first();
              DB::table('entities')->where('id', $entityToActivate->id)->update(['active' => 0]);
              $e        = Entity::find($entityToActivate->id);
              // $lcentity = new LogConverter('entity', 'deactivate');
              // $lcentity->setDesc($e->entity_name . ' Deleted')->setFrom(Auth::user())->setTo($e)->save();
              DB::table('programs')->where('owner_id', $entityToActivate->id)->update(['active' => 0]);
              $p  = Program::where('owner_id', $entityToActivate->id)->first();
              // $lc = new LogConverter('program', 'deactivate');
              // $lc->setFrom(Auth::user())->setTo($p)->setDesc(Auth::user()->$email . ' Deactivated program ' . $p->program_name)->save();
            }
          }
          $message = "<h2>Cannot Delete " . ucwords($ownerFirstName) . "!</h2><p>" . ucwords($ownerFirstName) . " has entries in the database elsewhere and cannot be deleted. However, they are now marked inactive, they cannot login and use the system.</p>";
          $error   = "Looks like " . ucwords($ownerFirstName) . " has the following entries. $compliances Compliance entries. $documents Documents uploaded. $entities Entities. $imports imports made. $notes notes on parcels. $siteVisits site visits entered.";
          $type    = "danger";
          return view('pages.error', compact('error', 'message', 'type'));
        } else {
          DB::table('users')->where('id', $editUser->id)->delete();
          // $lc = new LogConverter('user', 'delete');
          // $lc->setDesc($editUser->email . ' Deleted')->setFrom(Auth::user())->setTo($editUser)->save();
          if (1 == $editUser->validate_all) {
            // remove their entity and their program
            $entityToDelete = DB::table('entities')->select('id')->where('owner_id', $editUser->id)->first();
            $e              = Entity::find($entityToActivate->id);
            DB::table('entities')->where('id', $entityToDelete->id)->delete();
            // $lcentity = new LogConverter('entity', 'deactivate');
            // $lcentity->setDesc($e->entity_name . ' Deleted')->setFrom(Auth::user())->setTo($e)->save();
            $p = Program::where('owner_id', $entityToDelete->id)->first();
            DB::table('programs')->where('owner_id', $entityToDelete->id)->delete();
            // $lc = new LogConverter("program", 'delete');
            // $lc->setFrom(Auth::user())->setTo($p)->setDesc(Auth::user()->email . "Deleted program " . $p->program_name)->save();
          }
          DB::table('users_roles')->where('user_id', $editUser->id)->delete();
          $ownerFirstNameEnd = strpos($editUser->name, " ");
          $ownerFirstName    = substr($editUser->name, 0, $ownerFirstNameEnd);
          $message           = "<h2>DELETED " . ucwords($ownerFirstName) . "!</h2><p>" . ucwords($ownerFirstName) . " has been removed from the system completely. I was able to confirm they had no other ties to the system. This has been logged in the history if you need to refer back to it.";
          $type              = "success";
          $error             = "Successfully deleted " . ucwords($ownerFirstName) . " from the system.";
          return view('pages.error', compact('error', 'message', 'type'));
        }
      } else {
        $tuser = Auth::user();
        // $lc    = new LogConverter('user', 'unauthorized delete');
        // $lc->setDesc($tuser->email . ' Attempted to delete user ' . $editUser->email)->setFrom($tuser)->setTo($editUser)->save();
        $error = "Sorry you don't have permission to delete that user.";
        $editUser->incrementTries();
        $message = "Looks like you may have an old link or perhaps the entire link didn't make it through? You can try again, however too many attempts will lock out the quick deletion for this user.";
        $type    = "danger";
        if ($editUser->tries > 2) {
          // $lc = new LogConverter('user', 'unauthorized delete');
          // $lc->setDesc($tuser->email . ' Attempted to delete user ' . $editUser->email . ' and was locked out due to too many failed attempts. Deletion is no longer an option for this user.')->setFrom($tuser)->setTo($editUser)->save();
          $message = "This user cannot be deleted using the quick link. Please go to the users tab from the dashboard and deactivate them there. Deletion of users is only available when the user first registers. After that, due to database dependencies... I can only deactivate them from accessing the system."; //" Entity ID:".$editUser->entity_id." to User Entity ID ".Auth::user()->entity_id." whose ownership id = ".$entity->owner_id.". With the token of ".$request->query('t')." versus ".$editUser->email_token;
        }
        return view('pages.error', compact('error', 'message', 'type'));
      }
    }
  }

  public function adminTools()
  {
    if (Gate::allows('view-all-parcels')) {
      $sumStatData = [];
      $stats       = [];
      return view('dashboard.admin', compact('stats', 'sumStatData'));
    } else {
      return 'Sorry you do not have access to this page';
    }
  }

  public function viewLog($logtype)
  {
    //Temp function
    $logs = '';
    if ('user' == $logtype) {
      $logs = getUserLogs();
    } elseif ('entity' == $logtype) {
      $logs = getEntityLogs();
    } elseif ('parcel' == $logtype) {
      $logs = getParcelLogs();
    } else {
      $logs = getAllLogs();
    }
    return view('pages.viewlog', compact('logs'));
  }

  public function viewLogJson($logtype, $start, $count)
  {
    //TODO: Add Gate
    $logarray = getJsonLogs($logtype, $start, $count);
    return $logarray;
  }

  public function searchLogJson(Request $request, $logtype, $start, $count)
  {
    //TODO: Add Gate
    $logarray = searchJsonLogs($request, $logtype, $start, $count);
    return $logarray;
  }

  public function createUser(Request $request)
  {
    if (Auth::user()->manager_access() && null == $request->contact) {
      $contact       = null;
      $projects      = null;
      $roles         = Role::where('id', '<', 2)->active()->orderBy('role_name', 'ASC')->get();
      $organizations = Organization::active()->orderBy('organization_name', 'ASC')->get();
      $states        = State::get();
      return view('modals.new-user', compact('roles', 'organizations', 'states', 'contact', 'projects'));
    } elseif (Auth::user()->auditor_access() && intval($request->contact)) {
      // check to make sure this person either is an admin or higher or is an auditor or lead on the project.
      // return dd(json_decode($request->on_project));
      // $projectIds = explode(',', $request->on_project);// make them an array;
      $projectIds = $request->on_project;
      $multiple   = $request->multiple;
      // if(is_array($projectIds)){
      if ($request->multiple) {
        $projectIds = json_decode($request->on_project);
      } else {
        $projectIds = explode(',', $request->on_project);
      }
      if (is_array($projectIds)) {
        $projects = Project::whereIn('id', $projectIds)->get();
        $allowed  = 0;
        if (count($projects)) {
          // make sure they are a auditor or lead on at least one of the projects
          if (Auth::user()->manager_access()) {
            $allowed = 1;
          } else {
            foreach ($projects as $p) {
              $check = Audit::join('audit_auditors', 'audit_id', 'audits.id')->where('project_id', $p->id)->where('user_id', Auth::user()->id)->count();
              if ($check > 0) {
                //return 'Found on audit '.$check[0]->id;
                $allowed = 1;
                break;
              }
              // check to see if they are the lead
              $check = Audit::where('lead_user_id', Auth::user()->id)->where('project_id', $p->id)->count();
              if ($check > 0) {
                //return 'Found on audit '.$check[0]->id;
                $allowed = 1;
                break;
              }
            }
          }
          if ($allowed) {
            $contact              = People::with('phone', 'allita_phone', 'organizations')->with('email')->with('fax')->find(intval($request->contact));
            $check                = ProjectContactRole::where('person_id', intval($request->contact))->whereIn('project_id', $projectIds)->count();
            $project_contact_role = ProjectContactRole::with('organization.address')->where('person_id', intval($request->contact))->whereIn('project_id', $projectIds)->first();
            if (null !== $contact && $check > 0) {
              $roles            = Role::where('id', '<', 2)->active()->orderBy('role_name', 'ASC')->get();
              $organizations    = Organization::active()->orderBy('organization_name', 'ASC')->get();
              $states           = State::get();
              $selected_project = $request->project;
              $org              = $project_contact_role->organization;
              return view('modals.new-user', compact('roles', 'organizations', 'states', 'contact', 'projects', 'projectIds', 'multiple', 'selected_project', 'org'));
            } else {
              return '<h1>Sorry!</h1><h3>The contact you provided could not be found in the database or they are not on the projects submitted.</h3>';
            }
          } else {
            return '<h1>Sorry!</h1><h3>To add them as a user, you need to be an auditor or lead on at least one audit associated with this contact\'s projects. Please contact an audit lead, one of the auditors on an audit, or your manager to add them.</h3>';
          }
        } else {
          return '<h3>Sorry the projects provided are not valid.</h3>';
        }
      } else {
        return '<h3>Sorry, a project was not provided and at least one needs to be provided to add a user.</h3>';
      }
    } else {
      return '<h3>Sorry you do not have access to this page.</h3>';
    }
  }

  public function editUser($id)
  {
    if (Auth::user()->manager_access()) {
      $organizations = Organization::active()->orderBy('organization_name', 'ASC')->get();
      $states        = State::get();
      $user          = User::with('person.allita_phone', 'roles', 'organization_details', 'addresses')->find($id);
      // If the user is not devco user, only property manager role can be assigned
      if ($user->devco_key) {
        // get roles based on hierarchy
        if ($user->roles->first()) {
          $user_role_id = $user->roles->first()->role_id;
        } else {
          $user_role_id = 2;
        }
        $roles = Role::where('id', '<=', $user_role_id)->active()->orderBy('id', 'ASC')->get();
      } else {
        $roles = Role::where('id', '<=', 2)->active()->orderBy('id', 'ASC')->get();
      }
      $default_address = $user->addresses->where('default', 1)->first();
      if (count($user->roles) > 0) {
        $user_role = $user->roles->first()->role_id;
      } else {
        $user_role = null;
      }
      if ($user->person->allita_phone) {
        $user_phone = $user->person->allita_phone->area_code . '-' . $user->person->allita_phone->phone_number;
      } else {
        $user_phone = null;
      }
      if ($user->organization_details) {
        $user_organization = $user->organization_details->id;
      } else {
        $user_organization = null;
      }
      return view('modals.edit-user', compact('roles', 'organizations', 'states', 'user', 'user_role', 'user_phone', 'user_organization', 'default_address'));
    } else {
      $tuser = Auth::user();
      // $lc    = new LogConverter('user', 'unauthorized createuser');
      // $lc->setDesc($tuser->email . ' Attempted to create a new user ')->setFrom($tuser)->setTo($tuser)->save();
      return 'Sorry you do not have access to this page.';
    }
  }

  public function resetPassword($id)
  {
    if (Auth::user()->manager_access()) {
      $user = User::find($id);
      return view('modals.reset-password', compact('user'));
    } else {
      $tuser = Auth::user();
      // $lc    = new LogConverter('user', 'unauthorized createuser');
      // $lc->setDesc($tuser->email . ' Attempted to create a new user ')->setFrom($tuser)->setTo($tuser)->save();
      return 'Sorry you do not have access to this page.';
    }
  }

  public function deactivateUser($id)
  {
    if (Auth::user()->manager_access()) {
      $user = User::find($id);
      return view('modals.deactivate-user', compact('user'));
    } else {
      $tuser = Auth::user();
      // $lc    = new LogConverter('user', 'unauthorized createuser');
      // $lc->setDesc($tuser->email . ' Attempted to create a new user ')->setFrom($tuser)->setTo($tuser)->save();
      return 'Sorry you do not have access to this page.';
    }
  }

  public function activateUser($id)
  {
    if (Auth::user()->manager_access()) {
      $user = User::find($id);
      return view('modals.activate-user', compact('user'));
    } else {
      $tuser = Auth::user();
      // $lc    = new LogConverter('user', 'unauthorized createuser');
      // $lc->setDesc($tuser->email . ' Attempted to create a new user ')->setFrom($tuser)->setTo($tuser)->save();
      return 'Sorry you do not have access to this page.';
    }
  }

  /**
   * [createUserSave description]
   * @param  Request $request [description]
   * @return [type]           [description]
   * Save data in following tables
   * Users
   * People
   * Email Addresses
   * Phone Numbers
   * Addresses
   * Users Roles
   *     phone number table
  email address table
  addresses
  people
  users
  users roles
   */
  public function createUserSave(Request $request)
  {
    if (Auth::user()->manager_access()) {
      $validator = \Validator::make($request->all(), [
        'first_name'            => 'required|max:255',
        'last_name'             => 'required|max:255',
        'email'                 => 'required|email|max:255|unique:users',
        //'password'              => ['required', 'string', 'min:8', 'confirmed'],
        'role'                  => 'required',
        'business_phone_number' => 'required|min:14',
        'zip'                   => 'nullable|min:5|max:5',
        // 'state_id'              => 'required',
      ], [
        'business_phone_number.min' => 'Enter valid Business Phone Number',
      ]);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      }

      DB::beginTransaction();
      try {
        $current_user = Auth::user();
        //Phone numbers table
        if ($request->filled('business_phone_number')) {
          $input_phone_number                  = $request->business_phone_number;
          $split_number                        = explode('-', $input_phone_number);
          $phone_number_type                   = PhoneNumberType::where('phone_number_type_name', 'Business')->first();
          $phone_number                        = new PhoneNumber;
          $phone_number->phone_number_type_id  = $phone_number_type->id;
          $phone_number->phone_number_type_key = $phone_number_type->phone_number_type_key;
          $last_record                         = PhoneNumber::whereNotNull('phone_number_key')->orderBy('id', 'DESC')->first();
          $phone_number->phone_number_key      = $last_record->phone_number_key + 1;
          $first_half                          = explode(' ', $split_number[0]);
          $area_code                           = str_replace(['(', ')'], '', $first_half[0]);
          $phone_number->area_code             = $area_code;
          $phone_number->phone_number          = $first_half[1] . $split_number[1];
          // $phone_number->area_code            = $split_number[0];
          // $phone_number->phone_number         = $split_number[1] . $split_number[2];
          $phone_number->extension = $request->phone_extension;
          $phone_number->save();
        } else {
          $phone_number = false;
        }

        // Email address table
        $email_address_type                    = EmailAddressType::where('email_address_type_name', 'Work')->first();
        $email_address                         = new EmailAddress;
        $email_address->email_address          = $request->email;
        $email_address->email_address_type_id  = $email_address_type->id;
        $email_address->email_address_type_key = $email_address_type->email_address_type_key;
        $last_record                           = EmailAddress::whereNotNull('email_address_key')->orderBy('id', 'DESC')->first();
        $email_address->email_address_key      = $last_record->email_address_key + 1;
        $email_address->save();

        // People table
        if ($request->has('from_contact')) {
          $people = People::find($request->person_id);
          if (!$people) {
            return 'Something went wrong, please contact admin';
          }
        } else {
          $people = new People;
        }
        $people->last_name  = $request->last_name;
        $people->first_name = $request->first_name;
        if ($phone_number) {
          $people->default_phone_number_id  = $phone_number->id;
          $people->default_phone_number_key = $phone_number->phone_number_key;
        }

        $people->default_email_address_id  = $email_address->id;
        $people->default_email_address_key = $email_address->email_address_key;
        $people->is_active                 = 1;
        $people->save();

        // User table
        $user        = new User;
        $user->name  = $people->first_name . ' ' . $people->last_name;
        $user->email = $email_address->email_address;
        //$user->active        = 1;
        $user->password     = bcrypt(str_random(8));
        $user->badge_color  = $request->badge_color;
        $input_organization = $request->organization;
        if (!is_null($input_organization)) {
          $organization_selected = Organization::find($input_organization);
          $user->organization    = $organization_selected->organization_name;
          $user->organization_id = $organization_selected->id;
        }
        $input_role = $request->role;
        if ($input_role > 1) {
          return $this->extraCheckErrors($validator);
        }
        $selected_role     = Role::find($input_role);
        $user->email_token = alpha_numeric_random(60);
        $user->person_id   = $people->id;
        $user->save();

        // Address table
        if ($request->filled('address_line_1') || $request->filled('city') || $request->filled('state_id') || $request->filled('zip')) {
          $address         = new Address;
          $address->line_1 = $request->address_line_1;
          $address->line_2 = $request->address_line_2;
          $address->city   = $request->city;
          $input_state_id  = $request->state_id;
          if (!is_null($input_state_id)) {
            $state_selected    = State::find($input_state_id);
            $address->state_id = $input_state_id;
            $address->state    = $state_selected->state_acronym;
          }
          $address->zip     = $request->zip;
          $address->zip_4   = $request->zip_4;
          $address->user_id = $user->id;
          $address->default = 1;
          $address->save();
        }

        // User role table
        if ($input_role) {
          $user_role          = new UserRole;
          $user_role->role_id = $input_role;
          $user_role->user_id = $user->id;
          $user_role->save();
        }
        // return 'done';
        //Trigger email to User to create password, save it in HistoricEmail - look into Mail/EmailNotification
        $email_notification = new EmailCreateNewUser($current_user, $user);
        \Mail::to($user->email)->send($email_notification);
        DB::commit();
        //$some = $email_notification->render(); // For some reason, email histories was not saving until this is being rendered
        if ($request->has('from_contact')) {
          return $user->id;
        } else {
          return 1;
        }
      } catch (\Exception $e) {
        DB::rollBack();
        $data_insert_error = $e->getMessage();
      }
      return $this->extraCheckErrors($validator);
    } else {
      $tuser = Auth::user();
      // $lc    = new LogConverter('user', 'unauthorized createUser');
      // $lc->setDesc($tuser->email . ' attempted to create user.')->setFrom($tuser)->setTo($tuser)->save();
      $msg = ['message' => 'Sorry you do not have access to create a user', 'status' => 0];
      return json_encode($msg);
    }
  }

  public function createUserForContactSave(Request $request)
  {
    if (Auth::user()->auditor_access() && intval($request->person_id)) {
      // return $request->all();
      // check access to do this action first:
      $projectIds = $request->projects;
      $multiple   = $request->multiple;
      $projectIds = json_decode($projectIds, true);
      if (is_array($projectIds)) {
        $projects = Project::whereIn('id', $projectIds)->get();
        $allowed  = 0;
        if (count($projects)) {
          // make sure they are a auditor or lead on at least one of the projects
          if (Auth::user()->manager_access()) {
            $allowed = 1;
          } else {
            foreach ($projects as $p) {
              $check = Audit::join('audit_auditors', 'audit_id', 'audits.id')->where('project_id', $p->id)->where('user_id', Auth::user()->id)->count();
              if ($check > 0) {
                //return 'Found on audit '.$check[0]->id;
                $allowed = 1;
                break;
              }
              // check to see if they are the lead
              $check = Audit::where('lead_user_id', Auth::user()->id)->where('project_id', $p->id)->count();
              if ($check > 0) {
                //return 'Found on audit '.$check[0]->id;
                $allowed = 1;
                break;
              }
            }
          }
          if ($allowed) {
            // return $request->person_id;
            // check the submitted user:
            $contact = People::with('phone')->with('email')->with('fax')->find(intval($request->person_id));
            $check   = ProjectContactRole::where('person_id', intval($request->person_id))->whereIn('project_id', $projectIds)->count();
            if (null !== $contact && $check > 0) {
              // $roles         = Role::where('id', '<', 2)->active()->orderBy('role_name', 'ASC')->get();
              // $organizations = Organization::active()->orderBy('organization_name', 'ASC')->get();
              // $states        = State::get();
              // return view('modals.new-user', compact('roles', 'organizations', 'states', 'contact', 'projects', 'projectIds'));
              $request->request->add(['from_contact' => 1]);
              $request->request->add(['person_id' => $request->person_id]);
              // allita_phone, phone, email, fax exists
              $user = $this->createUserSave($request);
              if ($user && is_int($user)) {
                foreach ($projectIds as $key => $project_id) {
                  $check_user = ReportAccess::where('project_id', $project_id)->where('user_id', $user)->get();
                  if (count($check_user) == 0) {
                    $report_user             = new ReportAccess;
                    $report_user->project_id = $project_id;
                    $report_user->user_id    = $user;
                    $report_user->save();
                    $activate_user = User::find($user);
                    if (Auth::user()->auditor_access() && !($activate_user->active)) {
                      $activate_user->active = 1;
                      $activate_user->save();
                    }
                  }
                }
                return 1;
              } else {
                return $user;
              }
              // return $this->extraCheckErrors($validator);
            } else {
              return '<h1>Sorry!</h1><h3>The contact you provided could not be found in the database or they are not on the projects submitted.</h3>';
            }
          } else {
            return '<h1>Sorry</h1><h3>The project specified could not be found. Please make sure the contact still has a contact role on each project submitted. You may need to just refresh the contacts list to have updates post.</h3>';
          }
        } else {
          return '<h3>Sorry, a project was not provided and at least one needs to be provided to add a user.</h3>';
        }
      } else {
        return '<h1>Sorry</h1><h3>You do not have sufficient priveledges to do this action.</h3>';
      }
    } else {

      $msg = ['message' => 'Sorry you do not have access to create a user', 'status' => 0];
      return json_encode($msg);
    }
  }

  public function editUserSave($id, Request $request)
  {
    if (Auth::user()->manager_access()) {
      $validator = \Validator::make($request->all(), [
        'first_name' => 'required|max:255',
        'last_name'  => 'required|max:255',
        'role'       => 'required',
        // 'business_phone_number' => 'required|min:12',
        'zip'        => 'nullable|min:5|max:5',
        // 'state_id'              => 'required',
      ], [
        'business_phone_number.min' => 'Enter valid Business Phone Number',
      ]);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      }
      $user = User::with('person.allita_phone', 'roles', 'organization_details', 'addresses')->find($id);
      if ($id != $request->user_id || !($user)) {
        $validator->getMessageBag()->add('error', 'Something went wrong. Try again later or contact Admin');
        return response()->json(['errors' => $validator->errors()->all()]);
      }
      try {
        $roles           = Role::active()->orderBy('role_name', 'ASC')->get();
        $organizations   = Organization::active()->orderBy('organization_name', 'ASC')->get();
        $states          = State::get();
        $default_address = $user->addresses->where('default', 1)->first();
        if ($user->person->allita_phone) {
          $user_phone = $user->person->allita_phone->area_code . '-' . $user->person->allita_phone->phone_number;
        } else {
          $user_phone = null;
        }
        DB::beginTransaction();
        $current_user = Auth::user();
        //Check if phone number is changed and if changed, save it
        if ($request->filled('business_phone_number')) {
          $input_phone_number                 = $request->business_phone_number;
          $split_number                       = explode('-', $input_phone_number);
          $phone_number_type                  = PhoneNumberType::where('phone_number_type_name', 'Business')->first();
          $phone_number                       = new PhoneNumber;
          $phone_number->phone_number_type_id = $phone_number_type->id;
          $phone_number->area_code            = $split_number[0];
          $phone_number->phone_number         = $split_number[1] . $split_number[2];
          $phone_number->extension            = $request->phone_extension;
          $old_number                         = $user_phone;
          $new_number                         = $phone_number->area_code . $phone_number->phone_number;
          if ($old_number == $new_number && $user->person->allita_phone->extension == $request->extension) {
            $phone_number = $user->person->allita_phone;
          } else {
            $phone_number->save();
          }
          $phone_number_id = $phone_number->id;
        } else {
          $old_number      = $user_phone;
          $phone_number    = false;
          $phone_number_id = null;
        }

        // Email address table, Editing email is not allowed for in edit for now!
        // thus when they login - they should see the pending approval message as outlined in the flow. -- need to work on this, Div

        // People table, check if first name, last name and default phone number id are changed, if so, remove old people and new record
        if ($user->person->last_name != $request->last_name ||
          $user->person->first_name != $request->first_name ||
          $user->person->default_phone_number_id != $phone_number_id) {
          $people             = $user->person->replicate();
          $people->last_name  = $request->last_name;
          $people->first_name = $request->first_name;
          if ($phone_number) {
            $people->default_phone_number_id = $phone_number->id;
          } elseif (!is_null($old_number)) {
            $people->default_phone_number_id = null;
          }
          $people->is_active = 1;
          $people->save();
          $user->person->delete();
        } else {
          $people = $user->person;
        }

        // User table - There are numerous fileds, so just update the user records irrespective of changes made or not
        $user->name = $people->first_name . ' ' . $people->last_name;
        //$user->email = $email_address->email_address;
        //$user->active        = 1;
        $user->badge_color  = $request->badge_color;
        $input_organization = $request->organization;
        if (!is_null($input_organization)) {
          $organization_selected = Organization::find($input_organization);
          $user->organization    = $organization_selected->organization_name;
          $user->organization_id = $organization_selected->id;
        } else {
          $user->organization_id = null;
        }
        $input_role = $request->role;

        // if ($input_role > 1) {
        //   return $this->extraCheckErrors($validator);
        // }
        $selected_role = Role::find($input_role);
        if (is_null($user->devco_key) && $input_role > 2) {
          $user->api_token = $request->api_token;
        }
        $user->person_id = $people->id;
        $user->save();

        // Address table
        if ($default_address) {
          $address = $default_address;
        } else {
          $address = new Address;
        }
        if ($request->filled('address_line_1') || $request->filled('city') || $request->filled('state_id') || $request->filled('zip')) {
          $address->line_1 = $request->address_line_1;
          $address->line_2 = $request->address_line_2;
          $address->city   = $request->city;
          $input_state_id  = $request->state_id;
          if (!is_null($input_state_id)) {
            $state_selected    = State::find($input_state_id);
            $address->state_id = $input_state_id;
            $address->state    = $state_selected->state_acronym;
          }
          $address->zip     = $request->zip;
          $address->zip_4   = $request->zip_4;
          $address->user_id = $user->id;
          $address->default = 1;
          $address->save();
        }

        // If there is change in role, save that and remove old one
        if (count($user->roles) > 0) {
          if ($user->roles->first()->id != $input_role) {
            $del_user_role = $user->roles->first();
            $delete_role   = UserRole::where('role_id', $del_user_role->role_id)->where('user_id', $del_user_role->user_id)->delete();
            $insert_role   = true;
          } else {
            $user_role   = $user->roles->first();
            $insert_role = false;
          }
        } else {
          $insert_role = true;
        }
        if ($insert_role) {
          if ($input_role) {
            $user_role          = new UserRole;
            $user_role->role_id = $input_role;
            $user_role->user_id = $user->id;
            $user_role->save();
          }
        }
        DB::commit();
        return 1;
      } catch (\Exception $e) {
        DB::rollBack();
        $data_insert_error = $e->getMessage();
      }
      return $this->extraCheckErrors($validator);
    } else {
      $tuser = Auth::user();
      // $lc    = new LogConverter('user', 'unauthorized edituser');
      // $lc->setDesc($tuser->email . ' attempted to edit user.')->setFrom($tuser)->setTo($tuser)->save();
      $msg = ['message' => 'Sorry you do not have access to edit a user', 'status' => 0];
      return json_encode($msg);
    }
  }

  public function resetPasswordSave($id, Request $request)
  {
    if (Auth::user()->manager_access()) {
      $validator = \Validator::make($request->all(), [
        'password' => ['required', 'string', 'min:8', 'confirmed'],
      ]);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      }
      $user = User::find($id);
      if (!($user)) {
        $validator->getMessageBag()->add('error', 'Something went wrong. Try again later or contact Admin');
        return response()->json(['errors' => $validator->errors()->all()]);
      }
      DB::beginTransaction();
      try {
        $current_user = Auth::user();
        // User table - Password reset
        $user->password = bcrypt($request->password);
        $user->save();
        DB::commit();
        return 1;
      } catch (\Exception $e) {
        DB::rollBack();
        $data_insert_error = $e->getMessage();
      }
      return $this->extraCheckErrors($validator);
    } else {
      $tuser = Auth::user();
      // $lc    = new LogConverter('user', 'unauthorized resetUserPassword');
      // $lc->setDesc($tuser->email . ' attempted to reset user password.')->setFrom($tuser)->setTo($tuser)->save();
      $msg = ['message' => 'Sorry you do not have access to create a reset user password', 'status' => 0];
      return json_encode($msg);
    }
  }

  public function deactivateUserSave($id, Request $request)
  {
    if (Auth::user()->manager_access()) {
      $validator = \Validator::make($request->all(), [
        'user_id' => 'required',
      ], [
        'user_id' => 'Something went wrong. Try again later or contact Admin',
      ]);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      }
      $user = User::find($id);
      if (!($user)) {
        $validator->getMessageBag()->add('error', 'Something went wrong. Try again later or contact Admin');
        return response()->json(['errors' => $validator->errors()->all()]);
      }
      DB::beginTransaction();
      try {
        $current_user = Auth::user();
        // User table - Change active flag of user
        Auth::setUser($user);
        Auth::logout();
        $user->deactivate();
        DB::commit();
        Auth::setUser($current_user);
        Auth::loginUsingId($current_user->id);
        return 1;
      } catch (\Exception $e) {
        DB::rollBack();
        $data_insert_error = $e->getMessage();
      }
      return $this->extraCheckErrors($validator);
    } else {
      $tuser = Auth::user();
      // $lc    = new LogConverter('user', 'unauthorized deactivateUser');
      // $lc->setDesc($tuser->email . ' attempted to deactivate user.')->setFrom($tuser)->setTo($tuser)->save();
      $msg = ['message' => 'Sorry you do not have access to deactivate a user', 'status' => 0];
      return json_encode($msg);
    }
  }

  public function activateUserSave($id, Request $request)
  {
    if (Auth::user()->manager_access()) {
      $validator = \Validator::make($request->all(), [
        'user_id' => 'required',
      ], [
        'user_id' => 'Something went wrong. Try again later or contact Admin',
      ]);
      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->all()]);
      }
      $user = User::find($id);
      if (!($user)) {
        $validator->getMessageBag()->add('error', 'Something went wrong. Try again later or contact Admin');
        return response()->json(['errors' => $validator->errors()->all()]);
      }
      DB::beginTransaction();
      try {
        $current_user = Auth::user();
        // User table - Change active flag of user
        $user->activate();
        DB::commit();
        return 1;
      } catch (\Exception $e) {
        DB::rollBack();
        $data_insert_error = $e->getMessage();
      }
      return $this->extraCheckErrors($validator);
    } else {
      $tuser = Auth::user();
      // $lc    = new LogConverter('user', 'unauthorized activateUser');
      // $lc->setDesc($tuser->email . ' attempted to activate user.')->setFrom($tuser)->setTo($tuser)->save();
      $msg = ['message' => 'Sorry you do not have access to activate a user', 'status' => 0];
      return json_encode($msg);
    }
  }

  public function getUserCompleteRegistration($user_id, Request $request)
  {
    $email_token = $request->t;
    $user        = User::where('id', $user_id)->where('email_token', $email_token)->first();
    if (is_null($user)) {
      $message = "<h2>USER NOT FOUND!</h2><p>No user information has been found</p>";
      $error   = "Looks like the user doesn't exist with the provided information";
      $type    = "danger";
      return view('errors.error', compact('error', 'message', 'type'));
    } else {
      return view('auth.complete-registration', compact('user_id', 'email_token'));
    }
  }

  public function postUserCompleteRegistration(Request $request)
  {
    $validator = \Validator::make($request->all(), [
      'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);
    if ($validator->fails()) {
      $errors = $validator->errors()->all();
      return redirect()->back()->withErrors(['errors' => $validator->errors()->all()]);
      //return response()->json(['errors' => $validator->errors()->all()]); //used for ajax submit
    }
    $email_token = $request->email_token;
    $user_id     = $request->user_id;
    $user        = User::where('id', $user_id)->where('email_token', $email_token)->first();
    if (is_null($user)) {
      $validator->getMessageBag()->add('error', 'Something went wrong. Try again later or contact Admin');
      return response()->json(['errors' => $validator->errors()->all()]);
    } else {
      // use verify() and activate() methods at the time of resetting
      $user->activate();
      $user->verify();
      $user->password = bcrypt($request->password);
      $user->save();
    }
    return redirect('/login');
  }

  protected function extraCheckErrors($validator)
  {
    $validator->getMessageBag()->add('error', 'Something went wrong. Try again later or contact Technical Team');
    return response()->json(['errors' => $validator->errors()->all()]);
  }

  public function searchEmails(Request $request)
  {
    if ($request->has('communications-search')) {
      Session::put('communications-search', $request->get('communications-search'));
    } else {
      Session::forget('communications-search');
    }
    return 1;
  }

  public function emailsTab(Request $request)
  {
    $current_user = Auth::user();

    //Search (in session)
    if (Session::has('communications-search') && Session::get('communications-search') != '') {
      $search = Session::get('communications-search');
      if ($current_user->isFromEntity(1)) {
        // HFA
        $messages = HistoricEmail::where(function ($query) use ($search) {
          $query->where('body', 'LIKE', '%' . $search . '%');
        })
          ->where(function ($query) use ($current_user) {
            $query->where('user_id', '=', $current_user->id);
          })
          ->with('recipient')
          ->orderBy('created_at', 'desc')
          ->simplePaginate(100);
      } else {
        $messages = HistoricEmail::where('user_id', '=', $current_user->id)
          ->where(function ($query) use ($search) {
            $query->where('body', 'LIKE', '%' . $search . '%');
          })
          ->where(function ($query) use ($current_user) {
            $query->where('user_id', '=', $current_user->id);
          })
          ->with('recipient')
          ->orderBy('created_at', 'desc')
          ->simplePaginate(100);
      }
    } else {
      if ($current_user->isFromEntity(1)) {
        // HFA
        $messages = HistoricEmail::with('recipient')
          ->orderBy('created_at', 'desc')
          ->simplePaginate(100);
      } else {
        $messages = HistoricEmail::where('user_id', '=', $current_user->id)
          ->with('recipient')
          ->orderBy('created_at', 'desc')
          ->simplePaginate(100);
      }
    }

    $owners_array = [];

    if ($messages) {
      foreach ($messages as $message) {
        // create initials
        if ($message->recipient) {
          $words    = explode(" ", $message->recipient->name);
          $initials = "";
          foreach ($words as $w) {
            $initials .= substr($w, 0, 1);
          }
          $message->initials = $initials;
        } else {
          $message->initials = "";
        }

        // create associative arrays for initials and names
        if (!array_key_exists($message->recipient->id, $owners_array)) {
          $owners_array[$message->recipient->id]['initials'] = $initials;
          $owners_array[$message->recipient->id]['name']     = $message->recipient->name;
          $owners_array[$message->recipient->id]['color']    = $message->recipient->badge_color;
          $owners_array[$message->recipient->id]['id']       = $message->recipient->id;
        }

        $message->summary = $message->subject;
        //$message->summary = strlen($message->message) > 200 ? substr($message->message,0,200)."..." : $message->message;
      }
    }

    $owners_array = collect($owners_array)->sortBy('name')->toArray();
    $programs     = Program::orderBy('program_name', 'ASC')->get();

    return view('dashboard.emails', compact('messages', 'owners', 'owners_array', 'current_user', 'programs'));
  }

  public function viewFullEmail($emailid)
  {
    $current_user = Auth::user();

    if ($current_user->isFromEntity(1)) {
      // HFA
      $message = HistoricEmail::where('id', $emailid)
        ->with('recipient')
        ->first();
    } else {
      $message = HistoricEmail::where('id', $emailid)
        ->where('user_id', '=', $current_user->id)
        ->with('recipient')
        ->first();
    }

    return view('modals.email', compact('message'));
  }
}
