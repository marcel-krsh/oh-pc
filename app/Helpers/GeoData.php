<?php

namespace App\Helpers;

use SoapClient;

/**
 * Class GeoData.
 */
class GeoData
{
    public function getGoogleMapsData($address)
    {
        $qs = http_build_query([
            'address' => $address,
            'key'     => config('geodata.services.google.api_key'),
        ]);
        ini_set('default_socket_timeout', 900);
        $response = file_get_contents(config('geodata.services.google.api_url').$qs);

        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['results'][0])) {
                // Parse out the lat lng, and create google maps url.
                $address_info = [
                    'lat' => $data['results'][0]['geometry']['location']['lat'],
                    'lng' => $data['results'][0]['geometry']['location']['lng'],
                    'google_maps_link' => 'http://maps.google.com/?q='.urlencode($data['results'][0]['formatted_address']),
                ];

                // Break out address parts into array.
                foreach ($data['results'][0]['address_components'] as $component) {
                    foreach (['street_number', 'route', 'locality', 'administrative_area_level_1', 'country', 'postal_code'] as $part) {
                        if (in_array($part, $component['types'])) {
                            $address_info[$part] = $component['long_name'];
                        }
                    }
                }

                return $address_info;
            }
        }

        return false;
    }

    /**
     * Get Districts.
     *
     * @param $address_info
     *
     * @return bool
     */
    public function getDistricts($address_info)
    {
        $client = new SoapClient(config('geodata.services.ohiogeocode.url'), [
            'soap_version' => SOAP_1_2,
            'encoding'     => 'UTF-8',
            'login'        => config('geodata.services.ohiogeocode.login'),
            'password'     => config('geodata.services.ohiogeocode.password'),
        ]);

        $params = [
            'username'       => config('geodata.services.ohiogeocode.login'),
            'password'       => config('geodata.services.ohiogeocode.password'),
            'addressLine1'   => $address_info['street_number'].' '.$address_info['route'],
            'city'           => $address_info['locality'],
            'state'          => $address_info['administrative_area_level_1'],
            'zipCode'        => $address_info['postal_code'],
            'returnMultiple' => 'false',
            'matchMode'      => 2,
            'boundaryFiles'  => 'ohcongress12, ohhouse12, ohsenate12',
            'year'           => 2012,
        ];

        $result = $client->GeocodeAddress_MAF($params);

        return @$result->resultSet->GeocodedResult->boundaryFileResults->string ?: false;
    }

    /**
     * Get Geo Data.
     *
     * @param $address
     *
     * @return array|bool
     */
    public function getGeoData($address)
    {
        $address_info = $this->getGoogleMapsData($address);

        if (! $address_info) {
            return 'Unable to find address on GIS system. Please check complete address is correct.';
        }
        // Just because we got SOME address info back, doesn't mean we have all the info...

        $geoError = null;
        $geoWarning = null;
        if (! isset($address_info['lat']) || ! isset($address_info['lng'])) {
            $geoError = 'Unable to get full latitude and longitude coordinates for the address provided.<br />';
        }
        if (! isset($address_info['street_number'])) {
            $geoError .= 'Unable to confirm street number, please double check your street address.<br />';
        }
        if (! isset($address_info['route'])) {
            $geoError .= 'Unable to confirm street name, please double check your street address.<br />';
        }
        if (! isset($address_info['locality'])) {
            $geoError .= 'Unable to confirm city, please double check your entire address.<br />';
        }
        if (! isset($address_info['administrative_area_level_1'])) {
            $geoError .= 'Unable to confirm parcel is in the state, please double check your entire address.<br />';
        }
        if (! isset($address_info['postal_code'])) {
            $geoError .= 'Unable to confirm zip code, please double check your entire address.<br />';
        }

        if (strlen($geoError) > 0) {
            $address_info['geoError'] = $geoError;

            return $address_info;
        } else {
            // check if generated address matches given address
            $gisAddress = $address_info['street_number']
                        .' '.$address_info['route']
                        .', '.$address_info['locality']
                        .' '.$address_info['administrative_area_level_1']
                        .' '.$address_info['postal_code'];
            if ($gisAddress != $address) {
                $geoWarning = 'The address I got back from the GIS is not exactly the same as the one you provided... please confirm the GIS address is correct, as that is the one we will be storing.<br />
                    <pre>
                    GIS Address:        '.$gisAddress.'
                    Supplied Address:   '.$address.'</pre>';
            }
        }

        $districts = array_map('trim', $this->getDistricts($address_info) ?: []);

        if (isset($districts[0])) {
            $address_info['Congressional'] = $districts[0];
        } else {
            $address_info['Congressional'] = 0;
        }
        if (isset($districts[1])) {
            $address_info['OH House'] = $districts[1];
        } else {
            $address_info['OH House'] = 0;
        }
        if (isset($districts[2])) {
            $address_info['OH Senate'] = $districts[2];
        } else {
            $address_info['OH Senate'] = 0;
        }

        $address_info['geoWarning'] = $geoWarning;

        return $address_info;
    }

    /**
     * Get GPS Distance.
     *
     * @param     $lat
     * @param     $lon
     * @param     $targetLat
     * @param     $targetLon
     * @param int $earthRadius
     *
     * @return float|int
     */
    public function getGPSDistance($lat, $lon, $targetLat, $targetLon, $earthRadius = 3959)
    {
        $latFrom = deg2rad($lat);
        $lonFrom = deg2rad($lon);
        $latTo = deg2rad($targetLat);
        $lonTo = deg2rad($targetLon);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
          cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));

        return $angle * $earthRadius;
    }
}
