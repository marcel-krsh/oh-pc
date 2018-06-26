<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddInCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::raw('INSERT INTO `document_rules` (`id`, `amount`, `program_rules_id`, `expense_category_id`, `created_at`, `updated_at`) VALUES (1, 15000.00, 1, 2, NULL, NULL);
INSERT INTO `document_rules` (`id`, `amount`, `program_rules_id`, `expense_category_id`, `created_at`, `updated_at`) VALUES (2, 15500.00, 1, 3, NULL, NULL);
INSERT INTO `document_rules` (`id`, `amount`, `program_rules_id`, `expense_category_id`, `created_at`, `updated_at`) VALUES (3, 16000.00, 1, 4, NULL, NULL);
INSERT INTO `document_rules` (`id`, `amount`, `program_rules_id`, `expense_category_id`, `created_at`, `updated_at`) VALUES (4, 16500.00, 1, 5, NULL, NULL);
INSERT INTO `document_rules` (`id`, `amount`, `program_rules_id`, `expense_category_id`, `created_at`, `updated_at`) VALUES (5, 17000.00, 1, 6, NULL, NULL);
INSERT INTO `document_rules` (`id`, `amount`, `program_rules_id`, `expense_category_id`, `created_at`, `updated_at`) VALUES (6, 17500.00, 1, 7, NULL, NULL);
INSERT INTO `document_rules` (`id`, `amount`, `program_rules_id`, `expense_category_id`, `created_at`, `updated_at`) VALUES (7, 18000.00, 1, 8, NULL, NULL);
INSERT INTO `document_rules` (`id`, `amount`, `program_rules_id`, `expense_category_id`, `created_at`, `updated_at`) VALUES (8, 25000.01, 1, 9, NULL, NULL);
');
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
