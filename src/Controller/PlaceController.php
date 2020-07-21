<?php

namespace App\Controller;

use App\Entity\Place;
use App\Entity\Price;
use App\Form\PlaceType;
use App\Form\PriceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Json;

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

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/places", name="places_create", methods={"POST"})
     */
    public function postPlacesAction(Request $request): JsonResponse
    {
        $place = new Place();
        $form = $this->createForm(PlaceType::class, $place);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($place);
            $em->flush();
            return new JsonResponse($place);
        } else {
            return new JsonResponse($form);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/places", name="places_delete", methods={"DELETE"})
     */
    public function removePlaceAction(Request $request): JsonResponse
    {
        $em = $this->getDoctrine()->getManager();
        $place = $em->getRepository(Place::class)->find($request->get('id'));
        if ($place) {
            $em->remove($place);
            $em->flush();
        }

        return new JsonResponse('', 204);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/places", name="place_update", methods={"PUT"})
     */
    public function updatePlaceAction(Request $request): JsonResponse
    {
        $place = $this->getDoctrine()->getRepository(Place::class)->find($request->get('id'));

        if (empty($place)) {
            return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(PlaceType::class, $place);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->merge($place);
            $em->flush();

            return new JsonResponse($place);
        } else {
            return new JsonResponse($form);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/users/{id}", name="place_patch", methods={"PATCH"})
     */
    public function patchPlaceAction(Request $request): JsonResponse
    {
        $place = $this->getDoctrine()->getRepository(Place::class)->find($request->get('id'));

        if (empty($place)) {
            return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(PlaceType::class, $place);
        $form->submit($request->request->all(), false);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->merge($place);
            $em->flush();

            return new JsonResponse($place);
        } else {
            return new JsonResponse($form);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/places/{id}/prices", name="place_prices", methods={"GET"})
     */
    public function getPricesAction(Request $request): JsonResponse
    {
        $place = $this->getDoctrine()->getRepository(Place::class)->find($request->get('id'));

        if (empty($place)) {
            return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($place->getPrices());
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/places/{id}/prices", name="place_prices_create", methods={"POST"})
     */
    public function postPricesAction(Request $request): JsonResponse
    {
        $place = $this->getDoctrine()->getRepository(Place::class)->find($request->get('id'));

        if (empty($place)) {
            return $this->placeNotFound();
        }

        $price = new Price();
        $price->setPlace($place);

        $form = $this->createForm(PriceType::class, $price);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($price);
            $em->flush();

            return new JsonResponse($price);
        } else {
            return new JsonResponse($form);
        }

    }

    protected function placeNotFound(): JsonResponse
    {
        return new JsonResponse(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
    }
}
