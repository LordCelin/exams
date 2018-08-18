<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\DBAL\Driver\Connection;
use App\Entity\Users;

class InProgressController extends Controller
{
    /**
     * @Route("/in/progress", name="in_progress")
     */
    public function index(Connection $connection)
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
        
            // FIND USERS TO USE THEIT NAMES IN TWIG FILE
        $repository = $this->getDoctrine()->getRepository(Users::class);
        $users = $repository->findAll();
                
            // ASKED EXAMS    
        $askedexams = $connection->fetchAll("SELECT * FROM exams WHERE exam_status = 0 AND user_id IN "
                . "(SELECT user_id FROM users WHERE dpt_id = $dpt)");
        
            // SUBMITTED EXAMS    
        $submit = $connection->fetchAll("SELECT * FROM exams WHERE exam_status = 1 AND user_id IN "
                . "(SELECT user_id FROM users WHERE dpt_id = $dpt)");
        
            // FIRST VALID    
        $first = $connection->fetchAll("SELECT * FROM exams WHERE exam_status = 2 AND user_id IN "
                . "(SELECT user_id FROM users WHERE dpt_id = $dpt)");
        
            // SECOND VALID    
        $sec = $connection->fetchAll("SELECT * FROM exams WHERE exam_status = 3 AND user_id IN "
                . "(SELECT user_id FROM users WHERE dpt_id = $dpt)");
        
        return $this->render('in_progress/index.html.twig', [
            'askedexams' => $askedexams,
            'submit' => $submit,
            'first' => $first,
            'sec' => $sec,
            'users' => $users,
        ]);
       
    }
}
