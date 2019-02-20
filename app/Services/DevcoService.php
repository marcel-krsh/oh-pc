<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Carbon;
use \GuzzleHttp\RequestOptions;

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
     * @return object
     */
    public function listAddresses(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function getAddress(int $address_key, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function updateAddress(int $address_key, array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function listAmenities(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
    public function addAmenity(array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        //  $metadata = [
        //     'type_key' => '64',
        //     'description' => 'Bathroom',
        //     'type_guid' => 'value',
        //     'field_name' => 'value'
        //  ];

        $log_params = [
            'user' => $user,
            'user_email' => $user_email,
            'user_name' => $user_name,
            'device_id' => $device_id,
            'device_name' => $device_name,
        ];

        $payload = array_merge($metadata, $log_params);

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
     * @return object
     */
    public function updateAmenity(int $amenity_type_id, array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : string
    {
        // example call
        // /api/v1/devco/addresses/{{address_key}}

        //  $metadata = [
        //     'field_name' => 'value',
        //     'field_name' => 'value',
        //     'field_name' => 'value'
        //  ];

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->put("devco/amenity-types/{$amenity_type_id}?{$log_params}", $metadata);
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
     * @return object
     */
    public function listBuildings(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function getBuilding(int $building_key, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function updateBuilding(int $building_key, array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function getBuildingAmenities(int $building_key, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function listBuildingStatuses(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function updateBuildingStatus(int $building_status_key, array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function listBuildingTypes(int $page = 1, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function updateComplianceContact(int $compliane_contact_key, array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function listCounties(int $page = 1, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function listDevelopments(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function getDevelopment(int $development_key, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/developments/{$development_key}?{$log_params}");
    }

    /**
     * List Financial Types
     *
     * @param  int|integer $page
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listFinancialTypes(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/financial_types/?{$log_params}&{$params}");
    }

    /**
     * List Project Financials
     *
     * @param  int|integer $page
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listProjectFinancials(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/development_financials/?{$log_params}&{$params}");
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
     * @return object
     */
    public function updateDevelopment(int $development_key, array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function listDevelopmentAmenities(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null, int $development_key = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/developments/{$development_key}/amenities?{$params}&{$log_params}");
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
     * @return object
     */
    public function listDevelopmentBuildings(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null, int $development_key) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/developments/{$development_key}/buildings?{$params}&{$log_params}");
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
     * @return object
     */
    public function listDevelopmentActivities(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function listProjectActivityTypes(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function updateDevelopmentAmenity(int $development_amenity_key, array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function listDevelopmentContactRoles(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null, int $development_key = null, int $development_program_key = null, int $development_role_key = null, int $organization_key = null, int $person_key = null, string $group_by = 'developmentKey') : object
    {
        

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
     * @return object
     */
    public function addContactRole(array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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

        $payload = array_merge($metadata, $log_params);

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
     * @return object
     */
    public function updateDevelopmentDate(int $development_date_key, array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function listDevelopmentRoles(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";
        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";
        return $this->get("devco/development_roles?{$params}&{$log_params}");
    }

    /**
     * Development Programs
     */

    /**
     * List Development Programs
     *
     * @param  int|integer $page
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listDevelopmentPrograms(int $development_program_key, array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        //  $metadata = [
        //     'employee_unit_count' => 'value'
        //  ];

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->put("devco/list_development_programs/?{$log_params}", $metadata);
    }

    /**
     * List Development Program Status Types
     *
     * @param  int|integer $page
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listDevelopmentProgramStatusTypes(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/development_program_status_types/?{$log_params}&{$params}");
    }

    /**
     * List Multiple Building Election Types
     *
     * @param  int|integer $page
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listMultipleBuildingElectionTypes(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/multiple_building_election_types/?{$log_params}&{$params}");
    }

    /**
     * List Percentages
     *
     * @param  int|integer $page
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listPercentages(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/percentages/?{$log_params}&{$params}");
    }

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
     * @return object
     */

    public function updateDevelopmentProgram(int $development_program_key, array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function updateAudit(int $monitoring_key, array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function listMonitoringStatusTypes(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/monitoring_status_types?{$params}&{$log_params}");
    }


    /**
     * Federal Set Asides
     */
    
    /**
     * List Federal Set Asides
     *
     * @param  int|integer $page
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listFederalSetAsides(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/federal_minimum_set_asides?{$params}&{$log_params}");
    }

    /**
     * Amenity Types
     */

    /**
     * List Amenitiy Types
     *
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listAmenityTypes(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/amenity_types?{$params}&{$log_params}");
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
     * @return object
     */
    public function listOrganizations(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function getOrganization(int $organization_id, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function updateOrganization(int $organization_id, array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function listPeople(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function getPerson(int $person_key, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function updatePerson(int $person_key, array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function listPrograms(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/programs?{$params}&{$log_params}");
    }

    /**
     * Program Date Types
     */

    /**
     * List Program Date Types
     *
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listProgramDateTypes(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/program_date_types?{$params}&{$log_params}");
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
     * @return object
     */
    public function listProgramStatusTypes(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function listStates(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/states?{$params}&{$log_params}");
    }

    /**
     * Users
     */
    
    /**
     * List Users
     *
     * @param  int $page
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listUsers(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/users?{$params}&{$log_params}");
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
     * @return object
     */
    public function listUnits(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/units?{$params}&{$log_params}");
    }

    /**
     * List Unit Bedrooms
     *
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listUnitBedrooms(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/unit_bedrooms?{$params}&{$log_params}");
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
     * @return object
     */
    public function getUnit(int $unit_key, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * @return object
     */
    public function updateUnit(int $unit_key, array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
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
     * List Household Events
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listHouseHoldEvents(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/household_events?{$params}&{$log_params}");
    }

    /**
     * List Event Types
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listEventTypes(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/event_types?{$params}&{$log_params}");
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
     * @return object
     */
    public function updateUnitStatus(int $unit_status_key, array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        //  $metadata = [
        //     'unit_status' => 'value'
        //  ];

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->put("devco/unit_statuses/{$unit_status_key}?{$log_params}", $metadata);
    }

    /**
     * List Unit Status
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listUnitStatuses(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/unit_statuses?{$params}&{$log_params}");
    }

    /**
     * List Owner Certification Years
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listOwnerCertificationYears(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/owner_certification_years?{$params}&{$log_params}");
    }

    /**
     * List Households
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listHouseHolds(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/households?{$params}&{$log_params}");
    }

    /**
     * List Household Sizes
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listHouseHoldSizes(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/household_sizes?{$params}&{$log_params}");
    }

    /**
     * List Special Needs
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listSpecialNeeds(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/special_needs?{$params}&{$log_params}");
    }

    /**
     * List Rental Assistance Sources
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listRentalAssistanceSources(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/rental_assistance_sources?{$params}&{$log_params}");
    }

    /**
     * List Rental Assistance Types
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listRentalAssistanceTypes(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/rental_assistance_types?{$params}&{$log_params}");
    }

    /**
     * List Utility Allowances
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listUtilityAllowances(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";
        //Log::info("URL sent to API for Utility Allowances devco/utility_allowances?{$params}&{$log_params} pages of results.");
        return $this->get("devco/utility_allowances?{$params}&{$log_params}");
    }

    /**
     * List Utility Allowance Types
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listUtilityAllowanceTypes(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";
        //Log::info("URL sent to API for Utility Allowances devco/utility_allowances?{$params}&{$log_params} pages of results.");
        return $this->get("devco/utility_allowance_types?{$params}&{$log_params}");
    }

    /**
     * List Monitorings
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listMonitorings(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/monitorings?{$params}&{$log_params}");
    }

    /**
     * List Monitoring Monitors
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listMonitoringMonitors(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/monitoring_monitors?{$params}&{$log_params}");
    }

    /**
     * List Project Amenities
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listProjectAmenities(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/development_amenities?{$params}&{$log_params}");
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
     * @return object
     */
    public function listUnitAmenities(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/unit_amenities?{$params}&{$log_params}");
    }

    /**
     * List Building Amenities
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listBuildingAmenities(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/building_amenities?{$params}&{$log_params}");
    }

    /**
     * List Project Programs
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listProjectPrograms(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/development_programs?{$params}&{$log_params}");
    }

    /**
     * List Project Buildings
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listProjectBuildings(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/development_buildings?{$params}&{$log_params}");
    }

    /**
     * List Compliance Contacts
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listComplianceContacts(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/compliance_contacts?{$params}&{$log_params}");
    }

    /**
     * List Project Dates
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listProjectDates(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/development_dates?{$params}&{$log_params}");
    }

    /**
     * List Phone Numbers
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listPhoneNumbers(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/phone_numbers?{$params}&{$log_params}");
    }

    /**
     * List Phone Number Types
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listPhoneNumberTypes(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/phone_number_types?{$params}&{$log_params}");
    }

    /**
     * List Email Addresses
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listEmailAddresses(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/email_addresses?{$params}&{$log_params}");
    }

    /**
     * List Email Addresse Types
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listEmailAddressTypes(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/email_address_types?{$params}&{$log_params}");
    }
    /**
     * List Unit Identities
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function listUnitIdentities(int $page = 1, string $newer_than = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        $params = "page={$page}&newer_than={$newer_than}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("devco/unit_identities?{$params}&{$log_params}");
    }
    /**
     * Get Unit's Programs
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function getUnitPrograms(int $unitId = 1, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        //dd($unitId,$user,$user_email,$user_name,$device_id,$device_name);
        $params = "unit={$unitId}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        //dd("devco/unit_programs/{$unitId}?{$log_params}");

        return $this->get("devco/unit_programs/{$unitId}?{$log_params}");
    }

    /**
     * Get Unit's Project Program Records
     *
     * @param  int $unit_key
     * @param  int $page
     * @param  string|null $newer_than
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function getUnitProjectPrograms(int $unitKey = 1, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) : object
    {
        
        $params = "unit={$unitKey}";

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}}";//

        return $this->get("devco/unit_development_programs/{$unitKey}?{$log_params}");
    }

    

    public function putUnitProgram($unitKey, $programKey, $fundingProgramKey,$startDate,$endDate, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null) {

        $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->post("devco/unit_development_programs?",['form_params'=>['UnitKey'=>$unitKey,'DevelopmentProgramKey'=>$programKey,'StartDate'=>$startDate,'EndDate'=>$endDate]]
          );

    }

    /**
     * Get Project Docs
     *
     * @param  string $projectNumber
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */

    public function getProjectDocuments(string $projectNumber = '1', string $searchString = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null)
    {
        $cabinet = \App\Models\SystemSetting::where('key','docuware_cabinet')->first();
        $cabinetNumber = $cabinet->value;
        if(!is_null($searchString)){
            //$search = "DOCUMENTDATE:1/1/2018,2/1/2018;";
            $search = "PROJECTNUMBER:{$projectNumber};DocuWareFulltext:{$searchString}";
        } else {
            $search = "PROJECTNUMBER:{$projectNumber};";
        }


        $log_params = "cabinet={$cabinetNumber}&user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->getContents("docuware/documents/search?{$log_params}&search={$search}");
    }

    public function getDocuments(int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null)
    {
        $cabinet = \App\Models\SystemSetting::where('key','docuware_cabinet')->first();
        $cabinetNumber = $cabinet->value;

        $date = \App\Models\SyncDocuware::orderBy('synced_at','desc');
        if(!is_null($date)){$date="1/1/2018,2/1/2018";}
        
            $search = "DOCUMENTDATE:{$date};";
        


        $log_params = "cabinet={$cabinetNumber}&user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->getContents("docuware/documents/search?{$log_params}&search={$search}");
    }

    /**
     * Get Doc
     *
     * @param  int $projectNumber
     * @param  int|null $user
     * @param  string|null $user_email
     * @param  string|null $user_name
     * @param  int|null $device_id
     * @param  string|null $device_name
     * @return object
     */
    public function getDocument(int $documentId = 1,  int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null)
        {
            $cabinet = \App\Models\SystemSetting::where('key','docuware_cabinet')->first();
            $cabinetNumber = $cabinet->value;

           $log_params = "user={$user}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";
           
           
           return $this->getFile("docuware/documents/{$cabinetNumber}/{$documentId}?{$log_params}");
            

            
            
           
           //return response()->download($file, 'filename.pdf', $headers);

            
        }


}
