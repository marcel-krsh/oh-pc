<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * RecaptureInvoice Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class RecaptureInvoice extends Model
{
    protected $table = 'recapture_invoices';

    protected $fillable = [
        'entity_id',
        'program_id',
        'account_id',
        'status_id',
        'active',
        'recapture_invoice_due',
        'recapture_total_amount',
        'recapture_total_paid',
        'recapture_balance',
        'recapture_last_payment_cleared_date',
        'paid'
    ];

    /**
     * Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status() : HasOne
    {
        return $this->hasOne(\App\InvoiceStatus::class, 'id', 'status_id');
    }

    /**
     * Transactions
     *
     * @return mixed
     */
    public function transactions()
    {
        return $this->hasMany(\App\Transaction::class, 'link_to_type_id', 'id')
                ->where('transactions.type_id', '=', 6);
    }

    /**
     * Clear Transactions
     *
     * @return mixed
     */
    public function clearedTransactions() : HasMany
    {
        return $this->hasMany(\App\Transaction::class, 'link_to_type_id', 'id')
                ->where('transactions.type_id', '=', 6)
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
    public function RecaptureItem() : HasMany
    {
        return $this->hasMany(\App\RecaptureItem::class, 'recapture_invoice_id', 'id');
    }

    /**
     * Total Amount
     *
     * @return mixed
     */
    public function totalAmount()
    {
        return $this->RecaptureItem()->sum('amount');
    }

    /**
     * Balance
     *
     * @return mixed
     */
    public function balance()
    {
        return $this->totalAmount() - $this->totalPaid();
    }

    /**
     * Last Payment Cleared
     *
     * @return mixed
     */
    public function lastPaymentCleared()
    {
        return $this->clearedTransactions()
                ->orderBy('date_cleared', 'desc')
                ->first();
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
            'recapture_total_amount' => $this->totalAmount(),
            'recapture_total_paid' => $this->totalPaid(),
            'recapture_balance' => $this->balance(),
            'recapture_last_payment_cleared_date' => $lastDate
        ]);
    }

    /**
     * Entity
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function entity() : HasOne
    {
        return $this->hasOne(\App\Entity::class, 'id', 'entity_id');
    }

    /**
     * Program
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function program() : HasOne
    {
        return $this->hasOne(\App\Program::class, 'id', 'program_id');
    }

    /**
     * Notes
     *
     * @return mixed
     */
    public function notes()
    {
        return $this->hasMany(\App\RecaptureInvoiceNote::class, 'recapture_invoice_id', 'id')
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
        return $this->hasOne(\App\Account::class, 'id', 'account_id');
    }
}
