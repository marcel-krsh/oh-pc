<?php

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'key' => 'devco_token',
                'value' => null,
            ], [
                'key' => 'devco_refresh_token',
                'value' => null,
            ],
        ];

        foreach ($data as $item) {
            SystemSetting::create($item);
        }
    }
}
