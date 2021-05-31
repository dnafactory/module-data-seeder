<?php
namespace DNAFactory\DataSeeder\Command;

use DNAFactory\DataSeeder\Api\ImportBlockManagementInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportBlockCommand extends Command
{

    /**
     * @var ImportBlockManagementInterface
     */
    private $importBlockManagement;

    /**
     * ImportBlockCommand constructor
     * @param string|null $name
     */
    public function __construct(
        ImportBlockManagementInterface $importBlockManagement,
        string $name = null
    ) {
        parent::__construct($name);
        $this->importBlockManagement = $importBlockManagement;
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
        $fileName = $input->getArgument('block-filename');
        $this->importBlockManagement->import($fileName);
    }
}
