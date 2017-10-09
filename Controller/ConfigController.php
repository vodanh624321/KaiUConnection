<?php

namespace Plugin\KaiUConnection\Controller;

use Eccube\Application;
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
class ConfigController
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
        $arrConfig = new ConfigPlugin();
        /**
         * @var TagRepository $repo
         */
        $repo = $app['kaiu.repository.tag'];
        $tags = $repo->findAll();
        // Empty row
        $newTag = new Tag();
        $tags[] = $newTag;

//        dump($tags);
        $arrConfig->setTags($tags);
        /* @var $form FormInterface */
        $form = $app['form.factory']
            ->createBuilder('config', $arrConfig)
            ->getForm();
//        $form['tags']->setData($tag);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            dump($form['tags']->getData());
//die;
        }
//        dump($form);

        return $app->render('KaiUConnection/Resource/template/admin/config.twig', array('form' => $form->createView()));
    }
}
