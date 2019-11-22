<?php

return [

    'type' => [
        1 => 'Communications',
        2 => 'Reports - Send to PM',
        3 => 'Reports - To Manager Review',
    ],

    'frequency' => [
        1 => 'Immediately',
        2 => 'Hourly',
        3 => 'Daily',
    ],

    'main_tab' => [
        1 => 'detail-tab-2',
    ],

    'models' => [
        1 => App\Models\CommunicationRecipient::class,
        2 => App\Models\CrrReport::class,
    ],

];
