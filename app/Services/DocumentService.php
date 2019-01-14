<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Document;
use Illuminate\Support\Carbon;

class DocumentService extends PCAPIService
{
    /**
     * Docuware Cabinet ID
     *
     * @var string
     */
    private $_cabinet_id;

    /**
     * Docuware Cabinet Name
     *
     * @var string
     */
    private $_cabinet_name;

    public function __construct()
    {
        $this->_cabinet_id = config('docuware.cabinet_id');
        $this->_cabinet_name = config('docuware.cabinet_name');
    }
    
    /**
     * Search Documents
     *
     * @param array $search
     * @param array $sort
     * @param int $user
     * @param string $user_email
     * @param string $user_name
     * @param int $device_id
     * @param string $device_name
     * @param string $provider
     */
    public function getDocuments(array $search = null, array $sort = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null, string $provider = 'docuware')
    {
        // example call
        // /api/v1/docuware/documents/search?search=FIELD:criteria;FIELD1:fromcriteria,toCriteria;&page={{null/value}}&user={{user_id}}&user_email={{user_email}}&user_name={{user_full_name}}&device_id={{device_id}}&device_name={{device_name}}
        // /api/v1/docuware/documents/search?search=PROJECTNUMBER:050178;DOCUMENTDATE:1/1/2017%2012:00:00%20AM,1/30/2017%2012:00:00%20AM;&isandoperation=true&sortorderfields=PROJECTNUMBER,DOCUMENTDATE DESC

        // search array reference
        // $search = [
        //     'fields' => [
        //         [
        //             'field' => 'field1',
        //             'criteria' => 'criteria',
        //             'criteria2' => null
        //         ],
        //         [
        //             'field' => 'field1',
        //             'criteria' => 'from criteria',
        //             'criteria2' => 'to criteria'
        //         ]
        //     ],
        //     'isandoperation' => 'false'
        // ];

        // $sort = [
        //     'PROJECTNUMBER',
        //     'DOCUMENTDATE DESC'
        // ];

        if ($search !== null) {
            (isset($search['isandoperation']) || array_key_exists('isandoperation', $search)) ? $isandoperation = $search['isandoperation'] : $isandoperation = ''; // true, false or nothing

            if (isset($search['fields']) || array_key_exists('fields', $search)) {
                $search_fields = "search=";
                foreach ($search['fields'] as $field) {
                    if ((isset($field['field']) || array_key_exists('field', $field)) && (isset($field['criteria']) || array_key_exists('criteria', $field))) {
                        $search_fields = $search_fields."{$field['field']}:{$field['criteria']}";
                        
                        if ((isset($field['criteria2']) || array_key_exists('criteria2', $field))) {
                            $search_fields = "{$search_fields},{$field['criteria2']}";
                        }

                        $search_fields = "{$search_fields};";
                    }
                }
                $search_params = "{$search_fields}&cabinet={$this->_cabinet_name}&cabinet_id={$this->_cabinet_id}&isandoperation={$isandoperation}";
            } else {
                $search_params = "cabinet={$this->_cabinet_name}&cabinet_id={$this->_cabinet_id}&isandoperation={$isandoperation}";
            }
        } else {
            $search_params =  "cabinet={$this->_cabinet_name}&cabinet_id={$this->_cabinet_id}";
        }

        if ($sort != null) {
            $order_fields = "sortorderfields=";
            foreach ($sort as $sortorderfield) {
                if ($order_fields == "sortorderfields=") {
                    $order_fields = $order_fields."{$sortorderfield}";
                } else {
                    $order_fields = $order_fields.",{$sortorderfield}";
                }
            }
            $search_params = $search_params . $order_fields;
        }

        $log_params = "user={$user_id}&user_email={$user_email}&user_name={$user_full_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->get("{$provider}/documents/search?{$search_params}&{$log_params}");
    }

    /**
     * Get Specific Document
     *
     * @param int $id
     * @param int|null $user_id
     * @param string|null $user_email
     * @param string|null $user_name
     * @param int|null $device_id
     * @param string|null $device_name
     * @param string $provider
     * @return mixed
     */
    public function getDocument(int $id, int $user_id = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null, string $provider = 'docuware')
    {
        $cabinet = \App\Models\SystemSetting::where('key','docuware_cabinet')->first();
        $cabinetNumber = $cabinet->value;
        // example call
        // /api/v1/docuware/document/{{document_id}}?user={{user_id}}&user_email={{user_email}}&user_name={{user_full_name}}&device_id={{device_id}}&device_name={{device_name}}
        
        $log_params = "user={$user_id}&user_email={$user_email}&user_name={$user_name}&device_id={$device_id}&device_name={$device_name}";


        return $this->getFile("docuware/documents/{$cabinetNumber}/{$id}?{$log_params}");
    }

    /**
     * Get Document By Provider ID
     *
     * ie. Docuware ID
     *
     * @param $provider_id
     * @param int $user
     * @param string $user_email
     * @param string $user_name
     * @param int $device_id
     * @param string $device_name
     * @param string $provider
     */
    public function getDocumentByProviderId(int $provider_id, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null, string $provider = 'docuware') : string
    {
        $search = [
            'fields' => [
                [
                    'field' => 'doc-id',
                    'criteria' => $provider_id
                ]
            ],
            'isandoperation' => 'true'];

        return $this->getDocuments($search, null, $user, $user_email, $user_name, $device_id, $device_name, $provider);
    }

