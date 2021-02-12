<?php
namespace DNAFactory\DataSeeder\Command;

use DNAFactory\DataSeeder\Asset\FetchAssetContent;
use DNAFactory\DataSeeder\Asset\PageIntegrityResolver;
use Magento\Cms\Api\PageRepositoryInterface;
use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Module\Dir\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPageCommand extends Command
{
    const PAGE_PATH = 'pages/';
    const PAGE_CONTENT_PATH = self::PAGE_PATH . 'contents/';

    /**
     * @var Reader
     */
    protected $directoryReader;
    /**
     * @var WriterInterface
     */
    protected $configWriter;
    /**
     * @var InputInterface
     */
    protected $input;
    /**
     * @var OutputInterface
     */
    protected $output;
    /**
     * @var FetchAssetContent
     */
    private $fetchAssetContent;
    /**
     * @var PageIntegrityResolver
     */
    private $integrityResolver;
    /**
     * @var PageFactory
     */
    private $pageFactory;
    /**
     * @var PageRepositoryInterface
     */
    private $pageRepository;

    /**
     * ImportBlockCommand constructor.
     * @param PageFactory $pageFactory
     * @param PageRepositoryInterface $pageRepository
     * @param FetchAssetContent $fetchAssetContent
     * @param PageIntegrityResolver $integrityResolver
     * @param string|null $name
     */
    public function __construct(
        PageFactory $pageFactory,
        PageRepositoryInterface $pageRepository,
        FetchAssetContent $fetchAssetContent,
        PageIntegrityResolver $integrityResolver,
        string $name = null
    ) {
        parent::__construct($name);
        $this->fetchAssetContent = $fetchAssetContent;
        $this->integrityResolver = $integrityResolver;
        $this->pageFactory = $pageFactory;
        $this->pageRepository = $pageRepository;
    }

    protected function configure()
    {
        $this->setName("dnafactory:seeder:import-page");
        $this->setDescription("Import Page");

        $this->addArgument('page-filename', InputArgument::OPTIONAL, 'Page Filename', 'pages.php');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $pages = $this->getPages($input->getArgument('page-filename'));

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
