<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use \App\Models\ScheduleTime;
use Carbon;
use Event;
use Auth;
use Log;

class CachedAudit extends Model
{

    protected $fillable = [
        'id',
        'audit_id',
        'audit_key',
        'project_id',
        'project_key',
        'project_ref',
        'status',
        'lead',
        'lead_json',
        'title',
        'pm',
        'address',
        'city',
        'state',
        'zip',
        'total_buildings',
        'inspection_icon',
        'inspection_status',
        'inspection_status_text',
        'inspection_schedule_date',
        'inspection_schedule_text',
        'inspectable_items',
        'total_items',
        'audit_compliance_icon',
        'audit_compliance_status',
        'audit_compliance_status_text',
        'followup_status',
        'followup_status_text',
        'followup_date',
        'file_audit_icon',
        'file_audit_status',
        'file_audit_status_text',
        'nlt_audit_icon',
        'nlt_audit_status',
        'nlt_audit_status_text',
        'lt_audit_icon',
        'lt_audit_status',
        'lt_audit_status_text',
        'smoke_audit_icon',
        'smoke_audit_status',
        'smoke_audit_status_text',
        'auditor_status_icon',
        'auditor_status',
        'auditor_status_text',
        'message_status_icon',
        'message_status',
        'message_status_text',
        'document_status_icon',
        'document_status',
        'document_status_text',
        'history_status_icon',
        'history_status',
        'history_status_text',
        'step_id',
        'step_status_icon',
        'step_status',
        'step_status_text',
        'estimated_time',
        'estimated_time_needed',
        'created_at',
        'updated_at'
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function ($cached_audit) {
            Event::dispatch('cachedaudit.created', $cached_audit);
        });


        // static::updated(function ($cached_audit) {
        //     Event::fire('cachedaudit.created', $cached_audit);
        //         Log::info('created fired for Cached Audit');
        // });

