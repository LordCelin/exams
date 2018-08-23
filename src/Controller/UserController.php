<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Utils\Mail;
use App\Entity\Users;
use App\Entity\Departments;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserController extends Controller
{
    /**
     * @Route("/user", name="user")
     * 
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addNewUser(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
            // FIND ALL USERS
        $repository = $this->getDoctrine()->getRepository(Users::class);
        $myusers = $repository->findAll();
        
            // FIND ALL DEPARTMENTS
        $repository2 = $this->getDoctrine()->getRepository(Departments::class);
        $mydepartments = $repository2->findAll();
        
            // CREATE AN ARRAY WITH DPT_NAMES AS KEY AND DPT_ID AS VALUE FOR THE FORM
        $dpts = [];
        
        foreach($mydepartments as $line){
            $dpts[$line->getDptName()] = $line->getDptId();
        }
        
            // CREATE NEW USER WITH AUTO PASSORD
        $newuser = new Users();
        
        $newuser->setPlainPassword('123');
        
            // ENCODE PASSWORD AND SAVE IT IN PERSISTENT PASSWORD
        $password = $passwordEncoder->encodePassword($newuser, $newuser->getPlainPassword());
        $newuser->setPassword($password);

            // FORM
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
        $user = $form->getData();

            // SAVE DATA IN DOCTRINE DB        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
         
            // EMAIL NOTIFICATION
        Mail::mailNewUser($user);

        return $this->redirectToRoute('user');

    }
        
        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
            'myusers' => $myusers,
            ]
        );
    }
}
