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

use nova\framework\core\Context;
use nova\framework\core\Logger;
use nova\framework\core\StaticRegister;
use nova\framework\event\EventManager;
use nova\framework\exception\AppExitException;
use nova\framework\http\Response;
use nova\plugin\mail\phpmail\Exception;
use nova\plugin\mail\phpmail\PHPMailer;
use nova\plugin\mail\phpmail\SMTP;
use nova\plugin\tpl\ViewException;

class Mail extends StaticRegister
{
    const string MAIL_CONFIG_TPL = ROOT_PATH . DS . 'nova' . DS . 'plugin' . DS . 'mail' . DS . 'tpl' . DS . 'config';

    public static function registerInfo(): void
    {
        EventManager::addListener("route.before", function ($event, &$data) {
            if (!class_exists('\nova\plugin\cookie\Session')) {
                return;
            }
            if (class_exists('\nova\plugin\login\LoginManager')) {
                $user = \nova\plugin\login\LoginManager::getInstance()->checkLogin();
            } else {
                $user = \nova\plugin\cookie\Session::getInstance()->get("user");
            }
            if (!$user || $user->id != 1) {
                return;
            }
            // 邮件配置
            if ($data == "/mail/config") {
                Mail::handleConfig();
            } elseif ($data == "/mail/test") {
                Mail::handleTest();
            }
        });
    }

    /**
     * 处理邮件配置请求
     * @throws AppExitException
     */
    private static function handleConfig(): void
    {

        $mailConfig = new MailConfig();

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            throw new AppExitException(Response::asJson([
                'code' => 200,
                'data' => get_object_vars($mailConfig),
            ]));
        } else {
            $data = $_POST;
            $mailConfig->host = $data['host'] ?? $mailConfig->host;
            $mailConfig->port = (int)($data['port'] ?? $mailConfig->port);
            $mailConfig->username = $data['username'] ?? $mailConfig->username;
            $mailConfig->password = $data['password'] ?? $mailConfig->password;
            $mailConfig->site = $data['site'] ?? $mailConfig->site;
            $mailConfig->defaultRecipient = $data['defaultRecipient'] ?? $mailConfig->defaultRecipient;
            throw new AppExitException(Response::asJson([
                'code' => 200,
                'msg' => '邮件配置保存成功'
            ]));
        }

    }

    /**
     * 处理测试邮件请求
     * @throws AppExitException|ViewException
     */
    private static function handleTest(): void
    {
        try {
            $config = new MailConfig();
            $recipient = !empty($config->defaultRecipient) ? $config->defaultRecipient : $config->username;

            // 使用MailTpl构建邮件内容
            $mailTpl = new MailTpl();
            $logo = "data:image/svg+xml;base64," . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>');
            $content = "<h2>邮件测试</h2><p>这是一封测试邮件，用于验证邮件配置是否正确。</p><p>如果您收到了这封邮件，说明邮件配置已经正确设置。</p>";

            $htmlBody = $mailTpl->notice($logo, $content);

            // 使用新的send函数发送邮件
            self::send($recipient, '', '邮件测试 - ' . ($config->site ?: '系统'), $htmlBody);

            throw new AppExitException(Response::asJson([
                'code' => 200,
                'msg' => '测试邮件发送成功'
            ]));

        } catch (MailException $e) {
            throw new AppExitException(Response::asJson([
                'code' => 500,
                'msg' => '邮件配置错误: ' . $e->getMessage()
            ]));
        }
    }

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
