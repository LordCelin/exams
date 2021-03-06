<?php

namespace App\Controller;

use App\Entity\Exams;
use App\Entity\Users;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AskingTaskController extends Controller
{
    /**
     * @Route("/asking/task", name="asking_task")
     * 
     * @Security("has_role('ROLE_USER')")
     */
    
    public function askingExam(Request $request)
    {        
            // PICK INFORMATION FROM CONNECTED USER        
        $currentuser = $this->getUser();
        $id = $currentuser->getUserId();
        $dpt = $currentuser->getDptId();
        
            // RESTRICT ACESS
        if($currentuser->getSecretaryMember() != 1 && $currentuser->getHod() != 1)
        {
            return $this->redirectToRoute('error');
        }
        
            // PICK ALL THE TEACHERS IN THE DEPARTMENT OF THE CONNECTED SECRETARY MEMBER
        $repository = $this->getDoctrine()->getRepository(Users::class);
        $dptteachers = $repository->findBy(['dptId' => $dpt, 'secretaryMember' => 0]);        
        
            // CREATE AN ARRAY WITH NAMES OF THESE TEACHERS        
        $teachers = [];
        foreach($dptteachers as $line){
        $teachers[$line->getName().' '.$line->getFirstName()] = $line->getUserId();
        }
            // SORT BY ALPHABET
        ksort($teachers);
                
            // CREATE NEW EXAM : PUT NEW INFORMATIONS AND DATES FOR THE FORM        
        $exam = new Exams();
        $exam->setSecrUserId($id);
        $exam->setDeadline(new \DateTime('tomorrow'));
        $exam->setDate(new \DateTime('now'));

            // FORM        
        $form = $this->createFormBuilder($exam)
        ->add('subject', TextareaType::class, array('label' => 'Add the subject of this exam: (ex: mathematics...) '))
        ->add('description', TextareaType::class, array('label' => 'Add a description for the content of this exam: '))
        ->add('deadline', DateType::class, array('label' => 'Choose a deadline: '))
        ->add('userid', ChoiceType::class, array('choices' => $teachers, 'label' => 'Choose submitter: '))
        ->add('save', SubmitType::class, array('label' => 'SUBMIT'))
        ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        
            $task = $form->getData();

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('redirect');
        
        }

        return $this->render('asking_task/index.html.twig', array(
            'form' => $form->createView(),
        ));
    
    }
}
