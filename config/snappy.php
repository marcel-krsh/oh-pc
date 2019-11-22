<?php

return [

    'pdf' => [
        'enabled' => true,
        'binary' => env('WKHTMLTOPDF_PATH', '/usr/local/bin/wkhtmltopdf-amd64'),
        'timeout' => false,
        'env' => [],
    ],
    // 'image' => array(
    //     'enabled' => true,
    //     'binary'  => '/usr/local/bin/wkhtmltoimage',
    //     'timeout' => false,
    //     'options' => array(),
    //     'env'     => array(),
    // ),

];
