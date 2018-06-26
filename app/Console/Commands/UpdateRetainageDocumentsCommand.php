<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Retainage;
use App\Document;

/**
 * UpdateRetainageDocuments Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class UpdateRetainageDocumentsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'associate:retainagedocs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Associate retainage docs to paid retainages.';

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
        $retainageDocs = Document::where('categories', 'like', '%"9"%')->orWhere('categories', 'like', '%[9]%')->get();
        $i=0;
        $this->line('Ensuring all retainage docs are associated to their retainage.'.PHP_EOL);

        foreach ($retainageDocs as $doc) {

            //get matching retainage:
            $retainage = Retainage::where('parcel_id', $doc->parcel_id)->first();
                
            if ($retainage) {
                $check = $retainage->documents()->where('document_id', $doc->id)->first();
                if (count($check)<1) {
                    $this->line('Found unrelated retainage doc. Retainage:'.$retainage->id.' with a doc id:'.$doc->id);
                    $retainage->documents()->attach($doc->id);
                    //Get the parcel
                    $data = \App\Parcel::find($doc->parcel_id);
                    perform_all_parcel_checks($data);
                    guide_next_pending_step(2, $data->id);
                }
            } else {
                $i++;
                $data = \App\Parcel::find($doc->parcel_id);
                $this->line(PHP_EOL.$i.' !!!!! Found retainage doc with no Retainage on Parcel: '.$data->parcel_id.' / System id: '.$doc->parcel_id.' with a doc id:'.$doc->id.PHP_EOL);
            }
        }
    }
}
