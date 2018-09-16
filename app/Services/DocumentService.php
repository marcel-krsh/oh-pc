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
     * @param int $amount
     */
    public function getDocuments($amount=100)
    {
        return $this->get("docuware/documents/search?amount={$amount}");
    }

    /**
     * Get Recent Documents
     *
     * @param null $last_updated_at
     */
    public function getRecentDocuments($last_updated_at=null)
    {
        if(!$last_updated_at) {
            $last_updated_at = Carbon::now()->addMonth(-1);
        }

        $project_id = 0;
        $low_date = (new Carbon($last_updated_at))->format('n/j/Y g:i:s A');
        $high_date = Carbon::now()->addDay(1)->format('n/j/Y g:i:s A');
        $search_params = "DWMODDATETIME:{$low_date},{$high_date};&isandoperation=true;";

        return $this->get("docuware/documents/search?search={$search_params}");
    }

    /**
     * Get Recent Documents by Project ID
     *
     * @param int $project_id
     * @param null $last_updated_at
     */
    public function getRecentDocumentsByProjectId($project_id, $last_updated_at=null)
    {
        if(!$last_updated_at) {
            $last_updated_at = Carbon::now()->addMonth(-1);
        }

        $project_id = 0;
        $low_date = (new Carbon($last_updated_at))->format('n/j/Y g:i:s A');
        $high_date = Carbon::now()->addDay(1)->format('n/j/Y g:i:s A');
        $search_params = "PROJECTNUMBER:{$project_id};DWMODDATETIME:{$low_date},{$high_date};&isandoperation=true;";

        return $this->get("docuware/documents/search?search={$search_params}");
    }

    /**
     * Get Document By Project ID
     *
     * @param int $project_id
     */
    public function getDocumentsByProjectId($project_id)
    {
        $project_id = 0;
        $search_params = "PROJECTNUMBER:{$project_id};&isandoperation=true;";

        return $this->get("docuware/documents/search?search={$search_params}");
    }

    /**
     * Get Document By Provider ID
     *
     * ie. Docuware ID
     *
     * @param $provider_id
     */
    public function getDocumentByProviderId($provider_id)
    {
        $project_id = 0;
        $search_params = "doc-id:{$provider_id};&isandoperation=true;";

        return $this->get("docuware/documents/search?search={$search_params}");
    }

    /**
     * Update Document Metadata
     *
     * @param $provider_id
     * @param $metadata
     */
    public function updateDocumentMetadata($provider_id, $metadata)
    {
        $payload = '';


        return $this->put("docuware/documents/{$provider_id}", $payload);
    }

    /**
     * Delete Document By Provider ID
     *
     * @param $provider_id
     */
    public function deleteDocumentByProviderId($provider_id)
    {
        return $this->delete("docuware/documents/{$provider_id}");
    }

}