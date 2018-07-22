<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\DBAL\Driver\Connection;

class HomeController extends Controller
{
    /**
     * @Route("/home", name="home")
     */
    public function index(Connection $connection)
    {
        $myexams = $connection->fetchAll('SELECT * FROM exams '
               // . 'WHERE user_id = user_id AND exam_status = 0'
               );
        
     //   $examstovet = $connection->fetchAll('SELECT * FROM exams e '
     //           . 'JOIN subjectbyusers sbu ON sbu.user_id = e.user_id '
       //         . 'WHERE user_id IN (SELECT )');
        
        return $this->render('home/index.html.twig', [
            'myexams' => $myexams,
        ]);
    }
}
