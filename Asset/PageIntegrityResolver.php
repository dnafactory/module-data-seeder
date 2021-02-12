<?php

namespace DNAFactory\DataSeeder\Asset;

use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;

class PageIntegrityResolver
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
     * @var PageFactory
     */
    protected $pageFactory;

    public function __construct
    (
        SearchCriteriaBuilder $searchCriteriaBuilder,
        PageRepositoryInterface $pageRepository,
        PageFactory $pageFactory
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->pageRepository = $pageRepository;
        $this->pageFactory = $pageFactory;
    }

    public function execute($pageIdentifier)
    {
        try {
            $this->searchCriteriaBuilder->addFilter('identifier', $pageIdentifier);
            $pagesCollection = $this->pageRepository->getList($this->searchCriteriaBuilder->create())->getItems();
            $page = array_shift($pagesCollection);
            if ($page) {
                //Using oldPage to avoid identifier config check
                $oldPage = $page;
                $this->pageRepository->delete($page);
                $oldPage->setTitle($oldPage->getTitle() . ' [' . date("Y-m-d") . ']');
                $oldPage->setIdentifier($oldPage->getIdentifier() . '-' . time());
                $oldPage->setIsActive(false);
                $oldPage->setData('layout_update_selected', '_no_update_');
                $data = $oldPage->getData();
                unset($data['page_id']);
                $updatedPage = $this->pageFactory->create();
                $updatedPage->setData($data);
                $this->pageRepository->save($updatedPage);
            }
        } catch (\Exception $exception) {
            echo $exception->getMessage();
        }
    }
}
