<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Exams;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class NewUploadController extends Controller
{
    /**
     * @Route("/new/upload/{exam_id}", name="new_upload", requirements={"exam_id"="\d+"})
     * 
     * @Security("has_role('ROLE_USER')")
     */
    public function uploadNewFile($exam_id, Request $request)
    {        
            // PICK INFORMATION FROM CONNECTED USER
        $currentuser = $this->getUser();
        $id = $currentuser->getUserId();
        
            // MODIFY THE RIGHT EXAM        
        $repository = $this->getDoctrine()->getRepository(Exams::class);
        $myexam = $repository->findOneBy(['examId' => $exam_id]);
        
            // DENY ACCESS TO USERS NOT CONCERNED OR IF TASKS ARE OBSOLETE (IF USER IS HERE BY URL)
        if ($myexam->getUserId() != $id || $myexam->getExamStatus() > 2)
        {
            return $this->redirectToRoute('error');
        }
        
            // FORM                
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
            
                // SAVE DATA IN DOCTRINE DB        
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($myexam);
            $entityManager->flush();
            
            return $this->redirectToRoute('exam_view', ['exam_id' => $exam_id]);

        }
        
        return $this->render('new_upload/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}