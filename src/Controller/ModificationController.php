<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Validations;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class ModificationController extends Controller
{
    /**
     * @Route("/modification/{valid_id}", name="modification", requirements={"valid_id"="\d+"})
     * 
     * @Security("has_role('ROLE_USER')")
     */
    public function modify($valid_id, Request $request)
    {
            // PICK INFORMATION FROM CONNECTED USER        
        $currentuser = $this->getUser();
        $id = $currentuser->getUserId();
        
            // MODIFY THE RIGHT EXAM        
        $repository = $this->getDoctrine()->getRepository(Validations::class);
        $myvalid = $repository->findOneBy(['validId' => $valid_id]);
        $myexamid = $myvalid->getExamId();
        
            // DENY ACCESS TO USERS NOT CONCERNED OR IF TASKS ARE OBSOLETE
        if ($myvalid->getUserId() != $id || $myvalid->getValidStatus() > 2)
        {
            return $this->redirectToRoute('error');
        }
        
            // FORM                
        $form = $this->createFormBuilder($myvalid)
            ->add('file', FileType::class, array('label' => 'You can upload a new exam file: '))
            ->add('comment', TextType::class, array('label' => 'Add a description of your changes or propose some modifications: '))
            ->add('save', SubmitType::class, array('label' => 'SUBMIT'))
            ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
                // HOLDS THE SUBMITTED VALUE & UPDATE VARIABLE $form         
            $myvalid = $form->getData();
            
                // INCREMENT VALID STATUS            
            $myvalid->setValidStatus('2');
            
                // FILE
            $file = $myvalid->getFile();
                
            if (file_exists($file))
            {                        
                $fileName = $myexamid.'_'.$valid_id.'.'.$file->guessExtension();

                    // MOVES THE FILE IN THE DIRECTORY
                $file->move(
                    $this->getParameter('files_directory'),
                    $fileName
                );

                    // SET FILENAME TO FIND THE FILE LATER
                $myvalid->setFileName($fileName);
            }

                // SAVE DATA IN DOCTRINE DB        
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($myvalid);
            $entityManager->flush();

            return $this->redirectToRoute('home');

    }
        return $this->render('modification/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
