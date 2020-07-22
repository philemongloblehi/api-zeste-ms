<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
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

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/users/{id}", name="user_show", methods={"GET"})
     */
   public function getUserAction(Request $request): JsonResponse
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

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("/users", name="user_create", methods={"POST"})
     */
   public function postUsersAction(Request $request): JsonResponse
   {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse($user);
        } else {
            return new JsonResponse($form);
        }
   }

    /**
     * @param Request $request
     * @return JsonResponse
     * @Route("users", name="user_delete", methods={"DELETE"})
     */
   public function removeUserAction(Request $request): JsonResponse
   {
       $em = $this->getDoctrine()->getManager();
       $user = $em->getRepository(User::class)->find($request->get('id'));

       if ($user) {
           $em->remove($user);
           $em->flush();
       }

       return new JsonResponse('', 204);
   }

   public function patchUserAction(Request $request): JsonResponse
   {
        return $this->updateUserAction($request, false);
   }

    /**
     * @param Request $request
     * @param bool $clearMissing
     * @return JsonResponse
     * @Route("/users/{id}", name="user_update", methods={"PUT"})
     */
   public function updateUserAction(Request $request, bool $clearMissing): JsonResponse
   {
       $user = $this->getDoctrine()->getRepository(User::class)->find($request->get('id'));

       if (empty($user)) {
           return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
       }

       $form = $this->createForm(UserType::class, $user);

       $form->submit($request->request->all(), $clearMissing);

       if ($form->isValid()) {
           $em = $this->getDoctrine()->getManager();
           $em->merge($user);
           $em->flush();

           return new JsonResponse($user);
       } else {
           return new JsonResponse($form);
       }
   }



   protected function userNotFound(): JsonResponse
   {
       return new JsonResponse(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
   }

}
