<?php
namespace DNAFactory\DataSeeder\Command;

use DNAFactory\DataSeeder\Api\ImportPageManagementInterface;

use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Module\Dir\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPageCommand extends Command
{
    /**
     * @var ImportPageManagementInterface
     */
    protected $importPageManagement;
    
    /**
     * ImportBlockCommand constructor.
     * @param ImportPageManagementInterface $importPageManagement
     * @param string|null $name
     */
    public function __construct(
        ImportPageManagementInterface $importPageManagement,
        string $name = null
    ) {
        parent::__construct($name);
        $this->importPageManagement = $importPageManagement;
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

        $filename = $input->getArgument('page-filename');
        $this->importPageManagement->import($filename);
    }
}
