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
    public function index(Connection $connection)
    {
        $myexams = $connection->fetchAll('SELECT * FROM exams WHERE exam_status = 0'
               // . 'WHERE user_id = user_id AND exam_status = 0'
               );
        
     //   $examstovet = $connection->fetchAll('SELECT * FROM exams e '
     //           . 'JOIN subjectbyusers sbu ON sbu.user_id = e.user_id '
       //         . 'WHERE user_id IN (SELECT )');
        
        $myoldsub = $connection->fetchAll('SELECT * FROM exams WHERE exam_status = 1');
        
        return $this->render('home/index.html.twig', [
            'myexams' => $myexams,
            'myoldsub' => $myoldsub,
        ]);
    }
    
//    <ul>
//    {% for user in users if user.active %}
//        <li>{{ user.username }}</li>
//    {% else %}
//        <li>No users found</li>
//    {% endfor %}
//    </ul>
}
