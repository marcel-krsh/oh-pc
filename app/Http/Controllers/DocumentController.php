<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Http\Request;
use Gate;
use Auth;
use App\Models\User;
use File;
use Storage;
use DB;
use App\Models\SyncDocuware;
use App\Models\DocumentCategory;
use App\Models\DocumentDocumentCategory;
use App\Models\Project;
use App\Models\Audit;

use App\Services\AuthService;
use App\Services\DevcoService;

class DocumentController extends Controller
{
    public function __construct(Request $request)
    {
        // $this->middleware('auth');
    }

    /**
     * Show the documents' list for a specific parcel.
     *
     * @param  int  $parcel_id
     * @return Response
     */

    public function compare($value1,$value2){
        if($value1 != $value2){
            return false;
        } else {
            return true;
        }
    }

    public function getProjectDocuments(Project $project,Request $request){
        //dd($project);
        $apiConnect = new DevcoService();
        $searchString = null;
        $deviceId = null;
        $deviceName = null;
        $documentList = $apiConnect->getProjectDocuments($project->project_number, $searchString, Auth::user()->id, Auth::user()->email, Auth::user()->name, $deviceId, $deviceName);
        

        //dd($documentList,'Third doc id:'.$documentList->included[2]->id,'Page count:'.$documentList->meta->totalPageCount,'File type of third doc:'.$documentList->included[2]->attributes->fields->DWEXTENSION,'Document Class/Category:'.$documentList->included[2]->attributes->fields->DOCUMENTCLASS,'Userid passed:'. Auth::user()->id,'User email passed:'.Auth::user()->email,'Username Passed:'.Auth::user()->name,'Device id and Device name:'.$deviceId.','.$deviceName);

        // compare the list to what is in the sync table:
            if(count($documentList->included) > 0){
                $currentDocuwareProjectDocs = $documentList->included;
                
                foreach ($currentDocuwareProjectDocs as $cd) {
                    //check if the document is in our database:
                    //dd($cd, $cd->attributes->docId);
                    $checkAD = SyncDocuware::where('docuware_doc_id',$cd->attributes->docId)->first();
                    //dd($checkAD);
                    if(!is_null($checkAD)){
                        // there is a record - check to make sure it hasn't changed

                        // compare mod date:
                        if(strtotime($checkAD->dw_mod_date_time) < strtotime($cd->attributes->fields->DWMODDATETIME)){
                            // update the record - this one is older
                            
                            SyncDocuware::where('id',$checkAD->id)->update([
                                'docuware_doc_id'=>$cd->attributes->docId,
                                'type'=>$cd->attributes->docType,
                                'cabinet_name'=>$cd->attributes->cabinetName,
                                'cabinet_id'=>$cd->attributes->cabinetId,
                                'project_number'=>$cd->attributes->fields->PROJECTNUMBER,
                                'document_class'=>$cd->attributes->fields->DOCUMENTCLASS,
                                'document_description'=>$cd->attributes->fields->DOCUMENTDESCRIPTION,
                                'document_date'=>date("Y-m-d H:i:s", strtotime($cd->attributes->fields->DOCUMENTDATE)),
                                'notes'=>$cd->attributes->fields->NOTES,
                                'email_to'=>$cd->attributes->fields->EMAILTO,
                                'email_from'=>$cd->attributes->fields->EMAILFROM,
                                'email_subject'=>$cd->attributes->fields->EMAILSUBJECT,
                                'image_file_name'=>$cd->attributes->fields->IMAGEFILENAME,
                                'retention_sched_code'=>$cd->attributes->fields->RETENTIONSCHEDCODE,
                                'dw_page_count'=>$cd->attributes->fields->DWPAGECOUNT,
                                'dw_stored_date_time'=>date("Y-m-d H:i:s", strtotime($cd->attributes->fields->DWSTOREDATETIME)),
                                'dw_mod_date_time'=>date("Y-m-d H:i:s", strtotime($cd->attributes->fields->DWMODDATETIME)),
                                'dw_extension'=>$cd->attributes->fields->DWEXTENSION,
                                'dw_flags'=>$cd->attributes->fields->DWFLAGS,
                                'dw_doc_size'=>$cd->attributes->fields->DWDOCSIZE,
                                'dw_flag_sex'=>$cd->attributes->fields->DWFLAGSEX,
                                'project_id'=>$project->id
                                    ]);
                                //dd($doc,$doc->id);
                                if(!is_null($cd->attributes->fields->DOCUMENTCLASS)){
                                    //check if the categories are in the database
                                    $primaryCat = DocumentCategory::where('document_category_name',$cd->attributes->fields->DOCUMENTCLASS)->where('parent_id',0)->first();
                                    if(is_null($primaryCat)){
                                        //needs category entered
                                        $primaryCat = DocumentCategory::create([
                                            'document_category_name'=>$cd->attributes->fields->DOCUMENTCLASS,
                                            'from_docuware'=>1,
                                            'from_allita'=>0,
                                            'parent_id'=>0,
                                            'active'=>1
                                        ]);
                                    }
                                    DocumentDocumentCategory::where('sync_docuware_id',$checkAD->id)->where('docuware_document_class',1)->delete();
                                    DocumentDocumentCategory::create([
                                        'sync_docuware_id'=>$checkAD->id,
                                        'document_category_id'=>$primaryCat->id,
                                        'docuware_document_class'=>1
                                    ]);
                                }
                                if(!is_null($cd->attributes->fields->DOCUMENTDESCRIPTION)){
                                    $secondaryCat = DocumentCategory::where('document_category_name',$cd->attributes->fields->DOCUMENTDESCRIPTION)->where('parent_id',$primaryCat->id)->first();
                                    if(is_null($secondaryCat)){
                                        //needs category entered
                                        $secondaryCat = DocumentCategory::create([
                                            'document_category_name'=>$cd->attributes->fields->DOCUMENTDESCRIPTION,
                                            'from_docuware'=>1,
                                            'parent_id'=>$primaryCat->id,
                                            'active'=>1
                                        ]);
                                    }

                                    DocumentDocumentCategory::where('sync_docuware_id',$checkAD->id)->where('docuware_document_description',1)->delete();
                                    DocumentDocumentCategory::create([
                                        'sync_docuware_id'=>$checkAD->id,
                                        'document_category_id'=>$secondaryCat->id,
                                        'docuware_document_class'=>1
                                    ]);
                                }
                                
                        }




                        /// MISSSING CODE

                    } else {
                        // there is not a record - add it to the document list.
                        $doc = SyncDocuware::create([
                            'docuware_doc_id'=>$cd->attributes->docId,
                            'type'=>$cd->attributes->docType,
                            'cabinet_name'=>$cd->attributes->cabinetName,
                            'cabinet_id'=>$cd->attributes->cabinetId,
                            'project_number'=>$cd->attributes->fields->PROJECTNUMBER,
                            'document_class'=>$cd->attributes->fields->DOCUMENTCLASS,
                            'document_description'=>$cd->attributes->fields->DOCUMENTDESCRIPTION,
                            'document_date'=>date("Y-m-d H:i:s", strtotime($cd->attributes->fields->DOCUMENTDATE)),
                            'notes'=>$cd->attributes->fields->NOTES,
                            'email_to'=>$cd->attributes->fields->EMAILTO,
                            'email_from'=>$cd->attributes->fields->EMAILFROM,
                            'email_subject'=>$cd->attributes->fields->EMAILSUBJECT,
                            'image_file_name'=>$cd->attributes->fields->IMAGEFILENAME,
                            'retention_sched_code'=>$cd->attributes->fields->RETENTIONSCHEDCODE,
                            'dw_page_count'=>$cd->attributes->fields->DWPAGECOUNT,
                            'dw_stored_date_time'=>date("Y-m-d H:i:s", strtotime($cd->attributes->fields->DWSTOREDATETIME)),
                            'dw_mod_date_time'=>date("Y-m-d H:i:s", strtotime($cd->attributes->fields->DWMODDATETIME)),
                            'dw_extension'=>$cd->attributes->fields->DWEXTENSION,
                            'dw_flags'=>$cd->attributes->fields->DWFLAGS,
                            'dw_doc_size'=>$cd->attributes->fields->DWDOCSIZE,
                            'dw_flag_sex'=>$cd->attributes->fields->DWFLAGSEX,
                            'project_id'=>$project->id
                        ]);
                        //dd($doc,$doc->id);
                        if(!is_null($cd->attributes->fields->DOCUMENTCLASS)){
                            //check if the categories are in the database
                            $primaryCat = DocumentCategory::where('document_category_name',$cd->attributes->fields->DOCUMENTCLASS)->where('parent_id',0)->first();
                            if(is_null($primaryCat)){
                                //needs category entered
                                $primaryCat = DocumentCategory::create([
                                    'document_category_name'=>$cd->attributes->fields->DOCUMENTCLASS,
                                    'from_docuware'=>1,
                                    'from_allita'=>0,
                                    'parent_id'=>0,
                                    'active'=>1
                                ]);
                            }
                            DocumentDocumentCategory::where('sync_docuware_id',$doc->id)->where('docuware_document_class',1)->delete();
                            DocumentDocumentCategory::create([
                                'sync_docuware_id'=>$doc->id,
                                'document_category_id'=>$primaryCat->id,
                                'docuware_document_class'=>1
                            ]);
                        }
                        if(!is_null($cd->attributes->fields->DOCUMENTDESCRIPTION)){
                            $secondaryCat = DocumentCategory::where('document_category_name',$cd->attributes->fields->DOCUMENTDESCRIPTION)->where('parent_id',$primaryCat->id)->first();
                            if(is_null($secondaryCat)){
                                //needs category entered
                                $secondaryCat = DocumentCategory::create([
                                    'document_category_name'=>$cd->attributes->fields->DOCUMENTDESCRIPTION,
                                    'from_docuware'=>1,
                                    'parent_id'=>$primaryCat->id,
                                    'active'=>1
                                ]);
                            }

                            DocumentDocumentCategory::where('sync_docuware_id',$doc->id)->where('docuware_document_description',1)->delete();
                            DocumentDocumentCategory::create([
                                'sync_docuware_id'=>$doc->id,
                                'document_category_id'=>$secondaryCat->id,
                                'docuware_document_class'=>1
                            ]);
                        }
                        
                    }

                }

                $documents = SyncDocuware::where('project_id',$project->id)->orderBy('document_class')->orderby('document_description')->get()->all();
            
            } else {
                $documents = null;
            }
            dd($documents);
        

    }
    public function showTabFromParcelId(Parcel $parcel, Request $request)
    {

        // get documents
        $documents = Document::where('parcel_id', $parcel->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // get categories
        // Testing only - TBD: preselect using rules
        $document_categories = DocumentCategory::where('active', '1')->orderby('document_category_name', 'asc')->get();


        // build a list of all categories used for uploaded documents in this parcel
        $categories_used = [];
        // category keys for name reference ['id' => 'name']
        $document_categories_key = [];
        foreach ($document_categories as $document_category) {
            $document_categories_key[$document_category->id] = $document_category->document_category_name;
        }

        if (count($documents)) {
            // create an associative array to simplify category references for each document
            foreach ($documents as $document) {
                $categories = []; // store the new associative array cat id, cat name
                 
                if ($document->categories) {
                    $categories_decoded = json_decode($document->categories, true); // cats used by the doc

                    $categories_used = array_merge($categories_used, $categories_decoded); // merge document categories
                } else {
                    $categories_decoded = [];
                }

                foreach ($document_categories as $document_category) {
                    // sub key for each document's categories for quick reference
                    if (in_array($document_category->id, $categories_decoded)) {
                        $categories[$document_category->id] = $document_category->document_category_name;
                    }
                }
                $document->categoriesarray = $categories;

                // get approved category id in an array
                if ($document->approved) {
                    $document->approved_array = json_decode($document->approved, true);
                } else {
                    $document->approved_array = [];
                }

                // get notapproved category id in an array
                if ($document->notapproved) {
                    $document->notapproved_array = json_decode($document->notapproved, true);
                } else {
                    $document->notapproved_array = [];
                }
            }
        } else {
            $documents = [];
        }

        // 1) Rules give us what documents are required
        // 2) Cost items with expense category id 7 with amount > 1000 requires category #8
        // 3) Combine (1) and (2), this is the categories_needed array of ids
        // 4) Check existing documents, get their ids
        // 5) Get the difference in arrays, this is the pending cats.

        // get required categories based on parcel rules $this->getDocumentCategory(parcelid)
        $categories_needed = $this->getDocumentCategory($parcel->id);

        // categories currently used (documents that are in the database now)
        $categories_used = array_unique($categories_used);

        // based on costs added by the LB user, we need to upload certain categories
        $cost_items_expense_cats = CostItem::where('parcel_id', '=', $parcel->id)
                                        ->where('expense_category_id', '=', 7) // only admin > 1000 requires upload
                                        ->where('amount', '>', 1000)
                                        ->count();
        if ($cost_items_expense_cats) {
            $categories_needed[] = 8; //Reimbursement Support Document
        }

        // list all remaining categories in the pending row
        $pending_categories = array_diff($categories_needed, $categories_used);
        //       $pending_categories = $categories_used;

        if (count($pending_categories)) {
            $pending_categories_list = implode(",", $pending_categories);
        } else {
            $pending_categories_list = '';
        }

        return view('parcels.parcel_documents', compact('parcel', 'documents', 'document_categories', 'pending_categories', 'pending_categories_list', 'document_categories_key'));
    }

    public function editDocument(Document $document)
    {
        $document_categories = DocumentCategory::where('active', '1')->orderby('document_category_name', 'asc')->get();
        $categories_used = [];

        if ($document->categories) {
            $categories_used = json_decode($document->categories, true); // cats used by the doc
        }

        return view('modals.edit-document', compact('document', 'document_categories', 'categories_used'));
    }

    public function saveEditedDocument(Document $document, Request $request)
    {
        $parcel = Parcel::where('id', '=', $document->parcel_id)->first();

        $forminputs = $request->get('inputs');
        parse_str($forminputs, $forminputs);

        if (isset($forminputs['comments'])) {
            $comments = $forminputs['comments'];
        } else {
            $comments = '';
        }

        $user = Auth::user();

        $categories = $request->get('cats');

        if (is_array($categories)) {
            if (count($categories)) {
                $categories_json = json_encode($categories, true);

                if (in_array(47, $categories)) {
                    $is_advance = 1;
                } else {
                    $is_advance = 0;
                }
                if (in_array(9, $categories)) {
                    $is_retainage = 1;
                } else {
                    $is_retainage = 0;
                }

                // update document
                $document->update([
                    "comment" => $comments,
                    "categories" => $categories_json
                ]);

                
                if ($is_retainage) {
                    // if only one retainage in database, then no need to display the modal with the select form
                    if ($parcel->retainages) {
                        if (count($parcel->retainages) == 1) {
                            // assign to retainage
                            $retainage = $parcel->retainages->first();
                            $check = $retainage->documents()->where('document_id', $document->id)->first();
                            if (count($check)<1) {
                                $retainage->documents()->attach($document->id);
                            }
                        } elseif (count($parcel->retainages) == 0) {
                            $is_retainage = 0;
                        }
                    }
                }
                if ($is_advance) {
                    // if only one advance in database, then no need to display the modal with the select form
                    if ($parcel->costItemsWithAdvance) {
                        if (count($parcel->costItemsWithAdvance) == 1) {
                            // assign to cost item
                            $advance = $parcel->costItemsWithAdvance->first();
                            $advance->documents()->attach($document->id);
                        } elseif (count($parcel->costItemsWithAdvance) == 0) {
                            $is_advance = 0;
                        }
                    }
                }

                return 1;
            } else {
                // no category selected
                return 'You must select at least one category!';
            }
        } else {
            return 'You must select at least one category!';
        }

        perform_all_parcel_checks($parcel);
        guide_next_pending_step(2, $parcel->id);
    }

    /**
     * Upload documents to parcel.
     *
     * @param  int  $parcel_id
     * @return Response
     */
    public function upload(Parcel $parcel, Request $request)
    {
        if (app('env') == 'local') {
            app('debugbar')->disable();
        }

        if ($request->hasFile('files')) {
            $files = $request->file('files');
            $file_count = count($files);
            $uploadcount = 0; // counter to keep track of uploaded files
            $document_ids = '';

            $categories = explode(",", $request->get('categories'));
            $categories_json = json_encode($categories, true);

            $user = Auth::user();

            if (in_array(47, $categories)) {
                $is_advance = 1;
            } else {
                $is_advance = 0;
            }
            if (in_array(9, $categories)) {
                $is_retainage = 1;
            } else {
                $is_retainage = 0;
            }

            foreach ($files as $file) {
                // Create filepath
                $folderpath = 'documents/entity_'. $parcel->entity_id . '/program_' . $parcel->program_id . '/parcel_' . $parcel->id . '/';
                
                // sanitize filename
                $characters = [' ','´','`',"'",'~','"','\'','\\','/'];
                $original_filename = str_replace($characters, '_', $file->getClientOriginalName());

                // Create a record in documents table
                $document = new Document([
                    'user_id' => $user->id,
                    'parcel_id' => $parcel->id,
                    'categories' => $categories_json,
                    'filename' => $original_filename
                ]);

                $document->save();

                // Save document ids in an array to return
                if ($document_ids!='') {
                    $document_ids = $document_ids.','.$document->id;
                } else {
                    $document_ids = $document->id;
                }

                // Sanitize filename and append document id to make it unique
                // documents/entity_0/program_0/parcel_0/0_filename.ext
                $filename = $document->id . '_' . $original_filename;
                $filepath = $folderpath . $filename;

                $document->update([
                    'file_path' => $filepath,
                ]);
                
                // store original file
                Storage::put($filepath, File::get($file));

                if ($is_retainage) {
                    // if only one retainage in database, then no need to display the modal with the select form
                    if ($parcel->retainages) {
                        if (count($parcel->retainages) == 1) {
                            // assign to retainage
                            $retainage = $parcel->retainages->first();
                            $retainage->documents()->attach($document->id);
                        } elseif (count($parcel->retainages) == 0) {
                            $is_retainage = 0;
                        }
                    }
                }
                if ($is_advance) {
                    // if only one advance in database, then no need to display the modal with the select form
                    if ($parcel->costItemsWithAdvance) {
                        if (count($parcel->costItemsWithAdvance) == 1) {
                            // assign to cost item
                            $advance = $parcel->costItemsWithAdvance->first();
                            $advance->documents()->attach($document->id);
                        } elseif (count($parcel->costItemsWithAdvance) == 0) {
                            $is_advance = 0;
                        }
                    }
                }
                

                $uploadcount++;
            }
            if ($is_retainage) {
                if ($parcel->retainages) {
                    if (count($parcel->retainages) == 1) {
                        $is_retainage = 0;
                    }
                }
            }
            if ($is_advance) {
                if ($parcel->costItemsWithAdvance) {
                    if (count($parcel->costItemsWithAdvance) == 1) {
                        $is_advance = 0;
                    }
                }
            }

            if ($uploadcount != $file_count) {
                // something went wrong
            }

            perform_all_parcel_checks($parcel);
            guide_next_pending_step(2, $parcel->id);


            $data = [];
            $data['document_ids'] = $document_ids;
            $data['is_retainage'] = $is_retainage;
            $data['is_advance'] = $is_advance;

            return json_encode($data);
        } else {
            // shouldn't happen - UIKIT shouldn't send empty files
            // nothing to do here
        }
    }

    /**
     * Add comments to the documents uploaded.
     *
     * @param  int  $parcel_id
     * @return Response
     */
    public function uploadComment(Parcel $parcel, Request $request)
    {
        if (!$request->get('postvars')) {
            return 'Something went wrong...';
        }

        // get document ids
        $documentids = explode(",", $request->get('postvars'));

        // get comment
        $comment = $request->get('comment');

        if (is_array($documentids) && count($documentids)) {
            foreach ($documentids as $documentid) {
                $document = Document::find($documentid);
                $document->update([
                    'comment' => $comment,
                ]);
                
            }
            return 1;
        } else {
            return 0;
        }
    }

    /**
     * Get document attributes from ids and return JSON.
     *
     * @param  int  $parcel_id
     * @return Response
     */
    public function documentInfo(Parcel $parcel, Request $request)
    {
        if (!$request->get('postvars') || !$request->get('categories')) {
            return 'Something went wrong';
        }

        // get document ids
        $documentids = explode(",", $request->get('postvars'));

        $documents = Document::whereIn('id', $documentids)
            ->orderBy('created_at', 'desc')
            ->get();

        $category_array = $request->get('categories');
        $document_categories = DocumentCategory::whereIn('id', $category_array)->where('active', '1')->orderby('document_category_name', 'asc')->get();

        $document_info_array = [];

        foreach ($documents as $document) {
            $document_info_array[$document->id]['filename'] = $document->filename;
            foreach ($document_categories as $category) {
                $document_info_array[$document->id]['categories'][] = $category->document_category_name;
            }
        }
        
        return $document_info_array;
    }

    /**
     * Delete document.
     *
     * @param  int  $parcel_id
     * @return Response
     */
    public function deleteDocument(Parcel $parcel, Request $request)
    {
        $document_id = $request->get('id');
        $document = Document::find($document_id);

        // remove files
        Storage::delete($document->file_path);
        


        // remove database record
        $document->delete();

        perform_all_parcel_checks($parcel);
        guide_next_pending_step(2, $parcel->id);

        return 1;
    }

    /**
     * Download document.
     *
     * @param  int  $parcel_id
     * @return Response
     */
    public function downloadDocument(Parcel $parcel, Document $document)
    {
        $filepath = $document->file_path;

        if (Storage::exists($filepath)) {
            $file = Storage::get($filepath);
            
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename='.$document->filename);
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: '. Storage::size($filepath));
            
            return $file;
        } else {
            // Error
            exit('Requested file does not exist on our server! '.$filepath);
        }
    }

    /**
     * Approve document.
     *
     * @param  int  $parcel_id
     * @return Response
     */
    public function approveDocument(Parcel $parcel, Request $request)
    {
        if (!$request->get('id') &&  !$request->get('catid')) {
            return 'Something went wrong';
        }

        $catid = $request->get('catid');
        $document = Document::where('id', $request->get('id'))->first();

        // get current approval array (category ids that had their document approved)
        if ($document->approved) {
            $current_approval_array = json_decode($document->approved, true);
        } else {
            $current_approval_array = [];
        }

        // get current 'notapproval' array (category ids that had their document approved)
        if ($document->notapproved) {
            $current_notapproval_array = json_decode($document->notapproved, true);
        } else {
            $current_notapproval_array = [];
        }
        
        // if already "notapproved", remove from notapproved array
        if (in_array($catid, $current_notapproval_array)) {
            unset($current_notapproval_array[array_search($catid, $current_notapproval_array)]);
            $current_notapproval_array = array_values($current_notapproval_array);
            $notapproval = json_encode($current_notapproval_array);

            $document->update([
                'notapproved' => $notapproval,
            ]);
            //TODO: Add event logger
        }

        // if not already approved, add to approved array
        if (!in_array($catid, $current_approval_array)) {
            $current_approval_array[] = $catid;
            $approval = json_encode($current_approval_array);

            $document->update([
                'approved' => $approval,
            ]);
            //TODO: Add event logger
        }

        perform_all_parcel_checks($parcel);
        guide_next_pending_step(2, $parcel->id);

        return 1;
    }

    /**
     * "Not Approve" document.
     *
     * @param  int  $parcel_id
     * @return Response
     */
    public function notApproveDocument(Parcel $parcel, Request $request)
    {
        if (!$request->get('id') &&  !$request->get('catid')) {
            return 'Something went wrong';
        }

        $catid = $request->get('catid');
        $document = Document::where('id', $request->get('id'))->first();
        
        if ($document->approved) {
            $current_approval_array = json_decode($document->approved, true);
        } else {
            $current_approval_array = [];
        }

        // get current 'notapproval' array (category ids that had their document approved)
        if ($document->notapproved) {
            $current_notapproval_array = json_decode($document->notapproved, true);
        } else {
            $current_notapproval_array = [];
        }

        // if already approved, remove from approved array
        if (in_array($catid, $current_approval_array)) {
            unset($current_approval_array[array_search($catid, $current_approval_array)]);
            $current_approval_array = array_values($current_approval_array);
            $approval = json_encode($current_approval_array);
            
            $document->update([
                'approved' => $approval,
            ]);
            //TODO: Add event logger
        }

        // if not already notapproved  --confused yet? :), add to notapproved array
        if (!in_array($catid, $current_notapproval_array)) {
            $current_notapproval_array[] = $catid;
            $notapproval = json_encode($current_notapproval_array);

            $document->update([
                'notapproved' => $notapproval,
            ]);
            //TODO: Add event logger
        }

        perform_all_parcel_checks($parcel);
        guide_next_pending_step(2, $parcel->id);

        return 1;
    }


    /**
     * Retrieve document categories needed based on parcel rules.
     *
     * @param  int  $parcel_id
     * @return Response
     */
    public function getDocumentCategory($id)
    {
        $rid = Parcel::where('id', $id)->pluck('program_rules_id');
        $docRules = DocumentRule::where('program_rules_id', $rid)->pluck('id');
        $docCatIds = DocumentRuleEntry::whereIn('document_rule_id', $docRules)->pluck('document_category_id');
        if (is_array($docCatIds)) {
            return $docCatIds;
        } else {
            return [];
        }
    }

    public function retainageForm(Parcel $parcel, $documentids)
    {
        return view('modals.document-retainage-form', compact('parcel', 'documentids'));
    }

    public function retainageFormSave(Parcel $parcel, Request $request)
    {
        $retainage_ids = $request->get('retainages');
        $document_ids = $request->get('documentids');

        foreach ($retainage_ids as $retainage_id) {
            // check if retainage id belongs to parcel
            $retainage = Retainage::where('id', '=', $retainage_id)->where('parcel_id', '=', $parcel->id)->first();

            // attach document
            if (count($retainage)) {
                foreach ($document_ids as $document_id) {
                    $document = Document::where('parcel_id', '=', $parcel->id)->where('id', '=', $document_id)->first();
                    if (count($document)) {
                        $retainage->documents()->attach($document->id);
                    }
                }
            }
        }
        return 1;
    }

    public function advanceForm(Parcel $parcel, $documentids)
    {
        return view('modals.document-advance-form', compact('parcel', 'documentids'));
    }

    public function advanceFormSave(Parcel $parcel, Request $request)
    {
        $advance_ids = $request->get('advances');
        $document_ids = $request->get('documentids');

        foreach ($advance_ids as $advance_id) {
            // check if advance id belongs to parcel
            $advance = CostItem::where('id', '=', $advance_id)->where('parcel_id', '=', $parcel->id)->first();

            // attach document
            if (count($advance)) {
                foreach ($document_ids as $document_id) {
                    $document = Document::where('parcel_id', '=', $parcel->id)->where('id', '=', $document_id)->first();
                    if (count($document)) {
                        $advance->documents()->attach($document->id);
                    }
                }
            }
        }
        return 1;
    }
}
