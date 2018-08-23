<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Event;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Transaction Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Transaction extends Model
{
    protected $fillable = [
        'account_id',
        'credit_debit',
        'amount',
        'transaction_category_id',
        'type_id',
        'link_to_type_id',
        'status_id',
        'owner_id',
        'owner_type',
        'date_entered',
        'date_cleared',
        'transaction_note'
    ];

    public static function boot()
    {
        parent::boot();

        /* @todo: move to observer class */

        static::created(function ($transaction) {
            Event::fire('transactions.created', $transaction);
        });

        static::updated(function ($transaction) {
            Event::fire('transactions.updated', $transaction);
        });

        static::deleted(function ($transaction) {
            Event::fire('transactions.deleted', $transaction);
        });
    }

    /**
     * Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status() : HasOne
    {
        return $this->hasOne(\App\Models\TransactionStatus::class, 'id', 'status_id');
    }
}
