<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\LogConverter;
use App\Models\Entity;
use App\Models\Import;
use App\Models\ImportRow;
use App\Models\Programs;
use App\Models\TargetArea;
use Auth;
use Box\Spout\Common\Type;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\WriterFactory;
use Carbon\Carbon;
use DB;
use Excel;
use File;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Storage;

class HHFRetentionImportController extends Controller
{
    public function __construct(Request $request)
    {
        // $this->middleware('auth');
    }

    // Required fields for form
    //
    private $validation = ['table' => 'required', 'data' => 'required'];

    // Don't let anyone upload things into these columns on any table.
    //
    private $ignore_columns = ['created_at', 'updated_at', 'owner_id', 'owner_type'];

    // Prefill these columns with user info and don't let user fill them.
    //
    public $autofill_columns = ['account_id', 'program_id', 'entity_id', 'user_id', 'state_id', 'county_id'];

    // Is a string variable a boolean
    //
    public function checkBool($string)
    {
        $string = strtolower($string);

        return in_array($string, ['true', 'false', '1', '0', 'yes', 'no'], true);
    }

    // Get the table column names and Types for any table.
    // returns empty array if no table found
    //
    protected function getTableColumns($table)
    {
        $columns = [];
        $keys = [];
        $sm = DB::connection()->getDoctrineSchemaManager();
        $db_cols = $sm->listTableColumns($table);

        // First load foreign keys, read into an array.
        // Each "foreign key" returned is an array - to account for keys that span multiple columns.
        // We only support 1-column foreign keys in this code.
        //
        $foreign_keys = $sm->listTableForeignKeys($table);

        foreach ($foreign_keys as $foreign_key) {
            $local_col = current($foreign_key->getColumns());
            $foreign_col = current($foreign_key->getForeignColumns());
            $table = $foreign_key->getForeignTableName();

            // save into temporary array, to reference below.
            $keys[$local_col] = [
                    'references' => $foreign_col,
                    'on'         => $table,
                ];
        }

        foreach ($db_cols as $db_col) {
            if (! in_array($db_col->getName(), $this->ignore_columns)) {
                $columns[] = [
                        'name'       => $db_col->getName(),
                        'type'       => $db_col->getType(),
                        'references' => array_key_exists($db_col->getName(), $keys) ? $keys[$db_col->getName()]['references'] : null,
                        'on'         => array_key_exists($db_col->getName(), $keys) ? $keys[$db_col->getName()]['on'] : null,

                        // Find "required" columns
                        'notnull'    => $db_col->getNotnull(),
                        'default'    => $db_col->getDefault(),

                    ];
            }
        }

        return $columns;
    }

    // Display a form with table-name and file upload fields.
    //
    public function form(REQUEST $request)
    {
        if (Gate::allows('view-all-parcels')) {
            if ($request->input('validate') != 1) {
                // RESET SESSION VARIABLES.
                session(['hhf_retention_validation_totalCount'=>0]);
                session(['hhf_retention_validation_addressCount' => 0]);
                session(['hhf_retention_validation_ohSenateCount' => 0]);
                session(['hhf_retention_validation_ohHouseCount' => 0]);
                session(['hhf_retention_validation_usHouseCount' => 0]);
                //put in code here to set where to only get ids for their program
                //make it get all ids for OHFA
                switch (Auth::user()->entity_id) {
                    case '1':
                        $where = '%%';
                        $whereOperator = 'LIKE';
                        break;

                    default:
                        $where = Auth::user()->entity_id;
                        $whereOperator = '=';
                        break;
                }

                return view('pages.import.hhf_retention_form');
            } else {
                $rowCount = DB::table('sdo_parcels')->where('latitude', null)->count();
                $inserted = 0;
                $ignored = 0;
                $updated = 0;
                $start = time();
                $finish = time();

                return view('pages.import.validate_hhf_retention_parcels', compact('rowCount', 'ignored', 'inserted', 'updated', 'start', 'finish'));
            }
        } else {
            return 'Sorry you do not have access to this page.';
        }
    }

