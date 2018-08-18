<?php

namespace App\Utils;

use App\Entity\Validations;
use Doctrine\DBAL\Driver\Connection;
use App\Utils\Mail;

class Tasks
{

    public static function newValidations(Connection $connection, $exam_id, $exam_status, $id, $dpt, $entityManager)
    {
        
        
        if($exam_status == 2)
        {
                // CREATE A LINE IN VALIDATION FOR EACH TEACHER OUT OF THE DEPARTMENT
                // PICK TEACHERS EXEPT
            $users = $connection->fetchAll("SELECT * FROM users WHERE dpt_id <> $dpt AND secretary_member = 0");
        }
        
        elseif($exam_status == 1)
        {
                // CREATE A LINE IN VALIDATION FOR EACH TEACHER IN THE DEPARTMENT
                // PICK TEACHERS EXEPT CONNECTED USER
            $users = $connection->fetchAll("SELECT * FROM users WHERE dpt_id = $dpt AND secretary_member = 0 AND user_id <> $id");
        }
            
            // CREATE NEW VALIDATIONS
        foreach($users as $usr)
        {
            $valid = new Validations();
            $valid->setExamId($exam_id);
            $valid->setUserId($usr['user_id']);
                
            $entityManager->persist($valid);
            $entityManager->flush();
                
                // AND SEND MAIL NOTIFICATION
            Mail::mailNewTask($usr);
        }
    }
}
