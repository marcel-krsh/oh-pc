<?php

use Illuminate\Database\Seeder;

class ProgramRulesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\DB::table('program_rules')->insert(['hfa'=>1]);
    }
}
