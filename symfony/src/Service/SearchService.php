<?php

namespace App\Service;

class SearchService
{
    const SORT_PRICE = 'price';
    const SORT_PROXIMITY = 'proximity';

    /** @var string */
    private $apiUrl;

    /** @var string */
    private $apiKey;

    public function getApiUrl(): ?string
    {
        return $this->apiUrl;
    }

    public function setApiUrl(string $apiUrl): self
    {
        $this->apiUrl = $apiUrl;

        return $this;
    }

    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    public function setApiKey(string $apiKey): self
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * Get nearby hotels from the Webservice
     *
     * @param float $latitude
     * @param float $longitude
     * @param string $orderBy
     * @param int $maxDistance
     *
     * @return array
     */
    public function getNearbyHotels($latitude, $longitude, $orderBy = null, $maxDistance = 100): array
    {
        $hotels = $this->fetchHotels($latitude, $longitude, $maxDistance);
        if (null === $orderBy) {
            return $hotels;
        }
        if ($orderBy !== self::SORT_PROXIMITY && $orderBy !== self::SORT_PRICE) {
            return $hotels;
        }

        usort($hotels, [SearchService::class, (self::SORT_PRICE === $orderBy) ? 'sortHotelsByPrice' : 'sortHotelsByDistance']);

        return $hotels;
    }

    /**
     * Fetch the hotels list from the API
     *
     * @param float $latitude
     * @param float $longitude
     * @param int $maxDistance
     *
     * @return array
     */
    protected function fetchHotels($latitude, $longitude, $maxDistance = 100): array
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $this->apiUrl);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
            $curl,
            CURLOPT_POSTFIELDS,
            'apikey=' . $this->apiKey
                . '&latitude=' . $latitude
                . '&longitude=' . $longitude
        );
        $hotels = curl_exec($curl);

        if (false === $hotels) {
            return [['name' => '', 'distance' => 0, 'price' => 0]];
        }
        $hotels = json_decode($hotels)->message;
        $data = [];
        foreach ($hotels as $hotel) {
            // If the price is less than zero, ignore. It must be a mistake.
            if (0 > $hotel[3]) {
                continue;
            }

            // If the distance is higher that the maxDistante, ignore
            $distance = $this->haversineDistance($latitude, $longitude, $hotel[1], $hotel[2]);
            if ($maxDistance < $distance) {
                continue;
            }

            $matches = [];
            $name = $hotel[0];
            if (0 != preg_match('/\"(.+)\"/', $hotel[0], $matches)) {
                $name = $matches[1];
            }

            $data[] = [
                'name' => $name,
                'distance' => $distance,
                'price' => (float)$hotel[3]
            ];
        }


        return $data;
    }

    /**
     * Give the distance between two coordinates in kilometers
     *
     * @param float $latitudeOri
     * @param float $longitudeOri
     * @param float $latitudeDest
     * @param float $longitudeDest
     *
     * @return float
     */
    public function haversineDistance($latitudeOri, $longitudeOri, $latitudeDest, $longitudeDest): float
    {
        try {
            if (($latitudeOri === $latitudeDest) && ($longitudeOri === $longitudeDest)) {
                return 0;
            }
            $deltaLongitude =  $longitudeOri -  $longitudeDest;
            $sin = sin(deg2rad($latitudeOri)) * sin(deg2rad($latitudeDest));
            $cos = cos(deg2rad($latitudeOri)) * cos(deg2rad($latitudeDest)) * cos(deg2rad($deltaLongitude));
            $dist = acos($sin + $cos);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;

            return round($miles * 1.609344, 2);
        } catch (\Exception $ex) {
            // If something the distance is invalid, return a very big number so it won't be considered in the
            // distance sort
            return 999999.99;
        }
    }

    /** usort callback function for price */
    protected function sortHotelsByPrice($a, $b): int
    {
        if ($a['price'] == $b['price']) {
            return 0;
        }

        return ($a['price'] < $b['price']) ? -1 : 1;
    }

    /** usort callback function for distance */
    protected function sortHotelsByDistance($a, $b): int
    {
        if ($a['distance'] === $b['distance']) {
            return 0;
        }

        return ($a['distance'] < $b['distance']) ? -1 : 1;
    }
}
