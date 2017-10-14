<?php

namespace Plugin\KaiUConnection\Controller;

use Eccube\Application;
use Eccube\Controller\AbstractController;
use Plugin\KaiUConnection\Entity\ConfigPlugin;
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
    public function index(Application $app, Request $request, $id = null)
    {
        /**
         * @var TagRepository $repo
         */
        $repo = $app['kaiu.repository.tag'];
        $tags = $repo->findBy(array(), array('site_id' => 'DESC'));

        $tag = null;
        if ($id) {
            $tag = $repo->findOneBy($id);
        }

        if (!$tag) {
            $tag = new Tag();
        }

        /* @var $form FormInterface */
        $form = $app['form.factory']
            ->createBuilder('config', $tag)
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $tag = $form->getData();
            $app['kaiu.repository.tag']->save($tag);

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

        $app['kaiu.repository.tag']->save($tag);
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
}
