<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Departments;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class EditDptController extends Controller
{
    /**
     * @Route("/edit/dpt/{dpt_id}", name="edit_dpt", requirements={"dpt_id"="\d+"})
     * 
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editDepartment($dpt_id, Request $request)
    {
        
            // MODIFY THE RIGHT DEPARTMENT        
        $repository = $this->getDoctrine()->getRepository(Departments::class);
        $mydpt = $repository->findOneBy(['dptId' => $dpt_id]);
        
            // FORM                
        $form = $this->createFormBuilder($mydpt)
            ->add('dptname', TextType::class, array('label' => 'Edit name '))
            ->add('save', SubmitType::class, array('label' => 'SUBMIT'))
            ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
                // HOLDS THE SUBMITTED VALUE & UPDATE VARIABLE $form
            $mydpt = $form->getData();

                // SAVE DATA IN DOCTRINE DB        
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($mydpt);
            $entityManager->flush();
            
            return $this->redirectToRoute('form_department');

        }
        
        return $this->render('edit_dpt/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
