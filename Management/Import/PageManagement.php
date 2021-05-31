<?php
namespace DNAFactory\DataSeeder\Management\Import;

use DNAFactory\DataSeeder\Api\ImportPageManagementInterface;

use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageFactory;
use Magento\Cms\Api\PageRepositoryInterface;
use DNAFactory\DataSeeder\Asset\FetchAssetContent;
use DNAFactory\DataSeeder\Asset\PageIntegrityResolver;

class PageManagement implements ImportPageManagementInterface
{
    const PAGE_PATH = 'pages/';
    const PAGE_CONTENT_PATH = self::PAGE_PATH . 'contents/';

    public function __construct (
        PageFactory $pageFactory,
        PageRepositoryInterface $pageRepository,
        FetchAssetContent $fetchAssetContent,
        PageIntegrityResolver $integrityResolver
    ) {
        $this->fetchAssetContent = $fetchAssetContent;
        $this->integrityResolver = $integrityResolver;
        $this->pageFactory = $pageFactory;
        $this->pageRepository = $pageRepository;
    }

    public function import($filename = "pages.php")
    {
        $pages = $this->getPages($filename);

        foreach ($pages as $page) {
            try {
                $content = $this->fetchAssetContent->execute(self::PAGE_CONTENT_PATH . $page["file_name"]);

                $this->integrityResolver->execute($page['page_data']['identifier']);
                /** @var Page $pageModel */
                $pageModel = $this->pageFactory->create();
                $pageModel->setData($page['page_data']);
                $pageModel->setContent($content);
                $this->pageRepository->save($pageModel);
            } catch (\Exception $exception) {
                echo $exception->getMessage();
            }
        }
    }

    protected function getPages($filename)
    {
        $filePagesPath = $this->fetchAssetContent->getRootPath() . self::PAGE_PATH . $filename;
        return include $filePagesPath;
    }
}
