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

use app\Application;

use function nova\framework\config;

use nova\plugin\tpl\ViewException;
use nova\plugin\tpl\ViewResponse;

/**
 * 邮件模板处理类
 *
 * 用于生成邮件内容的模板类，提供邮件模板的渲染功能。
 * 支持自定义模板变量，生成格式化的邮件内容。
 *
 * @package nova\plugin\mail
 * @author Nova Framework
 * @since 1.0.0
 */
class MailTpl
{
    /**
     * 视图响应对象
     *
     * @var ViewResponse
     */
    private ViewResponse $viewResponse;

    /**
     * 构造函数
     *
     * 初始化邮件模板处理器，设置模板引擎的配置参数。
     * 包括模板路径、变量分隔符等基础配置。
     */
    public function __construct()
    {
        $this->viewResponse = new ViewResponse();
        $this->viewResponse->init("", [], "{", "}", ROOT_PATH."/nova/plugin/mail/tpl");
    }

    /**
     * 生成通知邮件模板
     *
     * 根据提供的参数生成格式化的通知邮件内容。
     * 模板包含站点信息、Logo图片和主要内容。
     *
     * @param  string        $logo    Logo图片的URL或路径
     * @param  string        $content 邮件的主要内容
     * @return string        渲染后的邮件HTML内容
     * @throws ViewException 当模板渲染失败时抛出异常
     */
    public function notice($logo, $content): string
    {
        return  $this->viewResponse->asTpl("notice", [
            "site" => config("mail.site") ?? Application::SYSTEM_NAME,
            "logo" => $logo,
            "content" => $content,
        ])->getData();
    }
}
