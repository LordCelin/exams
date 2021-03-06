<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Exams;
use App\Entity\Validations;
use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use App\Utils\Tasks;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class OwnerValidationController extends Controller
{
    /**
     * @Route("/owner/validation/{exam_id}", name="owner_validation", requirements={"exam_id"="\d+"})
     * 
     * @Security("has_role('ROLE_USER')")
     */
    public function validStepExam($exam_id, Request $request, Connection $connection)
    {
            // PICK INFORMATION FROM CONNECTED USER
        $currentuser = $this->getUser();
        $id = $currentuser->getUserId();
        $dpt = $currentuser->getDptId();
        
            // MODIFY THE RIGHT EXAM        
        $repository = $this->getDoctrine()->getRepository(Exams::class);
        $myexam = $repository->findOneBy(['examId' => $exam_id]);
        
            // RESTRICT ACCESS
        if ($myexam->getUserId() != $id || $myexam->getExamStatus() > 2)
        {
            return $this->redirectToRoute('error');
        }
        
            // FORM (WE DON'T PUT DIRECTLY VALUES IN THE ENTITY BECAUSE OF THE DEADLINE RANGE IN EXAMS ENTITY:
            // ELSE, IT'S IMPOSSIBLE TO EDIT THE EXAM AFTER THE DEADLINE)
        $form = $this->createFormBuilder()
            ->add('file', FileType::class, array('label' => 'You can add a new file: ', 'mapped' => false))
            ->add('save', SubmitType::class, array('label' => 'SUBMIT'))
            ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {            
                // HOLDS THE SUBMITTED VALUE & UPDATE VARIABLE $form         
            $datafile = $form['file']->getData();
            $myexam->setFile($datafile);
            
                // INCREMENT EXAM STATUS            
            $myexam->setExamStatus($myexam->getExamStatus()+1);

                // FILE            
            $file = $myexam->getFile();
            
            if (file_exists($file))
            {            
                $fileName = $myexam->getExamId().'_00.'.$file->guessExtension();

                    // MOVES THE FILE IN THE DIRECTORY
                $file->move(
                    $this->getParameter('files_directory'),
                    $fileName
                );

                    // SET FILENAME TO FIND THE FILE LATER
                $myexam->setFileName($fileName);
            }
            
                // ARCHIVE LAST STEP OF VALIDATION
            $repository2 = $this->getDoctrine()->getRepository(Validations::class);
            $oldvalidations = $repository2->findBy(['examId' => $exam_id]);
            foreach($oldvalidations as $val)
            {
                $val->setValidStatus('3');
            }
            
                // SAVE DATA IN DOCTRINE DB        
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($myexam);
            $entityManager->flush();
            
                // CREATE NEW VALIDATIONS LINES IN DB AND SEND MAIL NOTIFICATIONS
            if($myexam->getExamStatus() === 2)
            {
                Tasks::newValidations($connection, $exam_id, 2, $id, $dpt, $entityManager);
            }
            
            return $this->redirectToRoute('home');

        }
        
        return $this->render('owner_validation/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }    
}