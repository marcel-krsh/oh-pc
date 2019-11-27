<?php
$domain = "http://localhost:8090/";
return [
  'get_urls' => [
    $domain,
    $domain . "logout",
  ],
  'post_urls' => [
    $domain . "login" => [
      'post_data' => ['email' => 'admin@admin.com', 'password' => '123456789'],
    ],
  ],
];
