<?php

namespace App\Controller;

use App\Entity\Departments;
use App\Entity\Subjects;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FormSubjectController extends Controller
{
    /**
     * @Route("/form/subject", name="form_subject")
     */
    
    public function new(Request $request)
    {
        
        $repository = $this->getDoctrine()->getRepository(Departments::class);
        $mydepartments = $repository->findAll();
        
        $repository2 = $this->getDoctrine()->getRepository(Subjects::class);
        $mysubjbydpt = $repository2->findAll();
        
        $dpts = [];
        foreach($mydepartments as $line){
        $dpts[$line->getDptName()] = $line->getDptId();
        }
                
        $newsubj = new Subjects();

        $form = $this->createFormBuilder($newsubj)
            ->add('subjname', TextType::class, array('label' => 'Add a new subject name: '))
            ->add('dptid', ChoiceType::class, array('choices' => $dpts, 'label' => 'Choose in wich Department it is: '))
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

        return $this->redirectToRoute('form_subject');

    }
    
        return $this->render('form_subject/index.html.twig', [
            'form' => $form->createView(),
            'mydepartments' => $mydepartments,
            'mysubjbydpt' => $mysubjbydpt,
            ]
        );
    
    }
}
