<?php
namespace DNAFactory\DataSeeder\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use DNAFactory\DataSeeder\Api\ImportConfigManagementInterface;

class ImportConfigCommand extends Command
{
    /**
     * @var ImportConfigManagementInterface
     */
    protected $importConfigManagement;

    public function __construct(
        ImportConfigManagementInterface $importConfigManagement,
        string $name = null
    ) {
        parent::__construct($name);
        $this->importConfigManagement = $importConfigManagement;
    }

    protected function configure()
    {
        $this->setName("dnafactory:seeder:import-config");
        $this->setDescription("Import config");

        $this->addArgument('environment', InputArgument::OPTIONAL, 'Environment', 'production');
        $this->addArgument('config-filename', InputArgument::OPTIONAL, 'Config Filename (without extension)', 'config');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('config-filename');
        $environment = $input->getArgument('environment');
        
        $this->importConfigManagement->import($filename, $environment);
    }
}
