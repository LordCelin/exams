<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EditUserController extends Controller
{
    /**
     * @Route("/edit/user/{user_id}", name="edit_user", requirements={"dpt_id"="\d+"})
     */
    public function index()
    {
        return $this->render('edit_user/index.html.twig', [
            'controller_name' => 'EditUserController',
        ]);
    }
}
