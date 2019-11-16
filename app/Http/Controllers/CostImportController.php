<?php

namespace App\Http\Controllers;

use Excel;
use App\Models\Import;
use App\Models\ImportRow;
// use App\LogConverter;
use App\Models\Program;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CostImportController extends Controller
{
    public function __construct(Request $request)
    {
        // $this->middleware('auth');
    }

    // Required fields for form
    private $validation = [
        'table' => 'required',
        'data' => 'required'
    ];

    // Don't let anyone upload things into these columns on any table.
    private $ignore_columns = [
        'created_at',
        'updated_at',
        'owner_id',
        'entity_id',
        'owner_type'
    ];

    /**
     * Is a string variable a boolean
     *
     * @param $string
     *
     * @return bool
     */
    public function checkBool($string)
    {
        return (in_array(strtolower($string), ["true", "false", "1", "0", "yes", "no"], true));
    }

    /**
     * Get Table Columns
     *
     * Get the table column names and Types for any table.
     * returns empty array if no table found
     *
     * @todo Refactor this out of the controller
     *
     * @param $table
     *
     * @return array
     */
    protected function getTableColumns($table)
    {
        $columns = [];
        $keys = [];
        $sm      = DB::connection()->getDoctrineSchemaManager();
        $db_cols = $sm->listTableColumns($table);

        // First load foreign keys, read into an array.
        // Each "foreign key" returned is an array - to account for keys that span multiple columns.
        // We only support 1-column foreign keys in this code.
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
                    ];
            }
        }

        return $columns;
    }

    /**
     * Form
     *
     * Display a form with table-name and file upload fields.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function form(Request $request)
    {
        if (Gate::allows('view-all-parcels')) {
            // put in code here to set where to only get ids for their program
            // make it get all ids for OHFA
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
            $programs = DB::table('programs')
                            ->select('id as program_id', 'program_name', 'entity_id')
                            ->where('entity_id', $whereOperator, $where)
                            ->get()
                            ->all();
            $accounts = DB::table('accounts')
                            ->select('id as account_id', 'account_name')
                            ->where('owner_id', $whereOperator, $where)
                            ->get()
                            ->all();
            if ($request->query('showHowTo') == 1) {
                // Give instruction on steps to take to upload files.
                $showHowTo = 11;
            } else {
                $showHowTo = 0;
            }

            return view('pages.import.form', compact(
                'targetAreaCheatSheet',
                'howAcquiredCheatSheet',
                'parcelTypeCheatSheet',
                'programs',
                'accounts',
                'showHowTo'
            ));
        } else {
            return "Sorry you do not have access to this page.";
        }
    }

    /**
     * Mappings
     *
     * Process initial file upload, pull table columns, show mappings form
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function mappings(Request $request)
    {
        // Validate the required fields.
        $this->validate($request, $this->validation);

        // Check that table exists and load columns
        $table   = $request->input('table');
        $columns = $this->getTableColumns($table);
        if (!$columns) {
            return view('pages.import.form')->withErrors(['message' => 'No table found with that name']);
        }

        // Upload the file.
        $file = $request->file('data');
        $ext  = $file->getClientOriginalExtension();
        $filename = 'imports/' . time() . '.' . $ext;
        Storage::put($filename, File::get($file));
        $excel = Excel::load('storage/app/' . $filename)->get();
        if (is_a($excel, 'Maatwebsite\Excel\Collections\SheetCollection')) {
            $excel = $excel->first();
        }

        return view('pages.import.mappings', compact('table', 'columns', 'filename', 'excel'));
    }

    /**
     * Corrections
     *
     * Process the uploaded file, load table columns
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function corrections(Request $request)
    {
        if (Gate::allows('view-all-parcels')) {
            // Check that table exists and load columns
            $table   = $request->input('table');
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

            // Iterate over rows and columns, make a record of input and expected input.
            // Save errors into 2D array by row and column
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
                            ];

                        // If the table column is a foreign key.
                        if ($ic[$r][$col]['db_references'] && $value != '' && $value != null) {
                            // First, check if an ID column exists.
                            $foreign_row = DB::table($ic[$r][$col]['db_on'])->where($ic[$r][$col]['db_references'], $ic[$r][$col]['excel_val'])->first();
                            if (empty($foreign_row)) {
                                // If not - then check for the "_name" version.
                                $foreign_row = DB::table($ic[$r][$col]['db_on'])->where(str_singular($ic[$r][$col]['db_on']) . "_name", $ic[$r][$col]['excel_val'])->first();
                                if (empty($foreign_row)) {
                                    $ie[$r][$col] = $ic[$r][$col];
                                    $ie[$r][$col]['message'] = 'Foreign Key error: No row found on table ' . $ic[$r][$col]['db_on'] . ' matching ' . $ic[$r][$col]['excel_val'];
                                } else {
                                    $ic[$r][$col]['insert_val'] = $foreign_row->id;
                                }
                            }
                        } // If expecting Integer, and recieve String.
                        elseif ($ic[$r][$col]['db_type'] == 'Integer' && $value != '' && !(is_null($value) || $ic[$r][$col]['excel_int'])) {
                            $ie[$r][$col] = $ic[$r][$col];
                            $ie[$r][$col]['message'] = 'Data Type Error: Expecting an Integer and recieved a ' . (is_numeric($value) ? 'Non-integer Number' : 'String');
                        } // If expecting Boolean, and recieve String.
                        elseif ($ic[$r][$col]['db_type'] == 'Boolean' && $value != '' && !(is_null($value) || is_bool($value) || $this->checkBool($value))) {
                            $ie[$r][$col] = $ic[$r][$col];
                            $ie[$r][$col]['message'] = 'Data Type Error: Expecting a Boolean and recieved a ' . (is_numeric($value) ? 'Non-boolean Number' : 'String');
                        } // If the table column is "id"
                        elseif ($ic[$r][$col]['db_col'] == 'id' && $value != '' && $value != null) {
                            $update_row = DB::table($table)->find($value);
                            if (empty($update_row)) {
                                $ie[$r][$col] = $ic[$r][$col];
                                $ie[$r][$col]['message'] = 'Update by ID Error: No row found with ID ' . $value;
                            } elseif (property_exists($update_row, "owner_type") && (
                                        ($update_row->owner_type == 'user' && $update_row->owner_id != auth()->user()->id) ||
                                        ($update_row->owner_type == 'entity' && $update_row->owner_id != auth()->user()->entity_id) ||
                                        ($update_row->owner_type == 'program' && empty(
                                            // Check for a program with ID of the Row's owner ID, and the same Entity_ID as the user.
                                            Program::where('id', $update_row->owner_id)->where('entity_id', auth()->user()->entity_id)->first()
                                        ))
                                    )
                                ) {
                                $ie[$r][$col] = $ic[$r][$col];
                                $ie[$r][$col]['message'] = 'Update Permission Error: You dont have permission to update that row.';
                            }
                        }
                    }
                }
            }

            // If no more errors - Process the results.
            if (count($ie) == 0 || $request->input('skip_errors') == 1) {
                $count_inserted = 0;
                $count_updated  = 0;
                $import         = null;

                DB::transaction(function () use ($request, $ic, $ie, $table, &$count_inserted, &$count_updated, &$import) {
                    $import = Import::create(['user_id' => auth()->user()->id]);
                    // $lc = new LogConverter('import', 'create');
                    // $lc->setFrom(Auth::user())->setTo($import)->setDesc(Auth::user()->email . ' created import')->save();
                    foreach ($ic as $r => $row) {
                        $id          = null;
                        $fields      = [];
                        $row_updated = 0;

                        if (isset($ie[$r]) && $request->input('skip_errors') == 1) {
                            // skip error row.
                        } else {
                            foreach ($row as $col => $cell) {
                                if ($cell['db_col'] == "id") {
                                    if ($cell['insert_val'] != '' && $cell['insert_val'] != null) {
                                        $id = $cell['insert_val'];
                                    }
                                } else {
                                    $fields[$cell['db_col']] = $cell['insert_val'];
                                }
                            }

                            if ($id) {
                                DB::table($table)->where('id', $id)->update($fields);
                                $row_updated = 1;
                                $count_updated++;
                            } else {
                                $id = DB::table($table)->insertGetId($fields);
                                $count_inserted++;
                            }

                            $import_row = ImportRow::create([
                                    'import_id'   => $import->id,
                                    'table_name'  => $table,
                                    'row_id'      => $id,
                                    'row_updated' => $row_updated
                                ]);
                        }
                    }
                });

                return view('pages.import.results', compact('import', 'count_inserted', 'count_updated', 'ie'));
            }

            return view('pages.import.corrections', compact('table', 'filename', 'columns', 'mappings', 'corrections', 'ic', 'ie'));
        } else {
            return "Sorry you do not have access to this page.";
        }
    }
}
