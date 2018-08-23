<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils)
{
        // DENY ACCESS TO CONNECTED USER
    if ($this->isGranted('IS_AUTHENTICATED_FULLY'))
    {
        return $this->redirectToRoute('redirect');
    }
    
        // SECURITY CONTROLLER BY SYMFONY DOC
    // get the login error if there is one
    $error = $authenticationUtils->getLastAuthenticationError();

    // last username entered by the user
    $lastUsername = $authenticationUtils->getLastUsername();

    return $this->render('security/index.html.twig', array(
        'last_username' => $lastUsername,
        'error' => $error,
    ));
}
    
}
