<?php
namespace DNAFactory\DataSeeder\Command;

use DNAFactory\DataSeeder\Asset\BlockIntegrityResolver;
use DNAFactory\DataSeeder\Asset\FetchAssetContent;
use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Cms\Model\Block;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Module\Dir\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\BlockRepository;

class ImportBlockCommand extends Command
{
    const BLOCK_PATH = 'blocks/';
    const BLOCK_CONTENT_PATH = self::BLOCK_PATH . 'contents/';

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
     * @param string|null $name
     */
    public function __construct(
        BlockFactory $blockFactory,
        BlockRepositoryInterface $blockRepository,
        FetchAssetContent $fetchAssetContent,
        BlockIntegrityResolver $integrityResolver,
        string $name = null
    ) {
        parent::__construct($name);
        $this->blockFactory = $blockFactory;
        $this->blockRepository = $blockRepository;
        $this->fetchAssetContent = $fetchAssetContent;
        $this->integrityResolver = $integrityResolver;
    }

    protected function configure()
    {
        $this->setName("dnafactory:seeder:import-block");
        $this->setDescription("Import Block");

        $this->addArgument('block-filename', InputArgument::OPTIONAL, 'Block Filename', 'blocks.php');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;

        $blocks = $this->getBlocks($input->getArgument('block-filename'));

        foreach ($blocks as $block) {
            try {
                $content = $this->fetchAssetContent->execute(self::BLOCK_CONTENT_PATH . $block["file_name"]);

                /** @var Block $newBlock */
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
