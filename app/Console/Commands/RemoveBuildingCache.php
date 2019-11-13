<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Audit;
use App\Models\CachedAudit;
use App\Models\BuildingInspection;
use App\Models\AmenityInspection;
use App\Models\UnitInspection;
use App\Models\CachedBuilding;
use App\Models\CachedUnit;

class RemoveBuildingCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete:building-caches';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove a building, its units, the associated findings, inspections, and caches from an audit.';

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
    public function getAudit(){
        $auditInput = $this->ask('What audit number is this for?');
        $audit = Audit::find($auditInput);
        if($audit){
            
            $this->line(PHP_EOL.$audit->project->project_number.': '.$audit->project->project_name.' Audit:'.$audit->id);
            if($this->confirm('Is that the audit you wanted?')){
                return $audit;
            } else {
                $this->line(PHP_EOL.'Sorry about that --- please try again with a different audit number.');
                return null;
            }
        }else{
            $this->line(PHP_EOL.'Sorry I could not find an audit matching that number:'.$auditInput);
            return null;
        }
    }

    

    public function handle()
    {
        $audit = null;
        $stop = 0;
        $audit = $this->getAudit;

        if($audit){
            $buildingIds = $audit->building_inspections->pluck('building_id')->toArray();
            $buildings = $audit->building_inspections->pluck('building_id','building_name','address')->toArray(); 
            if($this->confirm('Would you like to see a list of the buildings?'){
                     

                     $headers = ['Building ID','Building Name', 'Address'];
                     $this->table($headers,$buildings);
                    
            }
            $this->line(PHP_EOL.'Please note that you are only removing the building from the audit'.$audit->id.'. We are NOT removing the building(s) from the property for future audits.'.PHP_EOL);
            do{
               
                $buildingId = $this->anticipate('What is the building id of the building you would like to remove from the audit?',$buildingIds);

                if($buildingId){
                    $check = BuildingInspection::where('building_id',$buildingId)->where('audit_id',$audit->id)->first();

                    if($check){ 
                        if($this->confirm('You want to delete '.$check->building_name.'? (This will remove everything including findings associated with this building for this audit).')){
                            $units = $check->building->units->pluck('id')->to_array();
                            $check->delete();
                            // remove inspection items for that building on this audit
                            AmenityInspection::where('audit_id',$audit->id)->where('building_id',$buildingId)->delete();
                            AmenityInspection::where('audit_id',$audit->id)->whereIn('unit_id',$units)->delete();
                            // remove cached building
                            CachedBuilding::where('audit_id',$audit->id)->where('building_id',$buildingId)->delete();
                            // remove cached building
                            CachedUnit::where('audit_id',$audit->id)->where('building_id',$buildingId)->delete();
                            // remove findings assigned to this building
                            Finding::where('audit_id',$audit->id)->where('building_id',$buildingId)->delete();
                            Finding::where('audit_id',$audit->id)->whereIn('unit_id',$units)->delete();

                            $this->line('Building Removed From Audit.'.PHP_EOL);
                            $buildings = $audit->building_inspections->pluck('building_id','building_name','address')->toArray();
                        }

                    } else {
                        $this->error(PHP_EOL.'Building Not Found'.PHP_EOL);
                       
                    }
                }

                if(!$this->confirm('Do you have another Building to delete from this audit?'){
                    $stop = 1;
                } else {
                    if($this->confirm('Would you like to see an updated list of the buildings?'){
                                 
                                 $this->table($headers,$buildings);
                                
                    }
                }

            }while($stop == 0);
        }else{
            $this->line(PHP_EOL.'Sorry no audit was selected.');
        }


        
    }


}
