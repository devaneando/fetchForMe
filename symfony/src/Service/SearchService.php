<?php

namespace App\Service;

class SearchService
{
    const SORT_PRICE = 'price';
    const SORT_PROXIMITY = 'proximity';
    const EARTH_RADIUS = 6371000;

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

    public function getNearbyHotels($latitude, $longitude, $orderBy = null)
    {
        $hotels = $this->fetchHotels($latitude, $longitude);
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
     *
     * @return array
     */
    protected function fetchHotels($latitude, $longitude): array
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
            $matches = [];
            if (0 === preg_match('/\"(.+)\"/', $hotel[0], $matches)) {
                continue;
            }

            $data[] = [
                'name' => $matches[1],
                'distance' => (int)$this->haversineDistance($latitude, $hotel[1], $longitude, $hotel[2]),
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
     * @return int
     */
    protected function haversineDistance($latitudeOri, $longitudeOri, $latitudeDest, $longitudeDest): int
    {
        try {
            $deltaLat = ($latitudeDest * M_PI / 180) - ($latitudeOri * M_PI / 180);
            $deltaLon = ($longitudeDest * M_PI / 180) - ($longitudeOri * M_PI / 180);

            $angle = 2 * asin(sqrt(pow(sin($deltaLat / 2), 2) +
                cos($latitudeOri) * cos($latitudeDest) * pow(sin($deltaLon / 2), 2)));

            if (true === is_nan($angle)) {
                throw new \Exception('Not a number');
            }

            return round(($angle * self::EARTH_RADIUS) / 1000);
        } catch (\Exception $ex) {
            // If something the distance is invalid, return a very big number so it won't be considered in the
            // distance sort
            return 999999;
        }
    }

    protected function sortHotelsByPrice($a, $b)
    {
        if ($a['price'] == $b['price']) {
            return 0;
        }

        return ($a['price'] < $b['price']) ? -1 : 1;
    }

    protected function sortHotelsByDistance($a, $b)
    {
        if ($a['distance'] === $b['distance']) {
            return 0;
        }

        return ($a['distance'] < $b['distance']) ? -1 : 1;
    }
}
