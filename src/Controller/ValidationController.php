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

class ValidationController extends Controller
{
    /**
     * @Route("/validation/{valid_id}", name="validation", requirements={"valid_id"="\d+"})
     */
    public function validExam($valid_id, Request $request, Connection $connection)
    {
            // RETURN LOGIN PAGE IF USER IS NOT CONNECTED
        if ($this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY'))
        {
            return $this->redirectToRoute('login');
        }
        
            // PICK INFORMATION FROM CONNECTED USER        
        $currentuser = $this->getUser();
        $id = $currentuser->getUserId();
        $dpt = $currentuser->getDptId();
        
            // MODIFY THE RIGHT EXAM        
        $repository = $this->getDoctrine()->getRepository(Validations::class);
        $myvalid = $repository->findOneBy(['validId' => $valid_id]);
                
            // RESTRICT ACCESS
        if ($myvalid->getUserId() != $id)
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
            $entityManager->flush();
            
                // FOR AUTO VALIDATION IF EVERYBODY VET THE EXAM
                // NUMBER OF VETTED AND NUMBER OF TOTAL VALIDATIONS
            $totalvalidations = $connection->fetchAll("SELECT COUNT(*) AS nb FROM validations WHERE valid_status < 3 AND exam_id IN "
                    . "(SELECT exam_id FROM exams WHERE user_id = $id)");
            $totalvetted = $connection->fetchAll("SELECT COUNT(*) AS nb FROM validations WHERE valid_status = 1 AND exam_id IN "
                    . "(SELECT exam_id FROM exams WHERE user_id = $id)");
                // STATUS VALIDATION
            if ($totalvalidations === $totalvetted)
            {
                    // PICK ALL VALIDATIONS
                $validations = $repository->findBy(['examId' => $myvalid->getExamId()]);
                    // PICK THE RIGHT EXAM
                $repository2 = $this->getRepository(Exams::class);
                $myexam = $repository2->findOneBy(['examId' => $myvalid->getExamId()]);
                    // SET STATUS
                $validations->setValidStatus('3');
                $myexam->setExamStatus($myexam->getExamStatus()+1);
                
                    // CREATE NEW VALIDATIONS LINES IN DB AND SEND MAIL NOTIFICATIONS
                if($myexam->getExamStatus() === 2)
                {
                    $exam_id = $myexam->getExamId();
                    Tasks::newValidations($connection, $exam_id, 2, $id, $dpt, $entityManager);
                }
            }

            return $this->redirectToRoute('home');

        }
        
        return $this->render('validation/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

