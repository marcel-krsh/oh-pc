<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Gate;
use Auth;
use DB;
use App\Models\Parcel;
use App\Models\ExpenseCategory;
use App\Models\Program;
use App\Models\Vendor;
use App\Models\CostItem;
use App\Models\RequestItem;
use App\Models\PoItems;
use App\Models\InvoiceItem;

ini_set('max_execution_time', 600);
class ExpenseCategoriesController extends Controller
{
    public function __construct(Request $request)
    {
        // $this->middleware('auth');
    }

    /**
     * Show the documents' list for a specific parcel.
     *
     * @param int                  $output
     * @param \App\Models\ExpenseCategory $category
     * @param \App\Models\Program         $program
     * @param \App\Models\Parcel|null     $parcel
     * @param int                  $zero_values
     *
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showDetails($output = 0, ExpenseCategory $category, Program $program, Parcel $parcel = null, $zero_values = 0)
    {
        //get all categories
        $expense_categories = ExpenseCategory::where('id', '!=', 1)->get();

        // which items do we use to compute averages?
        $source = "Cost items";

        if (count($parcel->associatedInvoice) == 1) {
            $source = "Invoice items";

            // get the invoiced total, count and average for program and category
            $query_totals = DB::table('invoice_items')
                                        ->select(DB::raw('SUM(amount) AS total, 
                                                        AVG( amount ) AS average'))
                                        ->where('expense_category_id', '=', $category->id)
                                        ->where('program_id', '=', $program->id);
            if ($zero_values!=1) {
                $query_totals = $query_totals->where('amount', '!=', 0);
            }
            $amount_for_program = $query_totals->first();
                                        
            // Invoiced for Parcel
            $query_parcel = DB::table('invoice_items')
                                        ->where('expense_category_id', '=', $category->id)
                                        ->where('parcel_id', '=', $parcel->id);
            if ($zero_values!=1) {
                $query_parcel = $query_parcel->where('amount', '!=', 0);
            }
            $amount_for_parcel_query = $query_parcel->sum('amount');

            // invoiced_for all programs all entities
            $query_all_program = DB::table('invoice_items')
                                        ->select(DB::raw('SUM(amount) AS total, 
                                                        AVG( amount ) AS average'))
                                        ->where('expense_category_id', '=', $category->id)
                                        ->join('programs', function ($join) {
                                            $join->on('invoice_items.program_id', '=', 'programs.id')
                                                ->where('programs.active', '=', 1);
                                        });
            if ($zero_values!=1) {
                $query_all_program = $query_all_program->where('amount', '!=', 0);
            }
            $amount_for_entity = $query_all_program->first();

            // get median for this program
            $query_median_program = DB::table('invoice_items')
                                        ->select('amount')
                                        ->where('program_id', '=', $program->id)
                                        ->where('expense_category_id', '=', $category->id)
                                        ->orderBy('amount', 'ASC');
            if ($zero_values!=1) {
                $query_median_program = $query_median_program->where('amount', '!=', 0);
            }
            $amount_for_program_array = $query_median_program->get();

            // get median over all for all programs within an entity
            $query_median_entity = DB::table('invoice_items')
                                        ->select('amount')
                                        ->join('programs', function ($join) {
                                            $join->on('invoice_items.program_id', '=', 'programs.id')
                                                ->where('programs.active', '=', 1);
                                        })
                                        ->where('expense_category_id', '=', $category->id)
                                        ->orderBy('amount', 'ASC');
            if ($zero_values!=1) {
                $query_median_entity = $query_median_entity->where('amount', '!=', 0);
            }
            $amount_for_entity_array = $query_median_entity->get();
        } elseif (count($parcel->associatedPo) == 1) {
            $source = "PO items";

            // get the invoiced total, count and average for program and category
            $query_totals = DB::table('po_items')
                                        ->select(DB::raw('SUM(amount) AS total, 
                                                        AVG( amount ) AS average'))
                                        ->where('expense_category_id', '=', $category->id)
                                        ->where('program_id', '=', $program->id);
            if ($zero_values!=1) {
                $query_totals = $query_totals->where('amount', '!=', 0);
            }
            $amount_for_program = $query_totals->first();
                                        
            // Invoiced for Parcel
            $query_parcel = DB::table('po_items')
                                        ->where('expense_category_id', '=', $category->id)
                                        ->where('parcel_id', '=', $parcel->id);
            if ($zero_values!=1) {
                $query_parcel = $query_parcel->where('amount', '!=', 0);
            }
            $amount_for_parcel_query = $query_parcel->sum('amount');

            // invoiced_for all programs all entities
            $query_all_program = DB::table('po_items')
                                        ->select(DB::raw('SUM(amount) AS total, 
                                                        AVG( amount ) AS average'))
                                        ->where('expense_category_id', '=', $category->id)
                                        ->join('programs', function ($join) {
                                            $join->on('po_items.program_id', '=', 'programs.id')
                                                ->where('programs.active', '=', 1);
                                        });
            if ($zero_values!=1) {
                $query_all_program = $query_all_program->where('amount', '!=', 0);
            }
            $amount_for_entity = $query_all_program->first();

            // get median for this program
            $query_median_program = DB::table('po_items')

                                        ->select('amount')
                                        ->where('program_id', '=', $program->id)
                                        ->where('expense_category_id', '=', $category->id)
                                        ->orderBy('amount', 'ASC');
            if ($zero_values!=1) {
                $query_median_program = $query_median_program->where('amount', '!=', 0);
            }
            $amount_for_program_array = $query_median_program->get();

            // get median over all for all programs within an entity
            $query_median_entity = DB::table('po_items')
                                        ->select('amount')
                                        ->join('programs', function ($join) {
                                            $join->on('po_items.program_id', '=', 'programs.id')
                                                ->where('programs.active', '=', 1);
                                        })
                                        ->where('expense_category_id', '=', $category->id)
                                        ->orderBy('amount', 'ASC');
            if ($zero_values!=1) {
                $query_median_entity = $query_median_entity->where('amount', '!=', 0);
            }
            $amount_for_entity_array = $query_median_entity->get();
        } elseif (count($parcel->associatedRequest) == 1) {
            $source = "Request items";

            // get the invoiced total, count and average for program and category
            $query_totals = DB::table('request_items')
                                        ->select(DB::raw('SUM(amount) AS total, 
                                                        AVG( amount ) AS average'))
                                        ->where('expense_category_id', '=', $category->id)
                                        ->where('program_id', '=', $program->id);
            if ($zero_values!=1) {
                $query_totals->where('amount', '!=', 0);
            }
            $amount_for_program = $query_totals->first();
                                        
            // Invoiced for Parcel
            $query_parcel = DB::table('request_items')
                                        ->where('expense_category_id', '=', $category->id)
                                        ->where('parcel_id', '=', $parcel->id);
            if ($zero_values!=1) {
                $query_parcel->where('amount', '!=', 0);
            }
            $amount_for_parcel_query = $query_parcel->sum('amount');

            // invoiced_for all programs all entities
            $query_all_program = DB::table('request_items')
                                        ->select(DB::raw('SUM(amount) AS total, 
                                                        AVG( amount ) AS average'))
                                        ->where('expense_category_id', '=', $category->id)
                                        ->join('programs', function ($join) {
                                            $join->on('request_items.program_id', '=', 'programs.id')
                                                ->where('programs.active', '=', 1);
                                        });
            if ($zero_values!=1) {
                $query_all_program->where('amount', '!=', 0);
            }
            $amount_for_entity = $query_all_program->first();

            // get median for this program
            $query_median_program = DB::table('request_items')
                                        ->select('amount')
                                        ->where('program_id', '=', $program->id)
                                        ->where('expense_category_id', '=', $category->id)
                                        ->orderBy('amount', 'ASC');
            if ($zero_values!=1) {
                $query_median_program->where('amount', '!=', 0);
            }
            $amount_for_program_array = $query_median_program->get();

            // get median over all for all programs within an entity
            $query_median_entity = DB::table('request_items')
                                        ->select('amount')
                                        ->join('programs', function ($join) {
                                            $join->on('request_items.program_id', '=', 'programs.id')
                                                ->where('programs.active', '=', 1);
                                        })
                                        ->where('expense_category_id', '=', $category->id)
                                        ->orderBy('amount', 'ASC');
            if ($zero_values!=1) {
                $query_median_entity->where('amount', '!=', 0);
            }
            $amount_for_entity_array = $query_median_entity->get();
        } else {
            $source = "Cost items";

            // get the invoiced total, count and average for program and category
            $query_totals = DB::table('cost_items')
                                        ->select(DB::raw('SUM(amount) AS total, 
                                                        AVG( amount ) AS average'))
                                        ->where('expense_category_id', '=', $category->id)
                                        ->where('program_id', '=', $program->id);
            if ($zero_values!=1) {
                $query_totals->where('amount', '!=', 0);
            }
            $amount_for_program = $query_totals->first();
                                        
            // Invoiced for Parcel
            $query_parcel = DB::table('cost_items')
                                        ->where('expense_category_id', '=', $category->id)
                                        ->where('parcel_id', '=', $parcel->id);
            if ($zero_values!=1) {
                $query_parcel = $query_parcel->where('amount', '!=', 0);
            }
            $amount_for_parcel_query = $query_parcel->sum('amount');

            // invoiced_for all programs all entities
            $query_all_program = DB::table('cost_items')
                                        ->select(DB::raw('SUM(amount) AS total, 
                                                        AVG( amount ) AS average'))
                                        ->where('expense_category_id', '=', $category->id)
                                        ->join('programs', function ($join) {
                                            $join->on('cost_items.program_id', '=', 'programs.id')
                                                ->where('programs.active', '=', 1);
                                        });
            if ($zero_values!=1) {
                $query_all_program = $query_all_program->where('amount', '!=', 0);
            }
            $amount_for_entity = $query_all_program->first();

            // get median for this program
            $query_median_program = DB::table('cost_items')
                                        ->select('amount')
                                        ->where('program_id', '=', $program->id)
                                        ->where('expense_category_id', '=', $category->id)
                                        ->orderBy('amount', 'ASC');
            if ($zero_values!=1) {
                $query_median_program = $query_median_program->where('amount', '!=', 0);
            }
            $amount_for_program_array = $query_median_program->get();

            // get median over all for all programs within an entity
            $query_median_entity = DB::table('cost_items')
                                        ->select('amount')
                                        ->join('programs', function ($join) {
                                            $join->on('cost_items.program_id', '=', 'programs.id')
                                                ->where('programs.active', '=', 1);
                                        })
                                        ->where('expense_category_id', '=', $category->id)
                                        ->orderBy('amount', 'ASC');
            if ($zero_values!=1) {
                $query_median_entity = $query_median_entity->where('amount', '!=', 0);
            }
            $amount_for_entity_array = $query_median_entity->get();
        }


        if (!$amount_for_parcel_query) {
            $amount_for_parcel = 0;
        } else {
            $amount_for_parcel = $amount_for_parcel_query;
        }
                                         
        $count_amount_for_program_array = count($amount_for_program_array);
        if ($count_amount_for_program_array) {
            $middleval = floor(($count_amount_for_program_array-1)/2);
            if ($count_amount_for_program_array % 2) {
                $amount_for_program_median = $amount_for_program_array[$middleval]->amount;
            } else {
                $low = $amount_for_program_array[$middleval]->amount;
                $high = $amount_for_program_array[$middleval+1]->amount;
                $amount_for_program_median = (($low+$high)/2);
            }
        } else {
            $amount_for_program_median = 0;
        }
                         
        $count_amount_for_entity_array = count($amount_for_entity_array);
        if ($count_amount_for_entity_array) {
            $middleval = floor(($count_amount_for_entity_array-1)/2);
            if ($count_amount_for_entity_array % 2) {
                $amount_for_entity_median = $amount_for_entity_array[$middleval]->amount;
            } else {
                $low = $amount_for_entity_array[$middleval]->amount;
                $high = $amount_for_entity_array[$middleval+1]->amount;
                $amount_for_entity_median = (($low+$high)/2);
            }
        } else {
            $amount_for_entity_median = 0;
        }

        // money formatting
        setlocale(LC_MONETARY, 'en_US');
        $amount_for_entity_median = money_format('%-8n', $amount_for_entity_median);
        $amount_for_program_median = money_format('%-8n', $amount_for_program_median);
        $amount_for_parcel = money_format('%-8n', $amount_for_parcel);
        $amount_for_program_average = money_format('%-8n', $amount_for_program->average);
        $amount_for_entity_average = money_format('%-8n', $amount_for_entity->average);

        $programid = $program->id;
        if ($parcel->id) {
            $parcelid = $parcel->id;
        } else {
            $parcelid = 0;
        }
        
        $categoryid = $category->id;
        $categoryname = $category->expense_category_name;

        if ($output==1) {
            return compact('source', 'parcelid', 'programid', 'categoryid', 'categoryname', 'expense_categories', 'amount_for_parcel', 'amount_for_program_average', 'amount_for_entity_average', 'amount_for_program_median', 'amount_for_entity_median');
        } else {
            return view('modals.expense-categories-details', compact('source', 'parcelid', 'programid', 'categoryid', 'categoryname', 'expense_categories', 'amount_for_program_average', 'amount_for_parcel', 'amount_for_entity_average', 'amount_for_program_median', 'amount_for_entity_median'));
        }
    }

    /**
     * Show Vendor Expenses
     *
     * @param      $vendor_id
     * @param null $parcel_id
     * @param null $program_id
     * @param int  $zero_values
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showVendorExpenses($vendor_id, $parcel_id = null, $program_id = null, $zero_values = 0)
    {
        $include_legacy_vendor = session('include_legacy_vendors');

        // get vendor with states
        $vendor = Vendor::where('id', $vendor_id)->with('state')->first();

        //get all categories
        $expense_categories = ExpenseCategory::where('id', '!=', 1)->get();

        $in_parcel = 0;
        $in_program = 0;

        if ($include_legacy_vendor) {
            $cost_items = CostItem::where('vendor_id', '=', $vendor_id);
            $request_items = RequestItem::where('vendor_id', '=', $vendor_id);
            $po_items = PoItems::where('vendor_id', '=', $vendor_id);
            $invoice_items = InvoiceItem::where('vendor_id', '=', $vendor_id);
        } else {
            $cost_items = CostItem::where('vendor_id', '!=', 1)->where('vendor_id', '=', $vendor_id);
            $request_items = RequestItem::where('vendor_id', '!=', 1)->where('vendor_id', '=', $vendor_id);
            $po_items = PoItems::where('vendor_id', '!=', 1)->where('vendor_id', '=', $vendor_id);
            $invoice_items = InvoiceItem::where('vendor_id', '!=', 1)->where('vendor_id', '=', $vendor_id);
        }

        $data = [];

        if ($parcel_id) {
            $parcel = Parcel::where('id', '=', $parcel_id)->first(['id']);
        } else {
            $parcel = null;
        }
        if ($program_id) {
            $program = Program::where('id', '=', $program_id)->first(['id']);
        } else {
            $program = null;
        }

        // category colors & category names
        $cat_colors = '';
        $cat_names = '';

        foreach ($expense_categories as $cat) {
            $cat_colors = $cat_colors.'"'.$cat->color_hex.'",';
            $cat_names = $cat_names.'"'.$cat->expense_category_name.'",';

            if ($parcel) {
                $in_parcel =1;
                    
                if ($include_legacy_vendor) {
                    $data['parcel']['cost'][$cat->id]['total'] = CostItem::where('vendor_id', '=', $vendor_id)->where('parcel_id', '=', $parcel->id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['parcel']['cost'][$cat->id]['grand_total'] = CostItem::where('expense_category_id', '=', $cat->id)->where('parcel_id', '=', $parcel->id)->sum('amount');
                    $data['parcel']['cost'][$cat->id]['grand_total_all_categories'] = CostItem::where('parcel_id', '=', $parcel->id)->sum('amount');
                } else {
                    $data['parcel']['cost'][$cat->id]['total'] = CostItem::where('vendor_id', '=', $vendor_id)->where('parcel_id', '=', $parcel->id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['parcel']['cost'][$cat->id]['grand_total'] = CostItem::where('vendor_id', '!=', 1)->where('expense_category_id', '=', $cat->id)->where('parcel_id', '=', $parcel->id)->sum('amount');
                    $data['parcel']['cost'][$cat->id]['grand_total_all_categories'] = CostItem::where('vendor_id', '!=', 1)->where('parcel_id', '=', $parcel->id)->sum('amount');
                }
                if ($data['parcel']['cost'][$cat->id]['grand_total_all_categories']) {
                    $data['parcel']['cost'][$cat->id]['percentage'] = $data['parcel']['cost'][$cat->id]['total'] * 100 / $data['parcel']['cost'][$cat->id]['grand_total_all_categories'];
                } else {
                    $data['parcel']['cost'][$cat->id]['percentage'] = null;
                }
                
                if ($include_legacy_vendor) {
                    $data['parcel']['request'][$cat->id]['total'] = RequestItem::where('vendor_id', '=', $vendor_id)->where('parcel_id', '=', $parcel->id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['parcel']['request'][$cat->id]['grand_total'] = RequestItem::where('expense_category_id', '=', $cat->id)->where('parcel_id', '=', $parcel->id)->sum('amount');
                    $data['parcel']['request'][$cat->id]['grand_total_all_categories'] = RequestItem::where('parcel_id', '=', $parcel->id)->sum('amount');
                } else {
                    $data['parcel']['request'][$cat->id]['total'] = RequestItem::where('vendor_id', '!=', 1)->where('vendor_id', '=', $vendor_id)->where('parcel_id', '=', $parcel->id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['parcel']['request'][$cat->id]['grand_total'] = RequestItem::where('vendor_id', '!=', 1)->where('expense_category_id', '=', $cat->id)->where('parcel_id', '=', $parcel->id)->sum('amount');
                    $data['parcel']['request'][$cat->id]['grand_total_all_categories'] = RequestItem::where('vendor_id', '!=', 1)->where('parcel_id', '=', $parcel->id)->sum('amount');
                }
                if ($data['parcel']['request'][$cat->id]['grand_total_all_categories']) {
                    $data['parcel']['request'][$cat->id]['percentage'] = $data['parcel']['request'][$cat->id]['total'] * 100 / $data['parcel']['request'][$cat->id]['grand_total_all_categories'];
                } else {
                    $data['parcel']['request'][$cat->id]['percentage'] = null;
                }

                if ($include_legacy_vendor) {
                    $data['parcel']['po'][$cat->id]['total'] = PoItems::where('vendor_id', '=', $vendor_id)->where('parcel_id', '=', $parcel->id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['parcel']['po'][$cat->id]['grand_total'] = PoItems::where('expense_category_id', '=', $cat->id)->where('parcel_id', '=', $parcel->id)->sum('amount');
                    $data['parcel']['po'][$cat->id]['grand_total_all_categories'] = PoItems::where('parcel_id', '=', $parcel->id)->sum('amount');
                } else {
                    $data['parcel']['po'][$cat->id]['total'] = PoItems::where('vendor_id', '!=', 1)->where('vendor_id', '=', $vendor_id)->where('parcel_id', '=', $parcel->id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['parcel']['po'][$cat->id]['grand_total'] = PoItems::where('vendor_id', '!=', 1)->where('expense_category_id', '=', $cat->id)->where('parcel_id', '=', $parcel->id)->sum('amount');
                    $data['parcel']['po'][$cat->id]['grand_total_all_categories'] = PoItems::where('vendor_id', '!=', 1)->where('parcel_id', '=', $parcel->id)->sum('amount');
                }
                if ($data['parcel']['po'][$cat->id]['grand_total_all_categories']) {
                    $data['parcel']['po'][$cat->id]['percentage'] = $data['parcel']['po'][$cat->id]['total'] * 100 / $data['parcel']['po'][$cat->id]['grand_total_all_categories'];
                } else {
                    $data['parcel']['po'][$cat->id]['percentage'] = null;
                }

                if ($include_legacy_vendor) {
                    $data['parcel']['invoice'][$cat->id]['total'] = InvoiceItem::where('vendor_id', '=', $vendor_id)->where('parcel_id', '=', $parcel->id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['parcel']['invoice'][$cat->id]['grand_total'] = InvoiceItem::where('expense_category_id', '=', $cat->id)->where('parcel_id', '=', $parcel->id)->sum('amount');
                    $data['parcel']['invoice'][$cat->id]['grand_total_all_categories'] = InvoiceItem::where('parcel_id', '=', $parcel->id)->sum('amount');
                } else {
                    $data['parcel']['invoice'][$cat->id]['total'] = InvoiceItem::where('vendor_id', '!=', 1)->where('vendor_id', '=', $vendor_id)->where('parcel_id', '=', $parcel->id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['parcel']['invoice'][$cat->id]['grand_total'] = InvoiceItem::where('vendor_id', '!=', 1)->where('expense_category_id', '=', $cat->id)->where('parcel_id', '=', $parcel->id)->sum('amount');
                    $data['parcel']['invoice'][$cat->id]['grand_total_all_categories'] = InvoiceItem::where('vendor_id', '!=', 1)->where('parcel_id', '=', $parcel->id)->sum('amount');
                }
                if ($data['parcel']['invoice'][$cat->id]['grand_total_all_categories']) {
                    $data['parcel']['invoice'][$cat->id]['percentage'] = $data['parcel']['invoice'][$cat->id]['total'] * 100 / $data['parcel']['invoice'][$cat->id]['grand_total_all_categories'];
                } else {
                    $data['parcel']['invoice'][$cat->id]['percentage'] = null;
                }
            }

            if ($program) {
                $in_program = 1;
                if ($include_legacy_vendor) {
                    $data['program']['cost'][$cat->id]['total'] = CostItem::where('vendor_id', '=', $vendor_id)->where('program_id', '=', $program_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['program']['cost'][$cat->id]['grand_total'] = CostItem::where('program_id', '=', $program_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['program']['cost'][$cat->id]['grand_total_all_categories'] = CostItem::where('program_id', '=', $program_id)->sum('amount');
                } else {
                    $data['program']['cost'][$cat->id]['total'] = CostItem::where('vendor_id', '!=', 1)->where('vendor_id', '=', $vendor_id)->where('program_id', '=', $program_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['program']['cost'][$cat->id]['grand_total'] = CostItem::where('vendor_id', '!=', 1)->where('program_id', '=', $program_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['program']['cost'][$cat->id]['grand_total_all_categories'] = CostItem::where('vendor_id', '!=', 1)->where('program_id', '=', $program_id)->sum('amount');
                }
                if ($data['program']['cost'][$cat->id]['grand_total_all_categories']) {
                    $data['program']['cost'][$cat->id]['percentage'] = $data['program']['cost'][$cat->id]['total'] * 100 / $data['program']['cost'][$cat->id]['grand_total_all_categories'];
                } else {
                    $data['program']['cost'][$cat->id]['percentage'] = null;
                }

                if ($include_legacy_vendor) {
                    $data['program']['request'][$cat->id]['total'] = RequestItem::where('vendor_id', '=', $vendor_id)->where('program_id', '=', $program_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['program']['request'][$cat->id]['grand_total'] = RequestItem::where('program_id', '=', $program_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['program']['request'][$cat->id]['grand_total_all_categories'] = RequestItem::where('program_id', '=', $program_id)->sum('amount');
                } else {
                    $data['program']['request'][$cat->id]['total'] = RequestItem::where('vendor_id', '!=', 1)->where('vendor_id', '=', $vendor_id)->where('program_id', '=', $program_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['program']['request'][$cat->id]['grand_total'] = RequestItem::where('vendor_id', '!=', 1)->where('program_id', '=', $program_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['program']['request'][$cat->id]['grand_total_all_categories'] = RequestItem::where('vendor_id', '!=', 1)->where('program_id', '=', $program_id)->sum('amount');
                }
                if ($data['program']['request'][$cat->id]['grand_total_all_categories']) {
                    $data['program']['request'][$cat->id]['percentage'] = $data['program']['request'][$cat->id]['total'] * 100 / $data['program']['request'][$cat->id]['grand_total_all_categories'];
                } else {
                    $data['program']['request'][$cat->id]['percentage'] = null;
                }

                if ($include_legacy_vendor) {
                    $data['program']['po'][$cat->id]['total'] = PoItems::where('vendor_id', '=', $vendor_id)->where('program_id', '=', $program_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['program']['po'][$cat->id]['grand_total'] = PoItems::where('program_id', '=', $program_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['program']['po'][$cat->id]['grand_total_all_categories'] = PoItems::where('program_id', '=', $program_id)->sum('amount');
                } else {
                    $data['program']['po'][$cat->id]['total'] = PoItems::where('vendor_id', '!=', 1)->where('vendor_id', '=', $vendor_id)->where('program_id', '=', $program_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['program']['po'][$cat->id]['grand_total'] = PoItems::where('vendor_id', '!=', 1)->where('program_id', '=', $program_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['program']['po'][$cat->id]['grand_total_all_categories'] = PoItems::where('vendor_id', '!=', 1)->where('program_id', '=', $program_id)->sum('amount');
                }
                if ($data['program']['po'][$cat->id]['grand_total_all_categories']) {
                    $data['program']['po'][$cat->id]['percentage'] = $data['program']['po'][$cat->id]['total'] * 100 / $data['program']['po'][$cat->id]['grand_total_all_categories'];
                } else {
                    $data['program']['po'][$cat->id]['percentage'] = null;
                }

                if ($include_legacy_vendor) {
                    $data['program']['invoice'][$cat->id]['total'] = InvoiceItem::where('vendor_id', '=', $vendor_id)->where('program_id', '=', $program_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['program']['invoice'][$cat->id]['grand_total'] = InvoiceItem::where('program_id', '=', $program_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['program']['invoice'][$cat->id]['grand_total_all_categories'] = InvoiceItem::where('program_id', '=', $program_id)->sum('amount');
                } else {
                    $data['program']['invoice'][$cat->id]['total'] = InvoiceItem::where('vendor_id', '!=', 1)->where('vendor_id', '=', $vendor_id)->where('program_id', '=', $program_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['program']['invoice'][$cat->id]['grand_total'] = InvoiceItem::where('vendor_id', '!=', 1)->where('program_id', '=', $program_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                    $data['program']['invoice'][$cat->id]['grand_total_all_categories'] = InvoiceItem::where('vendor_id', '!=', 1)->where('program_id', '=', $program_id)->sum('amount');
                }
                if ($data['program']['invoice'][$cat->id]['grand_total_all_categories']) {
                    $data['program']['invoice'][$cat->id]['percentage'] = $data['program']['invoice'][$cat->id]['total'] * 100 / $data['program']['invoice'][$cat->id]['grand_total_all_categories'];
                } else {
                    $data['program']['invoice'][$cat->id]['percentage'] = null;
                }
            }

            if ($include_legacy_vendor) {
                $data['overview']['cost'][$cat->id]['total'] = CostItem::where('vendor_id', '=', $vendor_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                $data['overview']['cost'][$cat->id]['grand_total'] = CostItem::where('expense_category_id', '=', $cat->id)->sum('amount');
                $data['overview']['cost'][$cat->id]['grand_total_all_categories'] = CostItem::sum('amount');
            } else {
                $data['overview']['cost'][$cat->id]['total'] = CostItem::where('vendor_id', '!=', 1)->where('vendor_id', '=', $vendor_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                $data['overview']['cost'][$cat->id]['grand_total'] = CostItem::where('vendor_id', '!=', 1)->where('expense_category_id', '=', $cat->id)->sum('amount');
                $data['overview']['cost'][$cat->id]['grand_total_all_categories'] = CostItem::where('vendor_id', '!=', 1)->sum('amount');
            }
            if ($data['overview']['cost'][$cat->id]['grand_total_all_categories']) {
                $data['overview']['cost'][$cat->id]['percentage'] = $data['overview']['cost'][$cat->id]['total'] * 100 / $data['overview']['cost'][$cat->id]['grand_total_all_categories'];
            } else {
                $data['overview']['cost'][$cat->id]['percentage'] = null;
            }

            if ($include_legacy_vendor) {
                $data['overview']['request'][$cat->id]['total'] = RequestItem::where('vendor_id', '=', $vendor_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                $data['overview']['request'][$cat->id]['grand_total'] = RequestItem::where('expense_category_id', '=', $cat->id)->sum('amount');
                $data['overview']['request'][$cat->id]['grand_total_all_categories'] = RequestItem::sum('amount');
            } else {
                $data['overview']['request'][$cat->id]['total'] = RequestItem::where('vendor_id', '!=', 1)->where('vendor_id', '=', $vendor_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                $data['overview']['request'][$cat->id]['grand_total'] = RequestItem::where('vendor_id', '!=', 1)->where('expense_category_id', '=', $cat->id)->sum('amount');
                $data['overview']['request'][$cat->id]['grand_total_all_categories'] = RequestItem::where('vendor_id', '!=', 1)->sum('amount');
            }
            if ($data['overview']['request'][$cat->id]['grand_total_all_categories']) {
                $data['overview']['request'][$cat->id]['percentage'] = $data['overview']['request'][$cat->id]['total'] * 100 / $data['overview']['request'][$cat->id]['grand_total_all_categories'];
            } else {
                $data['overview']['request'][$cat->id]['percentage'] = null;
            }

            if ($include_legacy_vendor) {
                $data['overview']['po'][$cat->id]['total'] = PoItems::where('vendor_id', '=', $vendor_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                $data['overview']['po'][$cat->id]['grand_total'] = PoItems::where('expense_category_id', '=', $cat->id)->sum('amount');
                $data['overview']['po'][$cat->id]['grand_total_all_categories'] = PoItems::sum('amount');
            } else {
                $data['overview']['po'][$cat->id]['total'] = PoItems::where('vendor_id', '!=', 1)->where('vendor_id', '=', $vendor_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                $data['overview']['po'][$cat->id]['grand_total'] = PoItems::where('vendor_id', '!=', 1)->where('expense_category_id', '=', $cat->id)->sum('amount');
                $data['overview']['po'][$cat->id]['grand_total_all_categories'] = PoItems::where('vendor_id', '!=', 1)->sum('amount');
            }
            if ($data['overview']['po'][$cat->id]['grand_total_all_categories']) {
                $data['overview']['po'][$cat->id]['percentage'] = $data['overview']['po'][$cat->id]['total'] * 100 / $data['overview']['po'][$cat->id]['grand_total_all_categories'];
            } else {
                $data['overview']['po'][$cat->id]['percentage'] = null;
            }

            if ($include_legacy_vendor) {
                $data['overview']['invoice'][$cat->id]['total'] = InvoiceItem::where('vendor_id', '=', $vendor_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                $data['overview']['invoice'][$cat->id]['grand_total'] = InvoiceItem::where('expense_category_id', '=', $cat->id)->sum('amount');
                $data['overview']['invoice'][$cat->id]['grand_total_all_categories'] = InvoiceItem::sum('amount');
            } else {
                $data['overview']['invoice'][$cat->id]['total'] = InvoiceItem::where('vendor_id', '!=', 1)->where('vendor_id', '=', $vendor_id)->where('expense_category_id', '=', $cat->id)->sum('amount');
                $data['overview']['invoice'][$cat->id]['grand_total'] = InvoiceItem::where('vendor_id', '!=', 1)->where('expense_category_id', '=', $cat->id)->sum('amount');
                $data['overview']['invoice'][$cat->id]['grand_total_all_categories'] = InvoiceItem::where('vendor_id', '!=', 1)->sum('amount');
            }
            if ($data['overview']['invoice'][$cat->id]['grand_total_all_categories']) {
                $data['overview']['invoice'][$cat->id]['percentage'] = $data['overview']['invoice'][$cat->id]['total'] * 100 / $data['overview']['invoice'][$cat->id]['grand_total_all_categories'];
            } else {
                $data['overview']['invoice'][$cat->id]['percentage'] = null;
            }
        }

        return view('modals.expense-categories-vendor-details', compact('expense_categories', 'vendor', 'parcel', 'program', 'data', 'cat_colors', 'cat_names', 'in_parcel', 'in_program', 'include_legacy_vendor'));
    }
}
