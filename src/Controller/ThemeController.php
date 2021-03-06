<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ThemeController extends AbstractController
{
    /**
     * @Route("/theme", name="theme")
     */
    public function index()
    {
        return $this->render('theme/index.html.twig', [
            'controller_name' => 'ThemeController',
        ]);
    }
}
