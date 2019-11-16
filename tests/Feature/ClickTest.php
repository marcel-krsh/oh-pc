<?php

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClickTest extends TestCase
{
    public function testDashboardRoutes()
    {
        $this->mockUser();

        $this->visitRoute('dashboard')->assertResponseOk();
        $this->visit('/site_visit_manager')->assertResponseOk();
        $this->visit('/site_visit_manager/index')->assertResponseOk();
        $this->visit('/site_visit_manager/visit_list')->assertResponseOk();
        $this->visit('/site_visit_manager/manage_devices')->assertResponseOk();
        $this->visit('/dashboard/user_list')->assertResponseOk();
        $this->visit('/dashboard/invoice_list')->assertResponseOk();
        $this->visit('/dashboard/po_list')->assertResponseOk();
        $this->visit('/dashboard/request_list')->assertResponseOk();
        $this->visit('/dashboard/accounting')->assertResponseOk();
        $this->visit('/dashboard/parcel_list')->assertResponseOk();
        $this->visit('/dashboard/stats')->assertResponseOk();
        $this->visit('/dashboard/map')->assertResponseOk();
        $this->visit('/dashboard/admin_tools')->assertResponseOk();
        $this->visit('/dashboard/communications')->assertResponseOk();
        $this->visit('/dashboard/disposition_list')->assertResponseOk();
        $this->visit('/dashboard/disposition_invoice_list')->assertResponseOk();

        $this->visit('/home')->assertResponseOk();
    }

    public function testParcelRoutes()
    {
        $this->mockUser();

        $parcel = \App\Parcel::first();

        $this->visit("/parcel/{$parcel->id}")->assertResponseOk();
        $this->visit("/detail/parcel/{$parcel->id}")->assertResponseOk();
        $this->visit("/breakouts/parcel/{$parcel->id}")->assertResponseOk();
        $this->visit('/modals/breakout_cost_item/4')->assertResponseOk();
        $this->visit("/parcel/next_step/{$parcel->id}")->assertResponseOk();
    }

    public function testImportRoutes()
    {
        $this->mockUser();

        //$this->visit('/parcels')->assertResponseOk(); This isn't a valid URL
        $this->visit('/import_parcels')->assertResponseOk();
        $this->visit('/import_hhf_retention_parcels')->assertResponseOk();
        $this->visit('/import_costs')->assertResponseOk();
        $this->visit('/validate_parcels')->assertResponseOk();
        $this->visit('/validate_hhf_retention_parcels')->assertResponseOk();
        //$this->visit('/validate_historic_parcels')->assertResponseOk();
        $this->visit('/validate_parcel')->assertResponseOk();

        $parcel = \App\Parcel::first();
        $this->visit("/force_validate?parcelId={$parcel->id}")->assertResponseOk();
        $this->visit('/validate_hhf_retention_parcel')->assertResponseOk();
        //$this->visit('/validate_historic_parcel')->assertResponseOk(); I don't think this exists any more
        $this->visit('/geodata')->assertResponseOk();

        $program = \App\Program::first();
        //$this->visit("/import_parcels_template?program_id={$program->id}")->assertResponseOk();
        //$this->visit('/import_historic_parcels_template')->assertResponseOk();

        //$this->visit('/import_progress')->assertResponseOk();
        //$this->visit('/hhf_retention_import_progress')->assertResponseOk();
        //$this->visit('/historic_import_progress')->assertResponseOk();

        $this->visit('/reports/export_parcels')->assertResponseOk();
        // $this->visit('/reports/export_parcels/all_parcel_data_06-13-2017_6-03-55_pm.xls/download')->assertResponseOk();
        $this->visit('/reports/export_vendor_stats')->assertResponseOk();
        // $this->visit('/reports/export_vendor_stats_process')->assertResponseOk();
        $this->visit('/reports/export_vendor_stats/1/download')->assertResponseOk();
    }

    public function testModalsRoutes()
    {
        $this->mockUser();

        $this->visit('/modals/user/1')->assertResponseOk();
        $this->visit('/modals/accounting/statBreakDown/3')->assertResponseOk();
        $this->visit('/modals/rules/edit/1')->assertResponseOk();
        $this->visit('/modals/reimbursement_how_to')->assertResponseOk();

        $parcel = \App\Parcel::first();
        $this->visit("/modals/correct_parcel_address/{$parcel->id}")->assertResponseOk();
        $this->visit("/modals/resolve_validation/{$parcel->id}")->assertResponseOk();
        $this->visit("/modals/new-note-entry/{$parcel->id}")->assertResponseOk();
    }

    public function testDocumentsRoutes()
    {
        $this->mockUser();

        $parcel = \App\Parcel::first();
        $this->visit("/documents/parcel/{$parcel->id}")->assertResponseOk();
        //$this->visit("/documents/parcel/{$parcel->id}/downloaddocument/1")->assertResponseOk();
            // ** This download path works but displays a message that the file
            // doesn't exist on your local system.
    }

    public function testNotesRoutes()
    {
        $this->mockUser();

        $parcel = \App\Parcel::first();
        $this->visit("/notes/parcel/{$parcel->id}")->assertResponseOk();
        $this->visit("/notes/parcel/{$parcel->id}.json")->assertResponseOk();
        $this->visit("/external-window/print-notes-{$parcel->id}.html")->assertResponseOk();
    }

    public function testAdminRoutes()
    {
        $this->mockUser();

        $this->visit('/modals/admin/entity/create/')->assertResponseOk();
        $this->visit('/modals/admin/program/create/')->assertResponseOk();
        $this->visit('/modals/admin/rule/create/')->assertResponseOk();
        $this->visit('/modals/admin/account/create/')->assertResponseOk();
        $this->visit('/modals/admin/document_category/create/')->assertResponseOk();
        $this->visit('/modals/admin/expense_category/create/')->assertResponseOk();
        $this->visit('/modals/admin/vendor/create/')->assertResponseOk();
        $this->visit('/modals/admin/target_area/create/')->assertResponseOk();
        $this->visit('/modals/admin/county/create/')->assertResponseOk();
    }

    public function testAdminTabsRoutes()
    {
        $this->mockUser();

        $entity = \App\Entity::first();
        $this->visit("/modals/admin/deactivate/entity/{$entity->id}")->assertResponseOk();
        $this->visit("/modals/admin/activate/entity/{$entity->id}")->assertResponseOk();
        $this->visit('/modals/parcels/create/')->assertResponseOk();

        $program_rule = \App\ProgramRule::first();
        $this->visit("/testCreate/{$program_rule->id}")->assertResponseOk();
    }

    public function testPTRoutes()
    {
        $this->mockUser();

        $parcel = \App\Parcel::first();
        $this->visit("/notes/parcel/{$parcel->id}")->assertResponseOk();
        $this->visit('/communications/new-messages')->assertResponseOk();
        $this->visit("/communications/parcel/{$parcel->id}")->assertResponseOk();
        $this->visit("/communications/{$parcel->id}.json")->assertResponseOk();
        $this->visit("/modals/new-outbound-email-entry/{$parcel->id}")->assertResponseOk();

        $communication = \App\Communication::whereHas('parcel')->orderBy('id', 'DESC')->first();
        if ($communication) {
            $this->visit("/modals/communication/{$communication->parcel_id}/replies/{$communication->id}")->assertResponseOk();
        }
        //$this->visit('/preview/send/communication')->assertResponseOk(); I don't think this exists
        $this->visit("/view_message/{$communication->id}")->assertResponseOk();

        $invoice = \App\ReimbursementInvoice::first();
        $this->visit("/invoices/{$invoice->id}")->assertResponseOk();
        $this->visit("/modals/invoice/edit/{$invoice->id}")->assertResponseOk();

        //$this->visit('/modals/expense-categories-details/{output}/{category}/{program}/{parcel?}/{zero_values?}')->assertResponseOk();
        //$this->visit('/modals/expense-categories-vendor-details/{vendor}/{parcel?}/{program?}/{zero_values?}')->assertResponseOk();
        /*
        $this->visit('/dispositions/450/{disposition?}/{format?}')->assertResponseOk();
        // $this->visit('/disposition_invoice/1')->assertResponseOk();
        $this->visit('/modals/cost/450/add')->assertResponseOk();
        $this->visit('/modals/createuser')->assertResponseOk();
        // $this->visit('/modals/document-retainage-form/{parcel}/{documentids}')->assertResponseOk();
        // $this->visit('/modals/document-advance-form/{parcel}/{documentids}')->assertResponseOk();
        $this->visit('/modals/edit-document/1')->assertResponseOk();
        // $this->visit('/breakouts/450/advance')->assertResponseOk(); // has dd() output
        $this->visit('/compliance/450')->assertResponseOk();
        $this->visit('/modals/compliance/1')->assertResponseOk();
        $this->visit('/compliance/1196/1/edit')->assertResponseOk();
        $this->visit('/requests/1')->assertResponseOk();
        $this->visit('/po/1')->assertResponseOk();
        */
        // $this->visit('/modals/transaction/newFromInvoice/1')->assertResponseOk();
        // $this->visit('/modals/transaction/newFromDispositionInvoice/1')->assertResponseOk();
        // $this->visit('/modals/transaction/balance-credit')->assertResponseOk();
        // $this->visit('/modals/transaction/balance-debit')->assertResponseOk();
        // $this->visit('/modals/transaction/funding-award')->assertResponseOk();
        // $this->visit('/modals/transaction/funding-reduction')->assertResponseOk();
        // $this->visit('/modals/transaction/landbank-credit')->assertResponseOk();
        // $this->visit('/transactions/landbank-credit/options-1')->assertResponseOk();
        // $this->visit('/transactions/landbank-credit/options-2')->assertResponseOk();
        // $this->visit('/transactions/landbank-credit/options-3')->assertResponseOk();
        // $this->visit('/transactions/landbank-credit/options-4')->assertResponseOk();

        // $this->visit('/viewparcel/1')->assertResponseOk();
        // $this->visit('/viewvendor/1')->assertResponseOk();
        // $this->visit('/modals/email_history/{id}')->assertResponseOk();
    }

    public function testTimRoutes()
    {
        $this->mockUser();

        // $this->visit('register/verify/{token}')->assertResponseOk();

        $user = User::orderBy('id', 'desc')->first();
        $this->visit("/user/deactivate/{$user->id}")->assertResponseOk();
        $this->visit("/user/activate/{$user->id}")->assertResponseOk();
        $this->visit("/user/quick_delete/{$user->id}")->assertResponseOk();
        $this->visit("/user/quick_activate/{$user->id}")->assertResponseOk();
        $this->visit('/viewlogjson/all/0/10')->assertResponseOk();
        $this->visit('/dashboard/activity_logs')->assertResponseOk();
    }

    public function testBrianRoutes()
    {
        $this->visit('/history/parcel/1')->assertResponseOk();
        $this->visit('/parcels/parcel-lookup')->assertResponseOk();
        $this->visit('/parcels/parcel-autocomplete')->assertResponseOk();

        $transaction = \App\Transaction::orderBy('id', 'DESC')->first();
        $this->visit("/modals/transaction/edit/{$transaction->id}")->assertResponseOk();
        $this->visit('/change_to_validate/1')->assertResponseOk();
        $this->visit('/toggle_street_view_match/1')->assertResponseOk();
        $this->visit('/toggle_pretty/1')->assertResponseOk();
        $this->visit('/toggle_ugly/1')->assertResponseOk();
        // $this->visit('/parcels/delete/1')->assertResponseOk();
        $this->visit('/parcels/reassign/1')->assertResponseOk();
        $this->visit('/parcels/export')->assertResponseOk();
        //$this->visit('/dyanmic/images/1')->assertResponseOk();

        $this->visit('/notices/new')->assertResponseOk();
        $this->visit('/notices/unread')->assertResponseOk();

        //$this->visit('/modals/devices/users')->assertResponseOk();

        $device = \App\Device::first();
        if ($device) {
            $this->visit("/modals/wipe_device/{$device->id}")->assertResponseOk();
        }

        $visit = \App\SiteVisits::first();
        if ($visit) {
            $this->visit("/modals/site_visit/{$visit->id}")->assertResponseOk();
        }

        $photo = \App\Photo::first();
        if ($photo) {
            $this->visit("/images/files/{$photo->filename}")->assertResponseOk();
        }

        $this->visit('/notices/all')->assertResponseOk();
        $notice = \App\Notice::first();
        if ($notice) {
            $this->visit("/notices/images/{$notice->id}")->assertResponseOk();
        }
    }

    public function mockUser()
    {
        $user = User::findOrFail(1);
        $this->be($user);
    }
}
