<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Doctrine\DBAL\Driver\Connection;

class UserController extends Controller
{
    /**
     * @Route("/user", name="user")
     */
    public function index(Connection $connection)
    {
        $users = $connection->fetchAll('SELECT * FROM users');
        
        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }
}
