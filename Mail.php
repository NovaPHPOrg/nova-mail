<?php

declare(strict_types=1);

/*
 * Copyright (c) 2025. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
 * Morbi non lorem porttitor neque feugiat blandit. Ut vitae ipsum eget quam lacinia accumsan.
 * Etiam sed turpis ac ipsum condimentum fringilla. Maecenas magna.
 * Proin dapibus sapien vel ante. Aliquam erat volutpat. Pellentesque sagittis ligula eget metus.
 * Vestibulum commodo. Ut rhoncus gravida arcu.
 */

namespace nova\plugin\mail;

use Exception;
use nova\framework\core\Context;
use nova\framework\core\Logger;
use nova\plugin\mail\phpmail\PHPMailer;
use nova\plugin\mail\phpmail\SMTP;

class Mail
{
    /**
     * @throws MailException
     */
    public static function send($to, $toName, $title, $body): void
    {

        $mail = new PHPMailer(true);

        $config = new MailConfig();

        try {
            ob_start();
            //Server settings
            $mail->SMTPDebug = Context::instance()->isDebug() ? SMTP::DEBUG_SERVER : SMTP::DEBUG_OFF;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host = $config->host;                     //Set the SMTP server to send through
            $mail->SMTPAuth = true;                                   //Enable SMTP authentication
            $mail->Username = $config->username;                     //SMTP username
            $mail->Password = $config->password;                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port = $config->port;                                  //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
            $mail->CharSet = "UTF-8";
            //Recipients
            $mail->setFrom($config->username, $config->site);
            $mail->addAddress($to, $toName);

            //Content
            $mail->isHTML();                                  //Set email format to HTML
            $mail->Subject = $title;
            $mail->Body = $body;

            $mail->send();

            $data = ob_get_clean();

            if (!empty($data)) {
                Logger::info($data);
            }

        } catch (Exception $e) {
            throw new MailException($mail->ErrorInfo);
        }
    }

}
