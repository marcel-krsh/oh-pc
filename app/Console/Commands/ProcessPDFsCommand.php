<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Document;
use App\DocumentCategory;
use App\DocumentType;
use App\County;
use App\Parcel;
use App\Entity;
use App\Program;
use App\User;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\LogConverter;

/**
 * ProcessPDFs Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class ProcessPDFsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:pdfs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find PDFs and process them';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        // Setup custom logs
        $view_log = new Logger('View Logs');
        $view_log->pushHandler(new StreamHandler('storage/logs/processPDFs.log', Logger::INFO));

        $folders = glob("storage/app/docuware/*/*/");
        $view_log->info('Starting new PDF process');

        $used_categories = [];
        $key_categories = [];

        $categories = DocumentCategory::where('active', '=', 1)->get();
        if (count($categories) < 1) {
            $this->error("NO CATEGORIES!");
        } else {
            $this->line("CATEGORIES!");
        }
        foreach ($categories as $category) {
            $key_categories[$category->document_category_name] = $category->id;
            $this->line("Category ".$category->document_category_name." Processed ".PHP_EOL);
        }

        
        if (count($folders) < 1) {
            $this->error("NO FOLDERS!");
        } else {
            $this->line(count($folders)."FOLDERS!");
        }
        $processBar = $this->output->createProgressBar(count($folders));
        foreach ($folders as $folder) {
            $processBar->advance();
            // The two files in question
            $xml_file = current(glob($folder . "*.xml"));
            $pdf_file = current(glob($folder . "*.pdf"));
            $xlsx_file = current(glob($folder . "*.xlsx"));

            // Read XML into Data.
            
            $view_log->info('Working on folder '.$folder);
            if (file_exists($xml_file)) {
                $view_log->info('/ XML file '.$xml_file.' found');
            } else {
                $view_log->error('/ XML file '.$xml_file.' missing');
            }

            $xml = simplexml_load_string(file_get_contents($xml_file));
            $info = (array) $xml->Section->Metadata->FieldProperties->TextVar;

            if (!isset($info[1])) {
                $view_log->error('/ XML file '.$xml_file.' formatting problem', ['item' => 'type_name']);
            }
            if (!isset($info[2])) {
                $view_log->error('/ XML file '.$xml_file.' formatting problem', ['item' => 'county_name']);
            }
            if (!isset($info[3])) {
                $view_log->error('/ XML file '.$xml_file.' formatting problem', ['item' => 'parcel_id']);
            }
            if (!isset($info[4])) {
                $view_log->error('/ XML file '.$xml_file.' formatting problem', ['item' => 'parcel_name']);
            }
            if (!isset($info[6])) {
                $view_log->error('/ XML file '.$xml_file.' formatting problem', ['item' => 'submitted_by']);
            }

            // needed to add logs
            $adminuser = User::where('id', '=', 1)->first();

            if (isset($info[1])) {
                $this->line(PHP_EOL."Valid XML".PHP_EOL);
                $item = [
                    'ohfa_file'    => $info[0],
                    'type_name'    => $info[1],
                    'county_name'  => ucwords(strtolower($info[2])),
                    'parcel_idA'    => strtoupper($info[3]),
                    'parcel_idB'    => str_replace(" ", "", strtoupper($info[3])),
                    'parcel_idC'    => str_replace("-", "", str_replace(" ", "", strtoupper($info[3]))),
                    'parcel_name'  => $info[4],
                    'upload_date' => date("Y-m-d H:i:s", strtotime($info[5])),
                    'submitted_by' => $info[6]
                ];

                // Get the PDF Type
                $document_type = DocumentCategory::where('document_category_name', $item['type_name'])->first() ?: DocumentCategory::create(['document_category_name' => $item['type_name'], 'hfa' => 1, 'active' => 1]);

                // Get the Parcel, Entity, Program
                $proceed = 1;
                $parcel  = null;
                $entity  = null;
                $program = null;
                $county_name_from_xml = ucfirst(strtolower($item['county_name']));
                $county  = County::where('county_name', 'like', '%'.$county_name_from_xml.'%')->first();
                if ($county) {
                    // $parcel_id_no_dash_no_space = str_replace('-', '', str_replace(' ', '', strtolower($item['parcel_id'])));
                    // $parcel_id_from_xml = [$item['parcel_id'], strtoupper($item['parcel_id']), $parcel_id_no_dash_no_space];
                    //$parcel = Parcel::where('county_id', $county->id)->where('parcel_id', $item['parcel_id'])->first();
                    $parcel = Parcel::where('county_id', $county->id)->where('parcel_id', $item['parcel_idC'])->first();
                    /// See if the parcel was found
                    if (!isset($parcel->id)) {
                        /// No - Try option 2
                        $parcel = Parcel::where('county_id', $county->id)->where('parcel_id', $item['parcel_idB'])->first();
                    } elseif (!isset($parcel->id)) {
                        /// No - Try option 3
                        $parcel = Parcel::where('county_id', $county->id)->where('parcel_id', $item['parcel_idA'])->first();
                    }

                    if (isset($parcel->id)) {
                        $this->line("Parcel ".$parcel->parcel_id." Found.".PHP_EOL);
                        $program = Program::find($parcel->program_id);
                        $entity  = Entity::find($program->entity_id);
                    } else {
                        // move file to another folder for future adjustment
                        //$this->error("ERROR: No Parcel found for  " . $pdf_file);
                        $proceed = 0;
                        if ($pdf_file) {
                            $characters = [' ','´','`',"'",'~','"','\'','\\','/'];
                            $original_filename = str_replace($characters, '_', $item['ohfa_file']);
                            
                            $filename = $original_filename;

                            $new_dir = "storage/app/documents/entity_" .
                                ($entity ? $entity->id : 'NoEntity') . '/program_' .
                                ($program ? $program->id : 'NoProgram') . '/parcel_' .
                                ($parcel ? $parcel->id : 'NoParcel') . '/';

                            $new_path = $new_dir . $filename;

                            if (!file_exists($new_dir)) {
                                mkdir($new_dir, 0777, true);
                            }

                            if (!copy($pdf_file, $new_path)) {
                                $this->error("ERROR: Copying " . $pdf_file);
                                $view_log->error('/ ERROR: Copying ' . $pdf_file);
                            } else {
                                $this->line("[X] NO PARCEL FOR ".$pdf_file.PHP_EOL);
                            }
                            //$this->info("No Parcel found for " . $pdf_file);
                            $view_log->warning('/ No Parcel found for ' . $pdf_file.PHP_EOL);
                        }
                        if ($xlsx_file) {
                            $characters = [' ','´','`',"'",'~','"','\'','\\','/'];
                            $original_filename = str_replace($characters, '_', $item['ohfa_file']);
                            
                            $filename = $original_filename;

                            $new_dir = "storage/app/documents/entity_" .
                                ($entity ? $entity->id : 'NoEntity') . '/program_' .
                                ($program ? $program->id : 'NoProgram') . '/parcel_' .
                                ($parcel ? $parcel->id : 'NoParcel') . '/';

                            $new_path = $new_dir . $filename;

                            if (!file_exists($new_dir)) {
                                mkdir($new_dir, 0777, true);
                            }

                            if (!copy($xlsx_file, $new_path)) {
                                $this->error("ERROR: Copying " . $xlsx_file);
                                $view_log->error('/ ERROR: Copying ' . $xlsx_file.PHP_EOL);
                            } else {
                                $this->line("[X] NO PARCEL FOR ".$xlsx_file.PHP_EOL);
                            }
                            //$this->info("");
                            //$this->info("No Parcel found for " . $xlsx_file);
                            //$view_log->warning('/ No Parcel found for ' . $xlsx_file);
                        }
                    }
                } else {
                    $proceed = 0;
                    $this->error("ERROR: No county found for " . $pdf_file);
                    $view_log->error('/ ERROR: No county found for ' . $pdf_file);
                }

                // Get the User
                $user = null;
                if ($item['submitted_by'] == "SubGrantee") {
                    // user
                }

                $categories_json = json_encode([$document_type->id], true);
                
                // sanitize filename
                $characters = [' ','´','`',"'",'~','"','\'','\\','/'];
                $original_filename = str_replace($characters, '_', $item['ohfa_file']);
            
                // check if document has been processed already
                $parcelid_to_check = $parcel ? $parcel->id : null;
                $check_for_document = Document::where('parcel_id', '=', $parcelid_to_check)
                                                    ->where('user_id', '=', 26)
                                                    ->where('filename', '=', $original_filename)
                                                    ->first();


                if (isset($check_for_document->id)) {
                    // check to see if file path has the full path - if not add it
                    $fullPathCheck = substr($check_for_document->file_path, 0, 12);
                    if ($fullPathCheck != "storage/app/" && strlen($check_for_document->file_path) > 1) {
                        $check_for_document->file_path = "storage/app/".$check_for_document->file_path;
                    } else {
                        $this->line('Full Path Check = '.$fullPathCheck.PHP_EOL);
                    }
                    if (file_exists($check_for_document->file_path)) {
                        if (!unlink($check_for_document->file_path)) {
                            $this->error("COULD NOT DELETE PREVIOUS FILE".$check_for_document->file_path.PHP_EOL.PHP_EOL);
                            $this->error("COULD NOT DELETE PREVIOUS FILE".$check_for_document->file_path.PHP_EOL.PHP_EOL);
                            $this->error("COULD NOT DELETE PREVIOUS FILE".$check_for_document->file_path.PHP_EOL.PHP_EOL);
                            $this->error("COULD NOT DELETE PREVIOUS FILE".$check_for_document->file_path.PHP_EOL.PHP_EOL);
                            $this->error("COULD NOT DELETE PREVIOUS FILE".$check_for_document->file_path.PHP_EOL.PHP_EOL);
                            $this->error("COULD NOT DELETE PREVIOUS FILE".$check_for_document->file_path.PHP_EOL.PHP_EOL);
                        } else {
                            $this->error("DELETED PREVIOUS FILE".PHP_EOL);
                        }
                    } else {
                        $this->line('Could not find the file at '.$check_for_document->file_path);
                    }
                    $check_for_document->delete();
                    $this->error("DELETED PREVIOUS ENTRY".PHP_EOL);
                }

                if ($proceed) {
                    $document = new Document([
                        'parcel_id'        => $parcel ? $parcel->id : null,
                        'user_id'          => 26,
                        'categories'        => $categories_json,
                        'filename'          => $original_filename,
                        'comment'           => "Imported from Docuware on ".date("m/d/Y", time()).". Document is assumed to be approved.",
                        'approved'          => $categories_json
                    ]);

                    $document->save();
                    Document::where('id', $document->id)->update(['created_at'=> $item['upload_date']]);

                    $this->line('[+] ADDED '.$original_filename.' TO DB'.PHP_EOL);

                    // Copy the file to new directory.
                    if ($pdf_file) {
                        $filename = $document->id . '_' . $original_filename;

                        //$new_dir = "storage/app/documents/entity_ToReview" . '/';
                        $new_dir = "documents/entity_" .
                            ($entity ? $entity->id : 'NoEntity') . '/program_' .
                            ($program ? $program->id : 'NoProgram') . '/parcel_' .
                            ($parcel ? $parcel->id : 'NoParcel') . '/';

                        $new_path = $new_dir . $filename;

                        if (!file_exists("storage/app/".$new_dir)) {
                            mkdir("storage/app/".$new_dir, 0777, true);
                            $this->line('Directory did not exist - Made it: '.$new_dir.PHP_EOL);
                        }
                        if (!file_exists("storage/app/".$new_dir)) {
                            $this->error("Failed to create directory.");
                        }

                        if (!copy($pdf_file, "storage/app/".$new_path)) {
                            $this->error("ERROR: Copying " . $pdf_file);
                        } else {
                            //echo ".";
                        }

                        // Update Document path
                        $document->update(['file_path' => $new_path]);

                        $lc = new LogConverter('command', 'processpdfs');
                        if ($parcel->id) {
                            $lc->setFrom($adminuser)->setTo($parcel)->setDesc('Processed '.$new_path)->save();
                        }
                    }
                    if ($xlsx_file) {
                        $filename = $document->id . '_' . $original_filename;

                        // $new_dir = "storage/app/documents/entity_ToReview" . '/';
                        $new_dir = "documents/entity_" .
                            ($entity ? $entity->id : 'NoEntity') . '/program_' .
                            ($program ? $program->id : 'NoProgram') . '/parcel_' .
                            ($parcel ? $parcel->id : 'NoParcel') . '/';

                        $new_path = $new_dir . $filename;

                        //$this->info("XLSX FILE:  " . $xlsx_file);

                        if (!file_exists("storage/app/".$new_dir)) {
                            mkdir("storage/app/".$new_dir, 0777, true);
                            $this->line('Directory did not exist - Made it: '.$new_dir.PHP_EOL);
                        }
                        if (!file_exists("storage/app/".$new_dir)) {
                            $this->error("Failed to create directory.");
                        }


                        if (!copy($xlsx_file, "storage/app/".$new_path)) {
                            $this->error("ERROR: Copying " . $xlsx_file);
                        } else {
                            //echo ".";
                        }

                        // Update Document path
                        $document->update(['file_path' => $new_path]);

                        $lc = new LogConverter('command', 'processpdfs');
                        if ($parcel->id) {
                            $lc->setFrom($adminuser)->setTo($parcel)->setDesc('Processed '.$new_path)->save();
                        }
                    }
                }
            } else {
                // problem with xml file $xml_file
                $this->error("ERROR: Problem with XML file " . $xml_file);

                // move files to a separate folder for review
                if ($pdf_file) {
                    $filename = $original_filename;
                    $entityName = substr($original_filename, 5, 4);
                    $new_dir = "storage/app/documents/".$entityName."_ToReview" . '/';

                    $new_path = $new_dir . $filename;

                    if (!file_exists($new_dir)) {
                        mkdir($new_dir, 0777, true);
                        $this->line('Directory did not exist - Made it: '.$new_dir.PHP_EOL);
                    }
                    if (!file_exists($new_dir)) {
                        $this->error("Failed to create directory.");
                    }

                    if (!copy($pdf_file, $new_path)) {
                        $this->error("ERROR: Copying " . $pdf_file);
                    } else {
                        //echo ".";
                    }
                }
                if ($xlsx_file) {
                    $filename = $original_filename;

                    $entityName = substr($original_filename, 5, 4);
                    $new_dir = "storage/app/documents/".$entityName."_ToReview" . '/';

                    $new_path = $new_dir . $filename;

                    //$this->info("XLSX FILE:  " . $xlsx_file);

                    if (!file_exists($new_dir)) {
                        mkdir($new_dir, 0777, true);
                        $this->line('Directory did not exist - Made it: '.$new_dir.PHP_EOL);
                    }
                    if (!file_exists($new_dir)) {
                        $this->error("Failed to create directory.");
                    }

                    if (!copy($xlsx_file, $new_path)) {
                        $this->error("ERROR: Copying " . $xlsx_file);
                    } else {
                        echo ".";
                    }
                }
            }
        }

        $this->info("Processed " . count($folders) . " folders");
    }
}
