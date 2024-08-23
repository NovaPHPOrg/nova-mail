<?php

namespace nova\plugin\mail;


use nova\framework\App;
use nova\framework\log\Logger;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class Mail
{
 
    /**
     * @throws MailException
     */
    static function send($to, $toName, $title, $body): void
    {

        $mail = new PHPMailer(true);

        $config = new MailConfig(App::getInstance()->config()["mail"]);

        try {
            ob_start();
            //Server settings
            $mail->SMTPDebug = App::getInstance()->debug?SMTP::DEBUG_SERVER:SMTP::DEBUG_OFF;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host = $config->host;                     //Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   //Enable SMTP authentication
            $mail->Username = $config->username;                     //SMTP username
            $mail->Password = $config->password;                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port = $config->port;                                  //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom(  $config->username, $config->site);
            $mail->addAddress($to, $toName);

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = $title;
            $mail->Body = $body;

            $mail->send();

            $data = ob_get_clean();

            if(!empty($data)){
               Logger::info($data);
            }

        } catch (Exception $e) {
            throw new MailException($mail->ErrorInfo);
        }
    }

}