<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PreferenceController extends AbstractController
{
    /**
     * @Route("/preference", name="preference")
     */
    public function index()
    {
        return $this->render('preference/index.html.twig', [
            'controller_name' => 'PreferenceController',
        ]);
    }
}
