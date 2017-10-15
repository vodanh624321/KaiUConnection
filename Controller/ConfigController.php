<?php

namespace Plugin\KaiUConnection\Controller;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Plugin\KaiUConnection\Entity\ConfigPlugin;
use Plugin\KaiUConnection\Entity\Config;
use Plugin\KaiUConnection\Form\Type\ConfigType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Controller to handle module setting screen
 */
class ConfigController extends AbstractController
{
    /**
     * Edit config
     *
     * @param Application $app
     * @param Request $request
     * @return Response
     */
    public function index(Application $app, Request $request)
    {
        /**
         * @var ConfigRepository $repo
         */
        $repo = $app['kaiu.repository.config'];
        $config = $repo->find(1);
        $this->setDefault($app, $config);

        $token = $config->getToken();
        $sites = array();
        if (!empty($token)) {
            $api  = $app['kaiu.service.api'];
            $sites = $api->getSiteList($token);
        }

        /* @var $form FormInterface */
        $form = $app['form.factory']
            ->createBuilder('config', $config)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $app['orm.em']->persist($data);
            $app['orm.em']->flush($data);
            $url = $data->getUrl();
            if (!empty($url)) {
                $status = $app['kaiu.service.api']->registerSite($data);
                if (!$status) {
                    throw new \Exception("Site Url exist in this user! If you want to connect to your account, leave the url empty.");
                }
                $app->addSuccess('plugin.connect.add.success', 'admin');
            } else {
                $app->addSuccess('plugin.connect.success', 'admin');
            }

            return $app->redirect($app->url('plugin_KaiUConnection_config'));
        }

        return $app->render('KaiUConnection/Resource/template/admin/config.twig', 
            array(
                'form' => $form->createView(),
                'sites' => $sites
                ));
    }

    /**
     * get Tag
     *
     * @param Application $app
     * @param Request $request
     * @return Response
     */
    public function getTag(Application $app, Request $request, $id)
    {
        /**
         * @var TagRepository $repo
         */
        $repo = $app['kaiu.repository.config'];
        $config = $repo->find(1);
        if (!$config) {
            throw new NotFoundHttpException();
        }

        $tagStatus = $app['kaiu.service.api']->getSiteTag($config, $id);
        if (!$tagStatus) {
            throw new \Exception("Cannot found site id");
        }

        $app->addSuccess('plugin.connect.get_tag.success', 'admin');

        return $app->redirect($app->url('plugin_KaiUConnection_config'));
    }

    /**
     * remove tag
     *
     * @param Application $app
     * @param Request $request
     * @return Response
     */
    public function deleteTag(Application $app, Request $request, $id)
    {
        $this->isTokenValid($app);
        /**
         * @var TagRepository $repo
         */
        $repo = $app['kaiu.repository.tag'];

        $tag = null;
        if ($id) {
            $tag = $repo->find($id);
        }

        if (!$tag) {
            throw new NotFoundHttpException();
        }

        $app['kaiu.repository.tag']->delete($tag);

        $app->addSuccess('plugin.connect.delete.success', 'admin');

        return $app->redirect($app->url('plugin_KaiUConnection_config'));
    }

    private function setDefault($app, &$config)
    {
        if (!$config) {
            $config = new Config();
            $config->setId(1);
            $BaseInfo = $app['eccube.repository.base_info']->get();
            $config->setEmail($BaseInfo->getEmail01());
            $config->setName($BaseInfo->getShopName());
            if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
                $url = 'http://'.$_SERVER['HTTP_HOST'];
                $config->setUrl($url);
            }
        }
    }
}
