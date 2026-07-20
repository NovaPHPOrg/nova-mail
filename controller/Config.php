<?php

declare(strict_types=1);

namespace nova\plugin\mail\controller;

use nova\framework\http\Response;
use nova\plugin\login\controller\BaseAPIController;
use nova\plugin\mail\Mail;
use nova\plugin\mail\MailConfig;
use nova\plugin\mail\MailException;
use nova\plugin\mail\MailTpl;

class Config extends BaseAPIController
{
    public function config(): Response
    {
        $mailConfig = new MailConfig();

        if ($this->request->isGet()) {
            return Response::asJson([
                'code' => 200,
                'data' => get_object_vars($mailConfig),
            ]);
        }

        $mailConfig->host = $this->request->post('host', $mailConfig->host);
        $mailConfig->port = (int)$this->request->post('port', $mailConfig->port);
        $mailConfig->username = $this->request->post('username', $mailConfig->username);
        $mailConfig->password = $this->request->post('password', $mailConfig->password);
        $mailConfig->site = $this->request->post('site', $mailConfig->site);
        $mailConfig->defaultRecipient = $this->request->post('defaultRecipient', $mailConfig->defaultRecipient);

        return Response::asJson([
            'code' => 200,
            'msg' => '邮件配置保存成功',
        ]);
    }

    public function test(): Response
    {
        try {
            $config = new MailConfig();
            $recipient = !empty($config->defaultRecipient) ? $config->defaultRecipient : $config->username;

            $mailTpl = new MailTpl();
            $logo = 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>');
            $content = '<h2>邮件测试</h2><p>这是一封测试邮件，用于验证邮件配置是否正确。</p><p>如果您收到了这封邮件，说明邮件配置已经正确设置。</p>';
            $htmlBody = $mailTpl->notice($logo, $content);

            Mail::send($recipient, '', '邮件测试 - ' . ($config->site ?: '系统'), $htmlBody);

            return Response::asJson([
                'code' => 200,
                'msg' => '测试邮件发送成功',
            ]);
        } catch (MailException $e) {
            return Response::asJson([
                'code' => 500,
                'msg' => '邮件配置错误: ' . $e->getMessage(),
            ]);
        }
    }
}
