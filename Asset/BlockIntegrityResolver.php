<?php

namespace DNAFactory\DataSeeder\Asset;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;

class BlockIntegrityResolver
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;
    /**
     * @var PageRepositoryInterface
     */
    protected $pageRepository;
    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $pageFactory;
    /**
     * @var BlockRepositoryInterface
     */
    protected $blockRepository;
    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    public function __construct
    (
        SearchCriteriaBuilder $searchCriteriaBuilder,
        BlockRepositoryInterface $blockRepository,
        BlockFactory $blockFactory
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->blockRepository = $blockRepository;
        $this->blockFactory = $blockFactory;
    }

    /**
     * @param $blockIdentifier
     */
    public function execute($blockIdentifier)
    {
        try {
            $this->searchCriteriaBuilder->addFilter('identifier', $blockIdentifier);
            $blocksCollection = $this->blockRepository->getList($this->searchCriteriaBuilder->create())->getItems();
            $block = array_shift($blocksCollection);
            if ($block) {
                //Using oldBlock to avoid identifier config check
                $oldBlock = $block;
                $this->blockRepository->delete($block);
                $oldBlock->setTitle($oldBlock->getTitle() . ' [' . date("Y-m-d") . ']');
                $oldBlock->setIdentifier($oldBlock->getIdentifier() . '-' . time());
                $oldBlock->setIsActive(false);
                $data = $oldBlock->getData();
                unset($data['block_id']);
                $updatedBlock = $this->blockFactory->create();
                $updatedBlock->setData($data);
                $this->blockRepository->save($updatedBlock);
            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }
}
