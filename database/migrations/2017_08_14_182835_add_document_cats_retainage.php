<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDocumentCatsRetainage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('document_categories')->truncate();
        
        $doc_cats = array(
            
            array(
             'document_category_name'=>'Consolidated Certification Form',
             ),
            array(
             'document_category_name'=>'Draft NIP/HHF Note and Mortgage',
             ),
            array(
             'document_category_name'=>'Executed NIP/HHF Note and Mortgage',
             ),
            array(
             'document_category_name'=>'Historic Waiver Approval',
             ),
            array(
             'document_category_name'=>'Lien Release Request Form',
             ),
            array(
             'document_category_name'=>'Per Property Reimbursement Checklist',
             ),
            array(
             'document_category_name'=>'Per Property Reimbursement Coversheet',
             ),
            array(
             'document_category_name'=>'Reimbursement Support Document',
             ),
            array(
             'document_category_name'=>'Retainage Paid Documentation',
             ),
            array(
             'document_category_name'=>'Historic Waiver Request',
             ),
            array(
             'document_category_name'=>'Analyst Checklist',
             ),
            array(
             'document_category_name'=>'Approved Lien Release Form',
             ),
            array(
             'document_category_name'=>'Quarterly Report',
             ),
            array(
             'document_category_name'=>'Recorded Lien Release Form',
             ),
            array(
             'document_category_name'=>'Signed Invoice',
             ),
            array(
             'document_category_name'=>'Site Visit Land Bank Response',
             ),
            array(
             'document_category_name'=>'Site Visit OHFA',
             ),
            array(
             'document_category_name'=>'Target Area Amendment Form',
             ),
            array(
             'document_category_name'=>'Disbursement Request Form',
             ),
            array(
             'document_category_name'=>'Itemized Property Reimbursement Form',
             ),
            array(
             'document_category_name'=>'Proposed Invoice Summary Form',
             ),
            array(
             'document_category_name'=>'Draft Lien Release',
             ),
            array(
             'document_category_name'=>'Disposition Use Affidavit',
             ),
            array(
             'document_category_name'=>'Proof of Tax Exempt Status',
             ),
            array(
             'document_category_name'=>'Proof the entity will commence operation/construction within one year',
             ),
            array(
             'document_category_name'=>'Proof the property is zoned for its new use',
             ),
            array(
             'document_category_name'=>'Purchase Agreement',
             ),
            array(
             'document_category_name'=>'Proof that proposed owner is current on all real estate taxes and assessments in the county',
             ),
            array(
             'document_category_name'=>'Proof the owner was not a prior owner of foreclosed real property in the county since 1/1/10',
             ),
            array(
             'document_category_name'=>'Landbank Request Signature',
             ),
            array(
             'document_category_name'=>'Landbank Re-Request Signature',
             ),
            array(
             'document_category_name'=>'HFA PO Signature',
             ),
            array(
             'document_category_name'=>'Landbank Invoice Signature',
             ),
            array(
             'document_category_name'=>'HFA Invoice Primary Signature',
             ),
            array(
             'document_category_name'=>'HFA Invoice Secondary Signature',
             ),
            array(
             'document_category_name'=>'HFA Invoice Tertiary Signature',
             ),
            array(
             'document_category_name'=>'Disposition Signature',
             ),
            array(
             'document_category_name'=>'Disposition Supporting Documents',
             ),
            array(
             'document_category_name'=>'Disposition Invoice Signature',
             ),
            array(
             'document_category_name'=>'Executed NIP/HHF Note & Mortgage',
             ),
            array(
             'document_category_name'=>'Historic Wavier Approval',
             ),
            array(
             'document_category_name'=>'Site Visit: OHFA',
             ),
            array(
             'document_category_name'=>'Site Visit: Land Bank Response',
             ),
            array(
             'document_category_name'=>'Preapproval: 5+ Units',
             ),
            array(
             'document_category_name'=>'Preapproval: Mobile Home',
             ),
            array(
             'document_category_name'=>'Disposition Sale/Transfer Document',
             ),
            array(
             'document_category_name'=>'Advance Payment Document', // 47
             ),
            array(
             'document_category_name'=>'Retainage Payment Document', // 48
             )

        );
        DB::table('document_categories')->insert($doc_cats);
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
