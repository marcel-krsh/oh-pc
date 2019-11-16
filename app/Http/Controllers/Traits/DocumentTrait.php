<?php

namespace App\Http\Controllers\Traits;

use App\Models\DocumentCategory;
use App\Models\DocumentDocumentCategory;
use App\Models\SyncDocuware;
use App\Models\User;
use App\Services\DevcoService;
use Auth;

trait DocumentTrait
{
    public function projectDocuwareDocumets($project)
    {

        //return $documents = SyncDocuware::where('project_id', $project->id)->orderBy('document_class')->orderby('document_description')->get();

        $apiConnect = new DevcoService();
        $searchString = null;
        $deviceId = null;
        $deviceName = null;
        $documentList = $apiConnect->getProjectDocuments($project->project_number, $searchString, Auth::user()->id, Auth::user()->email, Auth::user()->name, $deviceId, $deviceName);
        $primaryCat = null;

        //dd($documentList,'Third doc id:'.$documentList->included[2]->id,'Page count:'.$documentList->meta->totalPageCount,'File type of third doc:'.$documentList->included[2]->attributes->fields->DWEXTENSION,'Document Class/Category:'.$documentList->included[2]->attributes->fields->DOCUMENTCLASS,'Userid passed:'. Auth::user()->id,'User email passed:'.Auth::user()->email,'Username Passed:'.Auth::user()->name,'Device id and Device name:'.$deviceId.','.$deviceName);

        // compare the list to what is in the sync table:
        if ($documentList && property_exists($documentList, 'included') && count($documentList->included) > 0) {
            $currentDocuwareProjectDocs = $documentList->included;

            foreach ($currentDocuwareProjectDocs as $cd) {
                //check if the document is in our database:
                //dd($cd, $cd->attributes->docId);
                $checkAD = SyncDocuware::where('docuware_doc_id', $cd->attributes->docId)->first();
                //dd($checkAD);
                if (! is_null($checkAD)) {
                    // there is a record - check to make sure it hasn't changed

                    // compare mod date:
                    if (strtotime($checkAD->dw_mod_date_time) < strtotime($cd->attributes->fields->DWMODDATETIME)) {
                        // update the record - this one is older

                        SyncDocuware::where('id', $checkAD->id)->update([
                            'docuware_doc_id' => $cd->attributes->docId,
                            'type' => $cd->attributes->docType,
                            'cabinet_name' => $cd->attributes->cabinetName,
                            'cabinet_id' => $cd->attributes->cabinetId,
                            'project_number' => $cd->attributes->fields->PROJECTNUMBER,
                            'document_class' => $cd->attributes->fields->DOCUMENTCLASS,
                            'document_description' => $cd->attributes->fields->DOCUMENTDESCRIPTION,
                            'document_date' => date('Y-m-d H:i:s', strtotime($cd->attributes->fields->DOCUMENTDATE)),
                            'notes' => $cd->attributes->fields->NOTES,
                            'email_to' => $cd->attributes->fields->EMAILTO,
                            'email_from' => $cd->attributes->fields->EMAILFROM,
                            'email_subject' => $cd->attributes->fields->EMAILSUBJECT,
                            'image_file_name' => $cd->attributes->fields->IMAGEFILENAME,
                            'retention_sched_code' => $cd->attributes->fields->RETENTIONSCHEDCODE,
                            'dw_page_count' => $cd->attributes->fields->DWPAGECOUNT,
                            'dw_stored_date_time' => date('Y-m-d H:i:s', strtotime($cd->attributes->fields->DWSTOREDATETIME)),
                            'dw_mod_date_time' => date('Y-m-d H:i:s', strtotime($cd->attributes->fields->DWMODDATETIME)),
                            'dw_extension' => $cd->attributes->fields->DWEXTENSION,
                            'dw_flags' => $cd->attributes->fields->DWFLAGS,
                            'dw_doc_size' => $cd->attributes->fields->DWDOCSIZE,
                            'dw_flag_sex' => $cd->attributes->fields->DWFLAGSEX,
                            'project_id' => $project->id,
                        ]);
                        //dd($doc,$doc->id);
                        if (! is_null($cd->attributes->fields->DOCUMENTCLASS)) {
                            //check if the categories are in the database
                            $primaryCat = DocumentCategory::where('document_category_name', $cd->attributes->fields->DOCUMENTCLASS)->where('parent_id', 0)->first();
                            if (is_null($primaryCat)) {
                                //needs category entered
                                $primaryCat = DocumentCategory::create([
                                    'document_category_name' => $cd->attributes->fields->DOCUMENTCLASS,
                                    'from_docuware' => 1,
                                    'from_allita' => 0,
                                    'parent_id' => 0,
                                    'active' => 1,
                                ]);
                            }
                            DocumentDocumentCategory::where('sync_docuware_id', $checkAD->id)->where('docuware_document_class', 1)->delete();
                            DocumentDocumentCategory::create([
                                'sync_docuware_id' => $checkAD->id,
                                'document_category_id' => $primaryCat->id,
                                'docuware_document_class' => 1,
                            ]);
                        }
                        if (! is_null($cd->attributes->fields->DOCUMENTDESCRIPTION)) {
                            $secondaryCat = DocumentCategory::where('document_category_name', $cd->attributes->fields->DOCUMENTDESCRIPTION)->where('parent_id', $primaryCat->id)->first();
                            if (is_null($secondaryCat)) {
                                //needs category entered
                                $secondaryCat = DocumentCategory::create([
                                    'document_category_name' => $cd->attributes->fields->DOCUMENTDESCRIPTION,
                                    'from_docuware' => 1,
                                    'parent_id' => $primaryCat->id,
                                    'active' => 1,
                                ]);
                            }

                            DocumentDocumentCategory::where('sync_docuware_id', $checkAD->id)->where('docuware_document_description', 1)->delete();
                            DocumentDocumentCategory::create([
                                'sync_docuware_id' => $checkAD->id,
                                'document_category_id' => $secondaryCat->id,
                                'docuware_document_class' => 1,
                            ]);
                        }
                    }
                } else {
                    // there is not a record - add it to the document list.
                    $doc = SyncDocuware::create([
                        'docuware_doc_id' => $cd->attributes->docId,
                        'type' => $cd->attributes->docType,
                        'cabinet_name' => $cd->attributes->cabinetName,
                        'cabinet_id' => $cd->attributes->cabinetId,
                        'project_number' => $cd->attributes->fields->PROJECTNUMBER,
                        'document_class' => $cd->attributes->fields->DOCUMENTCLASS,
                        'document_description' => $cd->attributes->fields->DOCUMENTDESCRIPTION,
                        'document_date' => date('Y-m-d H:i:s', strtotime($cd->attributes->fields->DOCUMENTDATE)),
                        'notes' => $cd->attributes->fields->NOTES,
                        'email_to' => $cd->attributes->fields->EMAILTO,
                        'email_from' => $cd->attributes->fields->EMAILFROM,
                        'email_subject' => $cd->attributes->fields->EMAILSUBJECT,
                        'image_file_name' => $cd->attributes->fields->IMAGEFILENAME,
                        'retention_sched_code' => $cd->attributes->fields->RETENTIONSCHEDCODE,
                        'dw_page_count' => $cd->attributes->fields->DWPAGECOUNT,
                        'dw_stored_date_time' => date('Y-m-d H:i:s', strtotime($cd->attributes->fields->DWSTOREDATETIME)),
                        'dw_mod_date_time' => date('Y-m-d H:i:s', strtotime($cd->attributes->fields->DWMODDATETIME)),
                        'dw_extension' => $cd->attributes->fields->DWEXTENSION,
                        'dw_flags' => $cd->attributes->fields->DWFLAGS,
                        'dw_doc_size' => $cd->attributes->fields->DWDOCSIZE,
                        'dw_flag_sex' => $cd->attributes->fields->DWFLAGSEX,
                        'project_id' => $project->id,
                    ]);
                    //dd($doc,$doc->id);
                    if (! is_null($cd->attributes->fields->DOCUMENTCLASS)) {
                        //check if the categories are in the database
                        $primaryCat = DocumentCategory::where('document_category_name', $cd->attributes->fields->DOCUMENTCLASS)->where('parent_id', 0)->first();
                        if (is_null($primaryCat)) {
                            //needs category entered
                            $primaryCat = DocumentCategory::create([
                                'document_category_name' => $cd->attributes->fields->DOCUMENTCLASS,
                                'from_docuware' => 1,
                                'from_allita' => 0,
                                'parent_id' => 0,
                                'active' => 1,
                            ]);
                        }
                        DocumentDocumentCategory::where('sync_docuware_id', $doc->id)->where('docuware_document_class', 1)->delete();
                        DocumentDocumentCategory::create([
                            'sync_docuware_id' => $doc->id,
                            'document_category_id' => $primaryCat->id,
                            'docuware_document_class' => 1,
                        ]);
                    }
                    if (! is_null($cd->attributes->fields->DOCUMENTDESCRIPTION) && ! is_null($primaryCat)) {
                        $secondaryCat = DocumentCategory::where('document_category_name', $cd->attributes->fields->DOCUMENTDESCRIPTION)->where('parent_id', $primaryCat->id)->first();
                        if (is_null($secondaryCat)) {
                            //needs category entered
                            $secondaryCat = DocumentCategory::create([
                                'document_category_name' => $cd->attributes->fields->DOCUMENTDESCRIPTION,
                                'from_docuware' => 1,
                                'parent_id' => $primaryCat->id,
                                'active' => 1,
                            ]);
                        }

                        DocumentDocumentCategory::where('sync_docuware_id', $doc->id)->where('docuware_document_description', 1)->delete();
                        DocumentDocumentCategory::create([
                            'sync_docuware_id' => $doc->id,
                            'document_category_id' => $secondaryCat->id,
                            'docuware_document_class' => 1,
                        ]);
                    }
                }
            }

            $documents = SyncDocuware::where('project_id', $project->id)->orderBy('document_class')->orderby('document_description')->get();
        } else {
            $documents = null;
        }

        return $documents;
    }
}