    /**
     * Get Recent Documents
     *
     * @param $last_updated_at Carbon date
     * @param array $sort
     * @param int $user
     * @param string $user_email
     * @param string $user_name
     * @param int $device_id
     * @param string $device_name
     * @param string $provider
     */
    public function getRecentDocuments(string $last_updated_at, array $sort = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null, string $provider = 'docuware')
    {
        if (!$last_updated_at) {
            $last_updated_at = Carbon::now()->addMonth(-1);
        }

        $low_date = (new Carbon($last_updated_at))->format('n/j/Y g:i:s A');
        $high_date = Carbon::now()->addDay(1)->format('n/j/Y g:i:s A');
        $search = [
            'fields' => [
                [
                    'field' => 'DWMODDATETIME',
                    'criteria' => $low_date,
                    'criteria2' => $high_date
                ]
            ],
            'isandoperation' => 'true'];

        return $this->getDocuments($search, $sort, $user, $user_email, $user_name, $device_id, $device_name, $provider);
    }

    /**
     * Get Recent Documents by Project ID
     *
     * @param int $project_id
     * @param array $sort
     * @param $last_updated_at Carbon date
     * @param int $user
     * @param string $user_email
     * @param string $user_name
     * @param int $device_id
     * @param string $device_name
     * @param string $provider
     */
    public function getRecentDocumentsByProjectId(int $project_id, array $sort = null, string $last_updated_at, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null, string $provider = 'docuware')
    {
        if (!$last_updated_at) {
            $last_updated_at = Carbon::now()->addMonth(-1);
        }

        $project_id = 0;
        $low_date = (new Carbon($last_updated_at))->format('n/j/Y g:i:s A');
        $high_date = Carbon::now()->addDay(1)->format('n/j/Y g:i:s A');

        $search = [
            'fields' => [
                [
                    'field' => 'PROJECTNUMBER',
                    'criteria' => $project_id
                ],
                [
                    'field' => 'DWMODDATETIME',
                    'criteria' => $low_date,
                    'criteria2' => $high_date
                ]
            ],
            'isandoperation' => 'true'
        ];

        return $this->getDocuments($search, $sort, $user, $user_email, $user_name, $device_id, $device_name, $provider);
    }

    /**
     * Get Documents By Project ID
     *
     * @param int $project_id
     * @param array $sort
     * @param int $user
     * @param string $user_email
     * @param string $user_name
     * @param int $device_id
     * @param string $device_name
     * @param string $provider
     */
    public function getDocumentsByProjectId(int $project_id, array $sort = null, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null, string $provider = 'docuware')
    {
        $search = [
            'fields' => [
                [
                    'field' => 'PROJECTNUMBER',
                    'criteria' => $project_id
                ]
            ],
            'isandoperation' => 'true'
        ];

        return $this->getDocuments($search, $sort, $user, $user_email, $user_name, $device_id, $device_name, $provider);
    }

    /**
     * Update Document Metadata
     *
     * @param int $id
     * @param array $metadata
     * @param int $user
     * @param string $user_email
     * @param string $user_name
     * @param int $device_id
     * @param string $device_name
     * @param string $provider
     * @return bool
     */
    public function updateDocumentMetadata(int $id, array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null, string $provider = 'docuware') : bool
    {
        //  $metadata = [
        //     'field_name' => 'value',
        //     'field_name' => 'value',
        //     'field_name' => 'value'
        //  ];

        $log_params = "user={$user_id}&user_email={$user_email}&user_name={$user_full_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->put("{$provider}/document/{$id}?{$log_params}", $metadata);
    }

    /**
     *
     * Delete Document by ID or by search query
     *
     * @param  int $id The id of the document to delete
     * @param  int $user
     * @param  string $user_email
     * @param  string $user_name
     * @param  int $device_id
     * @param  string $device_name
     * @param  string $provider The document provider's name
     * @return bool
     */
    public function deleteDocument(int $id, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null, string $provider = 'docuware') : bool
    {
        // example call
        // /api/v1/docuware/document/{{document_id}}?user={{user_id}}&user_email={{user_email}}&user_name={{user_full_name}}&device_id={{device_id}}&device_name={{device_name}}

        $log_params = "user={$user_id}&user_email={$user_email}&user_name={$user_full_name}&device_id={$device_id}&device_name={$device_name}";

        return $this->delete("{$provider}/document/{$id}?{$log_params}");
    }

    /**
     * Chain of Custody - adds a log in Docuware that the document was deleted from device
     *
     * @param  int $id Document ID
     * @param  int $user
     * @param  string $user_email
     * @param  string $user_name
     * @param  int $device_id
     * @param  string $device_name
     * @param  string $provider The document provider's name
     *
     * @return bool
     */
    public function chainOfCustodyDeleteFromDevice(int $id, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null, string $provider = 'docuware') : bool
    {
        $log_params = [
            'user' => $user,
            'user_email' => $user_email,
            'user_name' => $user_name,
            'device_id' => $device_id,
            'device_name' => $device_name,
        ];

        $payload = json_encode($log_params);

        return $this->post("{$provider}/document/{$id}/deleted", $payload);
    }

    /**
     * Store Document
     *
     * @param  array $metadata Document's fields and values
     * @param  $user
     * @param  string $user_email
     * @param  string $user_name
     * @param  int $device_id
     * @param  string $device_name
     * @param  string $provider The document provider's name
     *
     * @return int Document ID
     */
    public function storeDocument(array $metadata, int $user = null, string $user_email = null, string $user_name = null, int $device_id = null, string $device_name = null, string $provider = 'docuware') : int
    {

        //  $metadata = [
        //     'field_name' => 'value',
        //     'field_name' => 'value',
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

        return $this->post("{$provider}/document", $payload);
    }
}
