<?php

/*
 * Copyright (c) 2021 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\CompanyBundle\Util;

use Codefog\HasteBundle\UrlParser;
use Contao\Config;
use Contao\System;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBagInterface;

class LocationUtil
{
    const GOOGLE_MAPS_GEOCODE_URL = 'https://maps.googleapis.com/maps/api/geocode/json?address=%s&sensor=false';

    /**
     * Computes the coordinates from a given address. Supported array keys are:.
     *
     * - street
     * - postal
     * - city
     * - country
     *
     * @return array|bool
     */
    public function computeCoordinatesByArray(array $data)
    {
        $criteria = [
            'street',
            'postal',
            'city',
            'state',
            'country',
        ];

        $sortedData = [];

        // keep the right order
        foreach ($criteria as $name) {
            if (isset($data[$name])) {
                $sortedData[] = $data[$name];
            }
        }

        return $this->computeCoordinatesByString(implode(' ', $sortedData));
    }

    /**
     * Computes the coordinates from a given address. Supported array keys are:.
     *
     * - street
     * - postal
     * - city
     * - country
     *
     * @return array|bool
     */
    public function computeCoordinatesByString(string $address, string $apiKey = '')
    {
        $url = sprintf(static::GOOGLE_MAPS_GEOCODE_URL, urlencode($address));

        if ($apiKey) {
            $url = System::getContainer()->get(UrlParser::class)->addQueryString('key='.$apiKey, $url);
        } elseif (Config::get('googlemaps_apiKey')) {
            $url = System::getContainer()->get(UrlParser::class)->addQueryString('key='.Config::get('googlemaps_apiKey'), $url);
        }

        $result = file_get_contents($url);

        if (!$result) {
            return false;
        }

        $response = json_decode($result, true);

        if (isset($response['error_message'])) {
            return false;
        }

        return ['lat' => $response['results'][0]['geometry']['location']['lat'], 'lng' => $response['results'][0]['geometry']['location']['lng']];
    }
}
