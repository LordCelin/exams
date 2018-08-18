<?php

namespace App\Utils;

class Mail
{

    public function mailNewUser($user)
    {
            // MESSAGE
        $msg = "Hello ".$user['firstname']." ".$user['name']."\n"
                . "\n You are now a user of the exam management tool of the University. \n"
                . "Your username is ".$user['username'].", you can connect with it or your email adress. \n"
                . "Your password is 123, you can change it in Parameters on the homepage once you are connected.\n
                    \n Have a Good day!";
            // SEND
//        mail($user['mail'], "Welcome on the Exam Management Tool", $msg);
    }
    
        public function mailNewTask($user)
    {
            // MESSAGE
        $msg = "Hello ".$user['firstname']." ".$user['name']."\n"
                . "\n New task affected. \n"
                . "You have to vet a new exam. \n"
                . ".\n Go to your homepage
                    \n Have a Good day!";
            // SEND
//        mail($user['mail'], "New task from the Exam Management Tool!", $msg);
    }
}