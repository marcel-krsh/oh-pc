    <?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/
    Auth::routes();

    Route::group(['middleware' => 'web'], function () {
        app('debugbar')->disable();
        // rerun compliance run
        Route::get('/audit/{audit}/rerun', 'AuditController@rerunCompliance');

        Route::get('/cached_audit/{cached_audit}/caches','ComplianceGenerator@createCaches');

        Route::get('/audit/{audit}/details', 'ComplianceGenerator@details');

        // Document Test Route
        Route::get('/document/list/{projectNumber}','SyncController@getDocs');
        //Route::get('/document/{documentId}','SyncController@getDoc');
        Route::get('/document/{documentId}', function($documentId) {
            $docRecord = \App\Models\SyncDocuware::where('docuware_doc_id', $documentId)->first();
            // Do Devco Auth here?
            $deviceId=11;
            $deviceName='TestingSystem';
            // Look for existence of file on the provider location (ie. Docuware)
            // @todo: Add the log entry for the access request
            $doc_service = new \App\Services\DocumentService;
            $document_contents = $doc_service->getDocument($documentId,Auth::user()->id, Auth::user()->email, Auth::user()->name, $deviceId, $deviceName);

                //\Illuminate\Support\Facades\Storage::put('test.pdf', $document_contents);


                // Faking in a local test document
                //$document_contents = \Illuminate\Support\Facades\Storage::disk('base')->get('public/TestFile.pdf');

                // Respond Back
                //$response = response()->make($document_contents, 200);
                //$response = response()->make($document_contents);

            return response()->streamDownload(function () use ($document_contents) {
                echo $document_contents;
            }, "{$docRecord->project_number}-".str_replace("\\",'',str_replace('/','',$docRecord->document_class))."-".str_replace("\\",'',str_replace('/','',$docRecord->document_description))."{$docRecord->dw_extension}");

            //$response->header('Content-Type', 'application/pdf'); // change this to the download content type.

            //return $response;
            //return public_path('TestFile.pdf');
        });




        // Update Devco Test Routes
        Route::get('/update_devco/{model}/{referenceId}/{crud}','SyncController@crudDevco');

        //chat routes
        Route::get('/chat','ChatController@chat');
        Route::post('/send','ChatController@send');
        Route::post('/saveToSession','ChatController@saveToSession');
        Route::post('/deleteSession','ChatController@deleteSession');
        Route::post('/getOldMessage','ChatController@getOldMessage');
        Route::get('/check',function(){
            return session('chat');
        });

        //view tables
        Route::get('tables/users', 'HomeController@usersTable')->name('tables.users');
        Route::get('tables/usersdata', 'HomeController@usersTableAjax')->name('tables.usersdata');


        Route::get('/sync', 'SyncController@sync');
         Route::get('/brian_test', 'SyncController@brianTest');

        //Route::group(['middleware' => ['allita.auth']], function() {
        Route::get('unified_login', function () {
                //session(['brian'=>'test']);
                return redirect('/');
        });

        Route::get('tablet_login', function () {
                //session(['brian'=>'test']);
                if(Auth::user()->allowed_tablet){
                    return Auth::user()->api_token;
                } else {
                    return 'No Tablet Access';
                }
        });

        Route::post('/session/{name?}/{value?}', 'DataController@setSession');

        Route::get('/', 'DashboardController@index');
        //Route::get('/', function(){dd(\Auth::user(),session('brian'));});
        Route::get('dashboard/audits', 'DashboardController@audits')->name('dashboard.audits');
        Route::get('dashboard/audits/{audit}/buildings', 'AuditController@buildingsFromAudit')->name('audit.buildings');
        Route::get('dashboard/audits/{audit}/buildings/reorder', 'AuditController@reorderBuildingsFromAudit')->name('audit.reorder.buildings');
        Route::get('dashboard/audits/{audit}/building/{building}/units/reorder', 'AuditController@reorderUnitsFromAudit')->name('audit.reorder.units');
        Route::get('dashboard/audits/{audit}/amenities/reorder', 'AuditController@reorderAmenitiesFromAudit')->name('audit.reorder.amenities');
        Route::get('/dashboard/admin_tools', 'DashboardController@adminTools')->name('dashboard.admin');

        Route::get('dashboard/audits/{audit}/building/{building}/details', 'AuditController@detailsFromBuilding')->name('audit.building.details');
        Route::get('dashboard/audits/{audit_id}/building/{building_id}/inspection', 'AuditController@inspectionFromBuilding')->name('audit.inspection');
        Route::get('dashboard/audits/{audit_id}/building/{building_id}/details/{detail_id}/inspection', 'AuditController@inspectionFromBuildingDetail')->name('audit.building.inspection');
        Route::get('dashboard/reports', 'DashboardController@reports')->name('dashboard.reports');

        Route::get('autocomplete/all', 'DashboardController@autocomplete');
        Route::get('autocomplete/auditproject', 'DashboardController@autocomplete');
        Route::get('autocomplete/auditname', 'DashboardController@autocomplete');
        Route::get('autocomplete/auditaddress', 'DashboardController@autocomplete');

        Route::get('/session/filters/{type}/{value?}', function ($type, $value = null) {
            if ($value !== null) {
                session([$type => $value]);
                $new_filter = session($type);
                return $new_filter;
            } else {
                if (!session()->has($type)) {
                    session([$type => 1]);
                } else {
                    if (session($type) == 0 || session($type) === null || session($type) == '') {
                        session([$type => 1]);
                    } else {
                        session()->forget($type);
                    }
                }
                
                
                return 1;
            }
        })->name('session.setfilter');

        Route::post('/communications/parcel/{parcel?}', 'CommunicationController@searchCommunications')->name('communications.search');

        Route::get('/modals/audits/{id}/updateStep', 'AuditController@updateStep')->name('audits.updatestep');
        Route::post('audits/{id}/saveStep', 'AuditController@saveStep')->name('audits.savestep');

        Route::get('/projects/{id}', 'AuditController@getProject')->name('project');
        Route::get('/projects/{id}/title', 'AuditController@getProjectTitle')->name('project.title');
        Route::get('/projects/{id}/details', 'AuditController@getProjectDetails')->name('project.details');
        // Route::get('/projects/{project}/details/title', 'AuditController@getProjectDetailsTitle')->name('project.details.title');
         Route::get('/projects/{id}/details/{type}', 'AuditController@getProjectDetailsInfo')->name('project.details.info');
         Route::get('/projects/{project}/details/assignment/date/{dateid}', 'AuditController@getProjectDetailsAssignmentSchedule')->name('project.details.assignment.schedule');

        Route::get('/projects/{project}/communications/{page?}', 'CommunicationController@communicationsFromProjectTab')->name('project.communications');
        Route::get('/projects/{project}/communications/title', 'AuditController@getProjectCommunicationsTitle')->name('project.communications.title');
        Route::get('/projects/{project}/documents', 'DocumentController@getProjectDocuments')->name('project.documents');
        Route::get('/projects/{project}/documents/title', 'AuditController@getProjectDocumentsTitle')->name('project.documents.title');
        Route::get('/projects/{project_id}/notes', 'NoteController@showTabFromProjectId')->name('project.notes');
        // Route::get('/projects/{project}/notes/title', 'AuditController@getProjectNotesTitle')->name('project.notes.title');
        Route::get('/projects/{project}/comments', 'AuditController@getProjectComments')->name('project.comments');
        Route::get('/projects/{project}/comments/title', 'AuditController@getProjectCommentsTitle')->name('project.comments.title');
        Route::get('/projects/{project}/photos', 'AuditController@getProjectPhotos')->name('project.photos');
        Route::get('/projects/{project}/photos/title', 'AuditController@getProjectPhotosTitle')->name('project.photos.title');
        Route::get('/projects/{project}/findings', 'AuditController@getProjectFindings')->name('project.findings');
        Route::get('/projects/{project}/findings/title', 'AuditController@getProjectFindingsTitle')->name('project.findings.title');
        Route::get('/projects/{project}/followups', 'AuditController@getProjectFollowups')->name('project.followups');
        Route::get('/projects/{project}/followups/title', 'AuditController@getProjectFollowupsTitle')->name('project.followups.title');
        Route::get('/projects/{project}/reports', 'AuditController@getProjectReports')->name('project.reports');
        Route::get('/projects/{project}/reports/title', 'AuditController@getProjectReportsTitle')->name('project.reports.title');
        Route::get('/projects/{project}/stream', 'AuditController@getProjectStream')->name('project.stream');
        Route::get('/modals/projects/{project}/contact', 'AuditController@getProjectContact')->name('project.contact');

        Route::get('/modals/projects/{id}/programs/{programid}/summary', 'AuditController@modalProjectProgramSummary');
        Route::post('/modals/projects/{id}/programs/{programid}/summary', 'AuditController@modalProjectProgramSummaryFilterProgram');
        Route::get('/modals/findings/{type}/audit/{auditid}/building/{buildingid?}/unit/{unitid?}/amenity/{amenityid?}', 'FindingController@modalFindings');

        Route::get('/findings/{id}/items', 'FindingController@findingItems');
        Route::get('/modals/findings/{id}/items/{itemid}/photos/{photoid}', 'FindingController@findingItemPhoto');

        Route::get('/modals/audit/{audit_id}/scheduling/days/{day_id}/auditors/{auditorid?}', 'AuditController@addAssignmentAuditor')->name('project.assignment.addauditor');
        Route::post('/audit/{audit_id}/scheduling/days/{day_id}/auditors/{auditor_id}', 'AuditController@scheduleAuditor')->name('schedule.auditor');
        Route::post('scheduling/event/{event_id}/delete', 'AuditController@deleteSchedule')->name('schedule.delete');

        Route::get('projects/{id}/assignments/addauditor/{auditorid}/stats', 'AuditController@addAssignmentAuditorStats')->name('project.assignment.addauditorstats');
        Route::get('projects/{id}/assignments/addauditor/{auditorid}/loadcal/{currentdate}/{beforeafter}', 'AuditController@getAssignmentAuditorCalendar')->name('project.assignment.getauditorcalendar');

        Route::get('/modals/auditors/{id}/preferences', 'UserController@preferences')->name('auditor.preferences');
        Route::post('/auditors/{id}/addresses/create', 'UserController@saveAuditorAddress')->name('auditor.address.create');
        Route::post('/auditoraddresses/{address_id}/delete', 'UserController@deleteAuditorAddress')->name('auditor.address.delete');
        Route::post('/auditors/{auditor_id}/addresses/{address_id}/default', 'UserController@setDefaultAddress')->name('auditor.address.default');
        Route::post('/auditors/{id}/availability/create', 'UserController@saveAuditorAvailability')->name('auditor.availability.create');
        Route::get('auditors/{id}/availability/loadcal/{currentdate?}/{beforeafter?}', 'UserController@getAvailabilityCalendar')->name('auditor.availability.loadcal');
        Route::post('auditors/{userid}/availability/{id}/delete', 'UserController@deleteAvailability')->name('auditor.availability.delete');
        Route::post('auditors/{userid}/addtoaudit/{auditid}', 'AuditController@addAuditorToAudit')->name('auditor.addtoaudit');
        Route::post('auditors/{userid}/removefromaudit/{auditid}', 'AuditController@removeAuditorFromAudit')->name('auditor.removefromaudit');

        Route::get('/modals/amenities/add/{type}/{id}', 'AuditController@addAmenity')->name('amenities.add');
        Route::post('/modals/amenities/save', 'AuditController@saveAmenity')->name('amenities.save');

        Route::post('/autosave', 'DataController@autosave');


        // communications
        Route::get('dashboard/communications/{page?}', 'CommunicationController@communicationsTab')->name('communication.tab');
        Route::post('/modals/new-outbound-email-entry', 'CommunicationController@create')->name('communication.create');
        Route::get('/modals/new-outbound-email-entry/{audit_id?}', 'CommunicationController@newCommunicationEntry');
        Route::get('/modals/communication/{audit_id}/replies/{message}', 'CommunicationController@viewReplies');
        Route::post('/communications/audit/{audit?}', 'CommunicationController@searchCommunications')->name('communications.search');
        Route::get('/communications/unseen', 'CommunicationController@getUnseenMessages');
        Route::get('/view_message/{message}', 'CommunicationController@goToMessage');


        Route::post('/documents/audit/{audit}/upload', 'DocumentController@upload')->name('documents.upload');
        Route::get('/documents/audit/{audit}', 'DocumentController@showTabFromParcelId');
        Route::post('/documents/audit/{audit}/comment', 'DocumentController@uploadComment')->name('documents.uploadComment');
        Route::post('/documents/audit/{audit}/deletedocument', 'DocumentController@deleteDocument')->name('documents.deleteDocument');
        Route::get('/documents/audit/{audit}/downloaddocument/{document}', 'DocumentController@downloadDocument')->name('documents.downloadDocument');
        Route::post('/documents/audit/{audit}/approve', 'DocumentController@approveDocument')->name('documents.approve');
        Route::post('/documents/audit/{audit}/notapprove', 'DocumentController@notApproveDocument')->name('documents.notapprove');
        Route::post('/documents/audit/{audit}/documentinfo', 'DocumentController@documentInfo')->name('documents.documentInfo');


        // ADMIN
        Route::group(['prefix'=>'modals/admin'], function () {
            Route::get('boilerplate/create/{id?}', 'AdminToolController@boilerplateCreate');
            Route::get('program/create/{id?}', 'AdminToolController@programCreate');
            Route::get('document_category/create/{id?}', 'AdminToolController@documentCategoryCreate');
            Route::get('county/create/{id?}', 'AdminToolController@countyCreate');
            Route::get('finding_type/create/{id?}', 'AdminToolController@findingtypeCreate');
            Route::get('hud_area/create/{id?}', 'AdminToolController@hudAreaCreate');
            Route::get('amenity/create/{id?}', 'AdminToolController@amenityCreate');
            Route::get('users/{id}/manageroles', 'AdminToolController@userManageRoles');
        });

        // Admin tabs
        Route::group(['prefix'=>'tabs'], function () use (Auth::user()) {
            if(Auth::user()->admin_access()){
                Route::get('organization', 'AdminToolController@organizationIndex');
                Route::post('organization', 'AdminToolController@searchOrganizations')->name('organizations.search');
                Route::get('amenity', 'AdminToolController@amenityIndex');
                Route::get('hud', 'AdminToolController@hudIndex');
                Route::get('findingtype', 'AdminToolController@findingtypeIndex');
                Route::post('findingtype', 'AdminToolController@searchFindingTypes')->name('findingtypes.search');
                Route::get('defaultfollowup', 'AdminToolController@defaultfollowupIndex');
                Route::get('boilerplate', 'AdminToolController@boilerplateIndex');
                Route::get('program', 'AdminToolController@programIndex');
                Route::get('users', 'AdminToolController@usersIndex');
                Route::post('users', 'AdminToolController@searchUsers')->name('users.search');
                Route::get('document_category', 'AdminToolController@documentIndex');
                Route::get('county', 'AdminToolController@countyIndex');
                Route::get('emails', 'PagesController@emailsTab');
            }
        });

        // Admin store
        Route::group(['prefix'=>'admin'], function () {
            Route::post('boilerplate/store/{id?}', 'AdminToolController@boilerplateStore');
            Route::post('findingtype/store/{id?}', 'AdminToolController@findingtypeStore');
            Route::post('program/store/{id?}', 'AdminToolController@programStore');
            Route::post('document_category/store/{id?}', 'AdminToolController@documentCategoryStore');
            Route::post('county/store/{id?}', 'AdminToolController@countyStore');
            Route::post('hud_area/store/{id?}', 'AdminToolController@hudAreaStore');
            Route::post('amenity/store/{id?}', 'AdminToolController@amenityStore');
            Route::post('users/{id}/saveroles', 'AdminToolController@userSaveRoles');
        });

        //Project
        Route::get('/notes/project/{project_id}', 'NoteController@showTabFromProjectId')->name('notes.list');
        Route::get('/notes/project/{project}.json', 'NoteController@notesFromProjectIdJson')->name('notes.loadjson');
        Route::get('/external-window/print-notes-{project}.html', 'NoteController@printNotes')->name('notes.print');
        Route::post('/modals/create-note-entry', 'NoteController@create')->name('note.create');
        Route::get('/modals/new-note-entry/{project}', 'NoteController@newNoteEntry');
        Route::post('/notes/project/{project}', 'NoteController@searchNotes')->name('notes.search');

        // Audit
        Route::post('/audit/{id}/estimated/save', 'AuditController@saveEstimatedHours')->name('audit.estimated_hours.save');
        Route::post('/audit/{id}/scheduling/addaday', 'AuditController@addADay')->name('audit.scheduling.addaday');
        Route::post('/audit/{id}/scheduling/days/{day_id}/delete', 'AuditController@deleteDay')->name('audit.scheduling.deleteday');

        // });
    });
