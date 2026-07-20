<?php

declare(strict_types=1);

namespace nova\plugin\mail;

use nova\framework\core\Instance;
use nova\framework\http\Request;
use nova\framework\http\Response;

use function nova\framework\route;

use nova\framework\route\Route;
use nova\plugin\login\AdminPageInterface;
use nova\plugin\tpl\ViewResponse;

/**
 * 邮件配置后台页面（AdminPage）。
 * 与邮件正文模板类 {@see MailTpl} 分离，避免命名冲突。
 */
class MailAdminTpl extends Instance implements AdminPageInterface
{
    public function registerRouter(string $model, string $controller): void
    {
        $default = route($model, $controller, 'init');
        Route::getInstance()
            ->get('/mail/config', $default);
    }

    public function route(ViewResponse $view, Request $request): ?Response
    {
        if ($request->getPath() !== '/mail/config') {
            return null;
        }

        return $view->asTpl(Mail::MAIL_CONFIG_TPL);
    }

    public function menu(): array
    {
        return [
            'title' => '邮件配置',
            'icon' => 'email',
            'url' => '/mail/config',
            'pjax' => true,
        ];
    }
}
