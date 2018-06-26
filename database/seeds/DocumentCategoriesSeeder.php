<?php


namespace App;

use Illuminate\Database\Eloquent\Model;

class document_categories extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $documentCategoriesData = array(
            array(
                'document_category_name'=>'Consolidated Certification Form',
            ),
            array(
                'document_category_name'=>'Draft NIP/HHF Note & Mortgage',
            ),
            array(
                'document_category_name'=>'Executed NIP/HHF Note & Mortgage',
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
                'document_category_name'=>'Itemlized Property Reimbursement Form ',
            ),
            array(
                'document_category_name'=>'Proposed Invoice Summary Form',
            )
        );
        DB::table('document_categories')->insert($documentCategoriesData);

        $documentCategoriesData = array(
              array(
             'document_category_name'=>'Consolidated Certification Form',
             ),
            array(
             'document_category_name'=>'Draft NIP/HHF Note & Mortgage',
             ),
            array(
             'document_category_name'=>'Executed NIP/HHF Note & Mortgage',
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
             'document_category_name'=>'Itemlized Property Reimbursement Form ',
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
             )
      
        );
        DB::table('document_categories')->insert($documentCategoriesData); 
    } 
}
