<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * SdoParcel Model
 *
 * @category Models
 * @license  Proprietary and confidential
 */
class SdoParcel extends Model
{
    protected $fillable = [
        'File Number',
        'Property Address Number',
        'Property Address Street Name',
        'Property Address Street Suffix',
        'Property City',
        'Property State',
        'Property Zip',
        'Property County',
        'First Payment Date',
        'latitude',
        'longitude',
        'us_house_district',
        'oh_house_district',
        'oh_senate_district',
        'google_map_link',
        'Status',
        'street_address'
    ];
}
