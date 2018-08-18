<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ParametersController extends Controller
{
    /**
     * @Route("/parameters", name="parameters")
     */
    public function index()
    {
        return $this->render('parameters/index.html.twig', [
            'controller_name' => 'ParametersController',
        ]);
    }
}
