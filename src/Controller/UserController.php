<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/users", name="users_list", methods={"GET"})
     */
    public function getUsersAction(Request $request): JsonResponse
   {
       $users = $this->getDoctrine()->getRepository(User::class)->findAll();

       $formatted = [];
       foreach ($users as $user) {
           $formatted[] = [
             'id' => $user->getId(),
             'firstname' => $user->getFirstname(),
             'lastname'=> $user->getLastname(),
             'email' => $user->getEmail()
           ];
       }

       return new JsonResponse($formatted);
   }

   public function getUserAction(Request $request)
   {
        $user = $this->getDoctrine()->getRepository(User::class)->find($request->get('user_id'));

        if (empty($user)) {
            return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $formatted = [
            'id' => $user->getId(),
            'firstname' => $user->getFirstname(),
            'lastname' => $user->getLastname(),
            'email' => $user->getEmail()
        ];

        return new JsonResponse($formatted);
   }

}
