<?php

declare(strict_types=1);

namespace nova\plugin\mail;

use nova\framework\core\Context;
use nova\framework\core\Logger;
use nova\plugin\mail\phpmail\Exception;
use nova\plugin\mail\phpmail\PHPMailer;
use nova\plugin\mail\phpmail\SMTP;

/**
 * 邮件发送工具。
 *
 * 后台配置页由 NotifyTpl 挂载；API 由 MailPluginManager 注册。
 */
class Mail
{
    const string MAIL_CONFIG_TPL = ROOT_PATH . DS . 'nova' . DS . 'plugin' . DS . 'mail' . DS . 'tpl' . DS . 'config';

    /**
     * 发送邮件
     *
     * @param  string        $to     收件人邮箱地址
     * @param  string        $toName 收件人姓名（可选）
     * @param  string        $title  邮件标题
     * @param  string        $body   邮件HTML内容
     * @throws MailException 当邮件发送失败时抛出
     */
    public static function send($to, $toName, $title, $body): void
    {
        $mail = new PHPMailer(true);
        $config = new MailConfig();

        try {
            ob_start();

            $mail->SMTPDebug = Context::instance()->isDebug() ? SMTP::DEBUG_SERVER : SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host = $config->host;
            $mail->SMTPAuth = true;
            $mail->Username = $config->username;
            $mail->Password = $config->password;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $config->port;
            $mail->CharSet = 'UTF-8';

            $mail->setFrom($config->username, $config->site);
            $mail->addAddress($to, $toName);

            $mail->isHTML();
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
