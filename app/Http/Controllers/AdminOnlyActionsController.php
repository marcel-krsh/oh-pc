<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Audit;
use App\Models\CrrReport;

class AdminOnlyActionsController extends Controller
{
    //
    public function deleteAllitaAudit(Audit $audit){
    	if(\Auth::user()->can('access_admin')){
    		if(is_object($audit)){
    			/// change the audit status so compliance doesn't rerun
    			if($audit->findings->whereNull('cancelled_at')->count() < 1){
	    			$audit->monitoring_status_type_key = 3;
	    			$audit->save();
	    			/// remove all the parts of the audit allita has:
	    			\App\Models\UnitInspection::where('audit_id',$audit->id)->delete();
	    			/// \App\Models\SiteInspection::where('audit_id',$audit->id)->delete();
	    			\App\Models\Finding::where('audit_id',$audit->id)->delete();
	    			\App\Models\BuildingInspection::where('audit_id',$audit->id)->delete();
	    			\App\Models\UnitProgram::where('audit_id',$audit->id)->delete();
	    			\App\Models\AmenityInspection::where('audit_id',$audit->id)->delete();
	    			\App\Models\CachedAudit::where('audit_id',$audit->id)->delete();
	    			\App\Models\CachedBuilding::where('audit_id',$audit->id)->delete();
	    			\App\Models\CachedUnit::where('audit_id',$audit->id)->delete();
	    			/// delete any reports generated as they will no longer work when the audit is removed
	    			$reports = \App\Models\CrrReport::where('audit_id',$audit->id)->get();
	    			foreach ($reports as $report) {
	    				# code...
	    				\App\Models\CrrPart::where('crr_report_id',$report->id)->delete();
	    				\App\Models\CrrPartOrder::where('crr_report_id',$report->id)->delete();
	    				\App\Models\CrrSection::where('crr_report_id',$report->id)->delete();
	    				\App\Models\CrrSectionOrder::where('crr_report_id',$report->id)->delete();
	    				/// delete history??
	    				\App\Models\CrrReport::where('id',$report->id)->delete();
	    			}
	    			\App\Models\OrderingAmenity::where('audit_id',$audit->id)->delete();
	    			\App\Models\OrderingBuilding::where('audit_id',$audit->id)->delete();
	    			\App\Models\OrderingUnit::where('audit_id',$audit->id)->delete();
	    			\App\Models\ScheduleDay::where('audit_id',$audit->id)->delete();
	    			\App\Models\ScheduleTime::where('audit_id',$audit->id)->delete();
	    			\App\Models\Communication::where('audit_id',$audit->id)->update(['audit_id' => null]);

	    		}else{
	    			return '<h2>Sorry, this audit has uncancelled findings recorded and cannot be deleted. Please cancel the findings and try again.</h2>';
	    		}


    		} else {
    			return '<h2>Sorry, I cannot find the audit you are trying to delete.<h2>';
    		}

    	}else{
    		return '<h2>Sorry, your user has insufficient priveledges to do this action.<h2>';
    	}
    	/// all done - 
    	return '<p>Allita\'s managed copy of this audit has been deleted. It was confirmed that this did not have any findings recorded and was eligible to be deleted by you - an admin. Any time scheduled on this audit has been removed. If it had any reports, they have also been removed as they will not work without the Allita managed audit. This process did not delete the monitoring from DEVCO. If that same audit is set to a monitoring status that Allita recognizes - it will be run again.</p>';
    }

    public function deleteCrrReport(CrrReport $report){

    			if(\Auth::user()->can('access_admin')){
    				if(is_object($report)){
    					\App\Models\CrrPart::where('crr_report_id',$report->id)->delete();
	    				\App\Models\CrrPartOrder::where('crr_report_id',$report->id)->delete();
	    				\App\Models\CrrSection::where('crr_report_id',$report->id)->delete();
	    				\App\Models\CrrSectionOrder::where('crr_report_id',$report->id)->delete();
	    				/// delete history??
	    				\App\Models\CrrReport::where('id',$report->id)->delete();
	    			} else {
	    				return '<h2>Sorry, I cannot find the report you are referencing</h2>';
	    			}
	    		}else{
	    			return '<h2>Sorry, your user has insufficient priveledges to do this action.<h2>';
	    		}
	    		return '<p>Successfully deleted the report.</p>';
    }
}
