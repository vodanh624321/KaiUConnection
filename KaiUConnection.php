<?php

/*
 * Copyright(c) 2015 GMO Payment Gateway, Inc. All rights reserved.
 * http://www.gmo-pg.com/
 */

namespace Plugin\KaiUConnection;

use Eccube\Event\TemplateEvent;

class KaiUConnection {

    private $app;

    public function __construct($app) {
        $this->app = $app;
    }

    public function onChangeMetaTag(TemplateEvent $event)
    {
        dump($event);
        die;
    }
}
