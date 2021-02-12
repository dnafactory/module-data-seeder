<?php
namespace DNAFactory\DataSeeder\Command;

use DNAFactory\DataSeeder\Asset\FetchAssetContent;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Module\Dir\Reader;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportConfigCommand extends Command
{
    const FILE_EXT = ".php";

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

    public function __construct(
        FetchAssetContent $fetchAssetContent,
        Reader $directoryReader,
        WriterInterface $configWriter,
        string $name = null
    ) {
        $this->directoryReader = $directoryReader;
        $this->configWriter = $configWriter;
        parent::__construct($name);
        $this->fetchAssetContent = $fetchAssetContent;
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
        $this->input = $input;
        $this->output = $output;

        $configs = $this->getConfigs($input->getArgument('config-filename'));

        foreach ($configs as $config) {
            $this->configWriter->save(
                $config['path'],
                $config['value'],
                $config['scope'],
                $config['scope_id']
            );

            $output->writeln(sprintf("Imported %s => %s", $config['path'], $config['value']));
        }
    }

    protected function getConfigs($filename)
    {
        $configs = [];
        $fileConfigPath = $this->fetchAssetContent->getConfigsPath() . $filename;

        $baseConfig = include $fileConfigPath . self::FILE_EXT;
        $configs = array_merge($configs, $baseConfig);

        $environmentConfig = include $fileConfigPath . $this->input->getArgument('environment') . self::FILE_EXT;
        $configs = array_merge($configs, $environmentConfig);

        return $configs;
    }
}
