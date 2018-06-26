<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MakeBreakoutsRespectiveLinkedIdNullable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
            //
            DB::statement('ALTER TABLE `request_items` MODIFY `req_id` INTEGER UNSIGNED NULL;');
            DB::statement('ALTER TABLE `po_items` MODIFY `po_id` INTEGER UNSIGNED NULL;');
            DB::statement('ALTER TABLE `invoice_items` MODIFY `invoice_id` INTEGER UNSIGNED NULL;');
                
       
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
