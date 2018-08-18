<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class EditSubjectController extends Controller
{
    /**
     * @Route("/edit/subject/{subj_id}", name="edit_subject", requirements={"subj_id"="\d+"})
     */
    public function index()
    {
        return $this->render('edit_subject/index.html.twig', [
            'controller_name' => 'EditSubjectController',
        ]);
    }
}
