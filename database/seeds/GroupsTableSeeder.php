<?php

use Illuminate\Database\Seeder;

class GroupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $groups = [
            [
                'id' => 1,
                'group_name' => 'FAF NSP TCE RTCAP 811',
                'active' => 1,
            ],
            [
                'id' => 2,
                'group_name' => '811',
                'active' => 1,
            ],
            [
                'id' => 3,
                'group_name' => 'Medicaid',
                'active' => 1,
            ],
            [
                'id' => 4,
                'group_name' => 'HOME',
                'active' => 1,
            ],
            [
                'id' => 5,
                'group_name' => 'OHTF',
                'active' => 1,
            ],
            [
                'id' => 6,
                'group_name' => 'NHTF',
                'active' => 1,
            ],
            [
                'id' => 7,
                'group_name' => 'HTC',
                'active' => 1,
            ],
        ];
        DB::table('groups')->insert($groups);
    }
}
