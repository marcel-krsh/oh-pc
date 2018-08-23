<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * DispositionInvoice Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class DispositionInvoice extends Model
{
    protected $table = 'disposition_invoices';

    protected $fillable = [
        'entity_id',
        'program_id',
        'account_id',
        'status_id',
        'active',
        'disposition_invoice_due',
        'disposition_total_amount',
        'disposition_total_paid',
        'disposition_balance',
        'disposition_last_payment_cleared_date'
    ];

    /**
     * Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status() : HasOne
    {
        return $this->hasOne(\App\Models\InvoiceStatus::class, 'id', 'status_id');
    }

    /**
     * Dispositions
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function dispositions() : BelongsToMany
    {
        return $this->belongsToMany(\App\Models\Disposition::class, 'dispositions_to_invoices', 'disposition_invoice_id', 'disposition_id');
    }

    /**
     * Transactions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions() : HasMany
    {
        return $this->hasMany(\App\Models\Transaction::class, 'link_to_type_id', 'id')
                ->where('transactions.type_id', '=', 2);
    }

    /**
     * Cleared Transactions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clearedTransactions() : HasMany
    {
        return $this->hasMany(\App\Models\Transaction::class, 'link_to_type_id', 'id')
                ->where('transactions.type_id', '=', 2)
                ->where('transactions.status_id', '=', 2);
    }

    /**
     * Total Paid
     *
     * @return mixed
     */
    public function totalPaid()
    {
        return $this->clearedTransactions()->sum('amount');
    }

    /**
     * Invoice Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoiceItems() : HasMany
    {
        return $this->hasMany(\App\Models\DispositionItems::class, 'disposition_invoice_id', 'id');
    }

    /**
     * Total Amount
     *
     * @return mixed
     */
    public function totalAmount()
    {
        return $this->invoiceItems()->sum('amount');
    }

    /**
     * Balance
     *
     * @return mixed
     */
    public function balance()
    {
        return ($this->totalAmount() - $this->totalPaid());
    }

    /**
     * Last Payment Cleared
     *
     * @return mixed
     */
    public function lastPaymentCleared()
    {
        return $this->clearedTransactions()->orderBy('date_cleared', 'desc')->first();
    }

    /**
     * Update Payment Details
     */
    public function updatePaymentDetails()
    {
        if (!is_null($this->lastPaymentCleared())) {
            $lastDate = $this->lastPaymentCleared()->date_cleared;
        } else {
            $lastDate = null;
        }

        $this->update([
            'disposition_total_amount'              => $this->totalAmount(),
            'disposition_total_paid'                => $this->totalPaid(),
            'disposition_balance'                   => $this->balance(),
            'disposition_last_payment_cleared_date' => $lastDate
        ]);
    }

    /**
     * Entity
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function entity() : HasOne
    {
        return $this->hasOne(\App\Models\Entity::class, 'id', 'entity_id');
    }

    /**
     * Program
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function program() : HasOne
    {
        return $this->hasOne(\App\Models\Program::class, 'id', 'program_id');
    }

    /**
     * Notes
     *
     * @return mixed
     */
    public function notes()
    {
        return $this->hasMany(\App\Models\DispositionInvoiceNote::class, 'disposition_invoice_id', 'id')
                ->with('owner')
                ->orderBy('created_at', 'asc');
    }

    /**
     * Account
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function account() : HasOne
    {
        return $this->hasOne(\App\Models\Account::class, 'id', 'account_id');
    }
}
