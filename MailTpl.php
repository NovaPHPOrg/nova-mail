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

use nova\plugin\tpl\ViewException;
use nova\plugin\tpl\ViewResponse;

class MailTpl
{
    private ViewResponse $viewResponse;
    public function __construct()
    {
        $this->viewResponse = new ViewResponse();
        $this->viewResponse->init("", [], "{", "}", ROOT_PATH."/nova/plugin/mail/tpl");
    }

    /**
     * @throws ViewException
     */
    public function notice($site, $logo, $content): string
    {
        return  $this->viewResponse->asTpl("notice", [
            "site" => $site,
            "logo" => $logo,
            "content" => $content,
        ])->getData();
    }
}
