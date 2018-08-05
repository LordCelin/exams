<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ValidationController extends Controller
{
    /**
     * @Route("/validation/{valid_id}", name="validation", requirements={"valid_id"="\d+"})
     */
    public function validExam($valid_id, Request $request, Connection $connection)
    {
        
            // PICK INFORMATION FROM CONNECTED USER        
        $currentuser = $this->getUser();
        $id = $currentuser->getUserId();
        $dpt = $currentuser->getDptId();
        
            // MODIFY THE RIGHT EXAM        
        $repository = $this->getDoctrine()->getRepository(Validations::class);
        $myvalid = $repository->findOneBy(['validId' => $valid_id]);
        
            // FORM                
        $form = $this->createFormBuilder($myvalid)
            ->add('comment', TextType::class, array('label' => 'Add a comment? '))
            ->add('save', SubmitType::class, array('label' => 'SUBMIT'))
            ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
                // HOLDS THE SUBMITTED VALUE & UPDATE VARIABLE $form
            $myvalid = $form->getData();
            
                // INCREMENT VALID STATUS            
            $myvalid->setExamStatus('1');

                // SAVE DATA IN DOCTRINE DB        
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($myvalid);
            $entityManager->flush();

            return $this->redirectToRoute('home');

    }
        
        return $this->render('validation/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

