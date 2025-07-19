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

use nova\framework\core\ConfigObject;

/**
 * 邮件配置类
 *
 * 用于管理SMTP邮件服务器的配置信息，包括服务器地址、认证信息、端口等。
 * 继承自ConfigObject，提供配置验证功能。
 *
 * @package nova\plugin\mail
 * @author Nova Framework
 * @since 1.0.0
 */
class MailConfig extends ConfigObject
{
    /**
     * SMTP服务器地址
     *
     * 格式应为：smtp.example.com
     *
     * @var string
     */
    public string $host = "";

    /**
     * SMTP用户名/发件人邮箱地址
     *
     * 必须是有效的邮箱格式
     *
     * @var string
     */
    public string $username = "";

    /**
     * SMTP密码
     *
     * 用于SMTP服务器认证的密码
     *
     * @var string
     */
    public string $password = "";

    /**
     * SMTP端口号
     *
     * 常用端口：25(非加密)、465(SSL)、587(TLS)
     * 范围：1-65535
     *
     * @var int
     */
    public int $port = 0;

    /**
     * 网站地址
     *
     * 用于邮件中的链接等
     *
     * @var string
     */
    public string $site = "";

    /**
     * 默认邮件接收人
     *
     * 可选配置，用于系统通知等场景
     * 如果填写，必须是有效的邮箱格式
     *
     * @var string
     */
    public string $defaultRecipient = "";

    /**
     * 验证配置参数
     *
     * 检查所有必需的配置项是否有效：
     * - 验证SMTP服务器地址格式
     * - 验证发件人邮箱格式
     * - 检查密码是否为空
     * - 验证端口号范围
     * - 验证默认接收人邮箱格式（如果填写）
     *
     * @throws MailException 当配置验证失败时抛出异常
     * @return void
     */
    public function onValidate(): void
    {
        // 验证SMTP服务器地址格式
        if (!preg_match("/^smtp\..*$/", $this->host)) {
            throw new MailException("smtp服务器填写有误：".$this->host);
        }

        // 验证发件人邮箱格式
        if (!filter_var($this->username, FILTER_VALIDATE_EMAIL)) {
            throw new MailException("发件人邮箱错误");
        }

        // 检查密码是否为空
        if (empty($this->password)) {
            throw new MailException("密码不允许为空");
        }

        // 验证端口号范围（1-65535）
        if (!preg_match("/^(?:6553[0-5]|655[0-2]\d|65[0-4]\d{2}|6[0-4]\d{3}|[1-5]\d{4}|[1-9]\d{0,3}|[1-9])$/", (string)$this->port)) {
            throw new MailException("端口范围错误");
        }

        // 验证默认邮件接收人格式（如果填写了的话）
        if (!empty($this->defaultRecipient) && !filter_var($this->defaultRecipient, FILTER_VALIDATE_EMAIL)) {
            throw new MailException("默认邮件接收人格式错误");
        }
    }

}
