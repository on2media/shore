<?php

class EmailMethod
{
    private function __construct()
    {
       
    }
   
    public static function &getInstance()
    {
        static $transport;
       
        if (!is_object($transport)) {
           
            if (IS_LIVE == FALSE) {
               
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
