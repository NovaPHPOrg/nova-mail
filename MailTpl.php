<?php

namespace nova\plugin\mail;

use nova\plugin\tpl\ViewException;
use nova\plugin\tpl\ViewResponse;

class MailTpl
{
    private ViewResponse $viewResponse;
    public function __construct()
    {
        $this->viewResponse = new ViewResponse();
        $this->viewResponse->init("",[],false,"{","}",ROOT_PATH."/nova/plugin/mail/tpl");
    }

    /**
     * @throws ViewException
     */
    function notice($site, $logo, $content):string{
      return  $this->viewResponse->asTpl("notice",false,[
            "site" => $site,
            "logo" => $logo,
            "content" => $content,
        ])->getData();
    }
}