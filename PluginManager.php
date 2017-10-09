<?php

/*
 * Copyright(c) 2015 GMO Payment Gateway, Inc. All rights reserved.
 * http://www.gmo-pg.com/
 */

namespace Plugin\KaiUConnection;

use Eccube\Application;
use Eccube\Plugin\AbstractPluginManager;

class PluginManager extends AbstractPluginManager
{
    /**
     * @param $config
     * @param $app
     */
    public function install($config, $app)
    {
    }

    /**
     * @param $config
     * @param $app
     */
    public function uninstall($config, $app)
    {
        $this->migrationSchema($app, __DIR__ . '/Resource/doctrine/migration', $config['code'], 0);
    }

    /**
     * Handle event enable plugin
     *
     * @param array       $config
     * @param Application $app
     */
    public function enable($config, $app)
    {
        $this->migrationSchema($app, __DIR__ . '/Resource/doctrine/migration', $config['code']);
    }
    
    /**
     * Handle event disable plugin
     *
     * @param array       $config
     * @param Application $app
     */
    public function disable($config, $app)
    {
    }

    /**
     * @param array       $config
     * @param Application $app
     */
    public function update($config, $app)
    {
        $this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code']);
    }
}
