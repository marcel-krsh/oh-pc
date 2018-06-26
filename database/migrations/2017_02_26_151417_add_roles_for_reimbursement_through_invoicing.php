<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRolesForReimbursementThroughInvoicing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rolesData = array(
            
            array( //7
            'role_parent_id'=>2,
            'role_name'=>'Parcel Request Approver',
            'protected'=>1,
            'active'=>1
            ), 
            array( //8
            'role_parent_id'=>2,
            'role_name'=>'Reimbursement Request Approver',
            'protected'=>1,
            'active'=>1
            ), 
            array( //9
            'role_parent_id'=>1,
            'role_name'=>'Parcel PO Approver',
            'protected'=>1,
            'active'=>1
            ), 
            array( //10
            'role_parent_id'=>1,
            'role_name'=>'Compliance Auditor',
            'protected'=>1,
            'active'=>1
            ), 
            array( //11
            'role_parent_id'=>2,
            'role_name'=>'Invoice Aprrover',
            'protected'=>1,
            'active'=>1
            ), 
            array( //12
            'role_parent_id'=>1,
            'role_name'=>'Primary Invoice Approver',
            'protected'=>1,
            'active'=>1
            ), 
            array( //12
            'role_parent_id'=>1,
            'role_name'=>'Secondary Invoice Approver',
            'protected'=>1,
            'active'=>1
            ), 
            array( //12
            'role_parent_id'=>1,
            'role_name'=>'Tertiary Invoice Approver',
            'protected'=>1,
            'active'=>1
            ), 
            array( //12
            'role_parent_id'=>1,
            'role_name'=>'Fiscal Agent',
            'protected'=>1,
            'active'=>1
            )
        );
        DB::table('roles')->insert($rolesData);
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
