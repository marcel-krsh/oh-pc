<?php

return [

    'services' => [
        'google' => [
            'api_key' => env('GOOGLE_API_KEY'),
            'api_url' => env('GOOGLE_API_URL', 'https://maps.googleapis.com/maps/api/geocode/json?'),
        ],
        'ohiogeocode' => [
            'url' => env('OHIOGEOCODE_URL', 'http://geocodews.test.oit.ohio.gov/ohiogeocode/geocodeservice.asmx?wsdl'),
            'login' => env('OHIOGEOCODE_LOGIN'),
            'password' => env('OHIOGEOCODE_PASSWORD'),
        ],
    ],

];
