<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRolesForEditing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rolesData = array(
            
            array(
            'role_parent_id'=>2,
            'role_name'=>'ACCOUNTING:: Can Edit Requests',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>2,
            'role_name'=>'PARCEL:: Can Edit Parcels',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'ACCOUNTING:: Can Edit Requests',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'ACCOUNTING:: Can Edit POs',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'ACCOUNTING:: Can Edit Invoices',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'ACCOUNTING:: Can Edit Transactions',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'ACCESS:: Use Simple Approvals <i class="uk-icon-info" uk-tooltip="(Hides all other approvals)"></i>',
            'protected'=>1,
            'active'=>1
            ),

            array(
            'role_parent_id'=>2,
            'role_name'=>'ACCESS:: Use Simple Approvals <i class="uk-icon-info" uk-tooltip="(Hides all other approvals)"></i>',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'ACCESS:: Auditor <i class="uk-icon-info" uk-tooltip="Block write and edit permissions (This overrides all other write and edit permissions)"></i>',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'PARCEL NOTES:: Can View Notes',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'PARCEL NOTES:: Can Create Notes',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'PARCEL NOTES:: Can Create Private Notes <i class="uk-icon-info" uk-tooltip="Only visible to your entity."></i>',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>2,
            'role_name'=>'PARCEL NOTES:: Can View Notes',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>2,
            'role_name'=>'PARCEL NOTES:: Can Create Notes',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>2,
            'role_name'=>'PARCEL NOTES:: Can Create Private Notes <i class="uk-icon-info" uk-tooltip="Only visible to your entity."></i>',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'PARCEL SITE VISITS:: Can Create Site Visits',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'PARCEL SITE VISITS:: Can View Site Visits',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'PARCEL SITE VISITS:: Can Delete Site Visits',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'PARCEL COMMUNICATIONS:: Can View Communications',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'PARCEL COMMMUNICATIONS:: Can Send Communications',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'ADMIN TAB:: Can Manage Vendors',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'ADMIN TAB:: Can Manage Accounts',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'ADMIN TAB:: Can Manage Entities',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'ADMIN TAB:: Can Manage Counties',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'ADMIN TAB:: Can Access Admin Tab <i class="uk-icon-info" uk-tooltip="Access to admin sub tabs will still need to be granted."></i>',
            'protected'=>1,
            'active'=>1
            ),array(
            'role_parent_id'=>1,
            'role_name'=>'ADMIN TAB:: Can Manage Target Areas',
            'protected'=>1,
            'active'=>1
            ),array(
            'role_parent_id'=>1,
            'role_name'=>'ADMIN TAB:: Can Manage Document Categories',
            'protected'=>1,
            'active'=>1
            ),array(
            'role_parent_id'=>1,
            'role_name'=>'ADMIN TAB:: Can Manage Expense Categories',
            'protected'=>1,
            'active'=>1
            ),array(
            'role_parent_id'=>1,
            'role_name'=>'ADMIN TAB:: Can Manage Rules',
            'protected'=>1,
            'active'=>1
            ),array(
            'role_parent_id'=>1,
            'role_name'=>'HISTORY:: Can View All History Tab <i class="uk-icon-info" uk-tooltip="This is different than viewing history on REQ, PO, Invoices and Parcels as it allows you to see the full history log outside of those elements."></i>',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'HISTORY:: Can View Parcel History',
            'protected'=>1,
            'active'=>1
            ),array(
            'role_parent_id'=>1,
            'role_name'=>'HISTORY:: Can View Request History',
            'protected'=>1,
            'active'=>1
            ),array(
            'role_parent_id'=>1,
            'role_name'=>'HISTORY:: Can View PO History',
            'protected'=>1,
            'active'=>1
            ),array(
            'role_parent_id'=>1,
            'role_name'=>'HISTORY:: Can View Invoice History',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>1,
            'role_name'=>'HISTORY:: Can View Personal History Log <i class="uk-icon-info" uk-tooltip="This allows the user to view their own history log outside of just REQ, PO, Invoices and Parcels."></i>',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>2,
            'role_name'=>'HISTORY:: Can View All History Tab <i class="uk-icon-info" uk-tooltip="This is different than viewing history on REQ, PO, Invoices and Parcels as it allows you to see the full history log outside of those elements."></i>',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>2,
            'role_name'=>'HISTORY:: Can View Parcel History',
            'protected'=>1,
            'active'=>1
            ),array(
            'role_parent_id'=>2,
            'role_name'=>'HISTORY:: Can View Request History',
            'protected'=>1,
            'active'=>1
            ),array(
            'role_parent_id'=>2,
            'role_name'=>'HISTORY:: Can View PO History',
            'protected'=>1,
            'active'=>1
            ),array(
            'role_parent_id'=>2,
            'role_name'=>'HISTORY:: Can View Invoice History',
            'protected'=>1,
            'active'=>1
            ),
            array(
            'role_parent_id'=>2,
            'role_name'=>'HISTORY:: Can View Personal History Log <i class="uk-icon-info" uk-tooltip="This allows the user to view their own history log outside of just REQ, PO, Invoices and Parcels."></i>',
            'protected'=>1,
            'active'=>1
            ),

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
