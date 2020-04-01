<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\CachedAudit;
use App\Models\AuditRequiredDocument;
use App\Models\DocumentCategory;

class AddRequiredDocuments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:audit_required_documents';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Goes in and adds document requirements to all existing audits';

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
     * @return mixed
     */
    public function handle()
    {
        //get required document categories
        
        $continue = 0;

        if($this->confirm('Would you like to update this for all audits? If no, we will ask you for a starting date.')){
            $date = ('2000-01-01');
            $continue = 1;
        }else{
            $date = $this->ask('Please enter a starting schedule date including the year (example "3/1/2020")');
            $date = date('Y-m-d',strtotime($date));
            //dd($date);
            if($this->confirm('You entered the date: '.date('M d, Y',strtotime($date)))){
                $continue = 1;
            }else{
                $this->line('Sorry - please try running the command again, and make sure you format your date correctly');
            }
        }

        if($continue){
            $siteRequired = DocumentCategory::where('required_for_site',1)->pluck('id')->toArray();
            $binRequired = DocumentCategory::where('required_for_bin', 1)->pluck('id')->toArray();
            $unitRequired = DocumentCategory::where('required_for_unit', 1)->pluck('id')->toArray();

            // get audit ids
            $audits = CachedAudit::where('inspection_schedule_date','>=',$date)->pluck('audit_id')->toArray();

            // add to the audits
            $this->line('Updating Audits With Missing Document Requirements');
            $progress = $this->output->createProgressBar(count($audits));
            foreach ($audits as $a) {
                // Site
                foreach ($siteRequired as $docId) {
                    //check it is not already there
                    $check = AuditRequiredDocument::where('audit_id',$a)->where('document_category_id',$docId)->count();
                    # code...
                    if(!$check){
                        AuditRequiredDocument::insert(['audit_id'=>$a, 'document_category_id' => $docId, 'site_level'=>1, 'description' => 'DEFAULT']);
                    }
                }
                // Bin
                foreach ($binRequired as $docId) {
                    //check it is not already there
                    $check = AuditRequiredDocument::where('audit_id',$a)->where('document_category_id',$docId)->count();
                    # code...
                    if(!$check){
                        AuditRequiredDocument::insert(['audit_id'=>$a, 'document_category_id' => $docId, 'bin_level'=>1, 'description' => 'DEFAULT']);
                    }
                }
                // Unit
                foreach ($unitRequired as $docId) {
                    //check it is not already there
                    $check = AuditRequiredDocument::where('audit_id',$a)->where('document_category_id',$docId)->count();
                    # code...
                    if(!$check){
                        AuditRequiredDocument::insert(['audit_id'=>$a, 'document_category_id' => $docId, 'unit_level'=>1, 'description' => 'DEFAULT']);
                    }
                }
                # code...
                $progress->advance();
                //dd('check');
            }
        }



    }
}
