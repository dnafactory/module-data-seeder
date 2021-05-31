<?php
namespace DNAFactory\DataSeeder\Management\Import;

use DNAFactory\DataSeeder\Api\ImportBlockManagementInterface;

use DNAFactory\DataSeeder\Asset\BlockIntegrityResolver;
use DNAFactory\DataSeeder\Asset\FetchAssetContent;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\BlockRepository;
use Magento\Cms\Api\BlockRepositoryInterface;

class BlockManagement implements ImportBlockManagementInterface
{
    const BLOCK_PATH = 'blocks/';
    const BLOCK_CONTENT_PATH = self::BLOCK_PATH . 'contents/';

    /**
     * @var BlockFactory
     */
    private $blockFactory;
    /**
     * @var BlockRepositoryInterface
     */
    private $blockRepository;
    /**
     * @var FetchAssetContent
     */
    private $fetchAssetContent;
    /**
     * @var BlockIntegrityResolver
     */
    private $integrityResolver;

    /**
     * ImportBlockCommand constructor.
     * @param BlockFactory $blockFactory
     * @param BlockRepositoryInterface $blockRepository
     * @param FetchAssetContent $fetchAssetContent
     * @param BlockIntegrityResolver $integrityResolver
     */
    public function __construct (
        BlockFactory $blockFactory,
        BlockRepositoryInterface $blockRepository,
        FetchAssetContent $fetchAssetContent,
        BlockIntegrityResolver $integrityResolver
    ) {
        $this->blockFactory = $blockFactory;
        $this->blockRepository = $blockRepository;
        $this->fetchAssetContent = $fetchAssetContent;
        $this->integrityResolver = $integrityResolver;
    }

    public function import($fileName = "blocks.php")
    {
        $blocks = $this->getBlocks($fileName);

        foreach ($blocks as $block) {
            try {
                $content = $this->fetchAssetContent->execute(self::BLOCK_CONTENT_PATH . $block["file_name"]);

                $newBlock = $this->blockFactory->create();
                $newBlock->setData($block["block_data"]);
                $newBlock->setContent($content);
                $this->integrityResolver->execute($block["block_data"]["identifier"]);
                $this->blockRepository->save($newBlock);
            } catch (\Exception $exception) {
                echo $exception->getMessage();
                //Nothing to do
            }
        }
    }

    protected function getBlocks($filename)
    {
        $fileBlocksPath = $this->fetchAssetContent->getRootPath() . self::BLOCK_PATH . $filename;
        return include $fileBlocksPath;
    }
}
