<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SubmitController extends Controller
{
    /**
     * @Route("/submit", name="submit")
     */
    public function index()
    {
        return $this->render('submit/index.html.twig', [
            'controller_name' => 'SubmitController',
        ]);
    }
}
