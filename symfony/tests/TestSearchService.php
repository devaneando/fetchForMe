<?php

namespace App\Tests\Traits;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Service\SearchService;

class TestSearchService extends KernelTestCase
{
    protected function getService(string $service)
    {
        $kernel = self::bootKernel();
        $service = self::$container->get($service);

        return $service;
    }

    public function testHaversineDistance()
    {
        /** @var SearchService $searchService */
        $searchService = $this->getService(SearchService::class);
        $distance = $searchService->haversineDistance(
            38.7135939,
            -9.1579266,
            40.7135939,
            -10.1579266
        );
        $this->assertEquals(207, $distance);
    }

    public function testGetNearbyHotels()
    {
        /** @var SearchService $searchService */
        $searchService = $this->getService(SearchService::class);
        $data = $searchService->getNearbyHotels(38.7135939, -9.1579266);
        $this->assertFalse(empty($data));
        $data = $searchService->getNearbyHotels(38.7135939, -9.1579266, SearchService::SORT_PRICE);
        $this->assertGreaterThanOrEqual($data[50]['price'], $data[50]['price']);
        $data = $searchService->getNearbyHotels(38.7135939, -9.1579266, SearchService::SORT_PROXIMITY);
        $this->assertGreaterThanOrEqual($data[50]['distance'], $data[50]['distance']);
    }
}
