<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AdminHomeController extends Controller
{
    /**
     * @Route("/admin/home", name="admin_home")
     *
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function adminChoice()
    {
        return $this->render('admin_home/index.html.twig');
    }
}
