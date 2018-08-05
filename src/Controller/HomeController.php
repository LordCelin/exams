<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\DBAL\Driver\Connection;

class HomeController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function homepage(Connection $connection)
    {
        
            // PICK INFORMATION FROM CONNECTED USER        
        $currentuser = $this->getUser();
        $id = $currentuser->getUserId();
        
            // MY EXAMS TO SUBMIT        
        $myexams = $connection->fetchAll("SELECT * FROM exams WHERE exam_status = 0 AND user_id = $id");
        
            // MY EXAMS TO VET        
        $examstovet = $connection->fetchAll("SELECT * FROM exams WHERE exam_id IN "
                . "(SELECT exam_id FROM validations WHERE user_id = $id AND valid_status = 0)");
                
            // MY SUBMISSIONS        
        $myoldsub = $connection->fetchAll("SELECT * FROM exams WHERE exam_status > 0 AND user_id = $id");
                
            // WHAT I VALIDATED        
        $myvetted = $connection->fetchAll("SELECT * FROM exams WHERE exam_id IN "
                . "(SELECT exam_id FROM validations WHERE user_id = $id AND valid_status = 1)");
                
            // WHAT I DIDN'T VALIDATE        
        $mynonvetted = $connection->fetchAll("SELECT * FROM exams WHERE exam_id IN "
                . "(SELECT exam_id FROM validations WHERE user_id = $id AND valid_status = 2)");
        
                // NUMBER OF VALIDATIONS LEFT
        $totalvalidations = $connection->fetchAll("SELECT COUNT(*) AS nb FROM validations WHERE exam_id IN "
                . "(SELECT exam_id FROM exams WHERE user_id = $id)");
        $totalvetted = $connection->fetchAll("SELECT COUNT(*) AS nb FROM validations WHERE valid_status = 1 AND exam_id IN "
                . "(SELECT exam_id FROM exams WHERE user_id = $id)");
        $totalnonvetted = $connection->fetchAll("SELECT COUNT(*) AS nb FROM validations WHERE valid_status = 2 AND exam_id IN "
                . "(SELECT exam_id FROM exams WHERE user_id = $id)");
        
        return $this->render('home/index.html.twig', [
            'myexams' => $myexams,
            'examstovet' => $examstovet,
            'myoldsub' => $myoldsub,
            'myvetted' => $myvetted,
            'mynonvetted' => $mynonvetted,
            'totalvalidations' => $totalvalidations,
            'totalvetted' => $totalvetted,
            'totalnonvetted' => $totalnonvetted,
        ]);
    }
}
