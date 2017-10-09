<?php
namespace Plugin\KaiUConnection\ServiceProvider;

use Silex\Application as BaseApplication;
use Silex\ServiceProviderInterface;

class ConnectionServiceProvider implements ServiceProviderInterface
{
    public function register(BaseApplication $app)
    {
        // Setting
        $app->match('/' . $app["config"]["admin_route"] . '/plugin/connect/config', '\\Plugin\\KaiUConnection\\Controller\\ConfigController::index')->bind('plugin_KaiUConnection_config');

        $app->match('/block/kaiu_tag_block', '\Plugin\KaiUConnection\Controller\Block\TagController::index')
            ->bind('block_kaiu_tag_block');

        $app['kaiu.repository.tag'] = $app->share(function () use ($app) {
            return $app['orm.em']->getRepository('Plugin\KaiUConnection\Entity\Tag');
        });

        $app['kaiu.service.api'] = $app->share(function () use ($app) {
            return new \Plugin\KaiUConnection\Service\ApiService($app);
        });

        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new \Plugin\KaiUConnection\Form\Type\TagType($app);
            $types[] = new \Plugin\KaiUConnection\Form\Type\ConfigType($app);

            return $types;
        }));

        $file = __DIR__.'/../Resource/locale/message.'.$app['locale'].'.yml';
        if (file_exists($file)) {
            $app['translator']->addResource('yaml', $file, $app['locale']);
        }

        $app['config'] = $app->share($app->extend('config', function ($config) {
            $addNavi['id'] = "kaiu_connect";
            $addNavi['name'] = "Tags";
            $addNavi['url'] = "plugin_KaiUConnection_config";

            $nav = $config['nav'];
            foreach ($nav as $key => $val) {
                if ("content" == $val["id"]) {
                    $nav[$key]['child'][] = $addNavi;
                }
            }

            $config['nav'] = $nav;
            return $config;
        }));
    }

    public function boot(BaseApplication $app)
    {
    }
}
