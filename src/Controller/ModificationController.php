<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\DBAL\Driver\Connection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ModificationController extends Controller
{
    /**
     * @Route("/modification/{valid_id}", name="modification", requirements={"valid_id"="\d+"})
     */
    public function modify($valid_id, Request $request, Connection $connection)
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
        $myexamid = $myvalid->getExamId();
        
            // RESTRICT ACCESS
        if ($myvalid->getUserId() != $id)
        {
            return $this->redirectToRoute('error');
        }
        
            // FORM                
        $form = $this->createFormBuilder($myvalid)
            ->add('file', FileType::class, array('label' => 'upload your exam file: '))
            ->add('comment', TextType::class, array('label' => 'Add a description for your changes: '))
            ->add('save', SubmitType::class, array('label' => 'SUBMIT'))
            ->getForm();
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
         
                // HOLDS THE SUBMITTED VALUE & UPDATE VARIABLE $form         
            $myvalid = $form->getData();
            
                // INCREMENT VALID STATUS            
            $myvalid->setValidStatus('2');

                // SAVE DATA IN DOCTRINE DB        
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($myvalid);
            $entityManager->flush();
            
                // FILE
            
            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $myvalid->getFile();
                        
            $fileName = $myexamid.'_'.$valid_id.'.'.$file->guessExtension();

            // moves the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('files_directory'),
                $fileName
            );

            // updates the 'brochure' property to store the PDF file name
            // instead of its contents
            $myvalid->setFileName($fileName);

            // ... persist the $product variable or any other work

            return $this->redirectToRoute('home');

    }
        return $this->render('modification/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
