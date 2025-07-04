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

class MailConfig extends ConfigObject
{
    public string $host = "";
    public string $username = "";
    public string $password = "";
    public int $port = 0;
    public string $site = "";

    /**
     * @throws MailException
     */
    public function onValidate(): void
    {
        if (!preg_match("/^smtp\..*$/", $this->host)) {
            throw new MailException("smtp服务器填写有误：".$this->host);
        }
        if (!filter_var($this->username, FILTER_VALIDATE_EMAIL)) {
            throw new MailException("发件人邮箱错误");
        }
        if (empty($this->password)) {
            throw new MailException("密码不允许为空");
        }
        if (!preg_match("/^(?:6553[0-5]|655[0-2]\d|65[0-4]\d{2}|6[0-4]\d{3}|[1-5]\d{4}|[1-9]\d{0,3}|[1-9])$/", (string)$this->port)) {
            throw new MailException("端口范围错误");
        }
    }

}
