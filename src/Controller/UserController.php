<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Users;
use App\Controller\MailController;
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

        $repository = $this->getDoctrine()->getRepository(Users::class);
        $myusers = $repository->findAll();
        
        $repository2 = $this->getDoctrine()->getRepository(Departments::class);
        $mydepartments = $repository2->findAll();
        
        $dpts = [];
        
        foreach($mydepartments as $line){
            $dpts[$line->getDptName()] = $line->getDptId();
        }
        
        $newuser = new Users();
        
        $newuser->setPlainPassword('123');
        
        $password = $passwordEncoder->encodePassword($newuser, $newuser->getPlainPassword());
        $newuser->setPassword($password);

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
         
        $newuser->setUsername($newuser->getFirstName().'.'.$newuser->getName());
         
        // HOLDS THE SUBMITTED VALUE & UPDATE VARIABLE $form
         
        $user = $form->getData();

        // SAVE DATA IN DOCTRINE DB
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();
         
//            // EMAIL NOTIFICATION
//        $mail = new MailController();
//        $mail->mailNewUser($user);

        return $this->redirectToRoute('user');

    }
        
        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
            'myusers' => $myusers,
            ]
        );
    }
}
