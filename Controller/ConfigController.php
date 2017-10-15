<?php

namespace Plugin\KaiUConnection\Controller;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Plugin\KaiUConnection\Entity\ConfigPlugin;
use Plugin\KaiUConnection\Entity\Config;
use Plugin\KaiUConnection\Entity\Tag;
use Plugin\KaiUConnection\Form\Type\ConfigType;
use Plugin\KaiUConnection\Repository\TagRepository;
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
         * @var TagRepository $repo
         */
        $repo = $app['kaiu.repository.tag'];
        $tags = $repo->findBy(array(), array('site_id' => 'DESC'));

        $tag = new Tag();

        /**
         * @var ConfigRepository $repo
         */
        $repo = $app['kaiu.repository.config'];
        $config = $repo->find(1);
        $this->setDefaultTag($app, $tag, $config);

        /* @var $form FormInterface */
        $form = $app['form.factory']
            ->createBuilder('config', $tag)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tag = $form->getData();
            $app['kaiu.repository.tag']->save($tag);

            // Update config
            $this->updateConfig($app, $config, $tag);

            $app['kaiu.service.api']->registerSite($tag);
            $tagStatus = $app['kaiu.service.api']->getSiteId($tag);
            if (!$tagStatus) {
                throw new \Exception("Cannot found site id");
            }

            $app->addSuccess('plugin.connect.add.success', 'admin');
            return $app->redirect($app->url('plugin_KaiUConnection_config'));
        }

        return $app->render('KaiUConnection/Resource/template/admin/config.twig', 
            array(
                'form' => $form->createView(),
                'tags' => $tags
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
        $repo = $app['kaiu.repository.tag'];

        $tag = null;
        if ($id) {
            $tag = $repo->find($id);
        }

        if (!$tag) {
            throw new NotFoundHttpException();
        }

        $tagStatus = $app['kaiu.service.api']->getSiteTag($tag);
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

    private function setDefaultTag($app, &$tag, $config)
    {
        if (!$config) {
            $config = new Config();
            $BaseInfo = $app['eccube.repository.base_info']->get();
            $config->setEmail($BaseInfo->getEmail01());
            $config->setName($BaseInfo->getShopName());
            if (isset($_SERVER['HTTP_HOST']) && !empty($_SERVER['HTTP_HOST'])) {
                $url = 'http://'.$_SERVER['HTTP_HOST'];
            }
            $config->setURL($url);
        }
        // Set default
        $tag->setEmail($config->getEmail());
        $tag->setSiteName($config->getName());
        $tag->setSiteUrl($config->getUrl());
        $tag->setToken($config->getToken());
    }

    private function updateConfig($app, $config, $tag)
    {
        if (!$config) {
            $config = new Config();
        }
        $config->setToken($tag->getToken());
        $config->setUrl($tag->getSiteUrl());
        $config->setName($tag->getSiteName());
        $config->setEmail($tag->getEmail());
        $app['orm.em']->persist($config);
        $app['orm.em']->flush($config);
    }
}
