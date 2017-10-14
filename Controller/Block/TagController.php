<?php
namespace Plugin\KaiUConnection\Controller\Block;

use Doctrine\Common\Collections\ArrayCollection;
use Eccube\Application;
use Eccube\Entity\Master\Disp;
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
         * @var TagRepository $repo
         */
        $repo = $app['kaiu.repository.tag'];
        $tags = $repo->findAll();
        $arrTag = array();
        foreach ($tags as $value) {
            $arrTag[] = unserialize($value->getTag());
        }

        return $app->render('Block/kaiu_tag_block.twig', array(
            'tags' => $arrTag,
        ));
    }
}