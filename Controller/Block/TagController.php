<?php
namespace Plugin\KaiUConnection\Controller\Block;

use Eccube\Application;
use Plugin\KaiUConnection\Repository\ConfigRepository;
use Symfony\Component\HttpFoundation\Response;
/**
 * Class RecommendController.
 */
class TagController
{
    /**
     * Load block.
     *
     * @param Application $app
     *
     * @return Response
     */
    public function index(Application $app)
    {
        /**
         * @var ConfigRepository $repo
         */
        $repo = $app['kaiu.repository.config'];
        $config = $repo->find(1);
        $arrTag = array();
        $arrTag[] = unserialize($config->getTag());

        return $app->render('Block/kaiu_tag_block.twig', array(
            'tags' => $arrTag,
        ));
    }
}