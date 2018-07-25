<?php

namespace App\Controller;

use App\Entity\Exams;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AskingTaskController extends Controller
{
    /**
     * @Route("/asking/task", name="asking_task")
     */
    
    public function new(Request $request)
    {
        
        $exam = new Exams();
        $exam->setSecrUserId('3');
        $exam->setUserId('1');
        $exam->setExternDl(new \DateTime('tomorrow'));
        $exam->setDate(new \DateTime('now'));
        $exam->setTitle('Title');
        $exam->setInternDl(new \DateTime('tomorrow'));
        $exam->setDateOfSubmit(new \DateTime('now'));

        $form = $this->createFormBuilder($exam)
            ->add('description', TextareaType::class, array('label' => 'Add a description for this exam: '))
            ->add('externdl', DateType::class, array('label' => 'Choose a deadline: '))
            ->add('save', SubmitType::class, array('label' => 'SUBMIT'))
            ->getForm();
        
        $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        
        $task = $form->getData();

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($task);
        $entityManager->flush();

        return $this->redirectToRoute('asking_task');
        
        }

        return $this->render('asking_task/index.html.twig', array(
            'form' => $form->createView(),
        ));
    
    }
}
