<?php
namespace DNAFactory\DataSeeder\Command;

use DNAFactory\DataSeeder\Api\ImportEmailManagementInterface;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class ImportEmailCommand extends Command
{

    /**
     * @var ImportEmailManagementInterface
     */
    protected $importEmailManagement;

    public function __construct(
        ImportEmailManagementInterface $importEmailManagement,
        string $name = null
    ) {
        $this->importEmailManagement = $importEmailManagement;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName("dnafactory:seeder:import-email");
        $this->setDescription("Import email and set 'em as default email");

        $this->addArgument('sql-file-name', InputArgument::OPTIONAL, 'Dump Filename', 'email_template.sql');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $filename = $input->getArgument('sql-file-name');
        $this->importEmailManagement->import($filename);
    }
}
