<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * DispositionsToInvoice Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class DispositionsToInvoice extends Model
{
    protected $table = 'dispositions_to_invoices';

    public $timestamps = false;

    protected $fillable = [
        'disposition_id',
        'disposition_invoice_id'
    ];
}
