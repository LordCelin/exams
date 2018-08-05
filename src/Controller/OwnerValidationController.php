<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class OwnerValidationController extends Controller
{
    /**
     * @Route("/owner/validation", name="owner_validation")
     */
    public function index()
    {
        return $this->render('owner_validation/index.html.twig', [
            'controller_name' => 'OwnerValidationController',
        ]);
    }
}
