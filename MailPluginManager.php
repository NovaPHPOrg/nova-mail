<?php

declare(strict_types=1);

namespace nova\plugin\mail;

use nova\framework\core\StaticRegister;
use nova\framework\route\RouteTrait;
use nova\plugin\login\AdminPage;
use nova\plugin\login\route\Permission;

/**
 * 邮件插件启动器（对齐 WebdavManager）。
 */
class MailPluginManager extends StaticRegister
{
    use RouteTrait;

    public function __construct()
    {
        $this->controllerNamespace = 'nova\\plugin\\mail\\controller\\';
        $this->registerRoutes();
    }

    private function registerRoutes(): void
    {
        $this->getOrPost('/mail/api/config', $this->map('config', 'config'));
        $this->post('/mail/api/test', $this->map('config', 'test'));
    }

    public static function registerInfo(): void
    {
        Permission::getInstance()->registerPermissions('邮件配置', 'mail_manage', [
            'GET /mail/config',
            'ANY /mail/api*',
        ]);

        self::getInstance()->bindPrefixDispatch('/mail');
        AdminPage::bind(MailAdminTpl::getInstance());
    }
}