        // static::deleted(function ($audit) {
        //     Event::fire('audit.deleted', $audit);
        // });
    }

    public function update_auditor_status()
    {
        if($this->estimated_time_needed ){
            $time = explode(':', $this->estimated_time_needed);
            if(intval($time[0]) > 0 || intval($time[1]) > 0){
                if($this->auditor_status_icon != 'a-clock-not'){
                    $this->auditor_status_icon = 'a-clock-not';
                    $this->auditor_status = 'action-required';
                    $this->auditor_status_text ='THERE ARE '.$this->hours_still_needed().' HOURS THAT STILL NEED TO BE SCHEDULED';
                    $this->save();
                    return true;
                }
            } else {
                if($this->hasAmenityInspectionNotAssigned()){
                    if($this->auditor_status_icon != 'a-avatar-fail'){
                        $this->auditor_status_icon = 'a-avatar-fail';
                        $this->auditor_status = 'action-required';
                        $this->auditor_status_text ='THERE ARE UNASSIGNED INSPECTION AREAS';
                        $this->save();
                        return true;
                    }
                }else{
                    if($this->auditor_status_icon != 'a-avatar-clock'){
                        $this->auditor_status_icon = 'a-avatar-clock';
                        $this->auditor_status = '';
                        $this->auditor_status_text ='ALL AUDITORS ARE ASSIGNED AND ENOUGH ARE SCHEDULED TO COVER THE ESTIMATED HOURS';
                        $this->save();
                        return true;
                    }
                }
            }
        } else {
            if($this->auditor_status_icon != 'a-clock-5'){
                $this->auditor_status_icon = 'a-clock-5';
                $this->auditor_status = 'action-required';
                $this->auditor_status_text ='PLEASE ENTER A TIME ESTIMATE';
                $this->save();
                return true;
            }
        }
        return false;
    }
    public function update_mail_status(){
        $unreadMail = $this->unread_mail();
        if(count($unreadMail)){
            if($this->message_status_icon != 'a-envelope-4 attentions '){
                //we always update this because there may be new unread recipients
                $this->message_status_icon = 'a-envelope-4 attention';
                $this->message_status = 'ok-actionable';
                $unreadByUser = [];
                forEach($unreadMail as $mail){

                    forEach($mail->message_recipients as $m){
                        if(array_key_exists($m->id, $unreadByUser) && array_key_exists('count', $unreadByUser[$m->id])){
                        	if($m->pivot->seen == 0)
                          $unreadByUser[$m->id]['count']++;
                        }else{
                            if($m){
                            	if($m->pivot->seen == 0)
                              	$unreadByUser[$m->id]['count'] = 1;
                            	else
                              	$unreadByUser[$m->id]['count'] = 0;

                              //dd($m);
                              $unreadByUser[$m->id]['name'] = $m->name;
                            } else {
                                dd($m);
                            }
                        }

                    }

                }
                $message = '';
                forEach($unreadByUser as $u){
                    $message .= strtoupper($u['name']).' : '.$u['count'].'<BR>';
                }
                $this->message_status_text ='UNREAD MESSAGES <hr class=\'dashed-hr uk-margin-small-top uk-margin-small-bottom\'>'.$message.'';
                $this->save();
                return true;
            }
        }else{
            if($this->mail_count()){
                if($this->message_status_icon != 'a-envelope-4'){
                    $this->message_status_icon = 'a-envelope-4';
                    $this->message_status = '';
                    $this->message_status_text ='ALL MESSAGES HAVE BEEN READ';
                    $this->save();
                    return true;
                }
            } else {
                if($this->message_status_icon != 'a-envelope-4 gray-text'){
                    $this->message_status_icon = 'a-envelope-4 gray-text';
                    $this->message_status = '';
                    $this->message_status_text ='NO MESSAGES';
                    $this->save();
                    return true;
                }
            }
        }
        return false;
    }
    public function update_finding_stats() {
        $fileCount = count($this->audit->files);
        $unCorrectedFileCount = count($this->audit->files->where('auditor_last_approved_resolution_at', null));
        $nltCount = count($this->audit->nlts);
        $unCorrectedNltCount = count($this->audit->nlts->where('auditor_last_approved_resolution_at', null));

        $ltCount = count($this->audit->lts);
        $unCorrectedLtCount = count($this->audit->lts->where('auditor_last_approved_resolution_at', null));
        $updated = 0;
       if($fileCount !== $this->file_findings_count || $unCorrectedFileCount !== $this->unresolved_file_findings_count){

            if($unCorrectedFileCount > 0){
                $corrected = $fileCount - $unCorrectedFileCount;
                $plural = '';
                if($fileCount > 1){
                    $plural = 'S';
                }
                $this->file_audit_icon = 'a-folder attention';
                $this->file_audit_status = 'action-needed';
                $this->file_audit_status_text =$corrected.' / '.$fileCount.' FINDING'.$plural.' CORRECTED';

                $updated = 1;
            } else {
                $plural = '';
                if($fileCount > 1){
                    $plural = 'S';
                }
                $this->file_audit_icon = 'a-folder';
                $this->file_audit_status = '';
                if($fileCount > 0){
                    $this->file_audit_status_text = $fileCount.' FINDING'.$plural.' FULLY CORRECTED';
                } else {
                    $this->file_audit_status_text = 'NO FINDINGS';
                }


                $updated = 1;
            }
            $this->unresolved_file_findings_count = $unCorrectedFileCount;
            $this->file_findings_count = $fileCount;
       }
       if($nltCount !== $this->nlt_findings_count || $unCorrectedNltCount !== $this->unresolved_nlt_findings_count){

            if($unCorrectedNltCount > 0){
                $corrected = $nltCount - $unCorrectedNltCount;
                $plural = '';
                if($nltCount > 1){
                    $plural = 'S';
                }
                $this->nlt_audit_icon = 'a-booboo attention';
                $this->nlt_audit_status = 'action-needed';
                $this->nlt_audit_status_text =$corrected.' / '.$nltCount.' FINDING'.$plural.' CORRECTED';

                $updated = 1;
            } else {
                $plural = '';
                if($nltCount > 1){
                    $plural = 'S';
                }
                $this->nlt_audit_icon = 'a-booboo';
                $this->nlt_audit_status = '';
                if($nltCount > 0){
                    $this->nlt_audit_status_text = $nltCount.' FINDING'.$plural.' FULLY CORRECTED';
                } else {
                    $this->nlt_audit_status_text = 'NO FINDINGS';
                }

                $updated = 1;
            }
            $this->unresolved_nlt_findings_count = $unCorrectedNltCount;
            $this->nlt_findings_count = $nltCount;
       }
       if($ltCount !== $this->lt_findings_count || $unCorrectedLtCount !== $this->unresolved_lt_findings_count){

            if($unCorrectedLtCount > 0){
                $corrected = $ltCount - $unCorrectedLtCount;
                $plural = '';
                if($ltCount > 1){
                    $plural = 'S';
                }
                $this->lt_audit_icon = 'a-skull attention';
                $this->lt_audit_status = 'action-needed';
                $this->lt_audit_status_text =$corrected.' / '.$ltCount.' FINDING'.$plural.' CORRECTED';

                $updated = 1;
            } else {
                $plural = '';
                if($ltCount > 1){
                    $plural = 'S';
                }
                $this->lt_audit_icon = 'a-skull';
                $this->lt_audit_status = '';
                if($ltCount > 0){
                    $this->lt_audit_status_text = $ltCount.' FINDING'.$plural.' FULLY CORRECTED';
                } else {
                    $this->lt_audit_status_text = 'NO FINDINGS';
                }

                $updated = 1;
            }
            $this->unresolved_lt_findings_count = $unCorrectedLtCount;
            $this->lt_findings_count = $ltCount;
        }
        if($updated){
            $this->save();
            return true;
        }else{
            return false;
        }
    }
    public function update_document_status(){
        if($this->has_documents()){
            $uncheckedDocs = $this->has_unchecked_documents();
            if(count($uncheckedDocs)){
                if($this->document_status_icon != 'a-file-mails'){
                        // we trigger the update each time just in case there are new docs submitted.
                        $this->document_status_icon = 'a-file-mail attention';
                        $this->document_status = 'ok-actionable';

                        $message = '';
                        forEach($uncheckedDocs as $ud){
                            $output = 0;
                            $message .= $ud->user->name.' | ';
                            if(null !== $ud->audit_id){
                                $message .= 'AUDIT : '.$ud->audit_id.' | ';
                                $output = 1;
                            }

                            if(null !== $ud->comment){
                                $message .= "'".$ud->comment."' | ";
                                $output = 1;
                            }
                            if(null !== $ud->finding_ids){
                                $message .= "FOR FINDINGS: ".str_replace('"', '',str_replace(']', '',str_replace('[', '',$ud->finding_ids)));
                                $output = 1;
                            }
                            if($output){
                               $message.='<hr class=\'dashed-hr uk-margin-small-top uk-margin-small-bottom\'>';
                            }
                        }
                        $message = 'DOCS NEED REVIEWED<hr class=\'dashed-hr uk-margin-small-top uk-margin-small-bottom\'>'.$message;
                        $this->document_status_text =$message;
                        $this->save();
                        return true;
                    }
                }else{
                    if($this->document_status_icon != 'a-file-approve'){
                        $this->document_status_icon = 'a-file-approve';
                        $this->document_status = '';
                        $this->document_status_text ='ALL DOCUMENTS REVIEWED';
                        $this->save();
                        return true;
                    }
                }
        }else{
            if($this->document_status_icon != 'a-file-question'){
                    $this->document_status_icon = 'a-file-question';
                    $this->document_status = 'action-required';
                    $this->document_status_text ='NO DOCUMENTS HAVE BEEN FOUND WITH THIS PROJECT';
                    $this->save();
                    return true;
                }
        }
        return false;
    }

    public function update_unit_statuses() {
    	if($this->audit && $this->audit->unique_unit_inspections->count() !== $this->total_units) {
    		$unit_count = $this->audit->unique_unit_inspections->count();
    		$this->total_units = $unit_count;
    		$this->save();
    	} else {
    		//do nothing
    	}

    		return true;

    }

    public function update_building_statuses() {
        if($this->audit && $this->audit->building_inspections->count() !== $this->total_buildings) {
            $building_count = $this->audit->building_inspections->count();
            $this->total_buildings = $building_count;
            $this->save();
        } else {
            //do nothing
        }

            return true;

    }

    public function update_report_statuses(){
        $updated = 0;
        if(count($this->audit->reports)){

            $car = $this->audit->reports->where('from_template_id',1)->first();
            if(null !== $car){
                //dd('CAR!');
                switch ($car->crr_approval_type_id) {
                        case '1':
                            $carIcon = "a-file-pencil-2"; // draft
                            break;
                        case '2':
                            $carIcon = "a-file-clock"; // pending manager review
                            break;
                        case '3':
                            $carIcon = "a-file-fail manager-fail attention"; // declined by manager
                            break;
                        case '4':
                            $carIcon = "a-file-repeat"; // approved with changes
                            break;
                        case '5':
                            $carIcon = "a-file-certified"; // approved
                            break;
                        case '6':
                            $carIcon = "a-file-mail"; // Unopened by PM
                            break;
                        case '7':
                            $carIcon = "a-file-pen"; // Viewed by a PM
                            break;
                        case '9':
                            $carIcon = "a-file-approve"; // All items resolved
                            break;
                        default:
                            $carIcon = "a-file-fail";
                            break;
                    }
                    $unCorrected = count($this->audit->findings->where('auditor_last_approved_resolution_at', null));
                    if($this->car_icon != $carIcon || (time() > strtotime($car->response_due_date) && $car->response_due_date !== null && $unCorrected > 0)){
                        // we trigger the update each time just in case there are new docs submitted.
                        $this->car_icon = $carIcon;
                        $statusText = '';
                        if(time() > strtotime($car->response_due_date) && $car->response_due_date !== null && $unCorrected > 0){
                                    $status = 'action-required';
                                    $statusText = " | RESOLUTIONS ARE PAST DUE.";
                                    $this->car_icon .= ' attention';
                                }else{
                                    $status = '';
                                }
                        $this->car_status = $status;
                        $this->car_status_text ='CAR #'.$car->id.' '.strtoupper($car->status_name()).$statusText;
                        $this->car_id = $car->id;
                        $this->car_approval_type_id = $car->crr_approval_type_id;
                        $updated = 1;
                    }
                }else{
                    if($this->car_icon != null){
                        $this->car_icon = null;
                        $this->car_status = null;
                        $this->car_status_text =null;
                        $this->car_id = null;
                        $this->car_approval_type_id = null;
                        $updated = 1;
                    }
                }
                //////////////////////////////////////
                $ehs = $this->audit->reports->where('from_template_id',2)->first();
                if(null !== $ehs){
                    //dd('CAR!');
                    switch ($ehs->crr_approval_type_id) {
                                                        case '1':
                                                            $ehsIcon = "a-file-pencil-2"; // draft
                                                            break;
                                                        case '2':
                                                            $ehsIcon = "a-file-clock"; // pending manager review
                                                            break;
                                                        case '3':
                                                            $ehsIcon = "a-file-fail manager-fail attention"; // declined by manager
                                                            break;
                                                        case '4':
                                                            $ehsIcon = "a-file-repeat"; // approved with changes
                                                            break;
                                                        case '5':
                                                            $ehsIcon = "a-file-certified"; // approved
                                                            break;
                                                        case '6':
                                                            $ehsIcon = "a-file-mail"; // Unopened by PM
                                                            break;
                                                        case '7':
                                                            $ehsIcon = "a-file-pen"; // Viewed by a PM
                                                            break;
                                                        case '9':
                                                            $ehsIcon = "a-file-approve"; // All items resolved
                                                            break;
                                                        default:
                                                            $ehsIcon = "a-file-fail";
                                                            break;
                                                    }
                    if($this->ehs_icon != $ehsIcon || ($ehs->signed_by == null && $ehs->signed_by_id == null)){
                            // we trigger the update each time just in case there are new docs submitted.
                            $this->ehs_icon = $ehsIcon;
                            $statusText = '';
                            if($ehs->signed_by == null && $ehs->signed_by_id == null){
                                    $status = 'action-required';
                                    $statusText = ' | REPORT HAS NOT BEEN SIGNED';
                                    $this->ehs_icon .= ' attention';
                                }else{
                                    $status = '';
                                }
                            $this->ehs_status = $status;
                            $this->ehs_status_text ='EHS #'.$ehs->id.' '.strtoupper($ehs->status_name()).$statusText;
                            $this->ehs_id = $ehs->id;
                            $this->ehs_approval_type_id = $ehs->crr_approval_type_id;
                            $updated = 1;
                        }
                    }else{
                        if($this->ehs_icon != null){
                            $this->ehs_icon = null;
                            $this->ehs_status = null;
                            $this->ehs_status_text =null;
                            $this->ehs_id = null;
                            $this->ehs_approval_type_id = null;
                            $updated = 1;
                        }
                    }
                    /////////////////////////////////////
                    $_8823 = $this->audit->reports->where('from_template_id',5)->first();
                    if(null !== $_8823){
                        //dd('CAR!');
                        switch ($_8823->crr_approval_type_id) {
                                                            case '1':
                                                                $_8823Icon = "a-file-pencil-2"; // draft
                                                                break;
                                                            case '2':
                                                                $_8823Icon = "a-file-clock"; // pending manager review
                                                                break;
                                                            case '3':
                                                                $_8823Icon = "a-file-fail manager-fail attention"; // declined by manager
                                                                break;
                                                            case '4':
                                                                $_8823Icon = "a-file-repeat"; // approved with changes
                                                                break;
                                                            case '5':
                                                                $_8823Icon = "a-file-certified"; // approved
                                                                break;
                                                            case '6':
                                                                $_8823Icon = "a-file-mail"; // Unopened by PM
                                                                break;
                                                            case '7':
                                                                $_8823Icon = "a-file-pen"; // Viewed by a PM
                                                                break;
                                                            case '9':
                                                                $_8823Icon = "a-file-approve"; // All items resolved
                                                                break;
                                                            default:
                                                                $_8823Icon = "a-file-fail";
                                                                break;
                                                        }
                        $unCorrected = count($this->audit->findings->where('auditor_last_approved_resolution_at', null));
                        if($this->_8823_icon != $_8823Icon || (time() > strtotime($_8823->response_due_date) && $_8823->response_due_date !== null && $unCorrected > 0)){
                                // we trigger the update each time just in case there are new docs submitted.
                                $this->_8823_icon = $_8823Icon;
                                if(time() > strtotime($_8823->response_due_date) && $_8823->response_due_date !== null){
                                    $status = 'action-required';
                                    $statusText = ' | UNCORRECTED ITEMS REMAIN : XXX DAYS REMAIN TO RESOLVE.';
                                    $this->_8823_icon .= ' attention';
                                }else{
                                    $status = '';
                                    $statusText = '';
                                }
                                $this->_8823_status = $status;
                                $this->_8823_status_text ='8823 #'.$_8823->id.' '.strtoupper($_8823->status_name()).$statusText;
                                $this->_8823_id = $_8823->id;
                                $this->_8823_approval_type_id = $_8823->crr_approval_type_id;
                                $updated = 1;
                            }
                        }else{
                            if($this->_8823_icon != null){
                                $this->_8823_icon = null;
                                $this->_8823_status = null;
                                $this->_8823_status_text =null;
                                $this->_8823_id = null;
                                $this->_8823_approval_type_id = null;
                                $updated = 1;
                            }
                        }
        }else{
            if($this->car_icon != null || $this->car_status != null || $this->car_id != null || $this->car_status_text != null || $this->ehs_icon != null || $this->ehs_status != null || $this->ehs_status_text != null || $this->ehs_id != null || $this->_8823_icon != null || $this->_8823_status != null || $this->_8823_status_text !=null || $this->_8823_id != null){
                    $this->car_icon = null;
                    $this->car_status = null;
                    $this->car_status_text = null;
                    $this->car_id = null;
                    $this->car_approval_type_id = null;
                    $this->ehs_icon = null;
                    $this->ehs_status = null;
                    $this->ehs_status_text =null;
                    $this->ehs_id = null;
                    $this->ehs_approval_type_id = null;
                    $this->_8823_icon = null;
                    $this->_8823_status = null;
                    $this->_8823_status_text =null;
                    $this->_8823_id = null;
                    $this->_8823_approval_type_id = null;
                    $updated = 1;
                }
        }
        if($updated){
            $this->save();
            return true;
        }else{
            return false;
        }
    }
    public function update_cached_audit(){
    	// Log::info('im here');
        $updated = false;

        if($this->update_auditor_status()){
            $updated = true;
        }
        if($this->update_mail_status()){
            $updated = true;
        }
        if($this->update_document_status()){
            $updated = true;
        }
        if($this->update_finding_stats()){
            $updated = true;
        }
        if($this->update_report_statuses()){
            $updated = true;
        }
        if($this->update_unit_statuses()){
            $updated = true;
        }
        if($this->update_building_statuses()){
            $updated = true;
        }
        return $updated;
    }

    public function has_documents(){
        if($this->project && $this->project->documents){
            $hasDocs = $this->project->documents->count();
        }else{
            $hasDocs = null;
        }
        return $hasDocs;
    }
    public function has_unchecked_documents() {
        $hasDocs = $this->project->documents->where('approved',null)->where('notapproved',null);
        return $hasDocs;
    }
    public function auditors() : HasMany
    {
        return $this->hasMany(\App\Models\AuditAuditor::class, 'audit_id', 'audit_id');
    }

    public function lead_auditor() : HasOne
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'lead');
    }

    public function days() : HasMany
    {
        return $this->hasMany(\App\Models\ScheduleDay::class, 'audit_id', 'audit_id')->orderBy('date','asc');
    }

    public function progress() : HasMany{
        return $this->hasMany(\App\Models\GuideProgress::class, 'audit_id')->orderBy('id','desc');
    }

    public function current_step()
    {
        if($this->has('progress')){
            return $this->progress()->first();
        }else{
            return 0;
        }
    }

    public function checkStatus($type = null)
    {
        return 'This is access through the audit relationship';
    }

    public function estimated_hours()
    {
        if($this->estimated_time){
            return explode(':', $this->estimated_time)[0];
        }else{
            return null;
        }
    }

    public function estimated_minutes()
    {
        if($this->estimated_time){
            return explode(':', $this->estimated_time)[1];
        }else{
            return null;
        }
    }

    public function hours_still_needed()
    {
        if($this->estimated_time_needed){
            $time = explode(':', $this->estimated_time_needed);
            if(intval($time[0]) > 0 || intval($time[1]) > 0){

                $time = explode(':', $this->estimated_time_needed);
                return $time[0].":".$time[1];
            }else{
                return '00:00';
            }
        }else{
            return null;
        }
    }

    public function formatted_address($format = 'default')
    {

        $address = '';

        if($format == 'extended'){
            if($this->title){
                $address = $this->title . "<br />";
            }
            if($this->pm){
                $address =  $address . $this->pm . "<br />";
            }
            if($this->address){
                $address = $this->address. "<br />";
            }
            if($this->city){
                $address = $address . "<br />" . $this->city. " ".$this->state. " " . $this->zip;
            }
        }elseif($format == 'simple'){
            if($this->address){
                $address = $this->address;
            }
            if($this->city){
                $address = $address . ", " . $this->city. " ".$this->state. " " . $this->zip;
            }
        }else{
            if($this->address){
                $address = $this->address. "<br />";
            }
            if($this->city){
                $address = $address . "<br />" . $this->city. " ".$this->state. " " . $this->zip;
            }
        }

        return $address;
    }

    public function estimated_chart_data()
    {
        // used to display the chart on the assignment page
        // chart data depends on the number of days
        // backgroundColor
        // labels
        // data

        // ----------------------------------------------------------------
        // put this in the AuditEvent when updating the cachedaudit!
        // calculate needed time
        if($this->estimated_time){
            $estimated_time = explode(':', $this->estimated_time);
            $estimated_time_in_minutes = $estimated_time[0]*60 + $estimated_time[1];
        }else{
            $output['data'] = '[]';
            $output['labels'] = '[]';
            $output['backgroundColor'] ='[]';
            return $output;
        }
        $time_scheduled = 0;
        foreach($this->days as $day){
            $time_scheduled = $time_scheduled + ScheduleTime::where('audit_id','=',$this->audit_id)->where('day_id','=',$day->id)->sum('span') * 15;
        }

        $needed_time_in_hours = floor(($estimated_time_in_minutes - $time_scheduled) / 60);
        $needed_time_in_minutes = ($estimated_time_in_minutes - $time_scheduled) % 60;


        $needed_time = $needed_time_in_hours.':'.$needed_time_in_minutes.':00';
        if($needed_time != $this->estimated_time_needed){
            // update the cachedaudit record with the new needed time
            $this->update(['estimated_time_needed' => $needed_time]);
            $this->fresh();
        }
        // ----------------------------------------------------------------

        $needed = 0;
        if($this->estimated_time_needed){
            $estimated_time_needed = explode(':', $this->estimated_time_needed);
            //$minutes = 100 * $estimated_time_needed[1] / 60;
            $needed = ltrim($estimated_time_needed[0], '0').'.'.$estimated_time_needed[1];
        }

        if($needed < 0) {
        	$needed = 0;
        }

        $output['data'] = '['.$needed;
        $output['labels'] = '[\'Needed\'';
        $output['backgroundColor'] = '[chartColors.needed';

        foreach($this->days as $day){
            $output['backgroundColor'] = $output['backgroundColor'].',chartColors.estimated';
            $output['labels'] = $output['labels'].',"'.formatDate($day->date, 'F d, Y').'"';

            $schedules_total = ScheduleTime::where('audit_id','=',$this->audit_id)->where('day_id','=',$day->id)->sum('span') * 15 / 60;
            $output['data'] = $output['data'].','.$schedules_total;
        }

        $output['data'] = $output['data'].']';
        $output['labels'] = $output['labels'].']';
        $output['backgroundColor'] = $output['backgroundColor'].']';

        return $output;
    }

    public function getLeadJsonAttribute($value)
    {
        return json_decode($value);
    }

    public function getInspectionScheduleJsonAttribute($value)
    {
        return json_decode($value);
    }

    /**
     * Project
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function project() : HasOne
    {
        return $this->hasOne(\App\Models\Project::class, 'id', 'project_id');
    }

    /**
     * audit
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function audit() : HasOne
    {
        return $this->hasOne(\App\Models\Audit::class, 'id', 'audit_id');
    }

    // amenity_inspections table is where we store all the amenities that need to be inspected
    public function inspection_items() : HasMany
    {
        return $this->hasMany(\App\Models\AmenityInspection::class, 'audit_id', 'audit_id');
    }

    public function total_items()
    {
        return $this->inspection_items()->count();
    }

    public function auditor_items()
    {
        // count all the amenity_inspections items belonging to the current user
        return $this->inspection_items()->where('auditor_id','=',Auth::user()->id)->count();
    }

    public function hasAmenityInspectionAssigned($building_id = null, $unit_id = null) {

        if($building_id !== null){
            $test = $this->inspection_items()->where('auditor_id','=',Auth::user()->id)->where('building_id','=',$building_id)->count();
        }elseif($unit_id !== null){
            $test = $this->inspection_items()->where('auditor_id','=',Auth::user()->id)->where('unit_id','=',$unit_id)->count();
        }else{
            $test = $this->inspection_items()->where('auditor_id','=',Auth::user()->id)->count();
        }

        if($test > 0){
            return true;
        } else {
            return false;
        }
    }
    public function hasAmenityInspectionNotAssigned($building_id = null, $unit_id = null) {

        if($building_id !== null){
            $test = $this->inspection_items()->where('auditor_id','=',null)->where('building_id','=',$building_id)->count();
        }elseif($unit_id !== null){
            $test = $this->inspection_items()->where('auditor_id','=',null)->where('unit_id','=',$unit_id)->count();
        }else{
            $test = $this->inspection_items()->where('auditor_id','=',null)->count();
        }

        if($test > 0){
            return true;
        } else {
            return false;
        }
    }
    public function unread_mail()
    {
        $unreadMail = Communication::select('communications.*')->join('communication_recipients','communication_id','communications.id')->where('project_id',$this->project_id)->where('seen',0)->groupBy('id')->with('message_recipients')->get();
        return $unreadMail;
    }
    public function myUnread_mail()
    {
        $unreadMail = Communication::join('communication_recipients','communication_id','id')->where('project_id',$this->project_id)->where('user_id',Auth::user()->id)->where('seen',0)->count();
        return $unreadMail;
    }
    public function mail(){
        $mail = Communication::join('communication_recipients','communication_id','communications.id')->where('project_id',$this->project_id)->with('recipients')->with('recipients.user')->get();
        return $mail;
    }
    public function mail_count(){
        $mail = Communication::join('communication_recipients','communication_id','communications.id')->where('project_id',$this->project_id)->count();
        return $mail;
    }


}
