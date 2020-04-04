<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Models\DocumentCategory;


class AddDefaultsToDocumentCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //normally this would be a seeder - but we are doing it this way so it happens in a single step in the update.
        
        //site defaults
        $categoriesToUpdate = ['160','914','648','1069','471','682','381','156','317','157','159','622','382','558'];
        // need to add categories for tennant file and certifications
        DocumentCategory::whereIn('id',$categoriesToUpdate)->update(['required_for_site' => 1]);

        //bin defaults
        $categoriesToUpdate = ['914','471','317','157'];
        // need to add categories for tennant file and certifications
        DocumentCategory::whereIn('id',$categoriesToUpdate)->update(['required_for_bin' => 1]);

        //unit defaults
        $categoriesToUpdate = ['381','157','1068'];
        // need to add categories for tennant file and certifications
        DocumentCategory::whereIn('id',$categoriesToUpdate)->update(['required_for_unit' => 1]);

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
