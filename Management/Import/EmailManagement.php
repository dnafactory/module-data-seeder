<?php
namespace DNAFactory\DataSeeder\Management\Import;

use DNAFactory\DataSeeder\Api\ImportEmailManagementInterface;

use DNAFactory\DataSeeder\Asset\EmailTemplateDiscover;
use DNAFactory\DataSeeder\Asset\FetchAssetContent;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ResourceConnection;
use Psr\Log\LoggerInterface;

class EmailManagement implements ImportEmailManagementInterface
{
    const SQL_FILE_PATH = 'dumps/email/';
    const EMAIL_CONFIG_FILENAME = 'emails.php';

    protected $resourceConnection;
    protected $fetchAssetContent;
    protected $emailTemplateDiscover;
    protected $configWriter;
    protected $logger;

    public function __construct (
        ResourceConnection $resourceConnection,
        FetchAssetContent $fetchAssetContent,
        EmailTemplateDiscover $emailTemplateDiscover,
        WriterInterface $configWriter,
        LoggerInterface $logger
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->fetchAssetContent = $fetchAssetContent;
        $this->emailTemplateDiscover = $emailTemplateDiscover;
        $this->configWriter = $configWriter;
        $this->logger = $logger;
    }

    public function import($sqlFilename = "email_template.sql")
    {
        $this->_emailConfig = include $this->fetchAssetContent->getConfigsPath() . self::EMAIL_CONFIG_FILENAME;

        try {
            $connection = $this->resourceConnection->getConnection();
            $sqlRaw = $this->fetchAssetContent->execute(self::SQL_FILE_PATH . $sqlFilename);

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

                    $this->logger->info(__('Imported \'%1\'', $config['path']));
                } catch (\Exception $exception) {
                    $this->logger->info(__('Error saving email config \'%1\': %2', $config['path'], $exception->getMessage()));
                }
            }
        } catch (\Exception $exception) {
            $this->logger->info(__('Error importing email: %1', $exception->getMessage()));
        }
    }
}
