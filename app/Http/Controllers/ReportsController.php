<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Parcel;
use App\Http\Requests;
use Gate;
use \DB;
use Auth;
use Excel;
use App\Models\User;
use App\Models\Report;
use App\Models\Vendor;
use App\Models\ExpenseCategory;
use App\Models\ReportDownload;
use App\Models\Role;
use Carbon;
use App\Models\Mail\DownloadReady;
use App\Models\Mail\EmailSystemAdmin;
use Illuminate\Support\Facades\Storage;
use App\LogConverter;
use App\Models\Jobs\VendorStatsExportJob;
use App\Models\Program;


use App\Models\CostItem;
use App\Models\RequestItem;
use App\Models\PoItems;
use App\Models\InvoiceItem;

class ReportsController extends Controller
{
    public function __construct(Request $request)
    {
        $this->middleware('auth');
    }

    public function viewVendor($vendor = 0)
    {
        if ($vendor) {
            return redirect('/home')->with('open_vendor_id', $vendor);
        }
    }

    public function listExportParcels()
    {
        if (Gate::allows('view-all-parcels')) {
            $reports = Report::orderBy('id', 'desc')->with('downloads')->where('type', '=', 'export_parcels')->get();

            $pending_reports = Report::where('pending_request', '=', 1)->where('type', '=', 'export_parcels')->orderBy('id', 'desc')->get();


            $directory = 'export/parcels';
            $files = [];

            $files_from_storage = Storage::files($directory);

            $downloaders_array = [];

            foreach ($files_from_storage as $file) {
                $filename = str_replace($directory."/", "", $file);
                $path = $file;
                $size = formatSizeUnits(Storage::size($file));
                $time = Storage::lastModified($file);
                $carbon = Carbon\Carbon::createFromTimestamp($time);
                //	$humantime = $carbon->toCookieString();
                $humantime = $carbon->format('l F d, Y h:i A');

                $report = Report::where('filename', '=', $filename)->where('type', '=', 'export_parcels')->with('user')->with('downloads', 'downloads.user')->first();

                if ($report) {
                    $downloaders = '';
                    if ($report->downloads) {
                        foreach ($report->downloads as $download) {
                            $date = date('l F d, Y h:i A', strtotime($download->created_at));
                            $downloaders = $downloaders.$download->user->name." on ".$date."<br />";
                        }
                    }
                    $downloaders_array[$report->id] = $downloaders;

                    $files[] = [
                        'id' => $report->id,
                        'filename' => $filename,
                        'path'  => $path,
                        'size' => $size,
                        'time' => $time,
                        'downloads' => $report->download_total(),
                        'humantime' => $humantime,
                        'requestor' => $report->user->name
                    ];
                } else {
                    $files[] = [
                        'id' => '',
                        'filename' => $filename,
                        'path'  => $path,
                        'size' => $size,
                        'time' => $time,
                        'downloads' => '',
                        'humantime' => $humantime,
                        'requestor' => ''
                    ];
                }
            }

            // order by time desc
            usort($files, function ($item1, $item2) {
                return $item2['time'] <=> $item1['time'];
            });
            

            return view('pages.export.parcels', compact('files', 'pending_reports', 'downloaders_array'));
        }
    }

    public function exportParcelsDownload($filename = null)
    {
        if (Gate::allows('view-all-parcels')) {
            $directory = 'export/parcels';

            // check if filename is sanitized
            if ($filename == null || !check_file_name($filename)) {
                return redirect('/reports/export_parcels');
            }

            // check if there is a filename in storage
            if (!Storage::exists('export/parcels/'.$filename)) {
                return redirect('/reports/export_parcels');
            }

            // record download in table report_downloads
            $report = Report::where('filename', '=', $filename)->with('user')->first();
            
            if ($report) {
                $requestor = Auth::user();
                
                $new_download = new ReportDownload([
                      'report_id' => $report->id,
                      'user_id' => $requestor->id
                ]);
                $new_download->save();

                // log
                $lc = new LogConverter('user', 'export parcels downloaded');
                $lc->setDesc($requestor->email . ' downloaded a report (export parcels)')->setFrom($requestor)->setTo($requestor)->save();
            }
            

            $url = storage_path('app/export/parcels/').$filename;
            // retrieve file
            return response()->download($url);
        }
    }

