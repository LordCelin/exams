<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\DBAL\Driver\Connection;
use App\Entity\Users;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class HomeController extends Controller
{
    /**
     * @Route("/home", name="home")
     * 
     * @Security("has_role('ROLE_USER')")
     */
    public function homepage(Connection $connection)
    {
            // PICK INFORMATION FROM CONNECTED USER        
        $currentuser = $this->getUser();
        $id = $currentuser->getUserId();
        
            // RESTRICT ACCESS
        if ($currentuser->getSecretaryMember() != 0)
        {
            return $this->redirectToRoute('error');
        }
        
            // FIND USERS TO USE THEIT NAMES IN TWIG FILE
        $repository = $this->getDoctrine()->getRepository(Users::class);
        $users = $repository->findAll();
        
            // MY EXAMS TO SUBMIT        
        $myexams = $connection->fetchAll("SELECT * FROM exams WHERE exam_status = 0 AND user_id = $id");
        
            // MY EXAMS TO VET        
        $examstovet = $connection->fetchAll("SELECT * FROM exams WHERE exam_id IN "
                . "(SELECT exam_id FROM validations WHERE user_id = $id AND valid_status = 0)");
                
            // MY SUBMISSIONS        
        $myoldsub = $connection->fetchAll("SELECT * FROM exams WHERE exam_status > 0 AND user_id = $id");
                
            // WHAT I ALREADY VALIDATED OR MODIFIED
        $myvets = $connection->fetchAll("SELECT * FROM exams WHERE exam_id IN "
                . "(SELECT exam_id FROM validations WHERE user_id = $id AND valid_status > 0)");
        
            // NUMBER OF VALIDATIONS LEFT
            // TOTAL OF VALIDATIONS LINE
        $totval = $connection->fetchAll("SELECT v.exam_id, (SELECT COUNT(*) "
                   . "FROM validations t "
                   . "WHERE valid_status < 3 AND t.exam_id = v.exam_id) AS nb "
                . "FROM validations v "
                . "JOIN exams e ON e.exam_id = v.exam_id "
                . "WHERE e.user_id = $id "
                . "GROUP BY v.exam_id");
        
            // CREATE AN ARRAY WITH key=exam_id & value=nb
        $totalvalidations = [];
        foreach($totval as $line){
        $totalvalidations[$line['exam_id']] = $line['nb'];
        }
        
            // TOTAL OF APPROVES
        $totvet = $connection->fetchAll("SELECT v.exam_id, (SELECT COUNT(*) "
                   . "FROM validations t "
                   . "WHERE valid_status = 1 AND t.exam_id = v.exam_id) AS nb "
                . "FROM validations v "
                . "JOIN exams e ON e.exam_id = v.exam_id "
                . "WHERE e.user_id = $id "
                . "GROUP BY v.exam_id");
        
            // CREATE AN ARRAY WITH key=exam_id & value=nb
        $totalvetted = [];
        foreach($totvet as $line){
        $totalvetted[$line['exam_id']] = $line['nb'];
        }
        
            // TOTAL OF NON APPROVED
        $totnonvet = $connection->fetchAll("SELECT v.exam_id, (SELECT COUNT(*) "
                   . "FROM validations t "
                   . "WHERE valid_status = 2 AND t.exam_id = v.exam_id) AS nb "
                . "FROM validations v "
                . "JOIN exams e ON e.exam_id = v.exam_id "
                . "WHERE e.user_id = $id "
                . "GROUP BY v.exam_id");
        
            // CREATE AN ARRAY WITH key=exam_id & value=nb
        $totalnonvetted = [];
        foreach($totnonvet as $line){
        $totalnonvetted[$line['exam_id']] = $line['nb'];
        }
        
        return $this->render('home/index.html.twig', [
            'myexams' => $myexams,
            'examstovet' => $examstovet,
            'myoldsub' => $myoldsub,
            'myvets' => $myvets,
            'totalvalidations' => $totalvalidations,
            'totalvetted' => $totalvetted,
            'totalnonvetted' => $totalnonvetted,
            'currentuser' => $currentuser,
            'users' => $users,
        ]);
    }
}
