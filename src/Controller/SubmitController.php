<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Utils\Tasks;
use App\Entity\Exams;
use App\Entity\Validations;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
//use Symfony\Component\OptionsResolver\OptionsResolver;

class SubmitController extends Controller
{
    /** 
     * @Route("/submit/{exam_id}", name="submit", requirements={"exam_id"="\d+"})
     */
    public function submitExam($exam_id, Request $request, Connection $connection)
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
        
        $myexam->setDateOfSubmit(new \DateTime('now'));
        
            // FORM                
        $form = $this->createFormBuilder($myexam)
            ->add('file', FileType::class, array('label' => 'upload your exam file: '))
            ->add('title', TextType::class, array('label' => 'Choose a clear & short title for your exam: '))
            ->add('save', SubmitType::class, array('label' => 'SUBMIT'))
            ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {            
                // HOLDS THE SUBMITTED VALUE & UPDATE VARIABLE $form         
            $myexam = $form->getData();
            
                // INCREMENT EXAM STATUS            
            $myexam->setExamStatus('1');

                // SAVE DATA IN DOCTRINE DB        
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($myexam);
            $entityManager->flush();
            
                // FILE
            $file = $myexam->getFile();
            
            $fileName = $myexam->getExamId().'_00.'.$file->guessExtension();

            $file->move(
                $this->getParameter('files_directory'),
                $fileName
            );

            $myexam->setFileName($fileName);
            
                // CREATE NEW VALIDATIONS LINES IN DB AND SEND MAIL NOTIFICATIONS
            Tasks::newValidations($connection, $exam_id, 1, $id, $dpt, $entityManager);
            
            return $this->redirectToRoute('home');

        }
        
        return $this->render('submit/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }    
}
