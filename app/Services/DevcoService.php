<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Carbon;

class DevcoService extends PCAPIService
{

	/**
	 * Devco Addresses
	 */
	
	/**
	 * List Addresses
	 * 
	 * @param  int $page
	 * @param  string|null $newer_than
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function listAddresses(int $page = 1, string $newer_than = null, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		// example call
		// /api/v1/devco/addresses?page={{default=1/value}}&newer_than={{null/value}}

		$params = "page={$page}&newer_than={$newer_than}";

		$log_params = "user={$user_id}&user_email={$user_email}&user_name={$user_full_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/addresses?{$params}&{$log_params}");
	}

	/**
	 * Get Address
	 * 
	 * @param  int $address_key
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function getAddress(int $address_key, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		// example call
		// /api/v1/devco/addresses/{{address_key}}

		$log_params = "user={$user_id}&user_email={$user_email}&user_name={$user_full_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/addresses/{$address_key}?{$log_params}");
	}

	/**
	 * Update Address
	 * 
	 * @param  int $address_key
	 * @param  array $metadata
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function updateAddress(int $address_key, array $metadata, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		// example call
		// /api/v1/devco/addresses/{{address_key}}

		//  $metadata = [
        //     'field_name' => 'value',
        //     'field_name' => 'value',
        //     'field_name' => 'value'
        //  ];

		$log_params = "user={$user_id}&user_email={$user_email}&user_name={$user_full_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->put("devco/addresses/{$address_key}?{$log_params}", $metadata);
	}
}