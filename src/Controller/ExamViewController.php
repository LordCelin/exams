<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Entity\Exams;
use App\Entity\Users;
use App\Entity\Validations;
use Doctrine\DBAL\Driver\Connection;

class ExamViewController extends Controller
{
    /**
     * @Route("/exam/view/{exam_id}", name="exam_view", requirements={"exam_id"="\d+"})
     */
    public function showExams($exam_id, Connection $connection)
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
        
            // PICK THE RIGHT EXAM        
        $repository = $this->getDoctrine()->getRepository(Exams::class);
        $myexam = $repository->findOneBy(['examId' => $exam_id]);
        
            // PICK THE RIGHT VALIDATION ID
        $repository2 = $this->getDoctrine()->getRepository(Validations::class);
        $myvalid = $repository2->findOneBy(['examId' => $exam_id, 'userId' => $id]);
        
            // RESTRICT ACCESS
        if ($myexam->getUserId() != $id && $myvalid->getUserId() != $id)
        {
            return $this->redirectToRoute('error');
        }
        
            // PICK INFORMATION FROM THE EXAM SUBMITTER
        $repository3 = $this->getDoctrine()->getRepository(Users::class);
        $owner = $repository3->findOneBy(['userId' => $myexam->getUserId()]);
        $ownerdptid = $owner->getDptId();
        $ownerdpt = $connection->fetchAll("SELECT dpt_name FROM departments WHERE dpt_id = $ownerdptid");
        
            // ALL VALIDATIONS OF THIS EXAM        
        $validated = $repository2->findBy(['examId' => $exam_id, 'validStatus' => 1]);
        $modified = $repository2->findBy(['examId' => $exam_id, 'validStatus' => 2]);
        
            // USERS FOR NAME IN TWIG FILE       
        $users = $repository3->findAll();
        
        return $this->render('exam_view/index.html.twig', [
            'myexam' => $myexam,
            'validated' => $validated,
            'modified' => $modified,
            'owner' => $owner,
            'ownerdpt' => $ownerdpt,
            'myvalid' => $myvalid,
            'id' => $id,
            'users' => $users
        ]);
    }
}
