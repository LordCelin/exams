<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class ParametersController extends Controller
{
    /**
     * @Route("/parameters", name="parameters")
     * 
     * @Security("has_role('ROLE_USER')")
     */

    public function index(Request $request,UserPasswordEncoderInterface $passwordEncoder)
    {
            // CURRENT USER
        $user=$this->getUser();

            // FORM
        $form = $this->createFormBuilder($user)
        ->add('plainPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'first_options'  => array('label' => 'Choose a new password: '),
            'second_options' => array('label' => 'Repeat new password: ')))
        ->add('save', SubmitType::class, array('label' => 'SUBMIT'))
        ->getForm();
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $user = $form->getData();

                // ENCODE & SAVE IN PERSISTENT PASSWORD
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            
                // SAVE DATA IN DOCTRINE DB
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            
            return $this->redirectToRoute('redirect');

        }
        return $this->render('parameters/index.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
