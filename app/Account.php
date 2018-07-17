<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Account Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Account extends Model
{
    protected $fillable = [
        'account_name',
        'entity_id',
        'owner_type',
        'owner_id',
        'account_type_id'
    ];

    /**
     * Program
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function program() : BelongsTo
    {
        return $this->belongsTo(\App\Program::class, 'owner_id');
    }

    /**
     * Transactions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions() : HasMany
    {
        return $this->hasMany(\App\Transaction::class, 'account_id', 'id');
    }

    /**
     * Cost Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function costItems() : HasMany
    {
        return $this->hasMany(\App\CostItem::class, 'account_id', 'id');
    }

    /**
     * Request Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function requestItems() : HasMany
    {
        return $this->hasMany(\App\RequestItem::class, 'account_id', 'id');
    }

    /**
     * PO Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function poItems() : HasMany
    {
        return $this->hasMany(\App\PoItems::class, 'account_id', 'id');
    }

    /**
     * Parcels
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function parcels() : HasMany
    {
        return $this->hasMany(\App\Parcel::class, 'account_id', 'id');
    }

    /**
     * Invoice Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoiceItems() : HasMany
    {
        return $this->hasMany(\App\InvoiceItem::class, 'account_id', 'id');
    }

    /**
     * Stats Parcels
     *
     * @return mixed
     */
    public function statsParcels()
    {
        return $this->hasMany(\App\Parcel::class)
                    ->selectRaw('COUNT( parcels.account_id ) AS Total_Parcels,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 1 THEN 1 END) AS LB__Pending,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 2 THEN 1 END) AS LB__Approved_HFA,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 3 THEN 1 END) AS LB__Withdrawn_By_HFA,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 4 THEN 1 END) AS LB__Declined_By_HFA,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 5 THEN 1 END) AS LB__InProcess_With_LB,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 6 THEN 1 END) AS LB__Ready_For_Signature_In_LB,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 7 THEN 1 END) AS LB__Ready_For_Submission_To_HFA,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 8 THEN 1 END) AS LB__Requested_Reimbursement,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 9 THEN 1 END) AS LB__Corrections_Requested_By_HFA,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 10 THEN 1 END) AS LB__Reimbursement_Approved_By_HFA,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 11 THEN 1 END) AS LB__Reimbursement_Declined_By_HFA,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 12 THEN 1 END) AS LB__Reimbursement_Withdrawn,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 13 THEN 1 END) AS LB__Invoiced_To_HFA,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 14 THEN 1 END) AS LB__Paid_By_HFA,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 15 THEN 1 END) AS LB__Disposition_Requested_To_HFA,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 16 THEN 1 END) AS LB__Disposition_Approved_By_HFA,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 17 THEN 1 END) AS LB__Disposition_Released_By_HFA,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 18 THEN 1 END) AS LB__Disposition_Declined_By_HFA,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 19 THEN 1 END) AS LB__Repayment_Required_From_HFA,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 20 THEN 1 END) AS LB__Repayment_Paid_To_HFA,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 41 THEN parcels.account_id END) AS LB__Disposition_Invoice_Due_To_HFA,
		                   COUNT(CASE WHEN parcels.landbank_property_status_id = 42 THEN parcels.account_id END) AS LB__Dispostion_Paid_To_HFA,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 21 THEN 1 END) AS HFA__Compliance_Review,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 22 THEN 1 END) AS HFA__Processing,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 23 THEN 1 END) AS HFA__Corrections_Requested_To_LB,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 24 THEN 1 END) AS HFA__Ready_For_Signators_In_HFA,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 25 THEN 1 END) AS HFA__Reimbursement_Denied_To_LB,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 26 THEN 1 END) AS HFA__Reimbursement_Approved_To_LB,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 27 THEN 1 END) AS HFA__Invoice_Received_From_LB,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 28 THEN 1 END) AS HFA__Paid_Reimbursement,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 29 THEN 1 END) AS HFA__Disposition_Requested_By_LB,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 30 THEN 1 END) AS HFA__Disposition_Approved_To_LB,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 31 THEN 1 END) AS HFA__Disposition_Invoiced_To_LB,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 32 THEN 1 END) AS HFA__Disposition_Paid_By_LB,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 33 THEN 1 END) AS HFA__Disposition_Released_To_LB,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 34 THEN 1 END) AS HFA__Repayment_Required_To_LB,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 35 THEN parcels.account_id END) AS HFA__Repayment_Invoiced_To_LB,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 36 THEN parcels.account_id END) AS HFA__Repayment_Received_From_LB,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 37 THEN parcels.account_id END) AS HFA__Withdrawn_To_LB,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 38 THEN parcels.account_id END) AS HFA__Unsubmitted,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 39 THEN parcels.account_id END) AS HFA__Declined_To_LB,
		                   COUNT(CASE WHEN parcels.hfa_property_status_id = 40 THEN parcels.account_id END) AS HFA__PO_Sent_To_LB');
    }

    /**
     * Stats Transactions
     *
     * @return mixed
     */
    public function statsTransactions()
    {
        return $this->hasMany(\App\Transaction::class)
                    ->selectRaw('SUM(CASE WHEN transactions.transaction_category_id = 1 THEN transactions.amount ELSE 0 END) AS Deposits_Made,
		                   SUM(CASE WHEN transactions.transaction_category_id = 3 THEN transactions.amount ELSE 0 END) AS Reimbursements_Paid,
		                   SUM(CASE WHEN transactions.transaction_category_id = 2 THEN transactions.amount ELSE 0 END) AS Recaptures_Received,
		                   SUM(CASE WHEN transactions.transaction_category_id = 6 THEN transactions.amount ELSE 0 END) AS Dispositions_Received,
		                   SUM(CASE WHEN transactions.transaction_category_id = 4 THEN transactions.amount ELSE 0 END) AS Transfers_Made,
		                   SUM(CASE WHEN transactions.transaction_category_id = 5 THEN transactions.amount ELSE 0 END) AS Line_Of_Credit');
    }

    /**
     * Stats Cost Items
     *
     * @return mixed
     */
    public function statsCostItems()
    {
        return $this->hasMany(\App\CostItem::class)
                    ->selectRaw('SUM(CASE WHEN cost_items.expense_category_id = 9 THEN cost_items.amount ELSE 0 END) AS NIP_Loan_Cost,
		                   SUM(CASE WHEN cost_items.expense_category_id = 2 THEN cost_items.amount ELSE 0 END) AS Acquisition_Cost,
		                   SUM(CASE WHEN cost_items.expense_category_id = 3 THEN cost_items.amount ELSE 0 END) AS PreDemo_Cost,
		                   SUM(CASE WHEN cost_items.expense_category_id = 4 THEN cost_items.amount ELSE 0 END) AS Demolition_Cost,
		                   SUM(CASE WHEN cost_items.expense_category_id = 5 THEN cost_items.amount ELSE 0 END) AS Greening_Cost,
		                   SUM(CASE WHEN cost_items.expense_category_id = 6 THEN cost_items.amount ELSE 0 END) AS Maintenance_Cost,
		                   SUM(CASE WHEN cost_items.expense_category_id = 7 THEN cost_items.amount ELSE 0 END) AS Administration_Cost,
		                   SUM(CASE WHEN cost_items.expense_category_id = 8 THEN cost_items.amount ELSE 0 END) AS Other_Cost,
		                   AVG(CASE WHEN cost_items.expense_category_id = 9 THEN cost_items.amount ELSE 0 END) AS NIP_Loan_Cost_Average,
		                   AVG(CASE WHEN cost_items.expense_category_id = 2 THEN cost_items.amount ELSE 0 END) AS Acquisition_Cost_Average,
		                   AVG(CASE WHEN cost_items.expense_category_id = 3 THEN cost_items.amount ELSE 0 END) AS PreDemo_Cost_Average,
		                   AVG(CASE WHEN cost_items.expense_category_id = 4 THEN cost_items.amount ELSE 0 END) AS Demolition_Cost_Average,
		                   AVG(CASE WHEN cost_items.expense_category_id = 5 THEN cost_items.amount ELSE 0 END) AS Greening_Cost_Average,
		                   AVG(CASE WHEN cost_items.expense_category_id = 6 THEN cost_items.amount ELSE 0 END) AS Maintenance_Cost_Average,
		                   AVG(CASE WHEN cost_items.expense_category_id = 7 THEN cost_items.amount ELSE 0 END) AS Administration_Cost_Average,
		                   AVG(CASE WHEN cost_items.expense_category_id = 8 THEN cost_items.amount ELSE 0 END) AS Other_Cost_Average,
		               COALESCE(SUM(cost_items.amount),0) AS Total_Cost');
    }

    /**
     * Stats Request Items
     *
     * @return mixed
     */
    public function statsRequestItems()
    {
        return $this->hasMany(\App\RequestItem::class)
                    ->selectRaw('SUM(CASE WHEN request_items.expense_category_id = 9 THEN request_items.amount ELSE 0 END) AS NIP_Loan_Requested,
		                   SUM(CASE WHEN request_items.expense_category_id = 2 THEN request_items.amount ELSE 0 END) AS Acquisition_Requested,
		                   SUM(CASE WHEN request_items.expense_category_id = 3 THEN request_items.amount ELSE 0 END) AS PreDemo_Requested,
		                   SUM(CASE WHEN request_items.expense_category_id = 4 THEN request_items.amount ELSE 0 END) AS Demolition_Requested,
		                   SUM(CASE WHEN request_items.expense_category_id = 5 THEN request_items.amount ELSE 0 END) AS Greening_Requested,
		                   SUM(CASE WHEN request_items.expense_category_id = 6 THEN request_items.amount ELSE 0 END) AS Maintenance_Requested,
		                   SUM(CASE WHEN request_items.expense_category_id = 7 THEN request_items.amount ELSE 0 END) AS Administration_Requested,
		                   SUM(CASE WHEN request_items.expense_category_id = 8 THEN request_items.amount ELSE 0 END) AS Other_Requested,
		               COALESCE(SUM(request_items.amount),0) AS Total_Requested');
    }

    /**
     * Stats PO Items
     *
     * @return mixed
     */
    public function statsPoItems()
    {
        return $this->hasMany(\App\PoItems::class)
                    ->selectRaw('SUM(CASE WHEN po_items.expense_category_id = 9 THEN po_items.amount ELSE 0 END) AS NIP_Loan_Approved,
		                   SUM(CASE WHEN po_items.expense_category_id = 2 THEN po_items.amount ELSE 0 END) AS Acquisition_Approved,
		                   SUM(CASE WHEN po_items.expense_category_id = 3 THEN po_items.amount ELSE 0 END) AS PreDemo_Approved,
		                   SUM(CASE WHEN po_items.expense_category_id = 4 THEN po_items.amount ELSE 0 END) AS Demolition_Approved,
		                   SUM(CASE WHEN po_items.expense_category_id = 5 THEN po_items.amount ELSE 0 END) AS Greening_Approved,
		                   SUM(CASE WHEN po_items.expense_category_id = 6 THEN po_items.amount ELSE 0 END) AS Maintenance_Approved,
		                   SUM(CASE WHEN po_items.expense_category_id = 7 THEN po_items.amount ELSE 0 END) AS Administration_Approved,
		                   SUM(CASE WHEN po_items.expense_category_id = 8 THEN po_items.amount ELSE 0 END) AS Other_Approved,
		               COALESCE(SUM(po_items.amount),0) AS Total_Approved');
    }

    /**
     * Stats Invoice Items
     *
     * @return mixed
     */
    public function statsInvoiceItems()
    {
        return $this->hasMany(\App\InvoiceItem::class)
                    ->selectRaw('SUM(CASE WHEN invoice_items.expense_category_id = 9 THEN invoice_items.amount ELSE 0 END) AS NIP_Loan_Invoiced,
		                   SUM(CASE WHEN invoice_items.expense_category_id = 2 THEN invoice_items.amount ELSE 0 END) AS Acquisition_Invoiced,
		                   SUM(CASE WHEN invoice_items.expense_category_id = 3 THEN invoice_items.amount ELSE 0 END) AS PreDemo_Invoiced,
		                   SUM(CASE WHEN invoice_items.expense_category_id = 4 THEN invoice_items.amount ELSE 0 END) AS Demolition_Invoiced,
		                   SUM(CASE WHEN invoice_items.expense_category_id = 5 THEN invoice_items.amount ELSE 0 END) AS Greening_Invoiced,
		                   SUM(CASE WHEN invoice_items.expense_category_id = 6 THEN invoice_items.amount ELSE 0 END) AS Maintenance_Invoiced,
		                   SUM(CASE WHEN invoice_items.expense_category_id = 7 THEN invoice_items.amount ELSE 0 END) AS Administration_Invoiced,
		                   SUM(CASE WHEN invoice_items.expense_category_id = 8 THEN invoice_items.amount ELSE 0 END) AS Other_Invoiced,
		               COALESCE(SUM(invoice_items.amount),0) AS Total_Invoiced');
    }
}
