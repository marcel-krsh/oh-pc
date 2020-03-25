<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use \App\Models\DocumentCategory;

class UpdateDocumentCategoriesTableValues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //normally this would be a seeder - but we are doing it this way so it happens in a single step in the update.
        $categoriesToUpdate = ['160','471','914','682','648','381','156','317','157','159','622','382','558','591'];
        // need to add categories for tennant file and certifications
        $tennantFile = new DocumentCategory;
        $tennantFile->document_category_name = "Tennant File";
        $tennantFile->from_allita = 1;
        $tennantFile->parent_id = 149;
        $tennantFile->active = 1;
        $tennantFile->allowed_role_id = 2;
        $tennantFile->show_pm = 1;
        $tennantFile->save();

        $certifications = new DocumentCategory;
        $certifications->document_category_name = "Certifications";
        $certifications->from_allita = 1;
        $certifications->parent_id = 149;
        $certifications->active = 1;
        $certifications->allowed_role_id = 2;
        $certifications->show_pm = 1;
        $certifications->save();

        DocumentCategory::whereIn('id',$categoriesToUpdate)->update(['show_pm' => 1]);



    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
