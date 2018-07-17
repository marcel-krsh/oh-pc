<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * Parcel Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class Parcel extends Model
{
    protected $fillable = [
        'parcel_id',
        'program_id',
        'owner_id',
        'entity_id',
        'account_id',
        'owner_type',
        'street_address',
        'city',
        'state_id',
        'zip',
        'county_id',
        'target_area_id',
        'oh_house_district',
        'oh_senate_district',
        'us_house_district',
        'latitude',
        'longitude',
        'google_map_link',
        'withdrawn_date',
        'sale_price',
        'how_acquired_id',
        'how_acquired_explanation',
        'hfa_property_status_id',
        'landbank_property_status_id',
        'legacy',
        'compliance',
        'compliance_manual',
        'compliance_score',
        'landbank_property_status_id_explanation',
        'hfa_property_status_id_explanation',
        'approved_in_po',
        'declined_in_po',
        'next_step'
    ];

    /**
     * State
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function state() : BelongsTo
    {
        return $this->belongsTo('App\State', 'state_id');
    }

    /**
     * County
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function county() : BelongsTo
    {
        return $this->belongsTo('App\County', 'county_id');
    }

    /**
     * Target Area
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function targetArea() : BelongsTo
    {
        return $this->belongsTo('App\TargetArea', 'target_area_id');
    }

    /**
     * Compliances
     *
     * @return mixed
     */
    public function compliances() : HasMany
    {
        return $this->hasMany('App\Compliance', 'parcel_id', 'id')->orderBy('id', 'DESC');
    }

    /**
     * Recaptures
     *
     * @return mixed
     */
    public function recaptures() : HasMany
    {
        return $this->hasMany('App\RecaptureItem', 'parcel_id', 'id')->orderBy('id', 'DESC');
    }

    /**
     * Resolutions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function resolutions() : HasMany
    {
        return $this->hasMany('App\ValidationResolutions', 'parcel_id', 'id');
    }

    /**
     * Communications
     *
     * @return mixed
     */
    public function communications() : HasMany
    {
        return $this->hasMany('App\Communication', 'parcel_id', 'id')
                ->orderBy('id', 'DESC');
    }

    /**
     * Documents
     *
     * @return mixed
     */
    public function documents() : HasMany
    {
        return $this->hasMany('App\Document', 'parcel_id', 'id')
                ->orderBy('id', 'DESC');
    }

    /**
     * Activity Log
     *
     * @return mixed
     */
    public function activities()
    {
        $activities = ActivityLog::where('subject_type', '=', 'App\Parcel')
                                ->where('subject_id', '=', $this->id)
                                ->leftJoin('users', 'users.id', '=', 'activity_log.causer_id')
                                ->get();
        return $activities;
    }

    /**
     * Entity
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function entity() : BelongsTo
    {
        return $this->belongsTo('App\Entity');
    }

    /**
     * Guide Next Step
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function guide_next_step() : HasOne
    {
        return $this->hasOne('App\GuideStep', 'id', 'next_step');
    }

    /**
     * Associated Request
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function associatedRequest() : HasOne
    {
        return $this->hasOne('App\ParcelsToReimbursementRequest', 'parcel_id', 'id');
    }

    /**
     * Associated PO
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function associatedPo() : HasOne
    {
        return $this->hasOne('App\ParcelsToPurchaseOrder', 'parcel_id', 'id');
    }

    /**
     * Associated Invoice
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function associatedInvoice() : HasOne
    {
        return $this->hasOne('App\ParcelsToReimbursementInvoice', 'parcel_id', 'id');
    }

    /**
     * Associated Dispositions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function associatedDispositions() : HasMany
    {
        return $this->hasMany('App\Disposition', 'parcel_id', 'id')
                ->orderBy('id', 'DESC');
    }

    /**
     * Associated Disposition
     *
     * @return mixed
     */
    public function associatedDisposition()
    {
        return $this->associatedDispositions()
                ->orderBy('dispositions.id', 'Desc');
    }

    /**
     * Associated Declined Dispositions
     *
     * @return mixed
     */
    public function associatedDeclinedDispositions()
    {
        return $this->associatedDispositions()
                ->where('dispositions.status_id', '=', 5)
                ->orderBy('dispositions.id', 'Desc');
    }

    /**
     * Program
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function program() : HasOne
    {
        return $this->hasOne('App\Program', 'id', 'program_id');
    }

    /**
     * Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function status() : HasOne
    {
        return $this->hasOne('App\PropertyStatusOption', 'id', 'status_id');
    }

    /**
     * Landbank Property Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function landbankPropertyStatus() : HasOne
    {
        return $this->hasOne('App\PropertyStatusOption', 'id', 'landbank_property_status_id');
    }

    /**
     * HFA Property Status
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function hfaPropertyStatus() : HasOne
    {
        return $this->hasOne('App\PropertyStatusOption', 'id', 'hfa_property_status_id');
    }

    /**
     * Dispositions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dispositions() : HasMany
    {
        return $this->hasMany('App\Disposition', 'parcel_id', 'id');
    }

    /**
     * Dispositions Submitted To Fiscal Agent
     *
     * @return mixed
     */
    public function dispositionsSubmittedToFiscalAgent()
    {
        if ($this->dispositions) {
            return $this->dispositions()
                    ->where('dispositions.status_id', 8);
        }
    }

    /**
     * Approved Dispositions
     *
     * @return mixed
     */
    public function approvedDispositions()
    {
        if ($this->dispositions) {
            return $this->dispositions()
                    ->where('dispositions.status_id', 7);
        }
    }

    /**
     * Paid Dispositions
     *
     * @return mixed
     */
    public function paidDispositions()
    {
        if ($this->dispositions) {
            return $this->dispositions()
                    ->where('dispositions.status_id', 6);
        }
    }

    /**
     * Declined Dispositions
     *
     * @return mixed
     */
    public function declinedDispositions()
    {
        if ($this->dispositions) {
            return $this->dispositions()
                    ->where('dispositions.status_id', 5);
        }
    }

    /**
     * Pending Payment Dispositions
     *
     * @return mixed
     */
    public function pendingPaymentDispositions()
    {
        if ($this->dispositions) {
            return $this->dispositions()
                    ->where('dispositions.status_id', 4);
        }
    }

    /**
     * Pending HFA Approval Dispositions
     *
     * @return mixed
     */
    public function pendingHfaApprovalDispositions()
    {
        if ($this->dispositions) {
            return $this->dispositions()
                    ->where('dispositions.status_id', 3);
        }
    }

    /**
     * Pending LB Approval Dispositions
     *
     * @return mixed
     */
    public function pendingLbApprovalDispositions()
    {
        if ($this->dispositions) {
            return $this->dispositions()
                    ->where('dispositions.status_id', 2);
        }
    }

    /**
     * Draft Dispositions
     *
     * @return mixed
     */
    public function draftDispositions()
    {
        if ($this->dispositions) {
            return $this->dispositions()
                    ->where('dispositions.status_id', 1);
        }
    }

    /**
     * Release Requested Dispositions
     *
     * where the release has been requested, but the released date is still null
     *
     * @return mixed
     */
    public function releaseRequestedDispositions()
    {
        if ($this->dispositions) {
            return $this->dispositions()
                    ->whereNotNull('dispositions.date_release_requested')
                    ->whereNull('dispositions.release_date');
        }
    }

    /**
     * Released Dispositions
     *
     * where the release has been requested, but the released date is still null
     *
     * @return mixed
     */
    public function releasedDispositions()
    {
        if ($this->dispositions) {
            return $this->dispositions()
                    ->whereNotNull('dispositions.release_date');
        }
    }

    /**
     * Retainages
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function retainages()
    {
        return $this->hasMany('App\Retainage', 'parcel_id', 'id');
    }

    /**
     * Unpaid Retainages
     *
     * @return mixed
     */
    public function unpaidRetainages()
    {
        if ($this->retainages) {
            return $this->retainages()
                    ->where('retainages.paid', 0);
        }
    }

    /**
     * Paid Retainages
     *
     * @return mixed
     */
    public function paidRetainages()
    {
        if ($this->retainages) {
            return $this->retainages()
                    ->where('retainages.paid', 1);
        }
    }

    /**
     * Cost Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function costItems()
    {
        return $this->hasMany('App\CostItem', 'parcel_id', 'id');
    }

    /**
     * Cost Items With Advance
     *
     * @return null
     */
    public function costItemsWithAdvance()
    {
        if ($this->costItems()) {
            return $this->costItems()
                    ->where('advance', '=', 1);
        }
        return null;
    }

    /**
     * Unpaid Cost Items With Advances
     *
     * @return mixed
     */
    public function unpaidCostItemsWithAdvances()
    {
        if ($this->costItemsWithAdvance) {
            return $this->costItemsWithAdvance()
                    ->where('cost_items.advance_paid', 0);
        }
    }

    /**
     * All Request Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allRequestItems() : HasMany
    {
        return $this->hasMany('App\RequestItem', 'parcel_id', 'id');
    }

    /**
     * Request Items
     *
     * @return mixed
     */
    public function requestItems()
    {
        return $this->allRequestItems()
                ->has('costItem');
    }

    /**
     * All PO Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allPoItems() : HasMany
    {
        return $this->hasMany('App\PoItems', 'parcel_id', 'id');
    }

    /**
     * All Cost Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allCostItems() : HasMany
    {
        return $this->hasMany('App\CostItem', 'parcel_id', 'id');
    }

    /**
     * PO Items
     *
     * @return mixed
     */
    public function poItems()
    {
        return $this->allPoItems()
                ->has('requestItem');
    }

    /**
     * Invoice Items
     *
     * @return mixed
     */
    public function invoiceItems()
    {
        return $this->hasMany('App\InvoiceItem', 'parcel_id', 'id')
                ->whereHas('poItem', function ($query) {
                    $query->has('requestItem');
                });
    }

    /**
     * All Invoice Items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function allInvoiceItems() : HasMany
    {
        return $this->hasMany('App\InvoiceItem', 'parcel_id', 'id');
    }

    /**
     * Acquisition Total
     *
     * @return int
     */
    public function acquisitionTotal()
    {
        if ($this->associatedInvoice) {
            return $this->invoiceItems()->where('invoice_items.expense_category_id', 2)->sum('amount');
        } elseif ($this->associatedPo) {
            return $this->poItems()->where('po_items.expense_category_id', 2)->sum('amount');
        } elseif ($this->associatedRequest) {
            return $this->requestItems()->where('request_items.expense_category_id', 2)->sum('amount');
        } else {
            return 0;
        }
    }

    /**
     * Pre-demo Total
     *
     * @return int
     */
    public function predemoTotal()
    {
        if ($this->associatedInvoice) {
            return $this->invoiceItems()->where('invoice_items.expense_category_id', 3)->sum('amount');
        } elseif ($this->associatedPo) {
            return $this->poItems()->where('po_items.expense_category_id', 3)->sum('amount');
        } elseif ($this->associatedRequest) {
            return $this->requestItems()->where('request_items.expense_category_id', 3)->sum('amount');
        } else {
            return 0;
        }
    }

    /**
     * Demolition Total
     *
     * @return int
     */
    public function demolitionTotal()
    {
        if ($this->associatedInvoice) {
            return $this->invoiceItems()->where('invoice_items.expense_category_id', 4)->sum('amount');
        } elseif ($this->associatedPo) {
            return $this->poItems()->where('po_items.expense_category_id', 4)->sum('amount');
        } elseif ($this->associatedRequest) {
            return $this->requestItems()->where('request_items.expense_category_id', 4)->sum('amount');
        } else {
            return 0;
        }
    }

    /**
     * Greening Total
     *
     * @return int
     */
    public function greeningTotal()
    {
        if ($this->associatedInvoice) {
            return $this->invoiceItems()->where('invoice_items.expense_category_id', 5)->sum('amount');
        } elseif ($this->associatedPo) {
            return $this->poItems()->where('po_items.expense_category_id', 5)->sum('amount');
        } elseif ($this->associatedRequest) {
            return $this->requestItems()->where('request_items.expense_category_id', 5)->sum('amount');
        } else {
            return 0;
        }
    }

    /**
     * Maintenance Total
     *
     * @return int
     */
    public function maintenanceTotal()
    {
        if ($this->associatedInvoice) {
            return $this->invoiceItems()->where('invoice_items.expense_category_id', 6)->sum('amount');
        } elseif ($this->associatedPo) {
            return $this->poItems()->where('po_items.expense_category_id', 6)->sum('amount');
        } elseif ($this->associatedRequest) {
            return $this->requestItems()->where('request_items.expense_category_id', 6)->sum('amount');
        } else {
            return 0;
        }
    }

    /**
     * Administration Total
     *
     * @return int
     */
    public function administrationTotal()
    {
        if ($this->associatedInvoice) {
            return $this->invoiceItems()->where('invoice_items.expense_category_id', 7)->sum('amount');
        } elseif ($this->associatedPo) {
            return $this->poItems()->where('po_items.expense_category_id', 7)->sum('amount');
        } elseif ($this->associatedRequest) {
            return $this->requestItems()->where('request_items.expense_category_id', 7)->sum('amount');
        } else {
            return 0;
        }
    }

    /**
     * Other Total
     *
     * @return int
     */
    public function other_total()
    {
        if ($this->associatedInvoice) {
            return $this->invoiceItems()->where('invoice_items.expense_category_id', 8)->sum('amount');
        } elseif ($this->associatedPo) {
            return $this->poItems()->where('po_items.expense_category_id', 8)->sum('amount');
        } elseif ($this->associatedRequest) {
            return $this->requestItems()->where('request_items.expense_category_id', 8)->sum('amount');
        } else {
            return 0;
        }
    }

    /**
     * NIP Loan Total
     *
     * @return int
     */
    public function nipLoanTotal()
    {
        if ($this->associatedInvoice) {
            return $this->invoiceItems()->where('invoice_items.expense_category_id', 9)->sum('amount');
        } elseif ($this->associatedPo) {
            return $this->poItems()->where('po_items.expense_category_id', 9)->sum('amount');
        } elseif ($this->associatedRequest) {
            return $this->requestItems()->where('request_items.expense_category_id', 9)->sum('amount');
        } else {
            return 0;
        }
    }

    /**
     * Has Supporting Documents
     *
     * @return bool
     */
    public function hasSupportingDocuments()
    {
        if ($this->documents()->count() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Is HFA Approved
     *
     * @return bool
     */
    public function isHfaApproved()
    {
        // check if the PO has been approved, if so the approved_in_po should be 1
        $po_id = ParcelsToPurchaseOrder::where('parcel_id', $this->id)->select('purchase_order_id')->first();
        if (isset($po_id->purchase_order_id)) {
            if (ReimbursementPurchaseOrders::where('id', $po_id->purchase_order_id)->where('status_id', 7)->count() > 0) {
                // update parcel's status
                $parcel_to_update = Parcel::where('id', '=', $this->id)->first();
                $parcel_to_update->update([
                    'approved_in_po' => 1
                ]);
                guide_set_progress($this->id, 55, $status = 'completed'); // parcel approved in PO
                $this->fresh(); // reload model
            }
        }

        if ($this->approved_in_po == 1) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Has Advance Items
     *
     * @return bool
     */
    public function hasAdvanceItems()
    {
        if ($this->costItems()->where('advance', 1)->count() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Has Paid Advance Items
     *
     * @return bool
     */
    public function hasPaidAdvanceItems()
    {
        if ($this->costItems()->where('advance', 1)->where('advance_paid', 1)->count() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Has Unpaid Advance Items
     *
     * @return bool
     */
    public function hasUnpaidAdvanceItems()
    {
        if ($this->costItems()->where('advance', 1)->whereNull('advance_paid')->orWhere('advance_paid', 0)->count() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Advance Items
     *
     * @return mixed
     */
    public function advanceItems()
    {
        return $this->hasMany('App\CostItem', 'parcel_id', 'id')
                ->where('advance', 1);
    }

    /**
     * Paid Advance Items
     *
     * @return mixed
     */
    public function paidAdvanceItems()
    {
        return $this->hasMany('App\CostItem', 'parcel_id', 'id')
                ->where('advance', 1)
                ->where('advance_paid', 1);
    }

    /**
     * Unpaid Advance Items
     *
     * @return mixed
     */
    public function unpaidAdvanceItems()
    {
        return $this->hasMany('App\CostItem', 'parcel_id', 'id')
                ->where('advance', 1)
                ->whereNull('advance_paid');
    }

    /**
     * Has Cost Items
     *
     * @return bool
     */
    public function hasCostItems() : bool
    {
        if ($this->costItems()->count() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Has Request Items
     *
     * @return bool
     */
    public function hasRequestItems() : bool
    {
        if ($this->requestItems()->count() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Has Po Items
     *
     * @return bool
     */
    public function hasPoItems() : bool
    {
        if ($this->poItems()->count() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Has Invoice Items
     *
     * @return bool
     */
    public function hasInvoiceItems() : bool
    {
        if ($this->invoiceItems()->count() > 0) {
            return true;
        }
        return false;
    }

    /**
     * Cost Total
     *
     * @return mixed
     */
    public function costTotal()
    {
        return $this->costItems()->sum('amount');
    }

    /**
     * Requested Total
     *
     * @return mixed
     */
    public function requestedTotal()
    {
        return $this->requestItems()->sum('amount');
    }

    /**
     * Approved Total
     *
     * @return mixed
     */
    public function approvedTotal()
    {
        return $this->poItems()->sum('amount');
    }

    /**
     * Get Approved Total Formatted Attribute
     *
     * @return mixed|string
     */
    public function getApprovedTotalFormattedAttribute()
    {
        return money_format('%n', $this->poItems()->sum('amount'));
    }

    /**
     * Invoiced Total
     *
     * @return mixed
     */
    public function invoicedTotal()
    {
        return $this->invoiceItems()->sum('amount');
    }

    /**
     * Import ID
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function importId()
    {
        $import = \App\ImportRow::where('row_id', $this->id)->first();
        if (!isset($import->import_id) && $this->lb_validated == 0 && isset($this->entity_id)) {
            // Create a standalone import
            $import = new \App\Import;
            $import->user_id = Auth::user()->id;
            $import->entity_id = $this->entity_id;
            $import->program_id = $this->program_id;
            $import->account_id = $this->account_id;
            $import->validated = 0;
            $import->save();
           
            // Create a row for the parcel
            $importRow = new \App\ImportRow;
            $importRow->import_id = $import->id;
            $importRow->row_id = $this->id;
            $importRow->table_name = "parcels";
            $importRow->save();
        }
        return $this->hasOne('App\ImportRow', 'row_id', 'id');
    }

    /**
     * Delete Parcel
     *
     * @return int
     * @throws \Exception
     */
    public function deleteParcel()
    {
        /* @todo: move this to a service class method */

        /// delete documents
        $documents = DB::table('documents')->select('*')->where('parcel_id', $this->id)->get()->all();
        if (count($documents)>0) {
            foreach ($documents as $data) {
                $document_id = $data->id;
                $document = Document::find($document_id);
                $document_categories = DocumentCategory::where('active', '1')->orderby('document_category_name', 'asc')->get();

                // build a list of all categories used for uploaded documents in this parcel
                $categories_used = [];
                $categories = []; // store the new associative array cat id, cat name
                 
                if ($document->categories) {
                    $categories_decoded = json_decode($document->categories, true); // cats used by the doc

                    $categories_used = array_merge($categories_used, $categories_decoded); // merge document categories
                } else {
                    $categories_decoded = [];
                }

                foreach ($document_categories as $document_category) {
                    // sub key for each document's categories for quick reference
                    if (in_array($document_category->id, $categories_decoded)) {
                        $categories[$document_category->id] = $document_category->document_category_name;
                    }
                }
                $document->categoriesarray = $categories;

                // remove file if it is not a executed doc (lien/mortgage) or a signature doc
                if (!isset($document->categoriesarray[2]) ||  !isset($document->categoriesarray[3]) || !isset($document->categoriesarray[30])     || !isset($document->categoriesarray[31]) || !isset($document->categoriesarray[32]) || !isset($document->categoriesarray[33]) || !isset($document->categoriesarray[34]) || !isset($document->categoriesarray[35])|| !isset($document->categoriesarray[36]) || !isset($document->categoriesarray[37])) {
                    Storage::delete($document->file_path);
                }
                $lc = new LogConverter('document', 'delete');
                $lc->setFrom(Auth::user())->setTo($document)->setDesc(Auth::user()->email . ' deleted document ' . $document->filename)->save();
                // remove database record
                $document->delete();
            }
        }
        /// delete notes
        $notes = DB::table('notes')->select('*')->where('parcel_id', $this->id)->get()->all();
        if (count($notes)>0) {
            foreach ($notes as $data) {
                $note_id = $data->id;
                $note = Note::find($note_id);
                // remove database record
                $note->delete();
            }
        }
        /// delete communications
        $communications = DB::table('communications')->select('*')->where('parcel_id', $this->id)->orderBy('id', 'desc')->get()->all();
        if (count($communications)>0) {
            foreach ($communications as $data) {
                $communication_id = $data->id;
                // delete comm documents
                DB::table('communication_documents')->where('communication_id', $data->id)->delete();
                // delte comm recipients
                DB::table('communication_recipients')->where('communication_id', $data->id)->delete();

                $communication = Communication::find($communication_id);
                $lc = new LogConverter('communication', 'delete');
                $lc->setFrom(Auth::user())->setTo($communication)->setDesc(Auth::user()->email . ' deleted communication ' . $data->id . ' as a part of parcel '.$this->parcel_id.' deletion.')->save();
                // remove database record
                $communication->delete();
            }
        }
        /// delete compliances
        $compliances = DB::table('compliances')->select('*')->where('parcel_id', $this->id)->get()->all();
        if (count($compliances)>0) {
            foreach ($compliances as $data) {
                $compliance_id = $data->id;
                $compliance = Compliance::find($compliance_id);
                $lc = new LogConverter('compliance', 'delete');
                $lc->setFrom(Auth::user())->setTo($compliance)->setDesc(Auth::user()->email . ' deleted compliance ' . $data->id . ' as a part of parcel '.$this->parcel_id.' deletion.')->save();
                // remove database record
                $compliance->forceDelete();
            }
        }
        /// delete retainages
        $retainages = DB::table('retainages')->select('*')->where('parcel_id', $this->id)->get()->all();
        if (count($retainages) > 0) {
            foreach ($retainages as $data) {
                $retainage_id = $data->id;
                $retainage = Retainage::find($retainage_id);
                $lc = new LogConverter('retainage', 'delete');
                $lc->setFrom(Auth::user())->setTo($retainage)->setDesc(Auth::user()->email . ' deleted reatainage ' . $data->id . ' as a part of parcel '.$this->parcel_id.' deletion.')->save();
                // remove database record
                $retainage->delete();
            }
        }
        /// delete dispositions
        $dispositions = DB::table('dispositions')->select('*')->where('parcel_id', $this->id)->get()->all();
        if (count($dispositions)>0) {
            foreach ($dispositions as $data) {
                $disposition_id = $data->id;
                $disposition = Disposition::find($disposition_id);
                // remove database record
                $lc = new LogConverter('disposition', 'delete');
                $lc->setFrom(Auth::user())->setTo($disposition)->setDesc(Auth::user()->email . ' deleted disposition and related disposition items ' . $data->id . ' as a part of parcel '.$this->parcel_id.' deletion.')->save();
                $disposition->delete();
                // remove disposition items
                DB::table('disposition_items')->where('disposition_id', $data->id)->delete();
                // get the disposition invoice number
                $dispositionInvoiceId = DB::table('dispositions_to_invoices')->select('disposition_invoice_id as id')->where('disposition_id', $data->id)->first();
                DB::table('dispositions_to_invoices')->where('disposition_id', $data->id)->delete();

                $otherDispositions = DB::table('dispositions_to_invoices')->where('disposition_invoice_id', $dispositionInvoiceId->id)->count();
                if ($otherDispositions < 1) {
                    /// it was the only disposition - delete the invoice
                    DB::table('disposition_invoices')->where('id', $dispositionInvoiceId->id)->delete();
                    $lc = new LogConverter('disposition_invoices', 'delete');
                    $lc->setFrom(Auth::user())->setTo($dispositionInvoiceId)->setDesc(Auth::user()->email . ' deleted displosition invoice ' . $dispositionInvoiceId->id . ' as a part of parcel '.$this->parcel_id.' deletion.')->save();
                    $payments = DB::table('transactions')->select('*')->where('type_id', 2)->where('link_to_type_id', $dispositionInvoiceId->id)->get()->all();
                        
                    if (count($payments > 0)) {
                        session(['systemMessage'=> session('systemMessage')."<br />Disposition Invoice ".$dispositionInvoiceId->id." was deleted because the parcel you deleted was its only parcel. However, that invoice had transactions against it. The transactions were NOT deleted, but are now tied to a non-existent invoice. Please reconcile these transactions. See below:"]);
                        foreach ($payments as $data) {
                            $lc = new LogConverter('payment', 'orphaned');
                            $lc->setFrom(Auth::user())->setTo($data)->setDesc(Auth::user()->email . ' orphaned transaction ' . $data->id . ' as a part of parcel '.$this->parcel_id.' deletion.')->save();
                            session(['systemMessage'=> session('systemMessage').'<br /> • Transaction ID '.$data->id.' for $'.$data->amount]);
                        }
                    }
                    $payments = "";
                    /// remove approvals
                    DB::table('approval_requests')->where('approval_type_id', 1)->where('link_type_id', $dispositionInvoiceId->id)->delete();
                }
            }
        }
        /// delete invoice items
        $invoice_items = DB::table('invoice_items')->select('*')->where('parcel_id', $this->id)->get()->all();
        if (count($invoice_items)>0) {
            foreach ($invoice_items as $data) {
                $invoiceId = $data->invoice_id;
                $invoice_item_id = $data->id;
                $invoice_item = InvoiceItem::find($invoice_item_id);
                // remove database record
                $invoice_item->delete();
            }
        }

        /// delete po items
        $po_items = DB::table('po_items')->select('*')->where('parcel_id', $this->id)->get()->all();
        if (count($po_items)>0) {
            foreach ($po_items as $data) {
                $poId = $data->po_id;
                $po_item_id = $data->id;
                $po_item = PoItems::find($po_item_id);
                // remove database record
                $po_item->delete();
            }
        }
        /// delete request items
        $request_items = DB::table('request_items')->select('*')->where('parcel_id', $this->id)->get()->all();
        if (count($request_items)>0) {
            foreach ($request_items as $data) {
                $reqId = $data->req_id;
                $request_item_id = $data->id;
                $request_item = RequestItem::find($request_item_id);
                // remove database record
                $request_item->delete();
            }
        }
        /// delete cost items

        $cost_items = DB::table('cost_items')->select('*')->where('parcel_id', $this->id)->get()->all();
        if (count($cost_items)>0) {
            foreach ($cost_items as $data) {
                $cost_item_id = $data->id;
                $cost_item = CostItem::find($cost_item_id);
                // remove database record
                $cost_item->delete();
            }
        }
        /// delete recapture items
        $recapture_items = DB::table('recapture_items')->select('*')->where('parcel_id', $this->id)->get()->all();
        if (count($recapture_items)>0) {
            foreach ($recapture_items as $data) {
                $recapture_item_id = $data->id;
                $recapture_item = RecaptureItem::find($recapture_item_id);
                // remove database record
                $recapture_item->delete();
                // check if there are any more recapture items in the invoice
                $otherRecaptures = DB::table('recapture_items')->where('recapture_invoice_id', $data->recapture_invoice_id)->count();
                if ($otherRecaptures < 0) {
                    // no other recaptures - delete the invoice
                    DB::table('recapture_invoices')->where('id', $data->recapture_invoice_id)->delete();
                    $lc = new LogConverter('recapture_invoices', 'delete');
                    $lc->setFrom(Auth::user())->setTo($data)->setDesc(Auth::user()->email . ' deleted recapture invoice ' . $data->id . ' as a part of parcel '.$this->parcel_id.' deletion.')->save();
                    $payments = DB::table('transactions')->select('*')->where('type_id', 6)->where('link_to_type_id', $data->recapture_invoice_id)->get()->all();
                    if (count($payments > 0)) {
                        session(['systemMessage'=> session('systemMessage')."<br />Recapture Invoice ".$data->recapture_invoice_id." was deleted because the parcel you deleted was its only parcel. However, that invoice had transactions against it. The transactions were NOT deleted, but are now tied to a non-existent invoice. Please reconcile these transactions. See below:"]);
                        foreach ($payments as $tdata) {
                            session(['systemMessage'=> session('systemMessage').'<br /> • Transaction ID '.$tdata->id.' for $'.$tdata->amount]);
                            $lc = new LogConverter('payment', 'orphaned');
                            $lc->setFrom(Auth::user())->setTo($data)->setDesc(Auth::user()->email . ' orphaned transaction ' . $tdata->id . ' as a part of parcel '.$this->parcel_id.' deletion.')->save();
                        }
                    }
                    $payments = "";
                    /// remove approvals
                    DB::table('approval_requests')->where('approval_type_id', 5)->where('link_type_id', $data->id)->delete();
                }
            }
        }
        /// remove from request, po, and invoices
        DB::table('parcels_to_reimbursement_invoices')->where('parcel_id', $this->id)->delete();
        DB::table('parcels_to_purchase_orders')->where('parcel_id', $this->id)->delete();
        DB::table('parcels_to_reimbursement_requests')->where('parcel_id', $this->id)->delete();

        /// check if the request, po, and invoices are now empty
        if (isset($reqId)) {
            $requestCount = DB::table('parcels_to_reimbursement_requests')->where('reimbursement_request_id', $reqId)->count();

            if ($requestCount < 1) {
                // delete the request etc because it only had this parcel in it.
                DB::table('request_notes')->where('reimbursement_request_id', $reqId)->delete();
                DB::table('approval_requests')->where('approval_type_id', 2)->where('link_type_id', $reqId)->delete();
                DB::table('reimbursement_requests')->where('id', $reqId)->delete();
                
                
                if (isset($poId)) {
                    DB::table('po_notes')->where('purchase_order_id', $poId)->delete();
                    DB::table('approval_requests')->where('approval_type_id', 3)->where('link_type_id', $poId)->delete();
                    DB::table('reimbursement_purchase_orders')->where('id', $poId)->delete();
                }
                if (isset($invoiceId)) {
                    DB::table('reimbursement_invoices')->where('id', $invoiceId)->delete();
                    $lc = new LogConverter('invoices', 'delete');
                    $lc->setFrom(Auth::user())->setTo($invoiceId)->setDesc(Auth::user()->email . ' deleted reimbursement invoice ' . $data->id . ' as a part of parcel '.$this->parcel_id.' deletion.')->save();
                    DB::table('invoice_notes')->where('reimbursement_invoice_id', $invoiceId)->delete();
                    /// check for payments made to this invoice and alert the user they were deleted via the system message.
                    $payments = DB::table('transactions')->select('*')->where('type_id', 1)->where('link_to_type_id', $invoiceId)->get()->all();
                    if (count($payments > 0)) {
                        session(['systemMessage'=> session('systemMessage')."<br />Invoice ".$invoiceId." was deleted because the parcel you deleted was its only parcel. However, that invoice had transactions against it. The transactions were NOT deleted, but are now tied to a non-existent invoice. Please reconcile these transactions. See below:"]);
                        foreach ($payments as $data) {
                            $lc = new LogConverter('payment', 'orphaned');
                            $lc->setFrom(Auth::user())->setTo($data)->setDesc(Auth::user()->email . ' orphaned transaction ' . $data->id . ' as a part of parcel '.$this->parcel_id.' deletion.')->save();
                            session(['systemMessage'=> session('systemMessage').'<br /> • Transaction ID '.$data->id.' for $'.$data->amount]);
                        }
                    }
                    DB::table('approval_requests')->where('approval_type_id', 8)->where('link_type_id', $invoiceId)->delete();
                    DB::table('approval_requests')->where('approval_type_id', 9)->where('link_type_id', $invoiceId)->delete();
                    DB::table('approval_requests')->where('approval_type_id', 10)->where('link_type_id', $invoiceId)->delete();
                    DB::table('approval_requests')->where('approval_type_id', 4)->where('link_type_id', $invoiceId)->delete();
                }
            }
        }

        /// remove from the import record and validations
        DB::table('import_rows')->where('row_id', $this->id)->delete();
        DB::table('validation_resolutions')->where('parcel_id', $this->id)->delete();
        DB::table('validation_resolutions')->where('resolution_id', $this->id)->where('resolution_type', 'parcels')->delete();

        /// delete any site visits
        DB::table('site_visits')->where('parcel_id', $this->id)->delete();

        /* HISTORIC WAIVER NEEDS ADDED ONCE DONE */
        $this->delete();
        return 1;
    }

    /**
     * Reassign Parcel
     *
     * @param $reqId
     *
     * @return int
     */
    public function reassignParcel($reqId)
    {
        /* @todo: move this to a service class method */

        // get info for new request
        $newRequest = ReimbursementRequest::find($reqId);
        // get counts of current items
        $currentRequestId = ParcelsToReimbursementRequest::where('parcel_id', $this->id)->first();
        $currentReqItems = RequestItem::where('parcel_id', $this->id)->count();
        $newReqItems = RequestItem::where('req_id', $reqId)->count();

        $current_cost_items = CostItem::where('parcel_id', $this->id)->count();
        $currentPOItems = PoItems::where('parcel_id', $this->id)->count();
        $current_invoice_items = InvoiceItem::where('parcel_id', $this->id)->count();

        //update request to this one
        ParcelsToReimbursementRequest::where('parcel_id', $this->id)->update(['reimbursement_request_id'=>$newRequest->id]);
        
        if ($currentReqItems > 0 && $newReqItems > 0) {
            RequestItem::where('parcel_id', $this->id)->update(['req_id'=>$newRequest->id]);
        } elseif ($currentReqItems < 1 && $newReqItems > 0) {
            // the new req has req items - we need to insert req_items for this parcel - we will default to same as the cost items.
            if ($current_cost_items > 0) {
                // we have cost items to work from
                $costItemsToCopy = CostItem::where('parcel_id', $this->id)->select('*')->get();
                foreach ($costItemsToCopy as $data) {
                    $reqItem = new RequestItem;
                    $reqItem->breakout_type = $data->breakout_type;
                    $reqItem->req_id = $reqId;
                    $reqItem->parcel_id = $data->parcel_id;
                    $reqItem->account_id = $data->account_id;
                    $reqItem->program_id = $data->program_id;
                    $reqItem->entity_id = $data->entity_id;
                    $reqItem->expense_category_id = $data->expense_category_id;
                    $reqItem->amount = $data->amount;
                    $reqItem->vendor_id = $data->vendor_id;
                    $reqItem->description = $data->description;
                    $reqItem->notes = $data->notes;
                    $reqItem->ref_id = $data->id;
                    $reqItem->save();
                }
                session(['systemMessage'=>session('systemMessage').'<p>Your current parcel did not have its request items in place, however the new request did - so I created request amounts based on your cost amounts. Adjust as needed.</p>']);
            } else {
                session(['systemMessage'=>session('systemMessage').'<p>Your current parcel did not have any costs entered, you will need to enter these.</p>']);
            }
        }

        // get info for new PO and Invoice
        $newPo = ReimbursementPurchaseOrders::where('rq_id', $newRequest->id)->first();
        if (isset($newPo->id)) {
            // check to see if the old one had a PO
            if ($currentPOItems > 0) {
                ParcelsToPurchaseOrder::where('parcel_id', $this->id)->update(['purchase_order_id'=>$newPo->id]);
                PoItems::where('parcel_id', $this->id)->update(['po_id'=>$newPo->id]);
            } elseif ($current_cost_items > 0) {
                //we know if there were costs - we made request items to copy as po items
                $itemsToCopy = RequestItem::where('parcel_id', $this->id)->select('*')->get();
                foreach ($itemsToCopy as $data) {
                    $poItem = new PoItems;
                    $poItem->breakout_type = $data->breakout_type;
                    $poItem->po_id = $newPo->id;
                    $poItem->parcel_id = $data->parcel_id;
                    $poItem->account_id = $data->account_id;
                    $poItem->program_id = $data->program_id;
                    $poItem->entity_id = $data->entity_id;
                    $poItem->expense_category_id = $data->expense_category_id;
                    $poItem->amount = $data->amount;
                    $poItem->vendor_id = $data->vendor_id;
                    $poItem->description = $data->description;
                    $poItem->notes = $data->notes;
                    $poItem->ref_id = $data->id;
                    $poItem->save();
                }
                /// add this to the parcels_to_purchase_orders table
                $addToPo = new ParcelsToPurchaseOrder;
                $addToPo->purchase_order_id = $newPo->id;
                $addToPo->parcel_id = $this->id;
                $addToPo->save();

                session(['systemMessage'=>session('systemMessage').'<p>Your current parcel did not have its po/approved items in place, however the new request did - so I created approved amounts based on your request amounts. Adjust as needed.</p>']);
            }
            $newInvoice = ReimbursementInvoice::where('po_id', $newPo->id)->first();
        } else {
            // there is no PO - let them know that the PO Amounts are going to be deleted.
            session(['systemMessage'=>session('systemMessage')."<p>The new request does not have a PO. I had to remove any approved amounts for the parcel.</p>"]);
            PoItems::where('parcel_id', $this->id)->delete();
            ParcelsToPurchaseOrder::where('parcel_id', $this->id)->delete();
            /// delete from the PO to parcel reference table
           

            $newInvoice = "";
        }
        if (isset($newPo->id)) {
            $newInvoice = ReimbursementInvoice::where('po_id', $newPo->id)->first();
            if (isset($newInvoice->id)) {
                // check to see if the old one had an Invoice
                if ($current_invoice_items > 0) {
                    ParcelsToReimbursementInvoice::where('parcel_id', $this->id)->update(['reimbursement_invoice_id'=>$newInvoice->id]);
                    InvoiceItem::where('parcel_id', $this->id)->update(['invoice_id'=>$newInvoice->id]);
                } elseif ($current_cost_items > 0) {
                    //we know if there were costs - we made po items to copy as po items
                    $itemsToCopy = PoItems::where('parcel_id', $this->id)->select('*')->get();
                    foreach ($itemsToCopy as $data) {
                        $invoiceItem = new InvoiceItem;
                        $invoiceItem->breakout_type = $data->breakout_type;
                        $invoiceItem->invoice_id = $newInvoice->id;
                        $invoiceItem->parcel_id = $data->parcel_id;
                        $invoiceItem->account_id = $data->account_id;
                        $invoiceItem->program_id = $data->program_id;
                        $invoiceItem->entity_id = $data->entity_id;
                        $invoiceItem->expense_category_id = $data->expense_category_id;
                        $invoiceItem->amount = $data->amount;
                        $invoiceItem->vendor_id = $data->vendor_id;
                        $invoiceItem->description = $data->description;
                        $invoiceItem->notes = $data->notes;
                        $invoiceItem->ref_id = $data->id;
                        
                        $invoiceItem->save();
                    }
                    /// add this to the parcels_to_purchase_orders table
                    $addToInvoice = new ParcelsToReimbursementInvoice;
                    $addToInvoice->reimbursement_invoice_id = $newInvoice->id;
                    $addToInvoice->parcel_id = $this->id;
                    $addToInvoice->save();

                    session(['systemMessage'=>session('systemMessage').'<p>Your current parcel did not have its inoived items in place, however the new request did - so I created invoiced amounts based on your po amounts. Adjust as needed.</p>']);
                }
                $newInvoice = ReimbursementInvoice::where('po_id', $newPo->id)->first();
            } else {
                // there is no PO - let them know that the PO Amounts are going to be deleted.
                //session(['systemMessage'=>session('systemMessage')."<p>The new request does not have a Invoice. I had to remove any invoiced amounts for the parcel.</p>"]);
                $invoice_items_to_delete = InvoiceItem::where('parcel_id', $this->id)->get();
                foreach ($invoice_items_to_delete as $delete_me) {
                    $deleteThis = InvoiceItem::find($delete_me->id);
                    $deleteThis->delete();
                    \Log::debug('Firing the delete method.');
                    // stepping through this way to check that the event is firing.
                    // YOU HAVE TO DO IT THIS WAY OR THE DELETED EVENT DOES NOT FIRE.
                }
                ParcelsToReimbursementInvoice::where('parcel_id', $this->id)->delete();
                /// delete from the PO to parcel reference table
            }
        } else {
            if ($current_invoice_items > 0) {
                // No PO means no invoice but the previous one had invoice items.
                $invoice_items_to_delete = InvoiceItem::where('parcel_id', $this->id)->get();
                foreach ($invoice_items_to_delete as $delete_me) {
                    $deleteThis = InvoiceItem::find($delete_me->id);
                    $deleteThis->delete();
                    \Log::debug('Firing the delete method on line 918.');
                    // stepping through this way to check that the event is firing.
                    // YOU HAVE TO DO IT THIS WAY OR THE DELETED EVENT DOES NOT FIRE.
                }
                ParcelsToReimbursementInvoice::where('parcel_id', $this->id)->delete();
                /// delete from the invoice to parcel reference table
            }
        }
        $lc = new LogConverter('parcel', 'reassign');
        if (isset($currentRequestId->reimbursement_request_id)) {
            $lc->setFrom(Auth::user())->setTo($this)->setDesc(Auth::user()->email . ' reassigned parcel from request '.$currentRequestId->reimbursement_request_id.' to '. $reqId)->save();
            return 1;
        } else {
            $lc->setFrom(Auth::user())->setTo($this)->setDesc(Auth::user()->email . ' assigned parcel to '. $reqId)->save();
            return 1;
        }
    }

    /**
     * Site Visits
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siteVisits() : HasMany
    {
        return $this->hasMany('App\SiteVisits', 'parcel_id', 'id')
                ->orderBy('visit_date', 'DESC');
    }

    /**
     * Site Visit Lists
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function siteVisitLists() : HasMany
    {
        return $this->hasMany('App\VisitLists', 'parcel_id', 'id')
                ->orderBy('created_at', 'DESC');
    }

    /**
     * Last Site Visit
     *
     * @return mixed
     */
    public function lastSiteVisit()
    {
        return $this->siteVisits()
                ->orderBy('visit_date', 'DESC')
                ->first();
    }
}