    public function listExportVendorStats()
    {

        // $vendors = Vendor::where('id','!=',1)->orderBy('vendor_name','ASC')->offset(0)->limit(5)->first();
        // dd($vendors->totals(2)['cost']);

        if (Gate::allows('view-all-parcels')) {
            //$reports = Report::orderBy('id','desc')->with('downloads')->where('type','=','export_vendor_stats')->get();

            $reports = Report::where('type', '=', 'export_vendor_stats')->orderBy('id', 'desc')->limit(10)->get();
            // check if there is a report and set pending if the last one is pending
            if (count($reports)>0) {
                // store files in array
                $files = [];
                $downloaders_array = [];
                
                foreach ($reports as $report) {
                    // if($previous_report->pending_request == 1){
                    //     $pending_reports = 1;
                    // }else{
                    //     $pending_reports = 0;
                    // }

                    $pending_reports = [];

                    //$directory = 'export/vendorstats';
                    $directory = $report->folder;

                    $files_from_storage = Storage::files($directory);

                    foreach ($files_from_storage as $file) {
                        $filename = str_replace($directory."/", "", $file);
                        $path = $file;

                        // list only the zip files
                        $tmp_filename = explode('.', $filename);
                        $extension = end($tmp_filename);
                        if ($extension != 'zip') {
                            continue;
                        }

                        $size = formatSizeUnits(Storage::size($file));
                        $time = Storage::lastModified($file);
                        $carbon = Carbon\Carbon::createFromTimestamp($time);
                        //  $humantime = $carbon->toCookieString();
                        $humantime = $carbon->format('l F d, Y h:i A');

                        $report = Report::where('filename', '=', $filename)->where('type', '=', 'export_vendor_stats')->with('user')->with('downloads', 'downloads.user')->first();

                        if ($report) {
                            $downloaders = '';
                            if ($report->downloads) {
                                foreach ($report->downloads as $download) {
                                    $date = date('l F d, Y h:i A', strtotime($download->created_at));
                                    $downloaders = $downloaders.$download->user->name." on ".$date."<br />";
                                }
                            }
                            $downloaders_array[$report->id] = $downloaders;

                            $files[] = [
                                'id' => $report->id,
                                'filename' => $filename,
                                'path'  => $path,
                                'size' => $size,
                                'time' => $time,
                                'downloads' => $report->download_total(),
                                'humantime' => $humantime,
                                'requestor' => $report->user->name
                            ];
                        } else {
                            $files[] = [
                                'id' => '',
                                'filename' => $filename,
                                'path'  => $path,
                                'size' => $size,
                                'time' => $time,
                                'downloads' => '',
                                'humantime' => $humantime,
                                'requestor' => ''
                            ];
                        }
                    }

                    // order by time desc
                    usort($files, function ($item1, $item2) {
                        return $item2['time'] <=> $item1['time'];
                    });


                    // $vendors = Vendor::where('id','!=',1)->with('state')->orderBy('vendor_name','ASC')->limit(5)->get();
                  // dd($vendors);
                }
            } else {
                $files = [];
                $pending_reports = [];
                $downloaders_array = [];
            }
            
            return view('pages.export.vendorstats', compact('files', 'pending_reports', 'downloaders_array'));
        }
    }
    
    public function exportVendorStats()
    {
        if (Auth::user()->entity_type == 'hfa') {
            $requestor = Auth::user();

            $programs = Program::where('id', '!=', 1)->get();
            $count_programs = count($programs);

            $date = date("m-d-Y_g-i-s_a", time());
            $directory = 'export/vendorstats/'.$date;
            $filename = 'vendor_stats_'.$date.'.zip';

            // Save report request in database
            $new_report = new Report([
                  'type' => "export_vendor_stats",
                  'folder' => $directory,
                  'filename' => $filename,
                  'pending_request' => 1,
                  'user_id' => $requestor->id,
                  'program_numbers' => $count_programs,
                  'program_processed' => 0
            ]);
            $new_report->save();

            foreach ($programs as $program) {
                $name = str_replace(' ', '_', $program->program_name);
            
                $job = new VendorStatsExportJob($requestor, $new_report->id, $program->id, 1, $date);
                dispatch($job);
            }

            $lc = new LogConverter('user', 'export vendor stats requested');
            $lc->setDesc($requestor->email . ' requested a report (export vendor stats)')->setFrom($requestor)->setTo($requestor)->save();

            return redirect('/reports/export_vendor_stats')->with('systemMessage', 'Vendor stats export is being processed. An email will be sent when the file is ready to download.');
        //return redirect()->back()->with('systemMessage','Parcels export is being processed. An email will be sent when the file is ready to download.');
        } else {
            return "<script>alert('Sorry, you do not have permission to do this');</script>";
        }
    }

    public function exportVendorStatsDownload($fileid = null)
    {
        if (Gate::allows('view-all-parcels')) {
            $directory = 'export/vendorstats';

            // record download in table report_downloads
            $report = Report::where('id', '=', $fileid)->where('type', '=', 'export_vendor_stats')->with('user')->first();

            if ($report) {
                // check if there is a filename in storage
                if (!Storage::exists($report->folder."/".$report->filename)) {
                    return redirect('/reports/export_vendor_stats');
                }
                
                $requestor = Auth::user();

                $new_download = new ReportDownload([
                      'report_id' => $report->id,
                      'user_id' => $requestor->id
                ]);
                $new_download->save();

                // log
                $lc = new LogConverter('user', 'export vendor stats downloaded');
                $lc->setDesc($requestor->email . ' downloaded a report (export vendor stats id '.$report->id.', '.$report->filename.')')->setFrom($requestor)->setTo($requestor)->save();
            } else {
                return redirect('/reports/export_vendor_stats');
            }

            $url = storage_path('app/'.$report->folder.'/').$report->filename;

            // retrieve file
            return response()->download($url);
        }
    }
}
