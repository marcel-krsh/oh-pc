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
	public function listAddresses(int $page = 1, string $newer_than = null, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : object
	{
		// example call
		// /api/v1/devco/addresses?page={{default=1/value}}&newer_than={{null/value}}

		$params = "page={$page}&newer_than={$newer_than}";

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

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

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

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

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->put("devco/addresses/{$address_key}?{$log_params}", $metadata);
	}

	/**
	 * Devco Amenities
	 */
	
	/**
	 * List Amenities
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
	public function listAmenities(int $page = 1, string $newer_than = null, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		// example call
		// /api/v1/devco/amenities?page={{default=1/value}}&newer_than={{null/value}}

		$params = "page={$page}&newer_than={$newer_than}";

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/amenities?{$params}&{$log_params}");
	}

	/**
	 * Add Amenity
	 * 
	 * @param array $metadata
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return  string
	 */
	public function addAmenity(array $metadata, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		//  $metadata = [
        //     'type_key' => '64',
        //     'description' => 'Bathroom',
        //     'type_guid' => 'value',
        //	   'field_name' => 'value'	
        //  ];

		$log_params = [
            'user' => $user,
            'user_email' => $user_email,
            'user_name' => $user_name,
            'device_id' => $device_id,
            'device_name' => $device_name,
        ];

        $payload = array_merge($metadata,$log_params);

        return $this->post("devco/amenities", $payload);

	}

	/**
	 * Update Amenity
	 * 
	 * @param  int $amenities_id
	 * @param  array $metadata
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function updateAmenity(int $amenities_id, array $metadata, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		// example call
		// /api/v1/devco/addresses/{{address_key}}

		//  $metadata = [
        //     'field_name' => 'value',
        //     'field_name' => 'value',
        //     'field_name' => 'value'
        //  ];

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->put("devco/amenities/{$amenities_id}?{$log_params}", $metadata);
	}

	/**
	 * Devco Buildings
	 */

	/**
	 * List Buildings
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
	public function listBuildings(int $page = 1, string $newer_than = null, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		// example call
		// /api/v1/devco/buildings?page={{default=1/value}}&newer_than={{null/value}}

		$params = "page={$page}&newer_than={$newer_than}";

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/buildings?{$params}&{$log_params}");
	}

	/**
	 * Get Building
	 * 
	 * @param  int $building_key
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function getBuilding(int $building_key, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		// example call
		// /api/v1/devco/buildings/{{building_key}}

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/buildings/{$building_key}?{$log_params}");
	}

	/**
	 * Update Building
	 * 
	 * @param  int $building_key
	 * @param  array $metadata
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function updateBuilding(int $building_key, array $metadata, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		// example call
		// /api/v1/devco/addresses/{{address_key}}

		//  $metadata = [
        //     'development_key' => 'value',
        //     'building_status_key' => 'value',
        //     'building_name' => 'value',
        //     'physical_address_key' => 'value',
        //     'in_service_date' => 'value',
        //     'applicable_fraction' => 100,
        //     'owner_paid_utilities' => true,
        //     'field_name' => 'value'
        //  ];

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->put("devco/buildings/{$building_key}?{$log_params}", $metadata);
	}

	/**
	 * Get Building Amenities
	 * 
	 * @param  int $building_key
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function getBuildingAmenities(int $building_key, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		// example call
		// /api/v1/devco/buildings/{{building_key}}/amenities

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/buildings/{$building_key}/amenities?{$log_params}");
	}

	/**
	 * Building Statuses
	 */
	
	/**
	 * List Building Statuses
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
	public function listBuildingStatuses(int $page = 1, string $newer_than = null, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		// example call
		// /api/v1/devco/building-statuses

		$params = "page={$page}&newer_than={$newer_than}";

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/building_statuses?{$params}&{$log_params}");
	}

	/**
	 * Update Building Status
	 * 
	 * @param  int $building_status_key
	 * @param  array $metadata
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function updateBuildingStatus(int $building_status_key, array $metadata, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		// example call
		// /api/v1/devco/building-statuses/{{building_status_key}}

		//  $metadata = [
        //     'building_status_name' => 'value',
        //     'building_status_short_name' => 'value'
        //  ];

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->put("devco/building_statuses/{$building_status_key}?{$log_params}", $metadata);
	}

	/**
	 * Building Types
	 */

	/**
	 * List Building Types
	 * 
	 * @param  int|integer $page
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function listBuildingTypes(int $page=1, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		// example call
		// /api/v1/devco/building-types?page={{default=1/value}}

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/building_types?page={$page}&{$log_params}");
	}

	/**
	 * Compliance Contacts
	 */

	/**
	 * Update Compliance Contact
	 * 
	 * @param  int $compliane_contact_key
	 * @param  array $metadata
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function updateComplianceContact(int $compliane_contact_key, array $metadata, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		// example call
		// /api/v1/devco/compliance-contacts/{{compliane_contact_key}}

		//  $metadata = [
        //     'address' => 'value',
        //     'city' => 'value',
        //     'zip' => 'value',
        //     'next_inspection' => 'value'
        //  ];

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->put("devco/compliance_contacts/{$compliane_contact_key}?{$log_params}", $metadata);
	}

	/**
	 * Counties
	 */

	/**
	 * List Counties
	 * 
	 * @param  int|integer $page
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function listCounties(int $page=1, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		// example call
		// /api/v1/devco/counties?page={{default=1/value}}

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/counties?page={$page}&{$log_params}");
	}

	/**
	 * Developments (projects)
	 */
	
	/**
	 * List Developments
	 * 
	 * @param  int|integer $page
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function listDevelopments(int $page=1, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		// example call
		// /api/v1/devco/developments?page={{default=1/value}}

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/developments?page={$page}&{$log_params}");
	}

	/**
	 * Get Development
	 * 
	 * @param  int $development_key
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function getDevelopment(int $development_key, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/developments/{$development_key}?{$log_params}");
	}

	/**
	 * Update Development
	 * 
	 * @param  int $development_key
	 * @param  array $metadata
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function updateDevelopment(int $development_key, array $metadata, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		//  $metadata = [
        //     'development_key' => 'value',
        //     'sample_size' => 'value',
        //     'field_name' => 'value'
        //  ];

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->put("devco/developments/{$development_key}?{$log_params}", $metadata);
	}

	/**
	 * List Development Amenities
	 * 
	 * @param  int $development_key
	 * @param  int|integer $page
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function listDevelopmentAmenities(int $development_key, int $page=1, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/developments/{$development_key}/amenities?page={$page}&{$log_params}");
	}

	/**
	 * List Development Buildings
	 * 
	 * @param  int $development_key
	 * @param  int|integer $page
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function listDevelopmentBuildings(int $development_key, int $page=1, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/developments/{$development_key}/buildings?page={$page}&{$log_params}");
	}

	/**
	 * Development Activities
	 */
	
	/**
	 * List Development Activities
	 * 
	 * @param  int|integer $page
	 * @param  string|null $newer_than
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function listDevelopmentActivities(int $page=1, string $newer_than = null, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : object
	{
		$params = "page={$page}&newer_than={$newer_than}";

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/development-activities?{$params}&{$log_params}");
	}

	/**
	 * List Development Activity Types
	 * 
	 * @param  int|integer $page
	 * @param  string|null $newer_than
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function listProjectActivityTypes(int $page=1, string $newer_than = null, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : object
	{
		$params = "page={$page}&newer_than={$newer_than}";

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/project_activity_types?{$params}&{$log_params}");
	}

	/**
	 * Update Development Activity
	 * 
	 * @param  int $development_activity_key
	 * @param  array $metadata
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function updateDevelopmentActivity(int $development_activity_key, array $metadata, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		//  $metadata = [
        //     'field_name' => 'value'
        //  ];

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->put("devco/development_activities/{$development_activity_key}?{$log_params}", $metadata);
	}

	/**
	 * Development Contact Roles
	 */
	
	/**
	 * List Contacts
	 * 
	 * @param  int|integer $page
	 * @param  string|null $newer_than
	 *
	 * 
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function listContacts(int $page=1, string $newer_than = null, 
		int $development_key=null, int $development_program_key=null, int $development_role_key=null, 
		int $organization_key=null, int $person_key=null, string $group_by=null, 
		int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		// example call
		// "/api/v1/devco/development-contact-roles?page={{default=1/value}}&development_key={{null/value}}&development_program_key={{null/value}}&development_role_key={{null/value}}&organization_key={{null/value}}&person_key={{null/value}}&group_by={{null/value}}&user={{user_id}}&user_email={{user_email}}&user_name={{user_name}}&device_id={{device_id}}&device_name={{device_name}}&newer_than={{null/value}}"

		$params = "page={$page}&newer_than={$newer_than}&development_key={$development_key}&development_program_key={$development_program_key}&development_role_key={$development_role_key}&organization_key={$organization_key}&person_key={$person_key}&group_by={$group_by}";

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/development_contact_roles?{$params}&{$log_params}");
	}

	/**
     * Add Contact
     *
     * @param  array $metadata
     * @param  int $user
     * @param  string $user_email
     * @param  string $user_name
     * @param  int $device_id
     * @param  string $device_name
     * @param  string $provider The document provider's name
     * 
     * @return string
     */
    public function addContact(array $metadata, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string 
    {
    	//  $metadata = [
        //     'field_name' => 'value'
        //  ];

        $log_params = [
            'user' => $user,
            'user_email' => $user_email,
            'user_name' => $user_name,
            'device_id' => $device_id,
            'device_name' => $device_name,
        ];

        $payload = array_merge($metadata,$log_params);

        return $this->post("devco/development_contact_roles", $payload);
    }

    /**
     * Development Dates
     */
    
    /**
	 * Update Development Date
	 * 
	 * @param  int $development_date_key
	 * @param  array $metadata
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function updateDevelopmentDate(int $development_date_key, array $metadata, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		//  $metadata = [
        //     'comment' => 'value'
        //  ];

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->put("devco/development_dates/{$development_date_key}?{$log_params}", $metadata);
	}

	/**
	 * Development Roles
	 */
	
	/**
	 * List Development Roles
	 * 
	 * @param  int|integer $page
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function listDevelopmentRoles(int $page=1, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : object
	{
		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/development_roles?page={$page}&{$log_params}");
	}

	/**
	 * Development Programs
	 */

	/**
	 * Update Development Program
	 * 
	 * @param  int $development_program_key
	 * @param  array $metadata
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function updateDevelopmentProgram(int $development_program_key, array $metadata, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		//  $metadata = [
        //     'employee_unit_count' => 'value'
        //  ];

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->put("devco/development_programs/{$development_program_key}?{$log_params}", $metadata);
	}

	/**
	 * Devco Monitoring (audits)
	 */
	
	/**
	 * Update Audit
	 * 
	 * @param  int $monitoring_key
	 * @param  array $metadata
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function updateAudit(int $monitoring_key, array $metadata, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		//  $metadata = [
        //     'field_name' => 'value'
        //  ];

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->put("devco/monitors/{$monitoring_key}?{$log_params}", $metadata);
	}

	/**
	 * Monitoring Statuses
	 */
	
	/**
	 * List Audit Statuses
	 * 
	 * @param  int|integer $page
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function listMonitoringStatusTypes(int $page = 1, string $newer_than = null, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : object
	{
		$params = "page={$page}&newer_than={$newer_than}";

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/monitoring_status_types?{$params}&{$log_params}");
	}

	/**
	 * Organizations
	 */

	/**
	 * List Organizations
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
	public function listOrganizations(int $page = 1, string $newer_than = null, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		$params = "page={$page}&newer_than={$newer_than}";

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/organizations?{$params}&{$log_params}");
	}

	/**
	 * Get Organization
	 * 
	 * @param  int $organization_id
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function getOrganization(int $organization_id, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/organizations/{$organization_id}?{$log_params}");
	}

	/**
	 * Update Organization
	 * 
	 * @param  int $organization_id
	 * @param  array $metadata
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function updateOrganization(int $organization_id, array $metadata, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		//  $metadata = [
        //     'field_name' => 'value',
        //     'field_name' => 'value',
        //     'field_name' => 'value'
        //  ];

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->put("devco/organizations/{$organization_id}?{$log_params}", $metadata);
	}

	/**
	 * People
	 */

	/**
	 * List People
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
	public function listPeople(int $page = 1, string $newer_than = null, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : object
	{
		$params = "page={$page}&newer_than={$newer_than}";

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/people?{$params}&{$log_params}");
	}

	/**
	 * Get Person
	 * 
	 * @param  int $person_key
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function getPerson(int $person_key, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/people/{$person_key}?{$log_params}");
	}

	/**
	 * Update Person
	 * 
	 * @param  int $person_key
	 * @param  array $metadata
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function updatePerson(int $person_key, array $metadata, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		//  $metadata = [
        //     'last_name' => 'value',
        //     'first_name' => 'value',
        //     'default_phone_number_key' => 'value',
        //     'default_fax_number_key' => 'value',
        //     'default_email_address_key' => 'value',
        //     'is_active' => 'value',
        //     'last_edited' => 'value'
        //  ];

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->put("devco/people/{$person_key}?{$log_params}", $metadata);
	}

	/**
	 * Programs
	 */

	/**
	 * List Programs
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
	public function listPrograms(int $page = 1, string $newer_than = null, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		$params = "page={$page}&newer_than={$newer_than}";

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/programs?{$params}&{$log_params}");
	}

	/**
	 * Program Status Types
	 */

	/**
	 * List Program Status Types
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
	public function listProgramStatusTypes(int $page = 1, string $newer_than = null, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		$params = "page={$page}&newer_than={$newer_than}";

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/development_program_status_types?{$params}&{$log_params}");
	}

	/**
	 * States
	 */
	
	/**
	 * List States
	 * 
	 * @param  int $page
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function listStates(int $page = 1, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		$params = "page={$page}";

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/states?{$params}&{$log_params}");
	}

	/**
	 * Units
	 */

	/**
	 * List Units
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
	public function listUnits(int $page = 1, string $newer_than = null, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		$params = "page={$page}&newer_than={$newer_than}";

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/units?{$params}&{$log_params}");
	}

	/**
	 * Get Unit
	 * 
	 * @param  int $unit_key
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function getUnit(int $unit_key, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/units/{$unit_key}?{$log_params}");
	}

	/**
	 * Update Unit
	 * 
	 * @param  int $unit_key
	 * @param  array $metadata
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function updateUnit(int $unit_key, array $metadata, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		//  $metadata = [
        //     'unit_square_feet' => 'value',
        //     'status_date' => 'value',
        //     'is_unit_handicap_accessible' => 'value'
        //  ];

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->put("devco/units/{$unit_key}?{$log_params}", $metadata);
	}

	/**
	 * List Unit Amenities
	 * 
	 * @param  int $unit_key
	 * @param  int $page
	 * @param  string|null $newer_than
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function listUnitAmenities(int $unit_key, int $page = 1, string $newer_than = null, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		$params = "page={$page}&newer_than={$newer_than}";

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->get("devco/units/{$unit_key}/amenities?{$params}&{$log_params}");
	}

	/**
	 * Unit Statuses
	 */

	/**
	 * Update Unit Status
	 * 
	 * @param  int $unit_status_key
	 * @param  array $metadata
	 * @param  int|null $user
	 * @param  string|null $user_email
	 * @param  string|null $user_name
	 * @param  int|null $device_id
	 * @param  string|null $device_name
	 * @return string
	 */
	public function updateUnitStatus(int $unit_status_key, array $metadata, int $user=null, string $user_email=null, string $user_name=null, int $device_id=null, string $device_name=null) : string
	{
		//  $metadata = [
        //     'unit_status' => 'value'
        //  ];

		$log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

		return $this->put("devco/unit_statuses/{$unit_status_key}?{$log_params}", $metadata);
	}
}