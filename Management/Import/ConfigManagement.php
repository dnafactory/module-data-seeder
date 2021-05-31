<?php
namespace DNAFactory\DataSeeder\Management\Import;

use DNAFactory\DataSeeder\Api\ImportConfigManagementInterface;

use DNAFactory\DataSeeder\Asset\FetchAssetContent;
use Magento\Framework\App\Config\Storage\WriterInterface;

class ConfigManagement implements ImportConfigManagementInterface
{
    const FILE_EXT = ".php";

    protected $fetchAssetContent;
    protected $configWriter;

    public function __construct (
        FetchAssetContent $fetchAssetContent,
        WriterInterface $configWriter
    ) {
        $this->fetchAssetContent = $fetchAssetContent;
        $this->configWriter = $configWriter;
    }

    public function import($fileName = "config", $environment = "production")
    {
        $configs = $this->getConfigs($fileName, $environment);

        foreach ($configs as $config) {
            $this->configWriter->save(
                $config['path'],
                $config['value'],
                $config['scope'],
                $config['scope_id']
            );
        }
    }

    protected function getConfigs($filename, $environment)
    {
        $configs = [];
        $fileConfigPath = $this->fetchAssetContent->getConfigsPath() . $filename;

        $baseConfig = include $fileConfigPath . self::FILE_EXT;
        $configs = array_merge($configs, $baseConfig);

        $environmentConfig = include $fileConfigPath . '.' . $environment . self::FILE_EXT;
        $configs = array_merge($configs, $environmentConfig);

        return $configs;
    }
}
