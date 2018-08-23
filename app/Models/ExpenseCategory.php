<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * ExpenseCategory Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class ExpenseCategory extends Model
{
    protected $table = "expense_categories";
    
    protected $fillable = [
        'expense_category_name',
        'parent_id',
        'active',
        'color_hex',
        'color_a',
        'trans_color_hex',
        'trans_color_a',
        'advance_color_hex',
        'advance_color_a',
        'advance_trans_color_hex',
        'advance_trans_color_a'
    ];
}
