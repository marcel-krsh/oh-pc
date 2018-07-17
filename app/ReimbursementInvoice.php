<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * ReimbursementInvoice Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class ReimbursementInvoice extends Model
{
    protected $fillable = [
        'sf_batch_id',
        'entity_id',
        'program_id',
        'account_id',
        'status_id',
        'po_id',
        'active',
        'created_at',
        'updated_at',
        'reimbursement_total_amount',
        'reimbursement_total_paid',
        'reimbursement_balance',
        'reimbursement_last_payment_cleared_date'
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
     * Parcels
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function parcels() : BelongsToMany
    {
        return $this->belongsToMany(\App\Parcel::class, 'parcels_to_reimbursement_invoices', 'reimbursement_invoice_id', 'parcel_id');
    }

    /**
     * Transactions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions() : HasMany
    {
        return $this->hasMany(\App\Transaction::class, 'link_to_type_id', 'id')
                ->where('transactions.type_id', '=', 1);
    }

    /**
     * Cleared Transactions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function clearedTransactions() : HasMany
    {
        return $this->hasMany(\App\Transaction::class, 'link_to_type_id', 'id')
                ->where('transactions.type_id', '=', 1)
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
     * Last Payment Cleared
     *
     * @return mixed
     */
    public function lastPaymentCleared()
    {
        return $this->clearedTransactions()
                ->orderBy('date_cleared', 'desc')->first();
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
        'reimbursement_total_amount' => $this->totalAmount(),
        'reimbursement_total_paid' => $this->totalPaid(),
        'reimbursement_balance' => $this->balance(),
        'reimbursement_last_payment_cleared_date' => $lastDate
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
     * Account
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function account() : HasOne
    {
        return $this->hasOne(\App\Account::class, 'id', 'account_id');
    }

    /**
     * Notes
     *
     * @return mixed
     */
    public function notes()
    {
        return $this->hasMany(\App\InvoiceNote::class, 'reimbursement_invoice_id', 'id')
                ->with('owner')
                ->orderBy('created_at', 'asc');
    }

    /**
     * Invoice Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoiceItems() : HasMany
    {
        return $this->hasMany(\App\InvoiceItem::class, 'invoice_id', 'id')
                ->whereHas('poItem', function ($query) {
                    $query->has('requestItem');
                });
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
        return $this->totalAmount() - $this->totalPaid();
    }

    /**
     * Reset Approvals
     */
    public function resetApprovals()
    {
        /* @todo: move to observer */

        $approvals = ApprovalRequest::whereIn('approval_type_id', [4, 8, 9, 10])
                    ->where('link_type_id', '=', $this->id)
                    ->get();

        foreach ($approvals as $approval) {
            ApprovalAction::where('approval_request_id', $approval->id)->delete();
        }
    }
}
