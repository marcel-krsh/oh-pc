<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API URL
    |--------------------------------------------------------------------------
    |
    | This string value acts as the base url that will be used for all PC-API calls.
    |
    */

    'pcapi_url' => env('ALLITA_PCAPI_URL'),

    /*
    |--------------------------------------------------------------------------
    | DEVCO TOKEN
    |--------------------------------------------------------------------------
    |
    | This string value is used for all authenticated calls to the PC-API. The value
    | is rotated on a normal schedule on both this codebase and the PC-API side and
    | must stay in sync.
    |
    */

    'devco_token' => env('ALLITA_DEVCO_TOKEN'),

];
