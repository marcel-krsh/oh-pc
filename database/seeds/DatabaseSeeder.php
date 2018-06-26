<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ProgramRulesSeeder::class);
        $this->call(TransactionsSeeder::class);
        $this->call(InvoiceStatusesSeeder::class);



        $this->call(EntitiesSeeder::class);
        $this->call(ProgramsSeeder::class);
        $this->call(AccountsSeeder::class);

        $this->call(TransactionCategoriesSeeder::class);
        $this->call(TransactionTypesSeeder::class);
        $this->call(TransactionStatusesSeeder::class);
        $this->call(AccountTypesSeeder::class);
        $this->call(ParcelTypeOptionsSeeder::class);
        $this->call(HowAcquiredOptionsSeeder::class);
        $this->call(PropertyStatusOptionsSeeder::class);

        $this->call(TargetAreasSeeder::class);

        $this->call(StatesSeeder::class);
        $this->call(CountiesSeeder::class);

        $this->call(UsersTableSeeder::class);
        $this->call(ExpenseCategoriesTableSeeder::class);
        $this->call(DispositionSeeder::class);

        $this->call(PermissionsSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(PermissionRoleSeeder::class);
        $this->call(UserRolesSeeder::class);

    }
}
