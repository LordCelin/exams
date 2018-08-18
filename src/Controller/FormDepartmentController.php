<?php

namespace App\Controller;

use App\Entity\Departments;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class FormDepartmentController extends Controller
{
    /**
     * @Route("/form/department", name="form_department")
     * 
     * @Security("has_role('ROLE_ADMIN')")
     */    
    public function AddDepartments(Request $request)
    {
        $newdpt = new Departments();

        $form = $this->createFormBuilder($newdpt)
            ->add('dptname', TextType::class, array('label' => 'Add a new department name: '))
            ->add('save', SubmitType::class, array('label' => 'SUBMIT'))
            ->getForm();
        
        $form->handleRequest($request);

     if ($form->isSubmitted() && $form->isValid()) {
         
        // HOLDS THE SUBMITTED VALUE & UPDATE VARIABLE $form
         
        $dpt = $form->getData();

        // SAVE DATA IN DOCTRINE DB
        
         $entityManager = $this->getDoctrine()->getManager();
         $entityManager->persist($dpt);
         $entityManager->flush(); 

        return $this->redirectToRoute('form_department');

    }
    
        $repository = $this->getDoctrine()->getRepository(Departments::class);
        $mydepartments = $repository->findAll();
        
        return $this->render('form_department/index.html.twig', [
            'form' => $form->createView(),
            'mydepartments' => $mydepartments,
            ]
        );
    
    }
}
