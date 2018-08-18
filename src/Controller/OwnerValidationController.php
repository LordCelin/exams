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

class OwnerValidationController extends Controller
{
    /**
     * @Route("/owner/validation/{exam_id}", name="owner_validation", requirements={"exam_id"="\d+"})
     */
    public function validStepExam($exam_id, Request $request, Connection $connection)
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
        $repository = $this->getDoctrine()->getRepository(Exams::class);
        $myexam = $repository->findOneBy(['examId' => $exam_id]);
        
            // RESTRICT ACCESS
        if ($myexam->getUserId() != $id)
        {
            return $this->redirectToRoute('error');
        }
        
            // FORM                
        $form = $this->createFormBuilder($myexam)
            ->add('file', FileType::class, array('label' => 'You can add a new file: '))
            ->add('save', SubmitType::class, array('label' => 'SUBMIT'))
            ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {            
                // HOLDS THE SUBMITTED VALUE & UPDATE VARIABLE $form         
            $myexam = $form->getData();
            
                // INCREMENT EXAM STATUS            
            $myexam->setExamStatus($myexam->getExamStatus()+1);

                // SAVE DATA IN DOCTRINE DB        
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($myexam);
            $entityManager->flush();
            
                // FILE
            
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $myexam->getFile();
            
            $fileName = $myexam->getExamId().'_00.'.$file->guessExtension();

            // moves the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('files_directory'),
                $fileName
            );

            // updates the 'brochure' property to store the PDF file name
            // instead of its contents
            $myexam->setFileName($fileName);

            // ... persist the $product variable or any other work
            
                // ARCHIVE LAST STEP OF VALIDATION
            $repository2 = $this->getDoctrine()->getRepository(Validations::class);
            $oldvalidations = $repository2->findBy(['examId' => $exam_id]);
            //$oldvalidations = $connection->fetchAll("SELECT * FROM validations WHERE exam_id = $exam_id");            
            foreach($oldvalidations as $val)
            {
                $val->setValidStatus('3');
            }
            
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
