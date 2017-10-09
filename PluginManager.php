<?php
namespace Plugin\KaiUConnection;

use Eccube\Application;
use Eccube\Plugin\AbstractPluginManager;
use Eccube\Common\Constant;
use Eccube\Entity\Block;
use Eccube\Entity\BlockPosition;
use Eccube\Entity\Master\DeviceType;
use Eccube\Entity\PageLayout;
use Eccube\Util\Cache;
use Symfony\Component\Filesystem\Filesystem;

class PluginManager extends AbstractPluginManager
{
    /**
     * @var string block file
     */
    private $originBlock;

    /**
     * @var string block name
     */
    private $blockName = 'KaiU Tag';

    /**
     * @var string file name
     */
    private $blockFileName = 'kaiu_tag_block';

    /**
     * PluginManager constructor.
     */
    public function __construct()
    {
        // set block location
        $this->originBlock = __DIR__.'/Resource/template/Block/'.$this->blockFileName.'.twig';
    }

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
        // remove block
        $this->removeDataBlock($app);
        if (file_exists($app['config']['block_realdir'].'/'.$this->blockFileName.'.twig')) {
            $this->removeBlock($app);
        }

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
        $this->copyBlock($app);
        // create layout
        $this->createDataBlock($app);

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
        $this->removeBlock($app);
        // delete block layout
        $this->removeDataBlock($app);
    }

    /**
     * @param array       $config
     * @param Application $app
     */
    public function update($config, $app)
    {
        // Update block
        $this->copyBlock($app);

        $this->migrationSchema($app, __DIR__.'/Resource/doctrine/migration', $config['code']);
    }

    /**
     * create block layout data.
     *
     * @param \Eccube\Application $app
     *
     * @throws \Exception
     */
    private function createDataBlock($app)
    {
        $em = $app['orm.em'];
        try {
            $DeviceType = $app['eccube.repository.master.device_type']->find(DeviceType::DEVICE_TYPE_PC);
            // check exists block
            /** @var Block $Block */
            $Block = $app['eccube.repository.block']->findOneBy(array('DeviceType' => $DeviceType, 'file_name' => $this->blockFileName));
            if (!$Block) {
                /** @var Block $Block */
                $Block = $app['eccube.repository.block']->findOrCreate(null, $DeviceType);
                // Blockの登録
                $Block->setName($this->blockName)
                    ->setFileName($this->blockFileName)
                    ->setDeletableFlg(Constant::DISABLED)
                    ->setLogicFlg(1);
                $em->persist($Block);
                $em->flush($Block);
            }
            // check exists block position
            $blockPos = $em->getRepository('Eccube\Entity\BlockPosition')->findOneBy(array('block_id' => $Block->getId()));
            if ($blockPos) {
                return;
            }
            // BlockPositionの登録
            $blockPos = $em->getRepository('Eccube\Entity\BlockPosition')->findOneBy(
                array('page_id' => 1, 'target_id' => PageLayout::TARGET_ID_HEAD),
                array('block_row' => 'DESC')
            );
            $BlockPosition = new BlockPosition();
            // Block position
            $BlockPosition->setBlockRow(1);
            if ($blockPos) {
                $blockRow = $blockPos->getBlockRow() + 1;
                $BlockPosition->setBlockRow($blockRow);
            }
            $PageLayout = $app['eccube.repository.page_layout']->find(1);
            $BlockPosition->setPageLayout($PageLayout)
                ->setPageId($PageLayout->getId())
                ->setTargetId(PageLayout::TARGET_ID_HEAD)
                ->setBlock($Block)
                ->setBlockId($Block->getId())
                ->setAnywhere(Constant::ENABLED);
            $em->persist($BlockPosition);
            $em->flush($BlockPosition);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * ブロックを削除.
     *
     * @param \Eccube\Application $app
     *
     * @throws \Exception
     */
    private function removeDataBlock($app)
    {
        /** @var \Eccube\Entity\Block $Block */
        $Block = $app['eccube.repository.block']->findOneBy(array('file_name' => $this->blockFileName));
        if (!$Block) {
            Cache::clear($app, false);
            return;
        }
        $em = $app['orm.em'];
        try {
            // Remove BlockPosition
            $blockPositions = $Block->getBlockPositions();
            /** @var \Eccube\Entity\BlockPosition $BlockPosition */
            foreach ($blockPositions as $BlockPosition) {
                $Block->removeBlockPosition($BlockPosition);
                $em->remove($BlockPosition);
            }
            // remove block
            $em->remove($Block);
            $em->flush();
        } catch (\Exception $e) {
            throw $e;
        }
        Cache::clear($app, false);
    }

    /**
     * Copy block template.
     *
     * @param $app
     */
    private function copyBlock($app)
    {
        $file = new Filesystem();
        // copy block
        $file->copy($this->originBlock, $app['config']['block_realdir'].'/'.$this->blockFileName.'.twig');
    }

    /**
     * Remove block template.
     *
     * @param $app
     */
    private function removeBlock($app)
    {
        $file = new Filesystem();
        $file->remove($app['config']['block_realdir'].'/'.$this->blockFileName.'.twig');
    }
}
