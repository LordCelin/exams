<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Validations;
use App\Entity\Exams;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\DBAL\Driver\Connection;
use App\Utils\Tasks;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ValidationController extends Controller
{
    /**
     * @Route("/validation/{valid_id}", name="validation", requirements={"valid_id"="\d+"})
     * 
     * @Security("has_role('ROLE_USER')")
     */
    public function validExam($valid_id, Request $request, Connection $connection)
    {
            // PICK INFORMATION FROM CONNECTED USER        
        $currentuser = $this->getUser();
        $id = $currentuser->getUserId();
        $dpt = $currentuser->getDptId();
        
            // MODIFY THE RIGHT VALIDATION        
        $repository = $this->getDoctrine()->getRepository(Validations::class);
        $myvalid = $repository->findOneBy(['validId' => $valid_id]);
        
            // PICK THE RIGHT EXAM
        $repository2 = $this->getDoctrine()->getRepository(Exams::class);
        $myexam = $repository2->findOneBy(['examId' => $myvalid->getExamId()]);
        $exam_id = $myexam->getExamId();
                
            // DENY ACCESS TO USERS NOT CONCERNED OR IF TASKS ARE OBSOLETE
        if ($myvalid->getUserId() != $id || $myvalid->getValidStatus() > 2)
        {
            return $this->redirectToRoute('error');
        }
               
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
            $myvalid->setValidStatus('1');
            
                // SAVE DATA IN DOCTRINE DB        
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($myvalid);
            
                // FOR AUTO VALIDATION IF EVERYBODY VET THE EXAM
                // NUMBER OF VETTED AND NUMBER OF TOTAL VALIDATIONS
            $totalvalidations = $connection->fetchAll("SELECT COUNT(*) AS nb FROM validations WHERE valid_status < 3 AND exam_id = $exam_id");
            $totalvetted = $connection->fetchAll("SELECT COUNT(*) AS nb FROM validations WHERE valid_status = 1 AND exam_id = $exam_id");
                // STATUS VALIDATION
            if ($totalvalidations[0]['nb'] === $totalvetted[0]['nb'])
            {
                    // PICK ALL VALIDATIONS
                $validations = $repository->findBy(['examId' => $exam_id]);
                
                    // SET STATUS TO ARCHIVED
                foreach($validations as $val)
                {
                    $val->setValidStatus('3');
                }
                $myexam->setExamStatus($myexam->getExamStatus()+1);
                
                    // CREATE NEW VALIDATIONS LINES IN DB AND SEND MAIL NOTIFICATIONS
                if($myexam->getExamStatus() === 2)
                {
                    Tasks::newValidations($connection, $exam_id, 2, $id, $dpt, $entityManager);
                }
            }

                // SAVE DATA IN DOCTRINE DB
            $entityManager->flush();

            return $this->redirectToRoute('home');

        }
        
        return $this->render('validation/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

