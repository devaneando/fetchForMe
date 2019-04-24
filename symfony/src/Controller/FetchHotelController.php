<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\FetchHotelType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Service\SearchService;

class FetchHotelController extends AbstractController
{
    /** @var SearchService $searchService */
    private $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(Request $request)
    {
        $form = $this->createForm(FetchHotelType::class);
        $hotels = [];
        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $data = $form->getData();
                $hotels = $this->searchService->getNearbyHotels($data['latitude'], $data['longitude'], $data['sortBy']);
            }
        }
        return $this->render('fetch_hotel/index.html.twig', [
            'form' => $form->createView(),
            'hotels' => $hotels
        ]);
    }

    /**
     * Undocumented function
     *
     * @Route("/search/nearby-hotels/{latitude}/{longitude}/{orderBy}", name="search_nearby_hotels_orderby")
     *
     * @param Request $request
     * @param float $latitude
     * @param float $longitude
     * @param string $orderBy
     *
     * @return JsonResponse
     */
    public function getNearbyHotels(Request $request, $latitude, $longitude, $orderBy = null)
    {
        return new JsonResponse($this->searchService->getNearbyHotels($latitude, $longitude, $orderBy));
    }
}
