<?php

namespace App\Controller;

use App\Entity\Exams;
use App\Entity\Users;
use App\Entity\Validations;
use App\Utils\Mail;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class DeleteController extends Controller
{    
    /**
     * @Route("/delete/user/{user_id}", name="delete_user", requirements={"user_id"="\d+"})
     * 
     * @Security("has_role('ROLE_ADMIN')")
     */
    
    public function deleteUser($user_id)
    {
            // PICK THE RIGHT USER
        $repository = $this->getDoctrine()->getRepository(Users::class);
        $myuser = $repository->findOneBy(['userId' => $user_id]);
        
            // PICK HIS VALIDATIONS
        $repository2 = $this->getDoctrine()->getRepository(Validations::class);
        $hisvalidations = $repository2->findBy(['userId' => $user_id]);
        
            // DELETE IN DOCTRINE DB
        $entityManager = $this->getDoctrine()->getManager();
        
        foreach ($hisvalidations as $one)
        {
            $entityManager->remove($one);
        }
        $entityManager->remove($myuser);
        $entityManager->flush();
        
        return $this->redirectToRoute('user');
    }
    
    /**
     * @Route("/delete/exam/{exam_id}", name="delete_exam", requirements={"exam_id"="\d+"})
     * 
     * @Security("has_role('ROLE_USER')")
     */
    
    public function deleteExam($exam_id)
    {
            // RESTRICT ACESS
        $currentuser = $this->getUser();
        
        if($currentuser->getSecretaryMember() != 1 && $currentuser->getHod() != 1)
        {
            return $this->redirectToRoute('error');
        }        
            // PICK THE RIGHT EXAM
        $repository = $this->getDoctrine()->getRepository(Exams::class);
        $myexam = $repository->findOneBy(['examId' => $exam_id]);
        
            // DELETE FILE
        $fileSystem = new Filesystem();
        $file = $myexam->getFileName();
        $fileSystem->remove('uploads/files/'.$file);
        
            // PICK ITS VALIDATIONS
        $repository2 = $this->getDoctrine()->getRepository(Validations::class);
        $itsvalidations = $repository2->findBy(['examId' => $exam_id]);
        
            // DELETE IN DOCTRINE DB
        $entityManager = $this->getDoctrine()->getManager();
        
        foreach ($itsvalidations as $one)
        {
            $fileSystem2 = new Filesystem();
            $fileval = $one->getFileName();
            $fileSystem2->remove('uploads/files/'.$fileval);
            $entityManager->remove($one);
        }
        $entityManager->remove($myexam);
        $entityManager->flush();
                
        
        return $this->redirectToRoute('in_progress'); 
    }
    
        /**
     * @Route("/reset/psswrd/{user_id}", name="reset_psswrd", requirements={"user_id"="\d+"})
     * 
     * @Security("has_role('ROLE_ADMIN')")
     */
    
    public function reset($user_id, UserPasswordEncoderInterface $passwordEncoder)
    {
            // PICK THE RIGHT USER
        $repository = $this->getDoctrine()->getRepository(Users::class);
        $myuser = $repository->findOneBy(['userId' => $user_id]);
        
            //CHANGE PASSWORD & ENCODE IT
        $myuser->setPlainPassword('123');
        
        $password = $passwordEncoder->encodePassword($myuser, $myuser->getPlainPassword());
        $myuser->setPassword($password);
        
            // SAVE IN DOCTRINE DB
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
        
            // EMAIL NOTIFICATION
        Mail::mailResetPsswrd($myuser);
        
        return $this->redirectToRoute('admin_home');
    }
}
