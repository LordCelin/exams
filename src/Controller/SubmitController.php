<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Users;
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
        
            // PICK INFORMATION FROM CONNECTED USER        
        $currentuser = $this->getUser();
        $id = $currentuser->getUserId();
        $dpt = $currentuser->getDptId();
        
            // MODIFY THE RIGHT EXAM        
        $repository = $this->getDoctrine()->getRepository(Exams::class);
        $myexam = $repository->findOneBy(['examId' => $exam_id]);
        
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
            
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $myexam->getFile();
            
            $fileName = $myexam->getTitle().'.'.$file->guessExtension();

            // moves the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('files_directory'),
                $fileName
            );

            // updates the 'brochure' property to store the PDF file name
            // instead of its contents
            $myexam->setFileName($fileName);

            // ... persist the $product variable or any other work
            
                // CREATE A LINE IN VALIDATION FOR EACH TEACHER IN THE DEPARTMENT
                // PICK TEACHERS EXEPT CONNECTED USER
            $userofmydpt = $connection->fetchAll("SELECT * FROM users WHERE dpt_id = $dpt AND secretary_member = 0 AND user_id <> $id");
            
                // CREATE VALIDATIONS WITH SOME RANDOM IN NOT NULL AREAS
            foreach($userofmydpt as $usr)
            {
                $valid = new Validations();
                $valid->setExamId($exam_id);
                $valid->setUserId($usr['user_id']);
                $valid->setComment('comment');
                $valid->setFileName('file');
                $valid->setFilePath('path');
                
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($valid);
                $entityManager->flush();                
            }
            
            return $this->redirectToRoute('home');

        }
        
        return $this->render('submit/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setDefaults(array(
//            'data_class' => Exams::class,
//        ));
//    }

    
}
