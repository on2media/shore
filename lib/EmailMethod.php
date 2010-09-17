<?php

class emailMethod
{
    private function __construct()
    {
       
    }
   
    public static function &getInstance()
    {
        static $transport;
       
        if (!is_object($transport)) {
           
            if ($_SERVER["SERVER_NAME"] == "localhost") {
               
                $transport = Swift_SmtpTransport::newInstance("smtp.gmail.com", 465, 'tls')
                    ->setUsername("***")
                    ->setPassword("***")
                ;
               
            } else {
               
                $transport = Swift_MailTransport::newInstance();
               
            }
           
        }
       
        return $transport;
    }
}
