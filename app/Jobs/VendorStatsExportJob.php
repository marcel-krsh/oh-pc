<?php

namespace App\Jobs;

use App\CostItem;
use App\ExpenseCategory;
use App\InvoiceItem;
use App\Mail\DownloadReady;
use App\PoItems;
use App\Program;
use App\Report;
use App\RequestItem;
use App\User;
use App\Vendor;
use Excel;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

set_time_limit(3600);
ini_set('max_execution_time', 2000);
ini_set('memory_limit', -1);
ini_set('request_terminate_timeout', 2000);

/**
 * VendorStatsExport Job.
 *
 * @category Events
 * @license  Proprietary and confidential
 */
class VendorStatsExportJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $requestorEmail;
    protected $report_id;
    protected $requestorId;
    protected $program_id;
    protected $csv;
    protected $date_ref;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user = null, $report_id = null, $program_id = null, $csv = null, $date_ref = null)
    {
        if ($user) {
            $this->requestorEmail = $user->email;
            $this->requestorId = $user->id;
        } else {
            $this->requestorEmail = null;
            $this->requestorId = null;
        }

        $this->report_id = $report_id;
        $this->program_id = $program_id;
        $this->csv = $csv;
        $this->date_ref = $date_ref;
    }

    /**
     * Execute the job.
     *
     * @throws \Exception
     */
    public function handle()
    {
        $debug = 0;
        $convert = 0;

        if ($this->date_ref == null) {
            $date = date('m-d-Y_g-i-s_a', time());
        } else {
            $date = $this->date_ref;
        }

        if ($this->program_id !== null) {
            $program = Program::where('id', '=', $this->program_id)->first();
            $name = str_replace(' ', '_', $program->program_name);
            $filename = 'vendor_stats_'.$name.'_'.$date;
        } else {
            $filename = 'vendor_stats_'.$date;
        }

        $filenames_array = [];

        // Create an array of totals with/without categories and programs
        $summary_totals = []; // [cat][program][cost type]

        $summary_totals[0][0]['cost'] = CostItem::where('vendor_id', '!=', 1)->sum('amount') ?: 0;
        $summary_totals[0][0]['request'] = RequestItem::where('vendor_id', '!=', 1)->sum('amount') ?: 0;
        $summary_totals[0][0]['po'] = PoItems::where('vendor_id', '!=', 1)->sum('amount') ?: 0;
        $summary_totals[0][0]['invoice'] = InvoiceItem::where('vendor_id', '!=', 1)->sum('amount') ?: 0;

        $expense_categories = ExpenseCategory::where('id', '!=', 1)->get();
        if ($this->program_id !== null) {
            $programs = Program::where('id', '=', $this->program_id)->get();
        } else {
            $programs = Program::where('id', '!=', 1)->get();
        }

        foreach ($programs as $program) {
            $summary_totals[0][$program->id]['cost'] = CostItem::where('vendor_id', '!=', 1)->where('program_id', '=', $program->id)->sum('amount') ?: 0;
            $summary_totals[0][$program->id]['request'] = RequestItem::where('vendor_id', '!=', 1)->where('program_id', '=', $program->id)->sum('amount') ?: 0;
            $summary_totals[0][$program->id]['po'] = PoItems::where('vendor_id', '!=', 1)->where('program_id', '=', $program->id)->sum('amount') ?: 0;
            $summary_totals[0][$program->id]['invoice'] = InvoiceItem::where('vendor_id', '!=', 1)->where('program_id', '=', $program->id)->sum('amount') ?: 0;
        }

        foreach ($expense_categories as $cat) {
            $summary_totals[$cat->id][0]['cost'] = CostItem::where('vendor_id', '!=', 1)->where('expense_category_id', '=', $cat->id)->sum('amount') ?: 0;
            $summary_totals[$cat->id][0]['request'] = RequestItem::where('vendor_id', '!=', 1)->where('expense_category_id', '=', $cat->id)->sum('amount') ?: 0;
            $summary_totals[$cat->id][0]['po'] = PoItems::where('vendor_id', '!=', 1)->where('expense_category_id', '=', $cat->id)->sum('amount') ?: 0;
            $summary_totals[$cat->id][0]['invoice'] = InvoiceItem::where('vendor_id', '!=', 1)->where('expense_category_id', '=', $cat->id)->sum('amount') ?: 0;

            foreach ($programs as $program) {
                $summary_totals[$cat->id][$program->id]['cost'] = CostItem::where('vendor_id', '!=', 1)->where('expense_category_id', '=', $cat->id)->where('program_id', '=', $program->id)->sum('amount') ?: 0;
                $summary_totals[$cat->id][$program->id]['request'] = RequestItem::where('vendor_id', '!=', 1)->where('expense_category_id', '=', $cat->id)->where('program_id', '=', $program->id)->sum('amount') ?: 0;
                $summary_totals[$cat->id][$program->id]['po'] = PoItems::where('vendor_id', '!=', 1)->where('expense_category_id', '=', $cat->id)->where('program_id', '=', $program->id)->sum('amount') ?: 0;
                $summary_totals[$cat->id][$program->id]['invoice'] = InvoiceItem::where('vendor_id', '!=', 1)->where('expense_category_id', '=', $cat->id)->where('program_id', '=', $program->id)->sum('amount') ?: 0;
            }
        }

        if ($this->csv == 1) {
            $directory = 'export/vendorstats/'.$date;
            $filename_summary_csv = 'vendor_stats_'.$date.'-summary.csv';
            $filename_csv = 'vendor_stats_'.$date.'.csv';

            if (! Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }

            //$vendors = Vendor::where('id','!=',1)->orderBy('vendor_name','ASC')->limit(2)->get();
            $vendors = Vendor::where('id', '!=', 1)->orderBy('vendor_name', 'ASC')->get();

            // only run the summary file once
            if (! Storage::exists($directory.'/'.'vendor_stats_'.$date.'-summary.xlsx')) {
                $headers = '"Name","ID","Link","Cost Type","Total","Total %"';

                foreach ($expense_categories as $cat) {
                    $headers = $headers.',"['.$cat->expense_category_name.'] Total","['.$cat->expense_category_name.'] Total %"';
                }

                $headers = $headers."\n";

                Storage::put($directory.'/'.$filename_summary_csv, $headers);
            }

            foreach ($vendors as $vendor) {
                $vendor_url = env('APP_URL', 'https://ohfa.allita.org').'/viewvendor/'.$vendor->id;

                // calculations
                // Cost
                $summary_total_cost = $vendor->totals()['cost'];
                $summary_grand_total_cost = $summary_totals[0][0]['cost'];
                if ($summary_grand_total_cost != 0) {
                    $summary_percentage_cost = number_format($summary_total_cost * 100 / $summary_grand_total_cost, 2).'%';
                } else {
                    $summary_percentage_cost = '0.00%';
                }
                $summary_total_cost = '$'.number_format($summary_total_cost, 2, '.', ',');

                // Requested
                $summary_total_requested = $vendor->totals()['request'];
                $summary_grand_total_requested = $summary_totals[0][0]['request'];
                if ($summary_grand_total_requested != 0) {
                    $summary_percentage_requested = number_format($summary_total_requested * 100 / $summary_grand_total_requested, 2).'%';
                } else {
                    $summary_percentage_requested = '0.00%';
                }
                $summary_total_requested = '$'.number_format($summary_total_requested, 2, '.', ',');

                // PO
                $summary_total_po = $vendor->totals()['po'];
                $summary_grand_total_po = $summary_totals[0][0]['po'];
                if ($summary_grand_total_po != 0) {
                    $summary_percentage_po = number_format($summary_total_po * 100 / $summary_grand_total_po, 2).'%';
                } else {
                    $summary_percentage_po = '0.00%';
                }
                $summary_total_po = '$'.number_format($summary_total_po, 2, '.', ',');

                // Invoiced
                $summary_total_invoice = $vendor->totals()['invoice'];
                $summary_grand_total_invoice = $summary_totals[0][0]['invoice'];
                if ($summary_grand_total_invoice != 0) {
                    $summary_percentage_invoice = number_format($summary_total_invoice * 100 / $summary_grand_total_invoice, 2).'%';
                } else {
                    $summary_percentage_invoice = '0.00%';
                }
                $summary_total_invoice = '$'.number_format($summary_total_invoice, 2, '.', ',');

                if (! Storage::exists($directory.'/'.'vendor_stats_'.$date.'-summary.xlsx')) {
                    $row1 = '"'.$vendor->vendor_name.'","'.$vendor->id.'","'.$vendor_url.'","Cost","'.$summary_total_cost.'","'.$summary_percentage_cost.'"';
                    $row2 = '"'.$vendor->vendor_name.'","'.$vendor->id.'","'.$vendor_url.'","Request","'.$summary_total_requested.'","'.$summary_percentage_requested.'"';
                    $row3 = '"'.$vendor->vendor_name.'","'.$vendor->id.'","'.$vendor_url.'","PO","'.$summary_total_po.'","'.$summary_percentage_po.'"';
                    $row4 = '"'.$vendor->vendor_name.'","'.$vendor->id.'","'.$vendor_url.'","Invoice","'.$summary_total_invoice.'","'.$summary_percentage_invoice.'"';
                }

                foreach ($expense_categories as $cat) {
                    // Cost
                    $total_cost = $vendor->totals($cat->id)['cost'];
                    $grand_total_cost = $summary_totals[$cat->id][0]['cost'];
                    if ($grand_total_cost != 0) {
                        $percentage_cost = number_format($total_cost * 100 / $grand_total_cost, 2).'%';
                    } else {
                        $percentage_cost = '0.00%';
                    }
                    $total_cost = '$'.number_format($total_cost, 2, '.', ',');

                    // Request
                    $total_requested = $vendor->totals($cat->id)['request'];
                    $grand_total_requested = $summary_totals[$cat->id][0]['request'];
                    if ($grand_total_requested != 0) {
                        $percentage_requested = number_format($total_requested * 100 / $grand_total_requested, 2).'%';
                    } else {
                        $percentage_requested = '0.00%';
                    }
                    $total_requested = '$'.number_format($total_requested, 2, '.', ',');

                    // PO
                    $total_po = $vendor->totals($cat->id)['po'];
                    $grand_total_po = $summary_totals[$cat->id][0]['po'];
                    if ($grand_total_po != 0) {
                        $percentage_po = number_format($total_po * 100 / $grand_total_po, 2).'%';
                    } else {
                        $percentage_po = '0.00%';
                    }
                    $total_po = '$'.number_format($total_po, 2, '.', ',');

                    // Invoiced
                    $total_invoice = $vendor->totals($cat->id)['invoice'];
                    $grand_total_invoice = $summary_totals[$cat->id][0]['invoice'];
                    if ($grand_total_invoice != 0) {
                        $percentage_invoice = number_format($total_invoice * 100 / $grand_total_invoice, 2).'%';
                    } else {
                        $percentage_invoice = '0.00%';
                    }
                    $total_invoice = '$'.number_format($total_invoice, 2, '.', ',');

                    if (! Storage::exists($directory.'/'.'vendor_stats_'.$date.'-summary.xlsx')) {
                        $row1 = $row1.',"'.$total_cost.'","'.$percentage_cost.'"';
                        $row2 = $row2.',"'.$total_requested.'","'.$percentage_requested.'"';
                        $row3 = $row3.',"'.$total_po.'","'.$percentage_po.'"';
                        $row4 = $row4.',"'.$total_invoice.'","'.$percentage_invoice.'"';
                    }
                }

                if (! Storage::exists($directory.'/'.'vendor_stats_'.$date.'-summary.xlsx')) {
                    $row = $row1."\n".$row2."\n".$row3."\n".$row4;
                    Storage::append($directory.'/'.$filename_summary_csv, $row);
                }
            }

            if (! Storage::exists($directory.'/'.'vendor_stats_'.$date.'-summary.xlsx')) {
                // convert that csv in xls
                Excel::load('storage/app/'.$directory.'/'.$filename_summary_csv, function ($file) {
                })->setFileName('vendor_stats_'.$date.'-summary')->store('xlsx', storage_path('app/'.$directory));

                // delete csv file
                unlink(storage_path('app/'.$directory.'/'.$filename_summary_csv));

                // save filename in array to prepare for zip
            //$filenames_array[] = storage_path('app/'.$directory.'/'.$filename.'-summary.xlsx');
            }

            // more files, one file per program is created, when done, combined into one zip
            foreach ($programs as $program) {
                $name = str_replace(' ', '_', $program->program_name);
                $filename_csv = 'vendor_stats_'.$name.'_'.$date.'.csv';
                $filename = 'vendor_stats_'.$name.'_'.$date;

                $headers = '"Name","ID","Link","Cost Type","Total","Total %","['.$program->program_name.'] Total","['.$program->program_name.'] Total %"';

                foreach ($expense_categories as $cat) {
                    $headers = $headers.',"['.$cat->expense_category_name.'] Total","['.$cat->expense_category_name.'] Total %"';
                }

                $headers = $headers."\n";

                Storage::put($directory.'/'.$filename_csv, $headers);

                foreach ($vendors as $vendor) {
                    // Cost
                    $total_cost = $vendor->totals(0, $program->id)['cost'];
                    $grand_total_cost = $summary_totals[0][$program->id]['cost'];
                    if ($grand_total_cost != 0) {
                        $percentage_cost = number_format($total_cost * 100 / $grand_total_cost, 2).'%';
                    } else {
                        $percentage_cost = '0.00%';
                    }
                    $total_cost = '$'.number_format($total_cost, 2, '.', ',');

                    // Request
                    $total_requested = $vendor->totals(0, $program->id)['request'];
                    $grand_total_requested = $summary_totals[0][$program->id]['request'];
                    if ($grand_total_requested != 0) {
                        $percentage_requested = number_format($total_requested * 100 / $grand_total_requested, 2).'%';
                    } else {
                        $percentage_requested = '0.00%';
                    }
                    $total_requested = '$'.number_format($total_requested, 2, '.', ',');

                    // PO
                    $total_po = $vendor->totals(0, $program->id)['po'];
                    $grand_total_po = $summary_totals[0][$program->id]['po'];
                    if ($grand_total_po != 0) {
                        $percentage_po = number_format($total_po * 100 / $grand_total_po, 2).'%';
                    } else {
                        $percentage_po = '0.00%';
                    }
                    $total_po = '$'.number_format($total_po, 2, '.', ',');

                    // Invoiced
                    $total_invoice = $vendor->totals(0, $program->id)['invoice'];
                    $grand_total_invoice = $summary_totals[0][$program->id]['invoice'];
                    if ($grand_total_invoice != 0) {
                        $percentage_invoice = number_format($total_invoice * 100 / $grand_total_invoice, 2).'%';
                    } else {
                        $percentage_invoice = '0.00%';
                    }
                    $total_invoice = '$'.number_format($total_invoice, 2, '.', ',');

                    $row1 = '"'.$vendor->vendor_name.'","'.$vendor->id.'","'.$vendor_url.'","Cost","'.$summary_total_cost.'","'.$summary_percentage_cost.'"';
                    $row2 = '"'.$vendor->vendor_name.'","'.$vendor->id.'","'.$vendor_url.'","Request","'.$summary_total_requested.'","'.$summary_percentage_requested.'"';
                    $row3 = '"'.$vendor->vendor_name.'","'.$vendor->id.'","'.$vendor_url.'","PO","'.$summary_total_po.'","'.$summary_percentage_po.'"';
                    $row4 = '"'.$vendor->vendor_name.'","'.$vendor->id.'","'.$vendor_url.'","Invoice","'.$summary_total_invoice.'","'.$summary_percentage_invoice.'"';

                    $row1 = $row1.',"'.$total_cost.'","'.$percentage_cost.'"';
                    $row2 = $row2.',"'.$total_requested.'","'.$percentage_requested.'"';
                    $row3 = $row3.',"'.$total_po.'","'.$percentage_po.'"';
                    $row4 = $row4.',"'.$total_invoice.'","'.$percentage_invoice.'"';

                    foreach ($expense_categories as $cat) {
                        // Cost
                        $total_cost = $vendor->totals($cat->id, $program->id)['cost'];
                        $grand_total_cost = $summary_totals[$cat->id][$program->id]['cost'];
                        if ($grand_total_cost != 0) {
                            $percentage_cost = number_format($total_cost * 100 / $grand_total_cost, 2).'%';
                        } else {
                            $percentage_cost = '0.00%';
                        }
                        $total_cost = '$'.number_format($total_cost, 2, '.', ',');

                        // Request
                        $total_requested = $vendor->totals($cat->id, $program->id)['request'];
                        $grand_total_requested = $summary_totals[$cat->id][$program->id]['request'];
                        if ($grand_total_requested != 0) {
                            $percentage_requested = number_format($total_requested * 100 / $grand_total_requested, 2).'%';
                        } else {
                            $percentage_requested = '0.00%';
                        }
                        $total_requested = '$'.number_format($total_requested, 2, '.', ',');

                        // PO
                        $total_po = $vendor->totals($cat->id, $program->id)['po'];
                        $grand_total_po = $summary_totals[$cat->id][$program->id]['po'];
                        if ($grand_total_po != 0) {
                            $percentage_po = number_format($total_po * 100 / $grand_total_po, 2).'%';
                        } else {
                            $percentage_po = '0.00%';
                        }
                        $total_po = '$'.number_format($total_po, 2, '.', ',');

                        // Invoiced
                        $total_invoice = $vendor->totals($cat->id, $program->id)['invoice'];
                        $grand_total_invoice = $summary_totals[$cat->id][$program->id]['invoice'];
                        if ($grand_total_invoice != 0) {
                            $percentage_invoice = number_format($total_invoice * 100 / $grand_total_invoice, 2).'%';
                        } else {
                            $percentage_invoice = '0.00%';
                        }
                        $total_invoice = '$'.number_format($total_invoice, 2, '.', ',');

                        $row1 = $row1.',"'.$total_cost.'","'.$percentage_cost.'"';
                        $row2 = $row2.',"'.$total_requested.'","'.$percentage_requested.'"';
                        $row3 = $row3.',"'.$total_po.'","'.$percentage_po.'"';
                        $row4 = $row4.',"'.$total_invoice.'","'.$percentage_invoice.'"';
                    } // end for each expense

                    $row = $row1."\n".$row2."\n".$row3."\n".$row4;
                    Storage::append($directory.'/'.$filename_csv, $row);
                } // end for each vendor

                Excel::load('storage/app/'.$directory.'/'.$filename_csv, function ($file) {
                })->setFileName($filename)->store('xlsx', storage_path('app/'.$directory));

                // delete csv file
                unlink(storage_path('app/'.$directory.'/'.$filename_csv));

                $report = Report::where('id', '=', $this->report_id)->first();
                if ($report) {
                    $new_program_processed = $report->program_processed + 1;
                    $report->update([
                    'program_processed' => $new_program_processed,
                    ]);
                    $report = $report->fresh();
                }
                //$filenames_array[] = storage_path('app/'.$directory.'/'.$filename.'.xlsx');
            } // end for each program
        } else {
            // Doc: http://www.maatwebsite.nl/laravel-excel/docs/export
  /*        \Excel::create($filename, function($excel) use($debug, $summary_totals, $expense_categories) {

          require_once(base_path("vendor/phpoffice/phpexcel/Classes/PHPExcel/NamedRange.php"));
          require_once(base_path("vendor/phpoffice/phpexcel/Classes/PHPExcel/Cell/DataValidation.php"));
          require_once(base_path("vendor/phpoffice/phpexcel/Classes/PHPExcel/Cell/DataValidation.php"));
          require_once(base_path("vendor/phpoffice/phpexcel/Classes/PHPExcel/Style/Protection.php"));

          if($debug){
            $vendors = Vendor::where('id','!=',1)->orderBy('vendor_name','ASC')->offset(0)->limit(5)->get();
          }else{
            $vendors = Vendor::where('id','!=',1)->orderBy('vendor_name','ASC')->get();
          }

          $excel->sheet('Page 1 - Summary', function($sheet) use($debug, $expense_categories, $summary_totals, $vendors) {

            $totalRows = count($vendors) + count($expense_categories)*2 + 1;

            $sheet->setColumnFormat([
                'A2:A'.$totalRows => '@',
                'B2:B'.$totalRows => '@',
                'C2:C'.$totalRows => '@',
                'D2:D'.$totalRows => '@',
                'E2:E'.$totalRows => '$#,##0.00_-',
                'F2:F'.$totalRows => '#.##%'
            ]);

            $sheet->cells('E1:E'.$totalRows, function($cells) {
              $cells->setAlignment('right');
            });

            $sheet->cells('F1:F'.$totalRows, function($cells) {
              $cells->setAlignment('right');
            });

            $sheet->SetCellValue("A1", "Vendor Name");
            $sheet->SetCellValue("B1", "Vendor ID");
            $sheet->SetCellValue("C1", "Link to Vendor Details");
            $sheet->SetCellValue("D1", "Cost Type");

            $sheet->SetCellValue("E1", "Total");
            $sheet->SetCellValue("F1", "Total %");

            $col = 'F';
            foreach($expense_categories as $cat){
              $col++;
              $sheet->SetCellValue($col."1", "[".$cat->expense_category_name."] Total");
              $sheet->setColumnFormat([
                $col.'1' => '$#,##0.00_-'
              ]);

              $col++;
              $sheet->SetCellValue($col."1", "[".$cat->expense_category_name."] Total %");
              $sheet->setColumnFormat([
                $col.'1' => '#.##%'
              ]);
            }
            $sheet->cells('A1:'.$col.'1', function($cells) {
                $cells->setTextRotation(90);
              });

            $row = 2;

            foreach($vendors as $vendor){

              $vendor_url = env('APP_URL', 'https://ohfa.allita.org')."/viewvendor/".$vendor->id;

              // calculations
              // Cost
              $summary_total_cost = $vendor->totals()['cost'];
              $summary_grand_total_cost = $summary_totals[0][0]['cost'];
              if($summary_grand_total_cost != 0){
                $summary_percentage_cost = number_format($summary_total_cost * 100 / $summary_grand_total_cost, 2).'%';
              }else{
                $summary_percentage_cost = '0.00%';
              }
              $summary_total_cost = '$'.number_format($summary_total_cost, 2, '.', ',');

              // Requested
              $summary_total_requested = $vendor->totals()['request'];
              $summary_grand_total_requested = $summary_totals[0][0]['request'];
              if($summary_grand_total_requested != 0){
                $summary_percentage_requested = number_format($summary_total_requested * 100 / $summary_grand_total_requested, 2).'%';
              }else{
                $summary_percentage_requested = '0.00%';
              }
              $summary_total_requested = '$'.number_format($summary_total_requested, 2, '.', ',');

              // PO
              $summary_total_po = $vendor->totals()['po'];
              $summary_grand_total_po = $summary_totals[0][0]['po'];
              if($summary_grand_total_po != 0){
                $summary_percentage_po = number_format($summary_total_po * 100 / $summary_grand_total_po, 2).'%';
              }else{
                $summary_percentage_po = '0.00%';
              }
              $summary_total_po = '$'.number_format($summary_total_po, 2, '.', ',');

              // Invoiced
              $summary_total_invoice = $vendor->totals()['invoice'];
              $summary_grand_total_invoice = $summary_totals[0][0]['invoice'];
              if($summary_grand_total_invoice != 0){
                $summary_percentage_invoice = number_format($summary_total_invoice * 100 / $summary_grand_total_invoice, 2).'%';
              }else{
                $summary_percentage_invoice = '0.00%';
              }
              $summary_total_invoice = '$'.number_format($summary_total_invoice, 2, '.', ',');

              // for each category, save calculations in array to have less loops and db calls
              $calculation = array();
              foreach($expense_categories as $cat){
                // Cost
                $total_cost = $vendor->totals($cat->id)['cost'];
                $grand_total_cost = $summary_totals[$cat->id][0]['cost'];
                if($grand_total_cost != 0){
                  $percentage_cost = number_format($total_cost * 100 / $grand_total_cost, 2).'%';
                }else{
                  $percentage_cost = '0.00%';
                }
                $total_cost = '$'.number_format($total_cost, 2, '.', ',');

                // Request
                $total_requested = $vendor->totals($cat->id)['request'];
                $grand_total_requested = $summary_totals[$cat->id][0]['request'];
                if($grand_total_requested != 0){
                  $percentage_requested = number_format($total_requested * 100 / $grand_total_requested, 2).'%';
                }else{
                  $percentage_requested = '0.00%';
                }
                $total_requested = '$'.number_format($total_requested, 2, '.', ',');

                // PO
                $total_po = $vendor->totals($cat->id)['po'];
                $grand_total_po = $summary_totals[$cat->id][0]['po'];
                if($grand_total_po != 0){
                  $percentage_po = number_format($total_po * 100 / $grand_total_po, 2).'%';
                }else{
                  $percentage_po = '0.00%';
                }
                $total_po = '$'.number_format($total_po, 2, '.', ',');

                // Invoiced
                $total_invoice = $vendor->totals($cat->id)['invoice'];
                $grand_total_invoice = $summary_totals[$cat->id][0]['invoice'];
                if($grand_total_invoice != 0){
                  $percentage_invoice = number_format($total_invoice * 100 / $grand_total_invoice, 2).'%';
                }else{
                  $percentage_invoice = '0.00%';
                }
                $total_invoice = '$'.number_format($total_invoice, 2, '.', ',');

                $calculation[$cat->id]['cost']['total'] = $total_cost;
                $calculation[$cat->id]['cost']['percentage'] = $percentage_cost;
                $calculation[$cat->id]['request']['total'] = $total_requested;
                $calculation[$cat->id]['request']['percentage'] = $percentage_requested;
                $calculation[$cat->id]['po']['total'] = $total_po;
                $calculation[$cat->id]['po']['percentage'] = $percentage_po;
                $calculation[$cat->id]['invoice']['total'] = $total_invoice;
                $calculation[$cat->id]['invoice']['percentage'] = $percentage_invoice;
              }

              // all headers
              $row2 = $row + 1;
              $row3 = $row + 2;
              $row4 = $row + 3;

              $sheet->SetCellValue("A".$row, $vendor->vendor_name);
              $sheet->SetCellValue("A".$row2, $vendor->vendor_name);
              $sheet->SetCellValue("A".$row3, $vendor->vendor_name);
              $sheet->SetCellValue("A".$row4, $vendor->vendor_name);

              $sheet->SetCellValue("B".$row, $vendor->id);
              $sheet->SetCellValue("B".$row2, $vendor->id);
              $sheet->SetCellValue("B".$row3, $vendor->id);
              $sheet->SetCellValue("B".$row4, $vendor->id);

              $sheet->SetCellValue("C".$row, $vendor_url);
              $sheet->getCell("C".$row)->getHyperlink()->setUrl($vendor_url);
              $sheet->SetCellValue("C".$row2, $vendor_url);
              $sheet->getCell("C".$row2)->getHyperlink()->setUrl($vendor_url);
              $sheet->SetCellValue("C".$row3, $vendor_url);
              $sheet->getCell("C".$row3)->getHyperlink()->setUrl($vendor_url);
              $sheet->SetCellValue("C".$row4, $vendor_url);
              $sheet->getCell("C".$row4)->getHyperlink()->setUrl($vendor_url);

              $sheet->SetCellValue("D".$row, 'Cost');
              $sheet->SetCellValue("D".$row2, 'Request');
              $sheet->SetCellValue("D".$row3, 'PO');
              $sheet->SetCellValue("D".$row4, 'Invoice');

              // all calculations
              $sheet->SetCellValue("E".$row, $summary_total_cost);
              $sheet->SetCellValue("E".$row2, $summary_total_requested);
              $sheet->SetCellValue("E".$row3, $summary_total_po);
              $sheet->SetCellValue("E".$row4, $summary_total_invoice);

              $sheet->SetCellValue("F".$row, $summary_percentage_cost);
              $sheet->SetCellValue("F".$row2, $summary_percentage_requested);
              $sheet->SetCellValue("F".$row3, $summary_percentage_po);
              $sheet->SetCellValue("F".$row4, $summary_percentage_invoice);

              $col = 'F';
              foreach($expense_categories as $cat){
                $col++;
                $sheet->SetCellValue($col.$row, $calculation[$cat->id]['cost']['total']);
                $sheet->SetCellValue($col.$row2, $calculation[$cat->id]['request']['total']);
                $sheet->SetCellValue($col.$row3, $calculation[$cat->id]['po']['total']);
                $sheet->SetCellValue($col.$row4, $calculation[$cat->id]['invoice']['total']);

                $col++;
                $sheet->SetCellValue($col.$row, $calculation[$cat->id]['cost']['percentage']);
                $sheet->SetCellValue($col.$row2, $calculation[$cat->id]['request']['percentage']);
                $sheet->SetCellValue($col.$row3, $calculation[$cat->id]['po']['percentage']);
                $sheet->SetCellValue($col.$row4, $calculation[$cat->id]['invoice']['percentage']);

              }

              // bottom border between vendors
              $sheet->cells("A".$row4.":".$col.$row4, function($cells) {
                $cells->setBorder('none', 'none', 'double', 'none');
              });

              $row = $row4 + 1;

            }

            $sheet->cells('E1:'.$col.$row4, function($cells) {
                $cells->setAlignment('right');
              });

            $sheet->row(1, function($row) {
              $row->setBackground('#005186');
              $row->setFontSize(12);
              $row->setFontColor('#ffffff');
            });

            $sheet->setAutoSize(true);
            $sheet->freezeFirstRow(1);

          }); // end sheet 1 - summary


          // create additional sheets
          $expense_categories_count = count($expense_categories);
          if($this->program_id !== null){
            $program_count = 1;
          }else{
            $program_count = Program::where('id','!=',1)->count();
          }

          $columns_per_program = $expense_categories_count * 2 + 2;
          $common_columns_count = 6;
          $max_columns_per_xls = 255;

          $n_programs_per_sheet = floor( ($max_columns_per_xls - $common_columns_count) / $columns_per_program );
          $n_sheets = ceil($program_count / $n_programs_per_sheet);

          $skip = 0;

          for($i=2; $i<=$n_sheets+1; $i++){
            $excel->sheet('Page '.$i, function($sheet) use($n_programs_per_sheet, $n_sheets, $program_count, $i, $summary_totals, $expense_categories, $vendors) {

              $skip = ($i-2) * $n_programs_per_sheet;
              if($program_count - $skip < $n_programs_per_sheet){
                $limit = $program_count - $skip;
              }else{
                $limit = $n_programs_per_sheet;
              }

              if($this->program_id !== null){
                $programs = Program::where('id','=',$this->program_id)->skip($skip)->take($limit)->get();
              }else{
                $programs = Program::where('id','!=',1)->skip($skip)->take($limit)->get();
              }


              $totalRows = count($vendors) + count($expense_categories)*2 + 1;

              $sheet->setColumnFormat([
                  'A2:A'.$totalRows => '@',
                  'B2:B'.$totalRows => '@',
                  'C2:C'.$totalRows => '@',
                  'D2:D'.$totalRows => '@',
                  'E2:E'.$totalRows => '$#,##0.00_-',
                  'F2:F'.$totalRows => '#.##%'
              ]);

              $sheet->cells('E1:E'.$totalRows, function($cells) {
                $cells->setAlignment('right');
              });

              $sheet->cells('F1:F'.$totalRows, function($cells) {
                $cells->setAlignment('right');
              });

              $sheet->SetCellValue("A1", "Vendor Name");
              $sheet->SetCellValue("B1", "Vendor ID");
              $sheet->SetCellValue("C1", "Link to Vendor Details");
              $sheet->SetCellValue("D1", "Cost Type");

              $sheet->SetCellValue("E1", "Total");
              $sheet->SetCellValue("F1", "Total %");

              $col = 'F';
              foreach($programs as $program){
                $col++;
                $sheet->SetCellValue($col."1", "[".$program->program_name."] Total");
                $sheet->setColumnFormat([
                  $col.'1' => '$#,##0.00_-'
                ]);

                $col++;
                $sheet->SetCellValue($col."1", "[".$program->program_name."] Total %");
                $sheet->setColumnFormat([
                  $col.'1' => '#.##%'
                ]);
                foreach($expense_categories as $cat){
                  $col++;
                  $sheet->SetCellValue($col."1", "[".$program->program_name."] [".$cat->expense_category_name."] Total");
                  $sheet->setColumnFormat([
                    $col.'1' => '$#,##0.00_-'
                  ]);

                  $col++;
                  $sheet->SetCellValue($col."1", "[".$program->program_name."] [".$cat->expense_category_name."] Total %");
                  $sheet->setColumnFormat([
                    $col.'1' => '#.##%'
                  ]);
                }
              }
              $sheet->cells('A1:'.$col.'1', function($cells) {
                $cells->setTextRotation(90);
              });


              $row = 2;

              foreach($vendors as $vendor){

                $vendor_url = env('APP_URL', 'https://ohfa.allita.org')."/viewvendor/".$vendor->id;

                // calculations
                // Cost
         //        $summary_total_cost = $vendor->totals()['cost'];
         //        $summary_grand_total_cost = $summary_totals[0][0]['cost'];
         //        if($summary_grand_total_cost != 0){
         //          $summary_percentage_cost = number_format($summary_total_cost * 100 / $summary_grand_total_cost, 2).'%';
         //        }else{
         //          $summary_percentage_cost = '0.00%';
         //        }
         //        $summary_total_cost = '$'.number_format($summary_total_cost, 2, '.', ',');

         //        // Requested
         //        $summary_total_requested = $vendor->totals()['request'];
         //        $summary_grand_total_requested = $summary_totals[0][0]['request'];
         //        if($summary_grand_total_requested != 0){
         //          $summary_percentage_requested = number_format($summary_total_requested * 100 / $summary_grand_total_requested, 2).'%';
         //        }else{
         //          $summary_percentage_requested = '0.00%';
         //        }
         //        $summary_total_requested = '$'.number_format($summary_total_requested, 2, '.', ',');

         //        // PO
         //        $summary_total_po = $vendor->totals()['po'];
         //        $summary_grand_total_po = $summary_totals[0][0]['po'];
         //        if($summary_grand_total_po != 0){
         //          $summary_percentage_po = number_format($summary_total_po * 100 / $summary_grand_total_po, 2).'%';
         //        }else{
         //          $summary_percentage_po = '0.00%';
         //        }
         //        $summary_total_po = '$'.number_format($summary_total_po, 2, '.', ',');

         //        // Invoiced
         //        $summary_total_invoice = $vendor->totals()['invoice'];
         //        $summary_grand_total_invoice = $summary_totals[0][0]['invoice'];
         //        if($summary_grand_total_invoice != 0){
         //          $summary_percentage_invoice = number_format($summary_total_invoice * 100 / $summary_grand_total_invoice, 2).'%';
         //        }else{
         //          $summary_percentage_invoice = '0.00%';
         //        }
         //        $summary_total_invoice = '$'.number_format($summary_total_invoice, 2, '.', ',');

                // for each program and category, save calculations in array to have less loops and db calls
                $calculation = array();


                foreach($programs as $program){

                  // Cost
                  $total_cost = $vendor->totals(0, $program->id)['cost'];
                  $grand_total_cost = $summary_totals[0][$program->id]['cost'];
                  if($grand_total_cost != 0){
                    $percentage_cost = number_format($total_cost * 100 / $grand_total_cost, 2).'%';
                  }else{
                    $percentage_cost = '0.00%';
                  }
                  $total_cost = '$'.number_format($total_cost, 2, '.', ',');

                  // Request
                  $total_requested = $vendor->totals(0, $program->id)['request'];
                  $grand_total_requested = $summary_totals[0][$program->id]['request'];
                  if($grand_total_requested != 0){
                    $percentage_requested = number_format($total_requested * 100 / $grand_total_requested, 2).'%';
                  }else{
                    $percentage_requested = '0.00%';
                  }
                  $total_requested = '$'.number_format($total_requested, 2, '.', ',');

                  // PO
                  $total_po = $vendor->totals(0, $program->id)['po'];
                  $grand_total_po = $summary_totals[0][$program->id]['po'];
                  if($grand_total_po != 0){
                    $percentage_po = number_format($total_po * 100 / $grand_total_po, 2).'%';
                  }else{
                    $percentage_po = '0.00%';
                  }
                  $total_po = '$'.number_format($total_po, 2, '.', ',');

                  // Invoiced
                  $total_invoice = $vendor->totals(0, $program->id)['invoice'];
                  $grand_total_invoice = $summary_totals[0][$program->id]['invoice'];
                  if($grand_total_invoice != 0){
                    $percentage_invoice = number_format($total_invoice * 100 / $grand_total_invoice, 2).'%';
                  }else{
                    $percentage_invoice = '0.00%';
                  }
                  $total_invoice = '$'.number_format($total_invoice, 2, '.', ',');

                  $calculation[0][$program->id]['cost']['total'] = $total_cost;
                  $calculation[0][$program->id]['cost']['percentage'] = $percentage_cost;
                  $calculation[0][$program->id]['request']['total'] = $total_requested;
                  $calculation[0][$program->id]['request']['percentage'] = $percentage_requested;
                  $calculation[0][$program->id]['po']['total'] = $total_po;
                  $calculation[0][$program->id]['po']['percentage'] = $percentage_po;
                  $calculation[0][$program->id]['invoice']['total'] = $total_invoice;
                  $calculation[0][$program->id]['invoice']['percentage'] = $percentage_invoice;


                  foreach($expense_categories as $cat){
                    // Cost
                    $total_cost = $vendor->totals($cat->id, $program->id)['cost'];
                    $grand_total_cost = $summary_totals[$cat->id][$program->id]['cost'];
                    if($grand_total_cost != 0){
                      $percentage_cost = number_format($total_cost * 100 / $grand_total_cost, 2).'%';
                    }else{
                      $percentage_cost = '0.00%';
                    }
                    $total_cost = '$'.number_format($total_cost, 2, '.', ',');

                    // Request
                    $total_requested = $vendor->totals($cat->id, $program->id)['request'];
                    $grand_total_requested = $summary_totals[$cat->id][$program->id]['request'];
                    if($grand_total_requested != 0){
                      $percentage_requested = number_format($total_requested * 100 / $grand_total_requested, 2).'%';
                    }else{
                      $percentage_requested = '0.00%';
                    }
                    $total_requested = '$'.number_format($total_requested, 2, '.', ',');

                    // PO
                    $total_po = $vendor->totals($cat->id, $program->id)['po'];
                    $grand_total_po = $summary_totals[$cat->id][$program->id]['po'];
                    if($grand_total_po != 0){
                      $percentage_po = number_format($total_po * 100 / $grand_total_po, 2).'%';
                    }else{
                      $percentage_po = '0.00%';
                    }
                    $total_po = '$'.number_format($total_po, 2, '.', ',');

                    // Invoiced
                    $total_invoice = $vendor->totals($cat->id, $program->id)['invoice'];
                    $grand_total_invoice = $summary_totals[$cat->id][$program->id]['invoice'];
                    if($grand_total_invoice != 0){
                      $percentage_invoice = number_format($total_invoice * 100 / $grand_total_invoice, 2).'%';
                    }else{
                      $percentage_invoice = '0.00%';
                    }
                    $total_invoice = '$'.number_format($total_invoice, 2, '.', ',');

                    $calculation[$cat->id][$program->id]['cost']['total'] = $total_cost;
                    $calculation[$cat->id][$program->id]['cost']['percentage'] = $percentage_cost;
                    $calculation[$cat->id][$program->id]['request']['total'] = $total_requested;
                    $calculation[$cat->id][$program->id]['request']['percentage'] = $percentage_requested;
                    $calculation[$cat->id][$program->id]['po']['total'] = $total_po;
                    $calculation[$cat->id][$program->id]['po']['percentage'] = $percentage_po;
                    $calculation[$cat->id][$program->id]['invoice']['total'] = $total_invoice;
                    $calculation[$cat->id][$program->id]['invoice']['percentage'] = $percentage_invoice;
                  }

                }


                // all headers
                $row2 = $row + 1;
                $row3 = $row + 2;
                $row4 = $row + 3;

                $sheet->SetCellValue("A".$row, $vendor->vendor_name);
                $sheet->SetCellValue("A".$row2, $vendor->vendor_name);
                $sheet->SetCellValue("A".$row3, $vendor->vendor_name);
                $sheet->SetCellValue("A".$row4, $vendor->vendor_name);

                $sheet->SetCellValue("B".$row, $vendor->id);
                $sheet->SetCellValue("B".$row2, $vendor->id);
                $sheet->SetCellValue("B".$row3, $vendor->id);
                $sheet->SetCellValue("B".$row4, $vendor->id);

                $sheet->SetCellValue("C".$row, $vendor_url);
                $sheet->getCell("C".$row)->getHyperlink()->setUrl($vendor_url);
                $sheet->SetCellValue("C".$row2, $vendor_url);
                $sheet->getCell("C".$row2)->getHyperlink()->setUrl($vendor_url);
                $sheet->SetCellValue("C".$row3, $vendor_url);
                $sheet->getCell("C".$row3)->getHyperlink()->setUrl($vendor_url);
                $sheet->SetCellValue("C".$row4, $vendor_url);
                $sheet->getCell("C".$row4)->getHyperlink()->setUrl($vendor_url);

                $sheet->SetCellValue("D".$row, 'Cost');
                $sheet->SetCellValue("D".$row2, 'Request');
                $sheet->SetCellValue("D".$row3, 'PO');
                $sheet->SetCellValue("D".$row4, 'Invoice');

                // all calculations
                $sheet->SetCellValue("E".$row, $summary_total_cost);
                $sheet->SetCellValue("E".$row2, $summary_total_requested);
                $sheet->SetCellValue("E".$row3, $summary_total_po);
                $sheet->SetCellValue("E".$row4, $summary_total_invoice);

                $sheet->SetCellValue("F".$row, $summary_percentage_cost);
                $sheet->SetCellValue("F".$row2, $summary_percentage_requested);
                $sheet->SetCellValue("F".$row3, $summary_percentage_po);
                $sheet->SetCellValue("F".$row4, $summary_percentage_invoice);

                $col = 'F';
                foreach($programs as $program){
                  $col++;
                  $sheet->SetCellValue($col.$row, $calculation[0][$program->id]['cost']['total']);
                  $sheet->SetCellValue($col.$row2, $calculation[0][$program->id]['request']['total']);
                  $sheet->SetCellValue($col.$row3, $calculation[0][$program->id]['po']['total']);
                  $sheet->SetCellValue($col.$row4, $calculation[0][$program->id]['invoice']['total']);

                  $col++;
                  $sheet->SetCellValue($col.$row, $calculation[0][$program->id]['cost']['percentage']);
                  $sheet->SetCellValue($col.$row2, $calculation[0][$program->id]['request']['percentage']);
                  $sheet->SetCellValue($col.$row3, $calculation[0][$program->id]['po']['percentage']);
                  $sheet->SetCellValue($col.$row4, $calculation[0][$program->id]['invoice']['percentage']);

                  foreach($expense_categories as $cat){
                    $col++;
                    $sheet->SetCellValue($col.$row, $calculation[$cat->id][$program->id]['cost']['total']);
                    $sheet->SetCellValue($col.$row2, $calculation[$cat->id][$program->id]['request']['total']);
                    $sheet->SetCellValue($col.$row3, $calculation[$cat->id][$program->id]['po']['total']);
                    $sheet->SetCellValue($col.$row4, $calculation[$cat->id][$program->id]['invoice']['total']);

                    $col++;
                    $sheet->SetCellValue($col.$row, $calculation[$cat->id][$program->id]['cost']['percentage']);
                    $sheet->SetCellValue($col.$row2, $calculation[$cat->id][$program->id]['request']['percentage']);
                    $sheet->SetCellValue($col.$row3, $calculation[$cat->id][$program->id]['po']['percentage']);
                    $sheet->SetCellValue($col.$row4, $calculation[$cat->id][$program->id]['invoice']['percentage']);
                  }
                }

                // bottom border between vendors
                $sheet->cells("A".$row4.":".$col.$row4, function($cells) {
                  $cells->setBorder('none', 'none', 'double', 'none');
                });

                $row = $row4 + 1;

              }

              $sheet->cells('E1:'.$col.$row4, function($cells) {
                $cells->setAlignment('right');
              });

              $sheet->row(1, function($row) {
                $row->setBackground('#005186');
                $row->setFontSize(12);
                $row->setFontColor('#ffffff');
              });

              $sheet->setAutoSize(true);
              $sheet->freezeFirstRow(1);

            });
          }

          })->download("xlsx");
          //})->store('xlsx', storage_path('app/export/vendorstats'));
*/
        }

        // Update report table with filename, folder and flag as ready.
        $report = Report::where('id', '=', $this->report_id)->first();
        if ($report) {
            // $new_program_processed = $report->program_processed + 1;
            // $report->update([
            //       'program_processed' => $new_program_processed
            // ]);
            // $report = $report->fresh();
            // if we are done with all the programs, update the report table
            if ($report->program_processed == $report->program_numbers) {
                // create zip with everything
                $zipper = new \Chumper\Zipper\Zipper;

                $files = glob(storage_path('app/'.$directory.'/*'));
                //$zipper->make('vendor_stats_'.$date.'.zip')->folder('storage/app/export/vendorstats/'.$date.'/')->add($filenames_array);
                $zipper->make(storage_path('app/'.$directory.'/').'vendor_stats_'.$date.'.zip')->add($files)->close();

                $report->update([
                  'pending_request' => 0,
                ]);

                // Send email notification to requestor
                if ($this->requestorEmail) {
                    $emailNotification = new DownloadReady('/reports/export_vendor_stats', $filename.'.xlsx', $this->requestorId);
                    \Mail::to($this->requestorEmail)->send($emailNotification);
                //  \Mail::to('p@newnectar.com')->send($emailNotification); // debug
                } else {
                    email_admins('Vendor Stats export processed automatically (no email address provided).', '/reports/export_vendor_stats');
                }
            }
        }

        $this->delete();
    }

    public function failed(Exception $exception)
    {
        try {
            email_admins('Crap: '.$exception->getMessage(), '/reports/export_vendor_stats');
        } catch (\Illuminate\Database\QueryException $ex) {
            dd($ex->getMessage());
        }
    }
}
