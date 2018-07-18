<?php

use Illuminate\Database\Seeder;

class document_categories extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $documentCategoriesData = [
            [
                'document_category_name'=>'Consolidated Certification Form',
            ],
            [
                'document_category_name'=>'Draft NIP/HHF Note & Mortgage',
            ],
            [
                'document_category_name'=>'Executed NIP/HHF Note & Mortgage',
            ],
            [
                'document_category_name'=>'Historic Waiver Approval',
            ],
            [
                'document_category_name'=>'Lien Release Request Form',
            ],
            [
                'document_category_name'=>'Per Property Reimbursement Checklist',
            ],
            [
                'document_category_name'=>'Per Property Reimbursement Coversheet',
            ],
            [
                'document_category_name'=>'Reimbursement Support Document',
            ],
            [
                'document_category_name'=>'Retainage Paid Documentation',
            ],
            [
                'document_category_name'=>'Historic Waiver Request',
            ],
            [
                'document_category_name'=>'Analyst Checklist',
            ],
            [
                'document_category_name'=>'Approved Lien Release Form',
            ],
            [
                'document_category_name'=>'Quarterly Report',
            ],
            [
                'document_category_name'=>'Recorded Lien Release Form',
            ],
            [
                'document_category_name'=>'Signed Invoice',
            ],
            [
                'document_category_name'=>'Site Visit Land Bank Response',
            ],
            [
                'document_category_name'=>'Site Visit OHFA',
            ],
            [
                'document_category_name'=>'Target Area Amendment Form',
            ],
            [
                'document_category_name'=>'Disbursement Request Form',
            ],
            [
                'document_category_name'=>'Itemlized Property Reimbursement Form ',
            ],
            [
                'document_category_name'=>'Proposed Invoice Summary Form',
            ]
        ];
        DB::table('document_categories')->insert($documentCategoriesData);

        $documentCategoriesData = [
              [
             'document_category_name'=>'Consolidated Certification Form',
              ],
              [
              'document_category_name'=>'Draft NIP/HHF Note & Mortgage',
              ],
              [
              'document_category_name'=>'Executed NIP/HHF Note & Mortgage',
              ],
              [
              'document_category_name'=>'Historic Waiver Approval',
              ],
              [
              'document_category_name'=>'Lien Release Request Form',
              ],
              [
              'document_category_name'=>'Per Property Reimbursement Checklist',
              ],
              [
              'document_category_name'=>'Per Property Reimbursement Coversheet',
              ],
              [
              'document_category_name'=>'Reimbursement Support Document',
              ],
              [
              'document_category_name'=>'Retainage Paid Documentation',
              ],
              [
              'document_category_name'=>'Historic Waiver Request',
              ],
              [
              'document_category_name'=>'Analyst Checklist',
              ],
              [
              'document_category_name'=>'Approved Lien Release Form',
              ],
              [
              'document_category_name'=>'Quarterly Report',
              ],
              [
              'document_category_name'=>'Recorded Lien Release Form',
              ],
              [
              'document_category_name'=>'Signed Invoice',
              ],
              [
              'document_category_name'=>'Site Visit Land Bank Response',
              ],
              [
              'document_category_name'=>'Site Visit OHFA',
              ],
              [
              'document_category_name'=>'Target Area Amendment Form',
              ],
              [
              'document_category_name'=>'Disbursement Request Form',
              ],
              [
              'document_category_name'=>'Itemlized Property Reimbursement Form ',
              ],
              [
              'document_category_name'=>'Proposed Invoice Summary Form',
              ],
              [
              'document_category_name'=>'Draft Lien Release',
              ],
              [
              'document_category_name'=>'Disposition Use Affidavit',
              ],
              [
              'document_category_name'=>'Proof of Tax Exempt Status',
              ],
              [
              'document_category_name'=>'Proof the entity will commence operation/construction within one year',
              ],
              [
              'document_category_name'=>'Proof the property is zoned for its new use',
              ],
              [
              'document_category_name'=>'Purchase Agreement',
              ],
              [
              'document_category_name'=>'Proof that proposed owner is current on all real estate taxes and assessments in the county',
              ],
              [
              'document_category_name'=>'Proof the owner was not a prior owner of foreclosed real property in the county since 1/1/10',
              ],
              [
              'document_category_name'=>'Landbank Request Signature',
              ],
              [
              'document_category_name'=>'Landbank Re-Request Signature',
              ],
              [
              'document_category_name'=>'HFA PO Signature',
              ],
              [
              'document_category_name'=>'Landbank Invoice Signature',
              ],
              [
              'document_category_name'=>'HFA Invoice Primary Signature',
              ],
              [
              'document_category_name'=>'HFA Invoice Secondary Signature',
              ],
              [
              'document_category_name'=>'HFA Invoice Tertiary Signature',
              ]
      
        ];
        DB::table('document_categories')->insert($documentCategoriesData);
    }
}
