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
        
            // PICK INFORMATION FROM CONNECTED USER        
        $currentuser = $this->getUser();
        $id = $currentuser->getUserId();
        $dpt = $currentuser->getDptId();
        
            // PICK THE RIGHT EXAM        
        $repository = $this->getDoctrine()->getRepository(Exams::class);
        $myexam = $repository->findOneBy(['examId' => $exam_id]);
        
            // PICK THE RIGHT VALIDATION ID
        $repository2 = $this->getDoctrine()->getRepository(Validations::class);
        $valid_id_line = $repository2->findOneBy(['examId' => $exam_id, 'userId' => $id]);
        $valid_id = $valid_id_line->getValidId();
        //$valid_id = $connection->fetchAll("SELECT valid_id FROM validations WHERE exam_id = $exam_id AND user_id = $id");
        
            // PICK INFORMATION FROM THE EXAM SUBMITTER
        $repository3 = $this->getDoctrine()->getRepository(Users::class);
        $owner = $repository3->findOneBy(['userId' => $myexam->getUserId()]);
        $ownerdptid = $owner->getDptId();
        $ownerdpt = $connection->fetchAll("SELECT dpt_name FROM departments WHERE dpt_id = $ownerdptid");
        
            // ALL VALIDATIONS OF THIS EXAM        
        $allvalidations = $connection->fetchAll("SELECT * FROM validations WHERE exam_id = $exam_id AND valid_status > 0");
        
        return $this->render('exam_view/index.html.twig', [
            'myexam' => $myexam,
            'allvalidations' => $allvalidations,
            'owner' => $owner,
            'ownerdpt' => $ownerdpt,
            'valid_id' => $valid_id
        ]);
    }
}
