<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AssetVersionUpdateCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'asset:version_upgrade';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Updates the asset version in env file, specifically adds 0.01 to existing ASSET_VERSION value';

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
    $env_key       = 'ASSET_VERSION';
    $current_value = getenv("ASSET_VERSION");
    $new_value     = $current_value + 0.01;
    $envFile = app()->environmentFilePath();
    $str     = file_get_contents($envFile);
    $search = $env_key.'='.$current_value;
		if(preg_match("/{$search}/i", $str)) {
			$pattern = '/\b' . $search . '\b/i';
    	$str = preg_replace($pattern,$env_key . '=' . $new_value,$str);
		}
    $fp       = fopen($envFile, 'w');
    fwrite($fp, $str);
    fclose($fp);
  }

}
