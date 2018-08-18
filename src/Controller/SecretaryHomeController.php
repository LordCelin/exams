<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class SecretaryHomeController extends Controller
{
    /**
     * @Route("/secretary/home", name="secretary_home")
     */
    public function secretaryChoice()
    {
            // RETURN LOGIN PAGE IF USER IS NOT CONNECTED
        if ($this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'))
        {
            return $this->redirectToRoute('login');
        }
        
        return $this->render('secretary_home/index.html.twig', [
            'controller_name' => 'SecretaryHomeController',
        ]);
    }
}
