<?php

namespace App\Console\Commands;

use App\Entity;
use App\Vendor;
use App\VendorToEntity;
use Illuminate\Console\Command;

/**
 * AssignVendors Command
 *
 * @category Commands
 * @license  Proprietary and confidential
 */
class AssignVendorsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'assign:vendors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign all vendors to all entities.';

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
        $vendors = Vendor::select('id')->get()->all();
        $this->line('Connecting Vendors to Entities.'.PHP_EOL);
        $bar = $this->output->createProgressBar(count($vendors));
        foreach ($vendors as $vendor) {
            $entities = Entity::select('id', 'entity_name')->get()->all();
            $this->line('Adding vendor id '.$vendor->id.' to all entities.'.PHP_EOL);
            foreach ($entities as $entity) {
                VendorToEntity::insert(['entity_id'=>$entity->id,'vendor_id'=>$vendor->id]);
            }
            $bar->advance();
        }
    }
}
