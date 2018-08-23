<?php

namespace App\Utils;

class Mail
{

    public function mailNewUser($user)
    {
            // MESSAGE
        $msg = "Hello ".$user->getFirstname()." ".$user->getName()."\n"
                . "\n You are now a user of the exam management tool of the University. \n"
                . "You can connect with email adress. \n"
                . "Your password is 123, you can change it in Parameters on the homepage once you are connected.\n
                    \n Have a Good day!";
            // SEND
//        mail($user->getMail(), "Welcome on the Exam Management Tool", $msg);
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
    
        public function mailResetPsswrd($user)
    {
            // MESSAGE
        $msg = "Hello ".$user->getFirstname()." ".$user->getName()."\n"
                . "\n Your password has been reset. \n"
                . "You can connect with your email adress and your password is 123. \n"
                . "You can change it in Parameters on the homepage once you are connected.\n
                    \n Have a Good day!";
            // SEND
//        mail($user->getMail(), "Your password has been reset", $msg);
    }
}