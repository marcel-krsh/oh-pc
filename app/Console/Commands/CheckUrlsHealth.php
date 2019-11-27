<?php

namespace App\Console\Commands;

use Auth;
use GuzzleHttp\Client;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Promise;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckUrlsHealth extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'urls:health';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Qucik way to check if all the requests are resulting in 200 status. This is just a quick check and don\'t considert this as full testing or nealy full testing.';

  /**
   * Create a new command instance.
   *
   * @return void
   */
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Execute the console command.
   *
   * @return mixed
   */
  public function handle()
  {
    $selection = $this->choice(
      'Select which type of requests to test?',
      ['Get requests', 'Post requests', 'Both Get & Post requests']
    );
    // $this->be($this->admin);

    if ($selection == "Get requests") {
      $get_routes = config('urls.get_urls');
      $this->runCheckGetRoutes($get_routes);
    } elseif ($selection == "Post requests") {
      $post_routes = config('urls.post_urls');
      $this->line("<fg=yellow>NOT READY YET</>");

      // $this->runCheckPostRoutes($post_routes);
    } elseif ($selection == "Both Get & Post requests") {
      $get_routes = config('urls.get_urls');
      $this->line("<fg=yellow>Running get requests first</>");
      $this->runCheckGetRoutes($get_routes);
      $post_routes = config('urls.post_urls');
      $this->line("<fg=yellow>Running post requests</>");
      $this->line("<fg=yellow>NOT READY YET</>");

      // $this->runCheckPostRoutes($post_routes);
    }
  }

  public function runCheckGetRoutes($get_routes)
  {
    if (@count($get_routes) == 0) {
      Log::info("There are no get requests. Please check config.urls file");
      $this->line("<fg=red>We dont have Get routes. Please check config.urls file</>");
    } else {
      $this->handleCheckGetRequest($get_routes);
    }
  }

  public function handleCheckGetRequest($urls)
  {
    $total = count($urls);
    $client = new Client(['timeout' => 300]);
    $cookieJar = new CookieJar();

    // $response = $client->post('http://homestead2.test/login/', [
    //   'form_params' => [
    //     'username' => 'BGreenwood7859@vadahmail.com',
    //     'password' => 'admin123',
    //     '_token' => csrf_token(),
    //   ],
    //   'cookies' => $cookieJar,
    // ]
    // );
    $request_promises = [];
    $processBar = $this->output->createProgressBar(count($urls));
    $count = 0;
    Auth::loginUsingId(7859, true);

    foreach ($urls as $key => $url) {
      try {
        $response = $client->request('GET', $url, ['cookies' => $cookieJar]);
        $response = $response->getStatusCode();
        $count++;
        if ($response == 200) {
          Log::info("Success in get url=" . $urls[$key] . " | Http Status code = " . $response);
          $this->line("<fg=green>Success in get url=" . $urls[$key] . " | Http Status code = " . $response . "</>");
        } else {
          Log::info("Failed in get url=" . $urls[$key] . " | Http Status code = " . $response);
          $this->line("<fg=red>Failed get url=" . $urls[$key] . " | Http Status code = " . $response . "</>");
        }
      } catch (ClientException $e) {
        if ($e->hasResponse()) {
          $exception = (string) $e->getResponse()->getBody();
          $exception = json_decode($exception);
          Log::info("Failed in get url=" . $urls[$key] . " | Http Status code = " . $e->getCode());
          $this->line("<fg=red>Failed get url=" . $urls[$key] . " | Http Status code = " . $e->getCode() . "</>");
          // return new JsonResponse($exception, $e->getCode());
        } else {
          echo $e->getMessage();
          // return new JsonResponse($e->getMessage(), 503);
        }
      } catch (RequestException $e) {
        // echo Psr7\str($e->getRequest());
        if ($e->hasResponse()) {
          $exception = (string) $e->getResponse()->getBody();
          $exception = json_decode($exception);
          Log::info("Failed in get url=" . $urls[$key] . " | Http Status code = " . $e->getCode());
          $this->line("<fg=red>Failed get url=" . $urls[$key] . " | Http Status code = " . $e->getCode() . "</>");}
      } catch (ServerException $e) {
        // echo Psr7\str($e->getRequest());
        if ($e->hasResponse()) {
          $exception = (string) $e->getResponse()->getBody();
          $exception = json_decode($exception);
          Log::info("Failed in get url=" . $urls[$key] . " | Http Status code = " . $e->getCode());
          $this->line("<fg=red>Failed get url=" . $urls[$key] . " | Http Status code = " . $e->getCode() . "</>");}
      } catch (BadResponseException $e) {
        // echo Psr7\str($e->getRequest());
        if ($e->hasResponse()) {
          $exception = (string) $e->getResponse()->getBody();
          $exception = json_decode($exception);
          Log::info("Failed in get url=" . $urls[$key] . " | Http Status code = " . $e->getCode());
          $this->line("<fg=red>Failed get url=" . $urls[$key] . " | Http Status code = " . $e->getCode() . "</>");}
      } catch (\Exception $e) {
        // echo Psr7\str($e->getRequest());
        if ($e->hasResponse()) {
          $exception = (string) $e->getResponse()->getBody();
          $exception = json_decode($exception);
          Log::info("Failed in get url=" . $urls[$key] . " | Http Status code = " . $e->getCode());
          $this->line("<fg=red>Failed get url=" . $urls[$key] . " | Http Status code = " . $e->getCode() . "</>");}
      }
      $processBar->advance();
    }
    $failed = $total - $count;
    $this->line("<fg=yellow>Total URLS = " . $total . "<fg=red>  | FAILED = " . $failed . "<fg=green>  | PASSED = " . $count . "</>");

    // return 12;
    // foreach ($urls as $key => $url) {
    //   $request_promises[$key] = $client->getAsync($url);
    // }
    // $one = microtime(true);
    // $results = Promise\settle($request_promises)->wait();
    // $two = microtime(true);
    // $count = 0;
    // foreach ($results as $key => $result) {
    //   if (array_key_exists('value', $result)) {
    //     $response = $result['value']->getReasonPhrase();
    //     $count++;
    //   } else {
    //     $response = 'ERROR';
    //   }
    //   if ($response == 'OK') {
    //     Log::info("Success in get url=" . $urls[$key] . " | Http Status code = " . $response);
    //     $this->line("<fg=green>Success in get url=" . $urls[$key] . " | Http Status code = " . $response . "</>");
    //   } else {
    //     Log::info("Failed in get url=" . $urls[$key] . " | Http Status code = " . $response);
    //     $this->line("<fg=red>Failed get url=" . $urls[$key] . " | Http Status code = " . $response . "</>");
    //   }
    // }
    // $failed = $total - $count;

    // $this->line("<fg=yellow>Total URLS = " . $total . "<fg=red>  | FAILED = " . $failed . "<fg=green>  | PASSED = " . $count . "</>");
  }

  public function runCheckPostRoutes($post_routes)
  {
    if (@count($post_routes) == 0) {
      Log::info("There are no post requests. Please check config.urls file");
      $this->line("<fg=red>We dont have Post routes. Please check config.urls file</>");
    } else {
      foreach ($post_routes as $post_route => $post_data) {
        $this->handleCheckPostRequest($post_route, $post_data['post_data']);
      }
    }
  }

  public function handleCheckPostRequest($url, $post_data)
  {
    $client = new Client(['timeout' => 300]);
    $request_promises = [];
    $request_promises = $client->postAsync($url, $post_data);
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
      if ($response == 'OK') {
        Log::info("Success in get url=" . $url . "-->Http Status code = " . $response);
        $this->line("<fg=green>Success in get url=" . $url . "-->Http Status code = " . $response . "</>");
      } else {
        Log::info("Failed in get url=" . $url . "-->Http Status code = " . $response);
        $this->line("<fg=red>Failed get url=" . $url . "-->Http Status code = " . $response . "</>");
      }
    }
  }

  // public function handleGetRequest($get_url)
  // {
  //   $ch = curl_init();

  //   // Set some options - we are passing in a useragent too here
  //   curl_setopt_array($ch, [
  //     CURLOPT_RETURNTRANSFER => 1,
  //     CURLOPT_URL => $get_url,
  //   ]);
  //   // Send the request & save response to $resp
  //   $server_output = curl_exec($ch);
  //   // Get Http Status Code
  //   $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  //   // Close request to clear up some resources
  //   curl_close($ch);
  //   // Further processing ...
  //   if ($statusCode != 200) {
  //     Log::info("Failed in get url=" . $get_url . "-->Http Status code = " . $statusCode);
  //     $this->line("<fg=red>Failed in get url=" . $get_url . "-->Http Status code = " . $statusCode . "</>");
  //   } else {
  //     Log::info("Failed in get url=" . $get_url . "-->Http Status code = " . $statusCode);
  //     $this->line("<fg=green>Success get url=" . $get_url . "-->Http Status code = " . $statusCode . "</>");
  //   }
  // }

  // public function handlePostRequest($post_url, $post_data)
  // {
  //   // Get cURL resource
  //   $ch = curl_init();
  //   // Set some options - we are passing in a useragent too here
  //   curl_setopt_array($ch, [
  //     CURLOPT_RETURNTRANSFER => 1,
  //     CURLOPT_URL => $post_url,
  //     CURLOPT_POST => 1,
  //     CURLOPT_POSTFIELDS => $post_data,
  //   ]);
  //   // Send the request & save response to $resp
  //   $server_output = curl_exec($ch);
  //   // Get Http Status Code
  //   $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
  //   // Close request to clear up some resources
  //   curl_close($ch);

  //   // Further processing ...
  //   if ($statusCode != 200) {
  //     Log::info("Failed in post url=" . $post_url . "-->Http Status code = " . $statusCode);
  //     $this->line("<fg=red>Failed in post url=" . $post_url . "-->Http Status code = " . $statusCode . "</>");
  //   } else {
  //     Log::info("Failed in post url=" . $post_url . "-->Http Status code = " . $statusCode);
  //     $this->line("<fg=green>Success post url=" . $post_url . "-->Http Status code = " . $statusCode . "</>");
  //   }
  // }
}
