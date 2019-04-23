<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Model\FetchHotelType;

class FetchHotelController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $form = $this->createForm(FetchHotelType::class);
        return $this->render('fetch_hotel/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
