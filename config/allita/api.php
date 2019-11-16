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

    'url' => env('ALLITA_PCAPI_URL'),

    /*
    |--------------------------------------------------------------------------
    | API BASE DIRECTORY
    |--------------------------------------------------------------------------
    |
    | This string value acts as the base directory that will be used for all PC-API calls.
    |
    */
    'base_directory' => env('ALLITA_PCAPI_BASE_DIRECTORY'),

    /*
    |--------------------------------------------------------------------------
    | API URL
    |--------------------------------------------------------------------------
    |
    | This string value acts as the base url that will be used for all PC-API calls.
    |
    */

    'username' => env('ALLITA_PCAPI_USERNAME'),

    /*
    |--------------------------------------------------------------------------
    | API URL
    |--------------------------------------------------------------------------
    |
    | This string value acts as the base url that will be used for all PC-API calls.
    |
    */

    'password' => env('ALLITA_PCAPI_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | LOGIN URL
    |--------------------------------------------------------------------------
    |
    | This is the main login url for the DEVCO system.
    |
    */

    'login_url' => env('DEVCO_LOGIN_URL'),

    /*
    |--------------------------------------------------------------------------
    | KEY
    |--------------------------------------------------------------------------
    |
    | This is the key for the DEVCO system.
    |
    */

    'key' => env('ALLITA_PCAPI_KEY'),

    /*
    |--------------------------------------------------------------------------
    | TOKEN EXPIRATION
    |--------------------------------------------------------------------------
    |
    | This length of time a token can live before we reauthenticate with the api
    | This should be considerably less than the actual expiration
    */
    'allita_pcapi_token_expires_in' => env('ALLITA_PCAPI_TOKEN_EXPIRES_IN'),

    /*
    |--------------------------------------------------------------------------
    | AUTH PARAMETERS FOR USER
    |--------------------------------------------------------------------------
    |
    | These variables are used primarily by the AllitaAuth middleware
    |
    */

    'max_login_tries' => env('MAX_LOGIN_TRIES'),
    'block_out_time_factor' => env('BLOCK_OUT_TIME_FACTOR'),
    'max_unlock_tries' => env('MAX_UNLOCK_TRIES'),
    'remember_me_session_length' => env('REMEMBER_ME_SESSION_LENGTH'),
    'devco_login_url' => env('DEVCO_LOGIN_URL'),

];
