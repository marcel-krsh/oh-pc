<?php

namespace App\Http\Controllers\Helper;

//
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;

//
class TestingController extends Controller
{

  public function getTestAll()
  {
    $urls = [
      'http://homestead2.test',
      'http://homestead2.test/logout',
    ];
    $post_urls = [
      'http://localhost/add-announcement',
    ];
    $client = new Client(['timeout' => 300]);
    $request_promises = [];
    foreach ($urls as $key => $url) {
      $request_promises[$key] = $client->getAsync($url);
    }
    // foreach ($post_urls as $key => $url) {
    //   $request_promises[$key + 100] = $client->postAsync($url);
    // }
    $one = microtime(true);
    $results = Promise\settle($request_promises)->wait();
    $two = microtime(true);
    $count = 0;
    foreach ($results as $key => $result) {
      if (array_key_exists('value', $result)) {
        $response = $result['value']->getReasonPhrase();
      } else {
        $response = 'ERROR';
      }
      if ($key > 99) {
        echo 'POST -- ' . $post_urls[$count] . ' -- RESPONSE: ' . $response;
        $count++;
      } else {
        echo '<a href="' . $urls[$key] . '">' . $urls[$key] . ' -- RESPONSE: ' . $response;
      }
      echo '<br>';
    }
    return ' <br> -- done in ' . ' -- Time: ' . ($two - $one);

    /*Teacher tests*/
  }
}
