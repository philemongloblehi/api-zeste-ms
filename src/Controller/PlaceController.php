<?php

namespace App\Controller;

use App\Entity\Place;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlaceController extends AbstractController
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/places", name="places_list", methods={"GET"})
     */
    public function getPlacesAction(Request $request): JsonResponse
    {
        $places = $this->getDoctrine()->getRepository(Place::class)->findAll();

        $formatted = [];
        foreach ($places as $place) {
            $formatted[] = [
              'id' => $place->getId(),
              'name' => $place->getName(),
              'address' => $place->getAddress()
            ];
        }

        return new JsonResponse($formatted);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/places/{place_id}", name="places_one", methods={"GET"})
     */
    public function getPlaceAction(Request $request): JsonResponse
    {
        $place = $this->getDoctrine()->getRepository(Place::class)->find($request->get('place_id'));

        if (empty($place)) {
            return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        $formatted = [
            'id' => $place->getId(),
            'name' => $place->getName(),
            'address' => $place->getAddress()
        ];

        return new JsonResponse($formatted);
    }
}