/* Route::get('/', 'PagesController@dashboard');

// Dashboard Routes
Route::get('/dashboard', 'PagesController@dashboard')->name('dashboard');
Route::get('/site_visit_manager', 'PagesController@dashboard'); // avoid redundant code by having dashboard load svm
Route::get('/site_visit_manager/index', 'SiteVisitController@index');
Route::get('/site_visit_manager/visit_list', 'SiteVisitController@visitList');
Route::get('/site_visit_manager/manage_devices', 'SiteVisitController@deviceList');
Route::get('/dashboard/user_list', 'PagesController@userList');
Route::get('/dashboard/invoice_list', 'AccountingController@invoiceList');
Route::get('/dashboard/po_list', 'AccountingController@poList');
Route::get('/dashboard/request_list', 'AccountingController@requestList');
Route::get('/dashboard/accounting', 'AccountingController@accounting');
Route::get('/dashboard/parcel_list', 'PagesController@parcelList');
Route::get('/dashboard/stats', 'PagesController@stats');
Route::get('/dashboard/map', 'PagesController@map');
Route::get('/dashboard/communications', 'CommunicationController@communicationsTab');
Route::get('/dashboard/disposition_list', 'DispositionController@dispositionList');
Route::get('/dashboard/disposition_invoice_list', 'DispositionController@dispositionInvoiceList');
Route::get('/dashboard/recapture_invoice_list', 'RecaptureController@recaptureInvoiceList');

Route::get('vendorstest', function () {
    $job = new VendorStatsExportJob(null, null, null);
    dd($job->handle());
});

// General Routes
Route::get('/home', 'PagesController@dashboard');

// Parcel Routes
Route::get('/parcel/{parcel}', 'ParcelsController@show');
Route::get('/detail/parcel/{parcel}', 'ParcelsController@detail');
Route::get('/breakouts/parcel/{parcel}', 'ParcelsPTController@breakouts');
Route::get('/modals/breakout_cost_item/{costid}', 'ParcelsPTController@breakoutViewCostItem');
Route::get('/parcel/next_step/{parcel}', 'PagesController@parcel_next_step');

// Admin Tool Routes
Auth::routes();
//Route::get('/parcels','ParcelsController@index');

/// IMPORT PAGES
Route::get('/import_parcels', 'ImportController@form')->name('import.form');
Route::get('/import_hhf_retention_parcels', 'HHFRetentionImportController@form')->name('import.hhf_retention_form');
//Route::get('/import_historic_parcels','HistoricImportController@form')->name('import.historic_form');

Route::get('/import_costs', 'CostImportController@form')->name('cost_import.form');

/// IMPORT MAPPINGS PAGES
Route::post('/import/mappings', 'ImportController@mappings')->name('import.mappings');
Route::post('/import/hhf_retention_mappings', 'HHFRetentionImportController@mappings')->name('import.hhf_retention_mappings');
Route::post('/import/cost_mappings', 'CostImportController@mappings')->name('import.cost_mappings');

/// IMPORT CORRECTIONS PAGES
Route::post('/import/corrections', 'ImportController@corrections')->name('import.corrections');
Route::post('/import/hhf_retention_corrections', 'HHFRetentionImportController@corrections')->name('import.hhf_retention_corrections');
//Route::post('/import/historic_corrections', 'HistoricImportController@corrections')->name('import.historic_corrections');

/// IMPORT VALIDATION PAGES
Route::get('/validate_parcels', 'ParcelsController@validateParcels');
Route::get('/validate_hhf_retention_parcels', 'ParcelsController@validateHHFRetentionParcel');
Route::get('/validate_historic_parcels', 'ParcelsController@validateHistoricParcels');

Route::get('/validate_parcel', 'ParcelsController@validateParcel');
Route::get('/force_validate', 'ParcelsController@forceValidate');
Route::get('/validate_hhf_retention_parcel', 'ParcelsController@validateHHFRetentionParcel');
//Route::get('/validate_historic_parcel','ParcelsController@validateHistoricParcel');

/// VALIDATION SUPPORT ROUTES AND PAGES
Route::get('/geodata', 'GeoDataController@test');
Route::get('/import_parcels_template', 'PagesController@parcel_import_template');
Route::get('/import_historic_parcels_template', 'PagesController@historic_parcel_import_template');

Route::get('/import_progress', 'ImportController@progress')->name('import.progress');
Route::get('/hhf_retention_import_progress', 'HHFRetentionImportController@progress')->name('import.hhf_retention_progress');
//Route::get('/historic_import_progress','HistoricImportController@progress')->name('import.historic_progress');

/// REPORT PAGES (EXPORT PARCELS, etc.)
Route::get('/reports/export_parcels', 'ReportsController@listExportParcels')->name('reports.listparcels');
Route::get('/reports/export_parcels/{filename}/download', 'ReportsController@exportParcelsDownload')->name('reports.exportparcelsdownload');
Route::get('/reports/export_vendor_stats', 'ReportsController@listExportVendorStats')->name('reports.listvendorstats');
Route::get('/reports/export_vendor_stats_process', 'ReportsController@exportVendorStats')->name('reports.vendorstats');
Route::get('/reports/export_vendor_stats/{fileid}/download', 'ReportsController@exportVendorStatsDownload')->name('reports.exportvendorstatsdownload');









// Modal Routes: line 150
Route::get('/modals/user/{userId}', 'PagesController@userShow');


Route::post('/modals/user/edit/{userId}', 'PagesController@userEdit');
Route::get('/modals/accounting/statBreakDown/{program}', 'AccountingController@statBreakDown');
Route::get('/modals/rules/edit/{rule}', 'rules@edit');
Route::post('/modals/rules/edit/{rule}', 'rules@update');
Route::get('/modals/reimbursement_how_to', 'PagesController@reimbursement_how_to');
Route::get('/modals/correct_parcel_address/{parcel}', 'ImportController@correctAddress');
Route::get('/modals/resolve_validation/{parcel}', 'ImportController@ResolveValidation');




// Documents Routes - line 220




//Admin store


//Admin Deactivate/Activate
Route::group(['prefix'=>'modals/admin'], function () {
    Route::get('deactivate/{type}/{id}', 'AdminToolController@deactivateTools');
    Route::get('activate/{type}/{id}', 'AdminToolController@activateTools');
});

Route::get('modals/parcels/create/{id?}', 'MAParcelsController@parcelCreate');
Route::post('parcel/store/{id?}', 'MAParcelsController@parcelStore');

Route::get('testCreate/{id?}', 'AdminToolController@getRequiredDocumentIds');

// Philippe T Routes
// Notes
// Parcel History
Route::post('/activities/parcel/{parcel}', 'bgHistoryController@searchActivities')->name('activities.search');
// Communications
Route::get('/communications/new-messages', 'CommunicationController@getNewMessages');

Route::get('/communications/parcel/{parcel}', 'CommunicationController@showTabFromParcelId')->name('communications.list');
Route::get('/communications/{parcel}.json', 'CommunicationController@communicationsFromParcelIdJson')->name('communications.loadjson');

Route::post('/modals/new-outbound-email-entry', 'CommunicationController@create')->name('communication.create');
Route::get('/modals/new-outbound-email-entry/{parcel?}', 'CommunicationController@newCommunicationEntry');
Route::get('/modals/communication/{parcel_id}/replies/{message}', 'CommunicationController@viewReplies');

// Emails
Route::get('/preview/send/communication', 'CommunicationController@previewEmail');

//Sessions
Route::get('/session/dynamicModalLoad', function () {
    $dynamicModalLoadid = session('dynamicModalLoad');
    session()->forget('dynamicModalLoad');
    session()->forget('parcel_subtab');
    session()->forget('open_parcel');
    return $dynamicModalLoadid;
});
Route::get('/session/parcel_subtab', function () {
    $parcel_subtab_id = session('parcel_subtab');
    session()->forget('parcel_subtab');
    return $parcel_subtab_id;
});
Route::get('/session/open_parcel', function () {
    $open_parcel_id = session('open_parcel');
    session()->forget('open_parcel');
    return $open_parcel_id;
});
Route::get('/session/open_vendor', function () {
    $open_vendor_id = session('open_vendor');
    session()->forget('open_vendor');
    return $open_vendor_id;
});
Route::get('/session/include_legacy_vendors', function () {
    session(['include_legacy_vendors'=>1]);
    $include_legacy_vendors = session('include_legacy_vendors');
    return $include_legacy_vendors;
});
Route::get('/session/exclude_legacy_vendors', function () {
    session(['include_legacy_vendors'=>0]);
    $include_legacy_vendors = session('include_legacy_vendors');
    return $include_legacy_vendors;
});
// Requests
Route::get('/requests/{r}/parcels', 'RequestController@getParcelsFromRequestId')->name('request.parcels');
// Invoice
Route::get('/invoices/{invoice}', 'InvoiceController@getInvoice');
Route::get('/modals/invoice/edit/{invoice}', 'InvoiceController@editInvoice');
Route::post('/modals/invoice/edit/{invoice}', 'InvoiceController@saveInvoice');
Route::post('/invoices/{invoice}/newnote', 'InvoiceController@newNoteEntry')->name('invoicenote.create');
Route::post('/invoices/{invoice}/parcels', 'InvoiceController@getParcelsFromInvoiceId')->name('invoice.parcels');
// Purchase Orders
Route::post('/purchase_orders/{po}/parcels', 'PurchaseOrderController@getParcelsFromPurchaseOrderId')->name('purchase_order.parcels');
// Expense Categories Modal
Route::get('/modals/expense-categories-details/{output}/{category}/{program}/{parcel?}/{zero_values?}', 'ExpenseCategoriesController@showDetails');
Route::get('/modals/expense-categories-vendor-details/{vendor}/{parcel?}/{program?}/{zero_values?}', 'ExpenseCategoriesController@showVendorExpenses');
// Dispositions 
Route::get('/dispositions/{parcel}/{disposition?}/{format?}', 'DispositionController@getDispositionFromParcelId')->name('export.disposition');
Route::get('/session/next_step', function () {
    $next_step = session('next_step');
    session()->forget('next_step');
    return $next_step;
});
Route::post('/disposition/{parcel}/update', 'DispositionController@processStep')->name('disposition.processStep');
Route::post('/disposition/{parcel}/owed', 'DispositionController@computeRecaptureOwed')->name('disposition.payback');
Route::post('/disposition/{parcel}/addapprover', 'DispositionController@addApprover')->name('disposition.addapprover');
Route::post('/disposition/{parcel}/addHFAApprover', 'DispositionController@addHFAApprover')->name('disposition.addHFAApprover');
Route::post('/disposition/{parcel}/removeapprover', 'DispositionController@removeApprover')->name('disposition.removeapprover');
Route::post('/disposition/{parcel}/removeHFAapprover', 'DispositionController@removeHFAApprover')->name('disposition.removeHFAapprover');
Route::post('/disposition/{parcel}/approve', 'DispositionController@approve')->name('disposition.approve');
Route::post('/disposition/{parcel}/approveHFA', 'DispositionController@approveHFA')->name('disposition.approveHFA');
Route::post('/disposition/{parcel}/decline', 'DispositionController@decline')->name('disposition.decline');
Route::post('/disposition/{parcel}/declineHFA', 'DispositionController@declineHFA')->name('disposition.declineHFA');
Route::post('/disposition/{parcel}/addmissingdate', 'DispositionController@addMissingDate')->name('disposition.addmissingdate');
Route::get('/view_disposition/{disposition}', 'DispositionController@goToDisposition');
Route::post('/disposition/{parcel}/uploadSignature', 'DispositionController@approveUploadSignature')->name('disposition.uploadSignature');
Route::post('/disposition/{parcel}/uploadSignatureComments', 'DispositionController@approveUploadSignatureComments')->name('disposition.uploadSignatureComments');
Route::post('/disposition/{parcel}/uploadHFASignature', 'DispositionController@approveHFAUploadSignature')->name('disposition.uploadHFASignature');
Route::post('/disposition/{parcel}/uploadSupportingDocuments', 'DispositionController@uploadSupportingDocuments')->name('disposition.uploadSupportingDocuments');
Route::post('/disposition/{parcel}/uploadSupportingDocumentsComments', 'DispositionController@uploadSupportingDocumentsComments')->name('disposition.uploadSupportingDocumentsComments');
Route::post('/disposition/{parcel}/getUploadedDocuments', 'DispositionController@getUploadedDocuments')->name('disposition.getUploadedDocuments');
// Disposition Invoices
Route::post('/disposition_invoices/{invoice}/dispositions', 'DispositionController@getDispositionsFromInvoiceId')->name('invoice.dispositions');
Route::post('/disposition_invoices/{invoice}/newnote', 'DispositionController@newNoteEntry')->name('dispositioninvoicenote.create');
Route::get('/disposition_invoice/{invoice}', 'DispositionController@viewInvoice')->name('invoice.dispositionInvoice');
Route::post('/disposition_invoice/{invoice}/uploadSignature', 'DispositionController@approveInvoiceUploadSignature')->name('disposition_invoice.uploadSignature');
Route::post('/disposition_invoice/{invoice}/uploadSignatureComments', 'DispositionController@approveInvoiceUploadSignatureComments')->name('disposition_invoice.uploadSignatureComments');
Route::post('/disposition_invoice/{invoice}/removeDisposition', 'DispositionController@removeDispositionFromInvoice')->name('disposition_invoice.removeDisposition');
Route::post('/disposition_invoice/{invoice}/requestRelease', 'DispositionController@requestRelease')->name('disposition_invoice.requestRelease');
Route::post('/disposition_invoice/{invoice}/released', 'DispositionController@released')->name('disposition_invoice.released');
Route::post('/disposition_invoice/{invoice}/submitForApproval', 'DispositionController@submitForApproval')->name('disposition_invoice.submitForApproval');
// Misc
Route::get('/modals/cost/{parcel}/add', 'ParcelCostController@showCostModal');
Route::post('/modals/cost/{parcel}/add', 'ParcelCostController@saveCost')->name('parcelcosts.save');
Route::get('/modals/createuser', 'PagesController@createUser');
Route::post('/modals/createuser', 'PagesController@createUserSave');
Route::get('/modals/document-retainage-form/{parcel}/{documentids}', 'DocumentController@retainageForm');
Route::post('/modals/document-retainage-form/{parcel}', 'DocumentController@retainageFormSave')->name('documentretainage.save');
Route::get('/modals/document-advance-form/{parcel}/{documentids}', 'DocumentController@advanceForm');
Route::post('/modals/document-advance-form/{parcel}', 'DocumentController@advanceFormSave')->name('documentadvance.save');
Route::get('/modals/edit-document/{document}', 'DocumentController@editDocument')->name('document.edit');
Route::post('/modals/edit-document/{document}', 'DocumentController@saveEditedDocument')->name('document.saveedit');
// Breakouts
Route::post('/breakouts/{parcel}/cost/delete', 'ParcelsPTController@deleteCostItem')->name('breakouts.deleteCost');
Route::post('/breakouts/{parcel}/cost/edit', 'ParcelsPTController@editCostAmount')->name('breakouts.editCost');
Route::post('/breakouts/{parcel}/requested/add', 'ParcelsPTController@addRequestedAmount')->name('breakouts.addRequested');
Route::post('/breakouts/{parcel}/approved/add', 'ParcelsPTController@addApprovedAmount')->name('breakouts.addApproved');
Route::post('/breakouts/{parcel}/invoiced/add', 'ParcelsPTController@addInvoicedAmount')->name('breakouts.addInvoiced');
Route::post('/breakout/request/{current_request}/newnote', 'ParcelsPTController@newNoteEntry')->name('requestnote.create');
Route::post('/breakout/po/{po}/newnote', 'ParcelsPTController@newPONoteEntry')->name('ponote.create');
Route::post('/breakouts/{parcel}/hfa/approve', 'ParcelsPTController@HFAApproveParcel')->name('breakouts.approveParcel');
Route::post('/breakouts/{parcel}/hfa/decline', 'ParcelsPTController@HFADeclineParcel')->name('breakouts.declineParcel');
Route::get('/breakouts/{parcel}/advance', 'ParcelsPTController@advanceDesignation');
// Compliance
Route::get('/compliance/{parcel}', 'ParcelsPTController@getCompliances')->name('compliance.list');
Route::get('/modals/compliance/{compliance}', 'ParcelsPTController@viewCompliance')->name('compliance.view');
Route::post('/compliance/{parcel}/new', 'ParcelsPTController@createCompliance')->name('compliance.create');
Route::get('/compliance/{parcel}/{compliance}/edit', 'ParcelsPTController@editCompliance')->name('compliance.edit');
Route::post('/compliance/{parcel}/{compliance}/edit', 'ParcelsPTController@saveCompliance')->name('compliance.save');
Route::post('/modals/compliance/{parcel}/delete', 'ParcelsPTController@deleteCompliance')->name('compliance.delete');
// Requests
Route::get('/requests/{request}', 'ParcelsPTController@getRequest');
// PO
Route::get('/po/{po}', 'ParcelsPTController@getPO');
// Approvals
Route::post('/approval/lb/parcel2request/{parcel}', 'ParcelsPTController@landbankSubmitParcelToRequest')->name('approval.lb_parcel_to_request');
Route::post('/approval/lb/removeparcelfromrequest/{parcel}', 'ParcelsPTController@landbankRemoveParcelFromRequest')->name('approval.lb_remove_parcel_from_request');
Route::post('/approval/lb/request/{request}/approve', 'ParcelsPTController@approveRequest')->name('request.approve');
Route::post('/approval/lb/request/{request}/decline', 'ParcelsPTController@declineRequest')->name('request.decline');
Route::post('/approval/lb/request/{current_request}/submit', 'ParcelsPTController@requestSubmit')->name('request.submit');
Route::post('/approval/lb/request/{current_request}/addLBApprover', 'ParcelsPTController@requestAddLBApprover')->name('request.addLBApprover');
Route::post('/approval/hfa/request/{current_request}/addHFAApprover', 'ParcelsPTController@requestAddHFAApprover')->name('request.addHFAApprover');
Route::post('/approval/lb/request/{current_request}/removeApprover', 'ParcelsPTController@requestRemoveApprover')->name('request.removeApprover');
Route::post('/approval/lb/request/{req}/uploadSignature', 'ParcelsPTController@approveRequestUploadSignature')->name('approval.uploadSignature');
Route::post('/approval/lb/request/{req}/uploadSignatureComments', 'ParcelsPTController@approveRequestUploadSignatureComments')->name('approval.uploadSignatureComments');

Route::post('/approval/hfa/po/{po}/addHFAApprover', 'ParcelsPTController@poAddHFAApprover')->name('po.addHFAApprover');
Route::post('/approval/hfa/po/{po}/removeApprover', 'ParcelsPTController@poRemoveApprover')->name('po.removeApprover');
Route::post('/approval/hfa/po/{po}/decline', 'ParcelsPTController@declinePO')->name('po.decline');
Route::post('/approval/hfa/po/{po}/approve', 'ParcelsPTController@approvePO')->name('po.approve');
Route::post('/approval/hfa/po/{po}/notifyLB', 'ParcelsPTController@poNotifyLB')->name('po.notifyLB');
Route::post('/approval/hfa/po/{po}/createInvoice', 'InvoiceController@createInvoice')->name('invoice.createInvoice');
Route::post('/approval/hfa/po/{po}/uploadSignature', 'ParcelsPTController@approvePOUploadSignature')->name('approval.uploadPOSignature');
Route::post('/approval/hfa/po/{po}/uploadSignatureComments', 'ParcelsPTController@approvePOUploadSignatureComments')->name('approval.uploadPOSignatureComments');

Route::post('/approval/hfa/invoice/{invoice}/approve', 'InvoiceController@approveInvoiceWithRequest')->name('invoice.approve');
Route::post('/approval/hfa/invoice/{invoice}/removeApprover', 'InvoiceController@removeApprover')->name('invoice.removeApprover');
Route::post('/approval/lb/invoice/{invoice}/addLBApprover', 'InvoiceController@invoiceAddLBApprover')->name('invoice.addLBApprover');
Route::post('/approval/hfa/invoice/{invoice}/addHFAApprover', 'InvoiceController@invoiceAddHFAApprover')->name('invoice.addHFAApprover');
Route::post('/approval/hfa/invoice/{invoice}/decline', 'InvoiceController@declineInvoice')->name('invoice.decline');
Route::post('/approval/lb/invoice/{invoice}/submit', 'InvoiceController@submitInvoice')->name('invoice.submitInvoice');
Route::post('/approval/hfa/invoice/{invoice}/sendForPayment', 'InvoiceController@sendForPayment')->name('invoice.sendForPayment');
Route::post('/approval/lb/invoice/{invoice}/uploadLBSignature', 'InvoiceController@approveInvoiceUploadSignature')->name('approval.uploadInvoiceSignature');
Route::post('/approval/lb/invoice/{invoice}/uploadLBSignatureComments', 'InvoiceController@approveInvoiceUploadSignatureComments')->name('approval.uploadInvoiceSignatureComments');

Route::post('/approval/hfa/disposition_invoice/{invoice}/submit', 'DispositionController@submitInvoice')->name('disposition_invoice.submitInvoice');
Route::post('/approval/hfa/disposition_invoice/{invoice}/addApprover', 'DispositionController@addHFAApproverToInvoice')->name('disposition_invoice.addApprover');
Route::post('/approval/hfa/disposition_invoice/{invoice}/sendForPayment', 'DispositionController@sendForPayment')->name('disposition_invoice.sendForPayment');
// Transactions
Route::get('/modals/transaction/newFromInvoice/{invoice}', 'TransactionController@createTransactionFromInvoice');
Route::post('/transaction/create', 'TransactionController@saveTransaction');
Route::post('/transaction/{transaction}/delete', 'TransactionController@deleteTransaction')->name('transaction.delete');
Route::get('/modals/transaction/newFromDispositionInvoice/{invoice}', 'TransactionController@createTransactionFromDispositionInvoice');
Route::get('/modals/transaction/balance-credit', 'TransactionController@transactionBalanceCredit');
Route::get('/modals/transaction/balance-debit', 'TransactionController@transactionBalanceDebit');
Route::get('/modals/transaction/funding-award', 'TransactionController@transactionFundingAward');
Route::get('/modals/transaction/funding-reduction', 'TransactionController@transactionFundingReduction');
Route::get('/modals/transaction/landbank-credit', 'TransactionController@transactionLandbankCredit');
Route::get('/transactions/landbank-credit/options-1', 'TransactionController@landbankRecaptureInvoiceOptions');
Route::get('/transactions/landbank-credit/options-2', 'TransactionController@landbankDispositionInvoiceOptions');
Route::get('/transactions/landbank-credit/options-3', 'TransactionController@landbankReimbursedParcelOptions');
Route::get('/transactions/landbank-credit/options-4', 'TransactionController@landbankGeneralAccountOptions');

// Recaptures
Route::post('/recapture_invoices/{invoice}/recaptures', 'RecaptureController@getRecapturesFromInvoiceId');
Route::get('/recapture_invoice/{invoice}', 'RecaptureController@viewInvoice');
Route::post('/recapture_invoice/{invoice}/uploadSignature', 'RecaptureController@approveInvoiceUploadSignature')->name('recapture_invoice.uploadSignature');
Route::post('/recapture_invoice/{invoice}/uploadSignatureComments', 'RecaptureController@approveInvoiceUploadSignatureComments')->name('recapture_invoice.uploadSignatureComments');
Route::post('/recapture_invoice/{invoice}/submitForApproval', 'RecaptureController@submitForApproval')->name('recapture_invoice.submitForApproval');
Route::post('/approval/hfa/recapture_invoice/{invoice}/submit', 'RecaptureController@submitInvoice')->name('recapture_invoice.submitInvoice');
Route::post('/approval/hfa/recapture_invoice/{invoice}/addApprover', 'RecaptureController@addApprover')->name('recapture_invoice.addApprover');
Route::post('/approval/hfa/recapture_invoice/{invoice}/sendForPayment', 'RecaptureController@sendForPayment')->name('recapture_invoice.sendForPayment');
Route::post('/approval/hfa/recapture_invoice/{invoice}/approve', 'RecaptureController@approveInvoice')->name('recapture_invoice.approve');
Route::post('/approval/hfa/recapture_invoice/{invoice}/decline', 'RecaptureController@declineInvoice')->name('recapture_invoice.decline');
Route::post('/approval/hfa/recapture_invoice/{invoice}/removeApprover', 'RecaptureController@removeApprover')->name('recapture_invoice.removeApprover');
Route::post('/recapture_invoices/{invoice}/newnote', 'RecaptureController@newNoteEntry')->name('recapture_invoicenote.create');
Route::post('/recapture_invoice/{invoice}/removeRecapture', 'RecaptureController@removeRecaptureFromInvoice')->name('recapture_invoice.removeRecapture');
Route::get('/modals/transaction/newFromRecaptureInvoice/{invoice}', 'TransactionController@createTransactionFromRecaptureInvoice');
Route::get('/recaptures/{parcel}/{recapture?}/{format?}', 'RecaptureController@getRecapturesFromParcelId');
Route::get('/modals/breakout_item/recapture/{parcel}/{costid}', 'RecaptureController@breakoutViewRecapture');
Route::post('/recapture/create/{costitem}', 'RecaptureController@saveRecapture');
Route::get('/modals/recapture/{recapture}/edit', 'RecaptureController@editRecapture');
Route::post('/recapture/{recapture}/save', 'RecaptureController@updateRecapture');
Route::post('/recapture/{recapture}/delete', 'RecaptureController@deleteRecapture')->name('recapture.delete');


// Site visits
Route::get('/sitevisits/{parcel}', 'SiteVisitController@sitevisitstab');
Route::post('/sitevisits/{parcel}/{sitevisit}/savedate', 'SiteVisitController@saveDate')->name('site_visit.saveDate');

// Parcels
Route::get('/viewparcel/{parcel}/{subtab?}', 'ParcelsPTController@viewParcel');
// Vendors
Route::get('/viewvendor/{vendor}', 'ReportsController@viewVendor');
// Guides
Route::post('/guide/parcel/{parcel}/validatehfa', 'ParcelsController@guide_validate_parcel_info_hfa')->name('parcel.guide.validateparcelhfa');
Route::post('/guide/parcel/{parcel}/markretainagepaidhfa', 'ParcelsController@guide_mark_retainage_paid_hfa')->name('parcel.guide.markretainagepaidhfa');
Route::post('/guide/parcel/{parcel}/markadvancepaidhfa', 'ParcelsController@guide_mark_advance_paid_hfa')->name('parcel.guide.markadvancepaidhfa');
// email history tab
Route::get('/modals/email_history/{id}', 'PagesController@viewFullEmail');
Route::post('/emails/search', 'PagesController@searchEmails')->name('emails.search');

// system messages
Route::post('/system_messages/exportPaidParcels', 'PagesController@exportPaidParcels');


// Tim Taylor Routes
Route::get('/testgates/{gatetype}', 'PagesController@testgates');
Route::get('/testgates/{gatetype}/{p1}', 'PagesController@testgates');
Route::get('/testgates/{gatetype}/{p1}/{p2}', 'PagesController@testgates');
Route::get('register/verify/{token}', 'Auth\RegisterController@verify');
Route::get('/user/activate/{userId}', 'PagesController@userActivate');
Route::get('/user/deactivate/{userId}', 'PagesController@userDeactivate');
Route::get('/user/quick_delete/{userId}', 'PagesController@userQuickDelete');
Route::get('/user/quick_activate/{userId}', 'PagesController@userQuickActivate');
//Route::get('/viewlog/{logtype}','PagesController@viewLog');
Route::get('/viewlogjson/{logtype}/{start}/{count}', 'PagesController@viewLogJson');
Route::post('/searchlogjson/{logtype}/{start}/{count}', 'PagesController@searchLogJson');
Route::get('/testaddlog/{type}/{userid}/{message}', 'PagesController@testAddLog');
Route::get('/dashboard/activity_logs', 'PagesController@activityLogs');

// Ken Jackson Routes

// Brian G Routes
Route::get('/history/parcel/{parcel}', 'bgHistoryController@parcelHistory');
Route::get('/parcels/parcel-lookup', 'ParcelsController@quickLookup');
Route::get('/parcels/parcel-autocomplete', 'ParcelsController@autocomplete');
Route::get('/modals/transaction/edit/{transaction}/{reload?}', 'AccountingController@editTransaction');
Route::post('/modals/transaction/edit/{transaction}', 'AccountingController@saveTransaction');
Route::get('/change_to_validate/{parcel}', 'ParcelsController@changeToValidate');
Route::get('/toggle_street_view_match/{parcel}', 'ParcelsController@toggleSteetViewMatch');
Route::get('/toggle_pretty/{parcel}', 'ParcelsController@togglePretty');
Route::get('/toggle_ugly/{parcel}', 'ParcelsController@toggleUgly');
Route::post('/parcels/retainage/store/{parcel}', 'ParcelsController@storeRetainage');
Route::post('/parcels/retainage/remove/{retainage}', 'ParcelsController@removeRetainage');
Route::post('/parcels/retainage/pay/{retainage}', 'ParcelsController@payRetainage');
Route::get('/parcels/delete/{parcel}', 'ParcelsController@deleteParcel');
Route::get('/parcels/reassign/{parcel}', 'ParcelsController@reassignParcel');
Route::get('/parcels/export', 'PagesController@export');
Route::get('/dyanmic/images/{image}', 'HomeController@imageGen');

Route::get('/notices/new', 'NoticeController@newNotice');
Route::post('/notices/new', 'NoticeController@createNotice');
Route::get('/notices/unread', 'NoticeController@unreadNotice');
Route::post('/notices/read/{notice}', 'NoticeController@readNotice');
Route::get('/modals/devices/users', 'SiteVisitController@deviceUsers');
Route::get('/modals/wipe_device/{device}', 'SiteVisitController@wipeDevice');
Route::get('/modals/site_visit/{site_visit}', 'SiteVisitController@viewVisit');
Route::get('/images/files/{file}', 'SiteVisitController@serveImages')
            ->where(['file'=>'.*']);
Route::get('/notices/all', 'NoticeController@allNotice');
Route::get('/notices/images/{notice}', 'HomeController@NoticeImageTrack');
*/

    Route::group(['prefix'=>'poc','namespace'=>'POC'], function () {

        Route::get('auth', 'AuthIndexController@index');
        Route::post('auth', 'AuthIndexController@store');
        Route::get('auth/second-factor', 'AuthSecondFactorController@index');
        Route::get('auth/second-factor/create', 'AuthSecondFactorController@create');
        Route::post('auth/second-factor', 'AuthSecondFactorController@store');
        Route::get('auth/logout', 'AuthLogoutController@destroy'); // @todo: CRUDify this

        Route::get('api-test', 'ApiTestController@index');

        Route::get('universal-header', 'UniversalHeaderController@index');
        Route::get('universal-header/hosted.js', function () {
            app('debugbar')->disable();
            return \view('poc.universal-header.hosted');
        });

        Route::get('devco-root-authenticate', function () {
            $service = new \App\Services\AuthService;
            return $service->rootAuthenticate();
        });

        // POC routes for 2FA using Twilio SMS, voice and fax
        Route::get('tfa/makecall', 'TwoFAController@makeVoiceCall');
    
        Route::post('tfa/getsms', 'TwoFAController@getsms')->name('device.receive.sms');
        Route::post('tfa/getsms/failed', 'TwoFAController@getsmsfailed');
        Route::post('tfa/getvoice', 'TwoFAController@getvoice')->name('device.receive.voice');
        Route::post('tfa/getvoice/response', 'TwoFAController@getvoiceresponse')->name('device.voice.response');
        Route::post('tfa/getvoice/failed', 'TwoFAController@getvoicefailed');
        Route::get('tfa/faxpdf/{code}', 'TwoFAController@generateFaxPdf')->name('device.create.fax.pdf');
        Route::get('tfa/{resend?}', 'TwoFAController@index')->name('device.code.check.form');
        Route::post('tfa_post', 'TwoFAController@validateSMSCode')->name('device.code.check');

        // POC routes for new UI
    });
