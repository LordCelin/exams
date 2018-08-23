<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Users;
use App\Entity\Departments;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class EditUserController extends Controller
{
    /**
     * @Route("/edit/user/{user_id}", name="edit_user", requirements={"dpt_id"="\d+"})
     * 
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editUser(Request $request, $user_id)
    {
        
        $repository = $this->getDoctrine()->getRepository(Users::class);
        $newuser = $repository->findOneBy(['userId' => $user_id]);
        
        $repository2 = $this->getDoctrine()->getRepository(Departments::class);
        $mydepartments = $repository2->findAll();
        
        $dpts = [];
        
        foreach($mydepartments as $line){
            $dpts[$line->getDptName()] = $line->getDptId();
        }
                
        $form = $this->createFormBuilder($newuser)
            ->add('firstname', TextType::class, array('label' => 'First Name: '))
            ->add('name', TextType::class, array('label' => 'Name: '))
            ->add('mail', EmailType::class, array('label' => 'Email Address: '))
            ->add('hod', CheckboxType::class, array('label'=> 'Head of Department? ','required' => false,))
            ->add('secretarymember', CheckboxType::class, array('label'=> 'Secretary Member? ','required' => false,))
            ->add('dptid', ChoiceType::class, array('choices' => $dpts, 'label' => 'Choose a department: '))
            ->add('save', SubmitType::class, array('label' => 'SUBMIT'))
            ->getForm();
        
        $form->handleRequest($request);

     if ($form->isSubmitted() && $form->isValid()) {
         
            // HOLDS THE SUBMITTED VALUE & UPDATE VARIABLE $form
        $newuser = $form->getData();

            // SAVE DATA IN DOCTRINE DB
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('user');

    }
    
        return $this->render('edit_user/index.html.twig', [
            'form' => $form->createView(),
            'userid' =>$user_id
        ]);
    }
}
