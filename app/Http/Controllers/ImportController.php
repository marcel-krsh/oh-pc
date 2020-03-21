<?php

namespace App\Http\Controllers;

use DB;
use Gate;
use File;
use Storage;
use Illuminate\Support\Facades\Schema;
use Excel;
use App\Models\Parcel;
use Auth;

use Carbon\Carbon;

use App\Models\Import;
use App\Models\ImportRow;
use App\Models\Program;
use App\Models\Entity;
use App\Models\TargetArea;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\LogConverter;
use App\Models\ValidationResolutions;
use App\Models\SfParcel;

class ImportController extends Controller
{
     public function __construct(){
        $this->allitapc();
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
        return (in_array($string, ["true", "false", "1", "0", "yes", "no"], true));
    }

    // take a word like states and return state. or counties and return county.
    //
    // public function depluralize($word) {
    //     $rules = ['ies' => 'y', 'ses' => 's', 's' => ''];
    //     foreach(array_keys($rules) as $key){
    //         if(substr($word, (strlen($key) * -1)) != $key)
    //             continue;
    //         if($key === false)
    //             return $word;
    //         return substr($word, 0, strlen($word) - strlen($key)) . $rules[$key];
    //     }
    //     return $word;
    // }

    // Get the table column names and Types for any table.
    // returns empty array if no table found
    //
    protected function getTableColumns($table)
    {
        $columns = [];
        $keys = [];
        $sm      = DB::connection()->getDoctrineSchemaManager();
        $db_cols = $sm->listTableColumns($table);

        // First load foreign keys, read into an array.
        // Each "foreign key" returned is an array - to account for keys that span multiple columns.
        // We only support 1-column foreign keys in this code.
        //
        $foreign_keys = $sm->listTableForeignKeys($table);

        foreach ($foreign_keys as $foreign_key) {
            $local_col   = current($foreign_key->getColumns());
            $foreign_col = current($foreign_key->getForeignColumns());
            $table       = $foreign_key->getForeignTableName();

            // save into temporary array, to reference below.
            $keys[$local_col] = [
                    'references' => $foreign_col,
                    'on'         => $table
                ];
        }

        foreach ($db_cols as $db_col) {
            if (!in_array($db_col->getName(), $this->ignore_columns)) {
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
            //put in code here to set where to only get ids for their program
            //make it get all ids for OHFA
            switch (Auth::user()->entity_id) {
                case '1':
                    $where = '%%';
                    $whereOperator = "LIKE";
                    break;
                    
                default:
                    $where = Auth::user()->entity_id;
                    $whereOperator = "=";
                    break;
            }
            $programs = DB::table('programs')->select('id as program_id', 'program_name', 'entity_id')->where('active', 1)->where('entity_id', $whereOperator, $where)->orderBy('program_name', 'asc')->get()->all();
            $accounts = DB::table('accounts')->select('id as account_id', 'account_name', 'owner_id as program_id')->where('active', 1)->where('owner_id', $whereOperator, $where)->orderBy('account_name', 'asc')->get()->all();
            if ($request->query('showHowTo') == 1) {
                // Give instruction on steps to take to upload files.
                $showHowTo = 11;
            } else {
                $showHowTo = 0;
            }
            if (count($programs)<1 || count($accounts)<1) {
                session(['systemMessage'=>"It apprears that you do not have an active account or program. Please check with your HFA for assistance."]);
            }
                
            return view('pages.import.form', compact('targetAreaCheatSheet', 'howAcquiredCheatSheet', 'parcelTypeCheatSheet', 'programs', 'accounts', 'showHowTo'));
        } else {
            return "Sorry you do not have access to this page.";
        }
    }



    // Process initial file upload, pull table columns, show mappings form
    //
    public function mappings(Request $request)
    {

            // Validate the required fields.
        $this->validate($request, $this->validation);

        // Check that table exists and load columns
        // $table   = $request->input('table');

        // Hardcoding this for Parcels:
        $table = "parcels";


        $program_id   = $request->input('program_id');
        $account_id   = $request->input('account_id');

        $columns = $this->getTableColumns($table);
        if (!$columns) {
            return view('pages.import.form')->withErrors(['message' => 'No table found with that name']);
        }

        // Upload the file.
        $file = $request->file('data');
        $ext  = $file->getClientOriginalExtension();
        $filename = 'imports/entity_' . Auth::user()->entity_id . '/program_' . $program_id . '/' . time() . '.' . $ext;
        Storage::put($filename, File::get($file));
        $excel = Excel::load('storage/app/' . $filename)->get();
        if (is_a($excel, 'Maatwebsite\Excel\Collections\SheetCollection')) {
            $excel = $excel->first();
        }

        if (isset($excel[0]->do_not_import)) {
            $skipMapping = 1;
        //dd($excel[0]->do_not_import,$skipMapping);
        } else {
            $skipMapping = 0;
        }

        $autofill = $this->autofill_columns;

        return view('pages.import.mappings', compact('table', 'columns', 'filename', 'excel', 'autofill', 'program_id', 'account_id', 'skipMapping'));
    }

    public function correctAddress(Parcel $parcel, Request $request)
    {
        $states = DB::table('states')->get()->all();
        $lat = $request->get('lat');
        $lon = $request->get('lon');
        return view('modals.correct_address', compact('parcel', 'states', 'lat', 'lon'));
    }

    // Process the uploaded file, load table columns
    //
    public function corrections(Request $request)
    {
        if (Gate::allows('view-all-parcels')) {
            $autofill = $this->autofill_columns;

            // Check that table exists and load columns
            // $table   = $request->input('table');

            // Hardcoding this for Parcels:
            $table = "parcels";

            $program_id   = $request->input('program_id');
            $account_id   = $request->input('account_id');

            $columns = $this->getTableColumns($table);

            // Load back file, create Mapping array from input
            $filename = $request->input('filename');
            $excel = Excel::load('storage/app/' . $filename)->get();
            if (is_a($excel, 'Maatwebsite\Excel\Collections\SheetCollection')) {
                $excel = $excel->first();
            }

            // Load mappings
            $mappings = $request->input('mappings');
            $map = array_combine($excel->first()->keys()->all(), $mappings);

            //	See if this is our template (should have the do not import column)

            if (isset($excel[0]->do_not_import)) {
                $skipMapping = 1;
            //dd($excel[0]->do_not_import,$skipMapping);
            } else {
                $skipMapping = 0;
            }

            // Check For "Required" columns that don't exist in the mapping.
            $required_column_errors = [];
            foreach ($columns as $key => $column) {
                if ($column['notnull'] && $column['default'] === null) {
                    if (!in_array($key, $map)) {
                        $required_column_errors[] = $column['name'];
                    }
                }
            }
            if (!empty($required_column_errors)) {
                $err_message = '';
                foreach ($required_column_errors as $required_column_error) {
                    $err_message .= "Looks like you missed a column: " . $required_column_error . ". ";
                }
                return view('pages.import.mappings', compact('table', 'columns', 'filename', 'excel', 'autofill', 'program_id', 'account_id', 'skipMapping'))->withErrors(['message' => $err_message]);
            }





            // Load corrections if we have any
            $corrections = [];
            foreach ($request->all() as $i_name => $i_val) {
                if (substr($i_name, 0, strlen('corrections_')) === 'corrections_') {
                    $i_ref = explode("_", $i_name);
                    $corrections[$i_ref[1]][$i_ref[2]] = $i_val;
                }
            }

            $ic = []; // Import Candidates
                $ie = []; // Import Errors
                $excelRowCount = 0; // Count for Excel Array Key to delete.

                // Iterate over rows and columns, make a record of input and expected input.
            // Save errors into 2D array by row and column

            // Remove empty rows for the parcels import.
            foreach ($excel as $r => $row) {
                if ($row->parcel_id == null) {
                    unset($excel[$excelRowCount]);
                }
                $excelRowCount++;
            }

                
                
            foreach ($excel as $r => $row) {
                foreach ($row as $col => $value) {
                    if ($map[$col] != '') {
                            // If we already have a correction value, use that instead of the excel file:
                        if (isset($corrections[$r][$map[$col]])) {
                            $value = $corrections[$r][$map[$col]];
                        }

                        $ic[$r][$col] = [
                                'excel_row'        => $r,
                                'excel_row_label'  => $r + 2,
                                'excel_col'        => $col,
                                'excel_val'        => $value,
                                'excel_type'       => gettype($value),
                                'excel_int'        => (is_numeric($value) && intval($value) == $value),
                                'insert_val'       => $value,
                                'db_col_num'       => $map[$col],
                                'db_col'           => $columns[$map[$col]]['name'],
                                'db_type'          => $columns[$map[$col]]['type'],
                                'db_references'    => $columns[$map[$col]]['references'],
                                'db_on'            => $columns[$map[$col]]['on'],
                                'not_null'         => $columns[$map[$col]]['notnull'],
                            ];


                        // If the table column is a foreign key.
                        //
                        if (($ic[$r][$col]['db_references'] != null && $value != '' && $value != null) || ($ic[$r][$col]['db_references'] != null && (($value == '') || ($value == null)) && $ic[$r][$col]['not_null'])) {
                            // First, check if an ID column exists. --
                                
                            if (is_numeric($ic[$r][$col]['excel_val'])) {
                                $foreign_row = DB::table($ic[$r][$col]['db_on'])->where($ic[$r][$col]['db_references'], "=", $ic[$r][$col]['excel_val'])->first();
                            } else {
                                $foreign_row = [];
                            }
                            // if($ic[$r][$col]['db_col'] =="parcel_type_id"){
                            // 	$note = "Trying is_numeric() eval";
                            // 	dd($note,$ic[$r][$col]['db_on'],$ic[$r][$col]['db_references'],$ic[$r][$col]['excel_val'],$foreign_row);
                            // }
                            if (empty($foreign_row)) {
                                // If not - then check for the "_name" version.
                                if ($ic[$r][$col]['db_on'] == '' || $ic[$r][$col]['db_on'] == null) {
                                    dd($ic[$r][$col], $excel);
                                }
                                // $foreign_row = DB::table($ic[$r][$col]['db_on'])->where($this->depluralize($ic[$r][$col]['db_on']) . "_name", $ic[$r][$col]['excel_val'])->first();
                                $foreign_row = DB::table($ic[$r][$col]['db_on'])->where(str_singular($ic[$r][$col]['db_on']) . "_name", $ic[$r][$col]['excel_val'])->first();
                                if (empty($foreign_row)) {
                                        // Add Select dropdowns for mismatched foreign keys.

                                    // Which column do we want to use for the option VALUE in the select dropdown?
                                    // use account_name, state_name, etc. Parcels is the exception where it shows parcel_id column.
                                    // $o_display_col = ($ic[$r][$col]['db_on'] == "parcels") ? "parcel_id" : $this->depluralize($ic[$r][$col]['db_on']) . "_name";
                                    $o_display_col = ($ic[$r][$col]['db_on'] == "parcels") ? "parcel_id" : str_singular($ic[$r][$col]['db_on']) . "_name";

                                    // Query that table.
                                    $o_query = DB::table($ic[$r][$col]['db_on']);

                                    // IF the user can't get full access, restrict the access.
                                        

                                    // Accounts and Programs are owned by programs.
                                    if ($ic[$r][$col]['db_on'] == "accounts" || $ic[$r][$col]['db_on'] == "parcels") {
                                        $o_query->where('owner_id', '=', $request->input('program_id'));

                                    // Programs are owned by entities.
                                    } elseif ($ic[$r][$col]['db_on'] == "programs") {
                                        $o_query->where('owner_id', '=', auth()->user()->entity_id);

                                    // target areas have a county_id that corresponds to the county_id of the program the user selected.
                                    } elseif ($ic[$r][$col]['db_on'] == "target_areas") {
                                        $o_program = Program::find($request->input('program_id'));
                                        if ($o_program) {
                                            $o_query->where('county_id', '=', $o_program->county_id);
                                        }
                                    }
                                        

                                    // Run the query and save into array.
                                    $ic[$r][$col]['options'] = $o_query->orderBy($o_display_col)->pluck($o_display_col, 'id')->all();

                                    // IF there is only 1 option. use it as the correction. Else - show them the error and make them pick.
                                    if (count($ic[$r][$col]['options']) == 1) {
                                        reset($ic[$r][$col]['options']);
                                        $ic[$r][$col]['insert_val'] = key($ic[$r][$col]['options']);
                                    } else {
                                        $ie[$r][$col]['options'] = $ic[$r][$col]['options'];
                                        $ie[$r][$col] = $ic[$r][$col];

                                        if ($value != '' && $value != null) {
                                            $ie[$r][$col]['message'] = 'I don\'t have an option that matches "' . $ic[$r][$col]['excel_val'].'". Please select an option from my available set.';
                                        } else {
                                            $ie[$r][$col]['message'] = 'This is a required field (it was blank in the excel file) - please select one of the options.';
                                        }
                                    }
                                } else {
                                    $ic[$r][$col]['insert_val'] = $foreign_row->id;
                                }
                            }
                        } // If expecting Integer, and recieve String.
                        elseif ($ic[$r][$col]['db_type'] == 'Integer' && $value != '' && !(is_null($value) || $ic[$r][$col]['excel_int'])) {
                            $ie[$r][$col] = $ic[$r][$col];
                            $ie[$r][$col]['message'] = 'I was expecting an integer. ' . (is_numeric($value) ? '' : 'Looks like you included some text, like a comma, period, dollar sign, or perhaps a trailing space?') .  (is_float($value) ? ' Looks like you submitted a "float", meaning it has a decimal point. Please just round to the next whole number.' : '');
                        } // If expecting Integer, and receive a Null AND its required
                        elseif ($ic[$r][$col]['db_type'] == 'Integer' && (is_null($value) || $value == '') && $ic[$r][$col]['not_null']) {
                            $ie[$r][$col] = $ic[$r][$col];
                            $ie[$r][$col]['message'] = 'I was expecting an integer in this required field... and well there was nothing! Please enter a whole number.';
                        } // If expecting Boolean, and recieve String.
                        elseif ($ic[$r][$col]['db_type'] == 'Boolean' && $value !== '' && !(is_null($value) || is_bool($value) || $this->checkBool($value)) && $value !== 0 && $value !== 1 || ($ic[$r][$col]['db_type'] == 'Boolean' &&strlen($value) > 1)) {
                            $ie[$r][$col] = $ic[$r][$col];
                            $ie[$r][$col]['message'] = 'I was expecting a boolean (0 or 1) ' . (is_numeric($value) ? '' : ' and it looks like you put in text instead.');
                        } // If expecting Boolean, and received a NULL and is REQUIRED
                        elseif ($ic[$r][$col]['db_type'] == 'Boolean' && (is_null($value) || $value === '') && $ic[$r][$col]['not_null'] && $value !== 0) {
                            $ie[$r][$col] = $ic[$r][$col];
                            $ie[$r][$col]['message'] = 'I was expecting a boolean (0 or 1) for this required field, and there wasn\'t anything supplied! Please enter a 1 for true, or 0 for false.';
                        } // If expecting String, and received a NULL and is REQUIRED
                        elseif ($ic[$r][$col]['db_type'] == 'String' && (is_null($value) || $value == '') && $ic[$r][$col]['not_null']) {
                            $ie[$r][$col] = $ic[$r][$col];
                            $ie[$r][$col]['message'] = 'I was expecting some text here, and there wasn\'t anything supplied! Please enter some text, even if it is simply "NA".';
                        } // If the table column is "id"
                        //
                        elseif ($ic[$r][$col]['db_col'] == 'id' && $value != '' && $value != null) {
                            $update_row = DB::table($table)->find($value);
                            if (empty($update_row)) {
                                $ie[$r][$col] = $ic[$r][$col];
                                $ie[$r][$col]['message'] = 'I can\t find a row with the ID ' . $value;
                            } elseif (property_exists($update_row, "owner_type") && (
                                        ($update_row->owner_type == 'user' && $update_row->owner_id != auth()->user()->id) ||
                                        ($update_row->owner_type == 'entity' && $update_row->owner_id != auth()->user()->entity_id) ||
                                        ($update_row->owner_type == 'program' && empty(

                                            // Check for a program with ID of the Row's owner ID, and the same Entity_ID as the user.
                                            //
                                            Program::where('id', $update_row->owner_id)->where('entity_id', auth()->user()->entity_id)->first()
                                        ))
                                    )
                                ) {
                                $ie[$r][$col] = $ic[$r][$col];
                                $ie[$r][$col]['message'] = 'Sorry, but you dont have permission to update this row.';
                            }
                        }
                    }
                }
            }


            // If no more errors - Process the results.
            //
            if (count($ie) == 0 || $request->input('skip_errors') == 1) {
                $count_inserted = 0;
                $count_updated  = 0;
                $import         = null;

                DB::transaction(function () use ($request, $ic, $ie, $table, &$count_inserted, &$count_updated, &$import, $columns, $filename) {
                    $entityForImport = DB::table('programs')->select('entity_id')->where('id', $request->input('program_id'))->first();

                    $import = Import::create([
                            'original_file' => $filename,
                            'user_id' => auth()->user()->id,
                            'program_id' => $request->input('program_id'),
                            'account_id' => $request->input('account_id'),
                            'entity_id' => $entityForImport->entity_id,

                            ]);
                        
                        
                    //dd($ic);
                    foreach ($ic as $r => $row) {
                        $id          = null;
                        $fields      = [];
                        $row_updated = 0;
                        // IGNORE EMPTY ROWS

                        if (isset($ie[$r]) && $request->input('skip_errors') == 1) {
                                // skip error row.
                        } else {
                                // AUTOFILL COLUMNS
                                
                            foreach ($columns as $column) {
                                if ($column['name'] == "account_id") {
                                    $fields["account_id"] = $request->input('account_id');
                                }
                                if ($column['name'] == "program_id") {
                                    $fields["program_id"] = $request->input('program_id');
                                }
                                if ($column['name'] == "entity_id") {
                                    $entityId = DB::table('entities')->join('programs', 'programs.owner_id', 'entities.id')->where('programs.id', $request->input('program_id'))->select('entities.id as entity_id')->first();
                                    $fields["entity_id"] = $entityId->entity_id;
                                }
                                if ($column['name'] == "user_id") {
                                    $fields["user_id"] = auth()->user()->id;
                                }
                                if ($column['name'] == "state_id") {
                                    $entity_for_state_id = Entity::find(auth()->user()->entity_id);
                                    if ($entity_for_state_id) {
                                        $fields["state_id"] = $entity_for_state_id->state_id;
                                    }
                                }
                                if ($column['name'] == "county_id") {
                                    $program_for_county_id = Program::find($request->input('program_id'));
                                    if ($program_for_county_id) {
                                        $fields["county_id"] = $program_for_county_id->county_id;
                                    }
                                }
                            }


                            foreach ($row as $col => $cell) {
                                if ($cell['db_col'] == "id") {
                                    if ($cell['insert_val'] != '' && $cell['insert_val'] != null) {
                                        $id = $cell['insert_val'];
                                    }
                                } else {
                                    $fields[$cell['db_col']] = $cell['insert_val'];
                                }
                            }
                                
                            //dd($fields, $request->input('account_id'), $request->input('program_id'),$request);
                            if ($id) {
                                DB::table($table)->where('id', $id)->update($fields);
                                $p = Parcel::find($id);
                                $properties=[];
                                $properties['fields'] = $fields;
                                addParcelWithArray(Auth::user(), $p, 'parcel', 'corrections', $fields, 'Corrections import');

                                $row_updated = 1;
                                $count_updated++;
                            } else {
                                // add in owner id.
                                $fields['owner_id'] = $entityId->entity_id;
                                $fields['created_at'] = date('Y-m-d H:i:s', time());
                                $id = DB::table($table)->insertGetId($fields);
                                $count_inserted++;
                            }


                            $import_row = ImportRow::create([
                                    'import_user_id' => Auth::user()->id,
                                    'import_id'   => $import->id,
                                    'table_name'  => $table,
                                    'row_id'      => $id,
                                    'row_updated' => $row_updated
                                ]);
                        }
                    }
                });


                // set session variables and redirect

                if (auth()->user()->entity_id == 1) {
                    session(['parcels_status_filter' => 39]);
                } else {
                    session(['parcels_status_filter' => 43]);
                }
                    

                return redirect('/validate_parcels?showHowTo=1');




                //return view('pages.import.results', compact('import', 'count_inserted', 'count_updated', 'ie'));
            }



            // retain the program and account ids:
            $program_id = $request->input('program_id');
            $account_id = $request->input('account_id');

            return view('pages.import.corrections', compact('table', 'filename', 'columns', 'mappings', 'corrections', 'ic', 'ie', 'program_id', 'account_id'));
        } else {
            return "Sorry you do not have access to this page.";
        }
    }
    public function ResolveValidation(Parcel $parcel, Request $request)
    {
        if (Gate::allows('view-all-parcels')) {
            if (Auth::user()->entity_id != $parcel->entity_id && Auth::user()->entity_type != "hfa") {
                return "Sorry. It doesn't appear you're a part of the organization that owns this parcel. If you think this is a mistake - please note the parcel's system ID:".$parcel->id.", and let IT know you were trying to update its address.";
            } else {
                // Get Resolutions for this Parcel
                //$resolutions = DB::table('validation_resolutions')->select('*')->where('parcel_id',$parcel->id)->orderBy('lb_resolved','asc')->orderBy('resolution_type','asc')->get()->all();
                $resolutions = ValidationResolutions::where('parcel_id', $parcel->id)
                                        ->with('parcel')
                                        ->orderBy('lb_resolved', 'asc')
                                        ->orderBy('resolution_type', 'asc')
                                        ->get();
                $resolutionOutput = [];
                $sharedParcelIds = "";
                        
                if (count($resolutions) > 0) {
                    $totalResolutions = count($resolutions);
                } else {
                    $totalResolutions = 0;
                }
                foreach ($resolutions as $d) {
                    // make sure parcels exists and if not, delete resolution
                    if ($d->resolution_type == "parcels") {
                        $matchingParcelInfo = Parcel::where('id', $d->resolution_id)->first();
                    } elseif ($d->resolution_type == "sdo_parcels") {
                        $matchingParcelInfo = SfParcel::where('id', $d->resolution_id)->first();
                    }
                    if (!isset($matchingParcelInfo->id)) {
                        $d->delete();
                    } else {
                        //Get the matching parcel info
                        //$matchingParcelInfo = DB::table($d->resolution_type)->select("*")->where('id',$d->resolution_id)->first();
                        $sharedParcels = [];
                        $sharedParcelId = [];
                        if ($d->resolution_type == "parcels") {
                            /// check the confilicting parcel is in the shared_parcel_id table
                            $sharedParcelId = DB::table('shared_parcel_to_parcels')->select('shared_parcel_id')->where('parcel_id', $matchingParcelInfo->id)->first();
                            if (count($sharedParcelId)>0) {
                                // there are some shared parcels existing
                                $sharedParcels = DB::table('parcels')
                                                        ->join('shared_parcel_to_parcels', 'parcels.id', '=', 'shared_parcel_to_parcels.parcel_id')
                                                        ->select('parcels.*', 'shared_parcel_to_parcels.reference_letter')
                                                        ->where('shared_parcel_id', $sharedParcelId->shared_parcel_id)
                                                        ->get()
                                                        ->all();
                            }
                        }


                        //Create the output array
                        $resolutionOutput[] = [[
                                'resolution_id'=> $d->id,
                                'resolution_lb_notes'=> $d->resolution_lb_notes,
                                'resolution_type'=> $d->resolution_type,
                                'matching_parcel_info'=>$matchingParcelInfo,
                                'shared_parcel_id'=>$sharedParcelId,
                                'shared_parcels'=>$sharedParcels,
                                'resolution_system_notes'=>$d->resolution_system_notes,
                                'resolution_hfa_notes'=>$d->resolution_hfa_notes,
                                'lb_resolved_at'=>$d->lb_resolved_at,
                                'lb_resolved'=>$d->lb_resolved,
                                'hfa_resolved_at'=>$d->hfa_resolved_at,
                                'hfa_resolved'=>$d->hfa_resolved,
                                'requires_hfa_resolution'=>$d->requires_hfa_resolution
                                // 'resolution_id'=> 1,
                                // 'resolution_lb_notes'=> 2,
                                // 'resolution_type'=> 3,
                                // 'matching_parcel_info'=>4,
                                // 'shared_parcel_ids'=>5,
                                // 'resolution_system_notes'=>6,
                                // 'resolution_hfa_notes'=>7,
                                // 'lb_resolved_at'=>8,
                                // 'lb_resolved'=>9,
                                // 'hfa_resolved_at'=>10,
                                // 'hfa_resolved'=>11,
                                // 'requires_hfa_resolution'=>13

                                ]];
                    }
                }
                $lat = floatval($request->get('lat'));
                $lon = floatval($request->get('lon'));
                $states = DB::table('states')->get()->all();

                return view('pages.import.resolve_validation', compact('lat', 'lon', 'parcel', 'states', 'resolutionOutput', 'sharedParcels', 'totalResolutions'));
            }
        } else {
            return "Sorry you do not have access to this page.";
        }
    }
}
