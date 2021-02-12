<?php
namespace DNAFactory\DataSeeder\Command;

use DNAFactory\DataSeeder\Asset\EmailTemplateDiscover;
use DNAFactory\DataSeeder\Asset\FetchAssetContent;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\Patch\PatchHistory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportEmailCommand extends Command
{
    const SQL_FILE_PATH = 'dumps/email/';
    const EMAIL_CONFIG_FILENAME = 'emails.php';

    /**
     * @var InputInterface
     */
    protected $input;
    /**
     * @var OutputInterface
     */
    protected $output;
    /**
     * @var ResourceConnection
     */
    protected $resourceConnection;
    /**
     * @var PatchHistory
     */
    protected $patchHistory;
    /**
     * @var FetchAssetContent
     */
    protected $fetchAssetContent;
    /**
     * @var EmailTemplateDiscover
     */
    protected $emailTemplateDiscover;
    /**
     * @var WriterInterface
     */
    protected $configWriter;
    /**
     * @var array
     */
    private $_emailConfig;

    public function __construct(
        ResourceConnection $resourceConnection,
        PatchHistory $patchHistory,
        FetchAssetContent $fetchAssetContent,
        EmailTemplateDiscover $emailTemplateDiscover,
        WriterInterface $configWriter,
        string $name = null
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->patchHistory = $patchHistory;
        $this->fetchAssetContent = $fetchAssetContent;
        $this->emailTemplateDiscover = $emailTemplateDiscover;
        $this->configWriter = $configWriter;
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
        $this->_emailConfig = include $this->fetchAssetContent->getConfigsPath() . self::EMAIL_CONFIG_FILENAME;
        $this->input = $input;
        $this->output = $output;

        try {
            $connection = $this->resourceConnection->getConnection();
            $sqlRaw = $this->fetchAssetContent->execute(self::SQL_FILE_PATH . $input->getArgument('sql-file-name'));

            $sqlQueries = explode('INSERT INTO email_template', $sqlRaw);

            foreach ($sqlQueries as $sqlQuery) {
                if (empty($sqlQuery)) {
                    continue;
                }

                try {
                    $connection->fetchAll('INSERT INTO email_template' . $sqlQuery);
                } catch (\Exception $exception) {
                    // Nothing to do
                }
            }

            foreach ($this->_emailConfig as $config) {
                try {
                    $value = $this->emailTemplateDiscover->getTemplateIdByNameAndCode($config['name'], $config['code']);

                    if (empty($value)) {
                        continue;
                    }

                    $this->configWriter->save(
                        $config['path'],
                        $value,
                        $config['scope'],
                        $config['scope_id']
                    );

                    $this->output->writeln(__('Imported \'%1\'', $config['path']));
                } catch (\Exception $exception) {
                    $this->output->writeln(__('Error saving email config \'%1\': %2', $config['path'], $exception->getMessage()));
                }
            }

        } catch (\Exception $exception) {
            $this->output->writeln(__('Error importing email: %1', $exception->getMessage()));
        }
    }
}
