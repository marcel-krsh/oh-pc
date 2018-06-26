<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Parcel;
use DB;

class FixMapLinksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:maps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix legacy map links to be just the link. ';

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
        $parcels = Parcel::where('google_map_link', "<>", null)->where('sf_parcel_id', "<>", null)->select('id', 'google_map_link')->get();
        foreach ($parcels as $data) {
            $start = 9;
            $end = strpos($data->google_map_link, '" target') -9;
            $actualLink= substr($data->google_map_link, $start, $end);
            $this->line(PHP_EOL.'Updating parcel id '.$data->id.' with link '.$actualLink);
            Parcel::where('id', $data->id)->update(['google_map_link'=>$actualLink]);
        }
    }
}
