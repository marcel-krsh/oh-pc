<?php

namespace App\Http\Controllers\Helper;

//
use DB;
use Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;

//
class DatafixController extends Controller
{

	public function __construct()
	{
		$this->middleware('allita.developer');
		$this->allitapc();
	}

	public function changeSyncTableLastEditedDateBackDecade($table)
	{
		$error = 0;
		if (Schema::hasTable($table)) {
			$now = Carbon::now();
			$decage_ago = $now->subYears(10);
			return view('helpers.table-latest-date', compact('table', 'decage_ago', 'error'));
		}
		$error = 1;
		return view('helpers.table-latest-date', compact('table', 'error'));
	}

	public function changeSyncTableLastEditedDateBackDecadeSave(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'table' => 'required',
		]);
		if (Schema::hasTable($request->table)) {
			ini_set('max_execution_time', 300);
			$now = Carbon::now();
			$decage_ago = $now->subYears(10); //->format('Y-m-d H:i:s.u');
			$records = DB::table($request->table)->get();
			DB::table($request->table)->where('id', '>', 0)->update([
				'last_edited' => $decage_ago,
			]);
			return 1;
		} else {
			$validator->getMessageBag()->add('table', 'Given table does not exist in Database');
			return response()->json(['errors' => $validator->errors()->all()]);
		}

		return $this->extraCheckErrors($validator);
	}

	protected function extraCheckErrors($validator)
	{
		$validator->getMessageBag()->add('error', 'Something went wrong. Check your code!!');
		return response()->json(['errors' => $validator->errors()->all()]);
	}
}
