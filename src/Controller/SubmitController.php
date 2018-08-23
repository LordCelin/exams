<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Utils\Tasks;
use App\Entity\Exams;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class SubmitController extends Controller
{
    /** 
     * @Route("/submit/{exam_id}", name="submit", requirements={"exam_id"="\d+"})
     * 
     * @Security("has_role('ROLE_USER')")
     */
    public function submitExam($exam_id, Request $request, Connection $connection)
    {
            // PICK INFORMATION FROM CONNECTED USER
        $currentuser = $this->getUser();
        $id = $currentuser->getUserId();
        $dpt = $currentuser->getDptId();
        
            // MODIFY THE RIGHT EXAM
        $repository = $this->getDoctrine()->getRepository(Exams::class);
        $myexam = $repository->findOneBy(['examId' => $exam_id]);
        
            // RESTRICT ACCESS
        if ($myexam->getUserId() != $id || $myexam->getExamStatus() != 0)
        {
            return $this->redirectToRoute('error');
        }
        
        $myexam->setDateOfSubmit(new \DateTime('now'));
        
            // FORM (WE DON'T PUT DIRECTLY VALUES IN THE ENTITY BECAUSE OF THE DEADLINE RANGE IN EXAMS ENTITY:
            // ELSE, IT'S IMPOSSIBLE TO EDIT THE EXAM AFTER THE DEADLINE)
        $form = $this->createFormBuilder()
            ->add('file', FileType::class, array('label' => 'Upload your exam file: ', 'mapped' => false))
            ->add('title', TextType::class, array(
                'label' => 'Choose a clear & short title for your exam: ',
                'mapped' => false,
                'empty_data' => 'no_title'
                ))
            ->add('save', SubmitType::class, array('label' => 'SUBMIT'))
            ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {            
                // SET DATA IN THE EXAM         
            $datafile = $form['file']->getData();
            $datatitle = $form['title']->getData();
            $myexam->setFile($datafile);
            $myexam->setTitle($datatitle);
            
                // INCREMENT EXAM STATUS            
            $myexam->setExamStatus('1');
            
                // FILE            
            $file = $myexam->getFile();
            
            if (file_exists($file))
            {
                $fileName = $myexam->getExamId().'_00.'.$file->guessExtension();

                $file->move(
                    $this->getParameter('files_directory'),
                    $fileName
                );

                $myexam->setFileName($fileName);
            
            }
            
                // SAVE DATA IN DOCTRINE DB        
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($myexam);
            $entityManager->flush();
            
                // CREATE NEW VALIDATIONS LINES IN DB AND SEND MAIL NOTIFICATIONS
            Tasks::newValidations($connection, $exam_id, 1, $id, $dpt, $entityManager);
            
            return $this->redirectToRoute('home');

        }
        
        return $this->render('submit/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }    
}