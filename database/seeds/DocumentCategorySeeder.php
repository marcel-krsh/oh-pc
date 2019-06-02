<?php

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;

class DocumentCategorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {

    $dc                         = new DocumentCategory;
    $dc->document_category_name = 'OWNER RESPONSE';
    $dc->from_allita            = 1;
    $dc->from_docuware          = 0;
    $dc->parent_id              = 0;
    $dc->active                 = 1;
    $dc->save();

    $dcc                         = new DocumentCategory;
    $dcc->document_category_name = 'WORK ORDER';
    $dcc->from_allita            = 1;
    $dcc->from_docuware          = 0;
    $dcc->parent_id              = $dc->id;
    $dcc->active                 = 1;
    $dcc->save();

  }
}
