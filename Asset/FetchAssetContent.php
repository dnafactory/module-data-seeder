<?php

namespace DNAFactory\DataSeeder\Asset;

use Magento\Framework\Filesystem\Directory\ReadFactory as DirectoryReaderFactory;
use Magento\Framework\Filesystem\DirectoryList;

class FetchAssetContent
{
    const ASSETS_FOLDER = "assets/seeder/";
    const CONFIG_FILE_PATH = "configs/";

    /**
     * @var \Magento\Framework\Filesystem\Directory\Read|\Magento\Framework\Filesystem\Directory\ReadInterface
     */
    private $directoryReader;
    /**
     * @var string
     */
    private $rootPath;

    /**
     * FetchAssetContent constructor.
     * @param DirectoryList $directoryList
     * @param DirectoryReaderFactory $directoryReaderFactory
     */
    public function __construct(
        DirectoryList $directoryList,
        DirectoryReaderFactory $directoryReaderFactory
    ) {
        $this->rootPath = $directoryList->getRoot() . self::ASSETS_FOLDER;
        $this->directoryReader = $directoryReaderFactory->create($this->rootPath);
    }

    /**
     * @param $path
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Magento\Framework\Exception\ValidatorException
     */
    public function execute($path): string
    {
        return $this->directoryReader->isExist($path) ? $this->directoryReader->readFile($path) : '';
    }

    public function getRootPath()
    {
        return $this->rootPath;
    }

    public function getConfigsPath()
    {
        return $this->rootPath . self::CONFIG_FILE_PATH;
    }
}
