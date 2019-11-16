<?php

namespace App\Jobs;

use App\Mail\DownloadReady;
use App\Parcel;
use App\Report;
use App\User;
use Excel;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

ini_set('max_execution_time', 600);
ini_set('memory_limit', '1000M');

/**
 * ParcelsExport Job.
 *
 * @category Events
 * @license  Proprietary and confidential
 */
class ParcelsExportJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $requestorEmail;
    protected $report_id;
    protected $requestorId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user = null, $report_id = null, $filter = null)
    {
        if ($user) {
            $this->requestorEmail = $user->email;
            $this->requestorId = $user->id;
        } else {
            $this->requestorEmail = null;
            $this->requestorId = null;
        }

        $this->report_id = $report_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $date = date('m-d-Y_g-i-s_a', time());
        $filename = 'all_parcel_data_'.$date;
        \Excel::create($filename, function ($excel) {
            require_once base_path('vendor/phpoffice/phpexcel/Classes/PHPExcel/NamedRange.php');
            require_once base_path('vendor/phpoffice/phpexcel/Classes/PHPExcel/Cell/DataValidation.php');
            require_once base_path('vendor/phpoffice/phpexcel/Classes/PHPExcel/Cell/DataValidation.php');
            require_once base_path('vendor/phpoffice/phpexcel/Classes/PHPExcel/Style/Protection.php');

            $excel->sheet('Per Unit Accounting '.date('m.d.y', time()), function ($sheet) {
                $parcels = Parcel::orderBy('entity_id', 'ASC')->orderBy('created_at')->get();
                $totalRows = count($parcels) + 1; /// add one to include header

                $sheet->setColumnFormat([
                'A2:A'.$totalRows => '@', 'B2:B'.$totalRows => '@', 'C2:C'.$totalRows => '@', 'D2:D'.$totalRows => '@', 'E2:E'.$totalRows => '@', 'F2:F'.$totalRows => '0.00000000', 'G2:G'.$totalRows => '0.00000000', 'H2:H5'.$totalRows => '@', 'I2:I'.$totalRows => '$#,##0.00_-', 'J2:J'.$totalRows => '$#,##0.00_-',
                'K2:K'.$totalRows => '$#,##0.00_-', 'L2:L'.$totalRows => '$#,##0.00_-', 'M2:M'.$totalRows => '$#,##0.00_-', 'N2:N'.$totalRows => '$#,##0.00_-', 'O2:O'.$totalRows => '$#,##0.00_-', 'P2:P'.$totalRows => '$#,##0.00_-', 'Q2:Q'.$totalRows => '$#,##0.00_-', 'R2:R'.$totalRows => 'm/d/y',
                ]);

                $sheet->SetCellValue('A1', 'Parcel');
                $sheet->SetCellValue('B1', 'Partner');
                $sheet->SetCellValue('C1', 'Street Address');
                $sheet->SetCellValue('D1', 'City');
                $sheet->SetCellValue('E1', 'Zip');
                $sheet->SetCellValue('F1', 'Lat Lon (Latitude)');
                $sheet->SetCellValue('G1', 'Lat Lon (Longitude)');
                $sheet->SetCellValue('H1', 'Property Status');
                $sheet->SetCellValue('I1', 'Acquisition');
                $sheet->SetCellValue('J1', 'NIP Loan');
                $sheet->SetCellValue('K1', 'Pre-Demo');
                $sheet->SetCellValue('L1', 'Demolition');
                $sheet->SetCellValue('M1', 'Greening');
                $sheet->SetCellValue('N1', 'Maintenance');
                $sheet->SetCellValue('O1', 'Admin');
                $sheet->SetCellValue('P1', 'Other');
                $sheet->SetCellValue('Q1', 'Disbursement');
                $sheet->SetCellValue('R1', 'Date Paid');

                $row = 1;
                foreach ($parcels as $data) {
                    $row++;
                    $sheet->SetCellValue('A'.$row, $data->parcel_id);
                    if ($data->program) {
                        $sheet->SetCellValue('B'.$row, $data->program->program_name);
                    } else {
                        $sheet->SetCellValue('B'.$row, '');
                    }
                    $sheet->SetCellValue('C'.$row, $data->street_address);
                    $sheet->SetCellValue('D'.$row, $data->city);
                    $sheet->SetCellValue('E'.$row, $data->zip);
                    $sheet->SetCellValue('F'.$row, $data->latitude);
                    $sheet->SetCellValue('G'.$row, $data->longitude);
                    if ($data->hfa_property_status) {
                        $sheet->SetCellValue('H'.$row, $data->hfa_property_status->option_name);
                    } else {
                        $sheet->SetCellValue('H'.$row, '');
                    }
                    $sheet->SetCellValue('I'.$row, $data->acquisitionTotal());
                    $sheet->SetCellValue('J'.$row, $data->nipLoanTotal());
                    $sheet->SetCellValue('K'.$row, $data->predemoTotal());
                    $sheet->SetCellValue('L'.$row, $data->demolitionTotal());
                    $sheet->SetCellValue('M'.$row, $data->demolitionTotal());
                    $sheet->SetCellValue('N'.$row, $data->maintenanceTotal());
                    $sheet->SetCellValue('O'.$row, $data->administrationTotal());
                    $sheet->SetCellValue('P'.$row, $data->otherTotal());
                    // get status of this parcel's invoice - if it is paid put in invoiced total

                    if (isset($data->associatedInvoice->reimbursement_invoice_id)) {
                        $invoice = \App\ReimbursementInvoice::find($data->associatedInvoice->reimbursement_invoice_id);
                        if (isset($invoice->status_id)) {
                            $invoiceStatus = $invoice->status_id;
                        } else {
                            $invoiceStatus = 0;
                        }
                        $payment = \App\Transaction::where('link_to_type_id', $invoice->id)->where('type_id', 1)->first();
                        if (isset($payment->date_entered)) {
                            $sheet->SetCellValue('Q'.$row, $data->invoicedTotal());
                            // get the paid date
                            $sheet->SetCellValue('R'.$row, date('m/d/Y', strtotime($payment->date_entered)));
                        } elseif ($invoiceStatus == 0) {
                            $sheet->SetCellValue('Q'.$row, 'Cannot determine invoice status.');
                        }
                    } else {
                        $sheet->SetCellValue('Q'.$row, 0);
                    }
                }

                // Set black background
                $sheet->row(1, function ($row) {

                // call cell manipulation methods
                    $row->setBackground('#005186');
                    $row->setFontSize(15);
                    $row->setFontColor('#ffffff');
                });
                $sheet->freezeFirstRow(1);
                $sheet->setWidth([
                  'A'     =>  20,
                  'B'     =>  25,
                  'C'     =>  25,
                  'D'     =>  20,
                  'E'     =>  20,
                  'F'     =>  20,
                  'G'     =>  20,
                  'H'     =>  20,
                  'I'     =>  15,
                  'J'     =>  15,
                  'K'     =>  15,
                  'L'     =>  15,
                  'M'     =>  15,
                  'N'     =>  15,
                  'O'     =>  15,
                  'P'     =>  15,
                  'Q'     =>  15,
                  'R'     =>  25,
                ]);
            });
            //})->download("xlsx");
        })->store('xls', storage_path('app/export/parcels'));

        // Update report table with filename, folder and flag as ready.
        $report = Report::where('id', '=', $this->report_id)->first();
        if ($report) {
            $report->update([
                  'folder' => 'export/parcels',
                  'filename' => $filename.'.xls',
                  'pending_request' => 0,
            ]);
        }

        // Send email notification to requestor
        if ($this->requestorEmail) {
            $emailNotification = new DownloadReady('/reports/export_parcels', $filename.'.xls', $this->requestorId);
            \Mail::to($this->requestorEmail)->send($emailNotification);
        } else {
            email_admins('Parcel export processed automatically (no email address provided).', 'export/parcels/');
        }

        $this->delete();
    }

    public function failed(Exception $exception)
    {
        try {
            email_admins('Crap: '.$exception->getMessage(), '/reports/export_parcels');
        } catch (\Illuminate\Database\QueryException $ex) {
            dd($ex->getMessage());
        }
    }
}
