<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class SecretaryHomeController extends Controller
{
    /**
     * @Route("/secretary/home", name="secretary_home")
     * 
     * @Security("has_role('ROLE_USER')")
     */
    public function secretaryChoice()
    {
        
        return $this->render('secretary_home/index.html.twig');
    }
}
