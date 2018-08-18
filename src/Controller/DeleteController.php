<?php

namespace App\Controller;

use App\Entity\Exams;
use App\Entity\Users;
use App\Entity\Validations;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Filesystem\Filesystem;

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
     */
    
    public function deleteExam($exam_id)
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
        
            // RESTRICT ACESS
        if($currentuser->getSecretaryMember() != 1 && $currentuser->getHod() != 1)
        {
            return $this->redirectToRoute('error');
        }        
            // PICK THE RIGHT EXAM
        $repository = $this->getDoctrine()->getRepository(Exams::class);
        $myexam = $repository->findOneBy(['examId' => $exam_id]);
        
            // DELETE FILE
        $fileSystem = new Filesystem();
        $fileSystem->remove(array('files_directory', $myexam->getFileName()));
        
            // PICK HIS VALIDATIONS
        $repository2 = $this->getDoctrine()->getRepository(Validations::class);
        $hisvalidations = $repository2->findBy(['examId' => $exam_id]);
        
            // DELETE IN DOCTRINE DB
        $entityManager = $this->getDoctrine()->getManager();
        
        foreach ($hisvalidations as $one)
        {
            $entityManager->remove($one);
            $fileSystem->remove(array('files_directory', $one->getFileName()));
        }
        $entityManager->remove($myexam);
        $entityManager->flush();
        
        
        
        return $this->redirectToRoute('in_progress'); 
    }
}
