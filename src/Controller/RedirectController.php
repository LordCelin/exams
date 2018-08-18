<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class RedirectController extends Controller
{
    /**
     * @Route("/", name="redirect")
     */
    public function index()
    {
        
            // RETURN LOGIN PAGE IF USER IS NOT CONNECTED
        if ($this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'))
        {
            return $this->redirectToRoute('login');
        }
        
        else if ($this->isGranted('ROLE_ADMIN'))
        {
        return $this->redirectToRoute('admin_home');
        }
        
        else{
                // ELSE, PICK INFORMATION FROM CONNECTED USER AND REDIRECT HIM       
            $currentuser = $this->getUser();
                // IF HE'S NOT A SECRECTARY MEMBER, REDIRECT TO HOME
            if ($currentuser->getSecretaryMember() == 0)
            {return $this->redirectToRoute('home');}
                // ELSE, REDIRECT TO SECRETARY PAGE
            else {return $this->redirectToRoute('secretary_home');}
        }
        
        return $this->render('redirect/index.html.twig', [
            'controller_name' => 'RedirectController',
        ]);
    }
}
