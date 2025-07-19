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

/**
 * 邮件发送类
 *
 * 提供完整的邮件发送功能，包括：
 * - SMTP邮件发送
 * - 邮件配置管理
 * - 测试邮件发送
 * - 邮件模板支持
 *
 * 继承自StaticRegister，支持静态注册和路由处理
 *
 * @author Ankio
 * @version 1.0
 * @since 2025-01-01
 */
class Mail extends StaticRegister
{
    /**
     * 邮件配置模板路径常量
     *
     * @var string
     */
    const string MAIL_CONFIG_TPL = ROOT_PATH . DS . 'nova' . DS . 'plugin' . DS . 'mail' . DS . 'tpl' . DS . 'config';

    /**
     * 注册邮件相关路由和事件监听器
     *
     * 在路由处理前检查用户权限，只有管理员（id=1）可以访问邮件配置功能
     * 注册了两个路由：
     * - /mail/config: 邮件配置管理
     * - /mail/test: 测试邮件发送
     *
     * @return void
     */
    public static function registerInfo(): void
    {
        EventManager::addListener("route.before", function ($event, &$data) {
            // 检查Session插件是否可用
            if (!class_exists('\nova\plugin\cookie\Session')) {
                return;
            }

            // 获取当前登录用户
            if (class_exists('\nova\plugin\login\LoginManager')) {
                $user = \nova\plugin\login\LoginManager::getInstance()->checkLogin();
            } else {
                $user = \nova\plugin\cookie\Session::getInstance()->get("user");
            }

            // 只有管理员（id=1）可以访问邮件配置功能
            if (!$user || $user->id != 1) {
                return;
            }

            // 邮件配置路由处理
            if ($data == "/mail/config") {
                Mail::handleConfig();
            } elseif ($data == "/mail/test") {
                Mail::handleTest();
            }
        });
    }

    /**
     * 处理邮件配置请求
     *
     * 支持GET和POST两种请求方式：
     * - GET: 返回当前邮件配置信息
     * - POST: 更新邮件配置信息
     *
     * @throws AppExitException 当需要返回JSON响应时抛出
     * @return void
     */
    private static function handleConfig(): void
    {
        $mailConfig = new MailConfig();

        if ($_SERVER['REQUEST_METHOD'] == 'GET') {
            // GET请求：返回当前配置
            throw new AppExitException(Response::asJson([
                'code' => 200,
                'data' => get_object_vars($mailConfig),
            ]));
        } else {
            // POST请求：更新配置
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
     *
     * 发送一封测试邮件来验证邮件配置是否正确
     * 使用默认收件人或发件人邮箱作为测试收件人
     *
     * @throws AppExitException 当需要返回JSON响应时抛出
     * @throws ViewException    当模板渲染失败时抛出
     * @return void
     */
    private static function handleTest(): void
    {
        try {
            $config = new MailConfig();
            // 确定测试收件人：优先使用默认收件人，否则使用发件人邮箱
            $recipient = !empty($config->defaultRecipient) ? $config->defaultRecipient : $config->username;

            // 使用MailTpl构建邮件内容
            $mailTpl = new MailTpl();
            // 生成邮件图标SVG的base64编码
            $logo = "data:image/svg+xml;base64," . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>');
            $content = "<h2>邮件测试</h2><p>这是一封测试邮件，用于验证邮件配置是否正确。</p><p>如果您收到了这封邮件，说明邮件配置已经正确设置。</p>";

            // 使用邮件模板生成HTML内容
            $htmlBody = $mailTpl->notice($logo, $content);

            // 发送测试邮件
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
     * 发送邮件
     *
     * 使用PHPMailer库通过SMTP发送HTML格式的邮件
     * 支持UTF-8字符集，使用SMTPS加密连接
     *
     * @param  string        $to     收件人邮箱地址
     * @param  string        $toName 收件人姓名（可选）
     * @param  string        $title  邮件标题
     * @param  string        $body   邮件HTML内容
     * @throws MailException 当邮件发送失败时抛出
     * @return void
     */
    public static function send($to, $toName, $title, $body): void
    {
        // 创建PHPMailer实例，启用异常处理
        $mail = new PHPMailer(true);
        $config = new MailConfig();

        try {
            // 开启输出缓冲，用于捕获调试信息
            ob_start();

            // 服务器设置
            // 根据调试模式设置SMTP调试级别
            $mail->SMTPDebug = Context::instance()->isDebug() ? SMTP::DEBUG_SERVER : SMTP::DEBUG_OFF;
            $mail->isSMTP();                                            // 使用SMTP发送
            $mail->Host = $config->host;                                // 设置SMTP服务器
            $mail->SMTPAuth = true;                                     // 启用SMTP身份验证
            $mail->Username = $config->username;                        // SMTP用户名
            $mail->Password = $config->password;                        // SMTP密码
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;           // 启用隐式TLS加密
            $mail->Port = $config->port;                                // TCP连接端口
            $mail->CharSet = "UTF-8";                                   // 设置字符集为UTF-8

            // 收件人设置
            $mail->setFrom($config->username, $config->site);           // 设置发件人
            $mail->addAddress($to, $toName);                           // 添加收件人

            // 邮件内容设置
            $mail->isHTML();                                            // 设置邮件格式为HTML
            $mail->Subject = $title;                                    // 设置邮件标题
            $mail->Body = $body;                                        // 设置邮件正文

            // 发送邮件
            $mail->send();

            // 获取并清理输出缓冲
            $data = ob_get_clean();

            // 如果有调试信息，记录到日志
            if (!empty($data)) {
                Logger::info($data);
            }

        } catch (Exception $e) {
            // 捕获PHPMailer异常，转换为MailException
            throw new MailException($mail->ErrorInfo);
        }
    }
}