    // Process initial file upload, pull table columns, show mappings form
    //
    public function mappings(Request $request)
    {

            // Validate the required fields.
        //$this->validate($request, $this->validation);

        $table = 'sdo_parcels';

        $program_id = 1;
        $account_id = 1;

        // $columns = $this->getTableColumns($table);
        // if (!$columns) {
        // 	return view('pages.import.hhf_retention_form')->withErrors(['message' => 'No table found with that name']);
        // }

        if ($request->input('validate') != 1) {
            // Upload the file.
            $file = $request->file('data');
            $ext = $file->getClientOriginalExtension();
            $filename = 'imports/entity_'.Auth::user()->entity_id.'/hhf_retention_'.time().'.'.$ext;
            Storage::put($filename, File::get($file));

            $reader = ReaderFactory::create(Type::CSV); // for XLSX files
            //$reader->setTempFolder(storage_path('app/imports/entity_' . Auth::user()->entity_id ));
            $reader->open(storage_path('app/'.$filename));
            $rowCount = 0;
            $indexes = 0;
            $updated = 0;
            $inserted = 0;
            $ignored = 0;
            $start = time();
            $exists = 0;
            DB::table('sdo_parcels')->truncate();

            foreach ($reader->getSheetIterator() as $sheet) { //sheet for
                foreach ($sheet->getRowIterator() as $row) { //row for
                    //dd($row);
                    if ($rowCount == 0) { //row count if
                        /// determine the indexes for the insert
                        $numKeys = count(array_keys($row));
                        do { // do

                            switch ($row[$indexes]) { // switch
                                case 'File Number':
                                    $fileNumber = $indexes;
                                    break;
                                case 'street_address':
                                    $streetAddress = $indexes;
                                    break;
                                case 'Property Address Number':
                                    $propertyAddressNumber = $indexes;
                                    break;
                                case 'Property Address Street Name':
                                    $propertyAddressName = $indexes;
                                    break;
                                case 'Property Address Street Suffix':
                                    $propertyAddressSuffix = $indexes;
                                    break;
                                case 'Property City':
                                    $propertyCity = $indexes;
                                    break;
                                case 'Property State':
                                    $propertyState = $indexes;
                                    break;
                                case 'Property Zip':
                                    $propertyZip = $indexes;
                                    break;
                                case 'Property County':
                                    $propertyCounty = $indexes;
                                    break;
                                case 'Status':
                                    $status = $indexes;
                                    break;
                                /*///////////////////////////////////////////////////////////////////////////////////////*
                                /*///////////////////////////////////////////////////////////////////////////////////////*
                                /*///////////////////////////////////////////////////////////////////////////////////////*

                                NEED DATE OF FIRST PAYMENT

                                case 'First Payment Date':
                                    $propertyFirstPayment = $indexes;
                                    break;

                                /*///////////////////////////////////////////////////////////////////////////////////////*
                                /*///////////////////////////////////////////////////////////////////////////////////////*
                                /*///////////////////////////////////////////////////////////////////////////////////////*

                                default:
                                    // code...
                                    break;
                            } // end switch
                            $indexes++;
                        } while ($indexes < $numKeys); //end do
                    } // rowcount if

                    /*///////////////////////////////////////////////////////////////////////////////////////*
                                if($rowCount != 0 && !isset($row['$propertyFirstPayment']) {
                    /*///////////////////////////////////////////////////////////////////////////////////////*
                    if ($rowCount != 0) { //row count iff
                        /// skip the headers
                        $new_sdo_data = [[
                              'Property Address Number'=>$row[$propertyAddressNumber],
                            'Property Address Street Name'=>$row[$propertyAddressName],
                            'Property Address Street Suffix'=>$row[$propertyAddressSuffix],
                            'Property City'=>$row[$propertyCity],
                            'Property State'=>$row[$propertyState],
                            'Property Zip'=>$row[$propertyZip],
                            'Property County'=>$row[$propertyCounty],
                            'Status'=>$row[$status],
                            'File Number'=>$row[$fileNumber],
                            'street_address'=>$row[$propertyAddressNumber].' '.$row[$propertyAddressName].' '.$row[$propertyAddressSuffix],
                            ]];

                        if ($exists > 0) {
                            /// update the parcel info
                            $new_sdo_data[0]['updated_at'] = date('Y-m-d H:i:s', time());
                            //dd($new_sdo_data);
                            $check = DB::table('sdo_parcels')->where('File Number', $row[$fileNumber])->update($new_sdo_data[0]);
                            if (! $check) {
                                return $check.' Bad things at row '.$rowCount;
                            }
                            $updated++;
                            $new_sdo_data = [];
                        } else {
                            /// doesn't exist - insert it.
                            $new_sdo_data[0]['created_at'] = date('Y-m-d H:i:s', time());
                            $check = DB::table('sdo_parcels')->insert($new_sdo_data);
                            if (! $check) {
                                return $check.' Bad things at row '.$rowCount;
                            }
                            $inserted++;
                            $new_sdo_data = [];
                        } // end exists if
                    } else {
                        $ignored++;
                    }//end row count if

                    $rowCount++;
                } // end row for
            } // end sheet for

                /// delete the file
            File::delete(storage_path('app/'.$filename));
        } else {
            $rowCount = DB::table('sdo_parcels')->where('latitude', null)->count();
            $inserted = 0;
            $updated = 0;
            $start = time();
        } // else end
        $finish = time();

        return view('pages.import.validate_hhf_retention_parcels', compact('rowCount', 'ignored', 'inserted', 'updated', 'start', 'finish'));
    }

    //fuction end
}// class
