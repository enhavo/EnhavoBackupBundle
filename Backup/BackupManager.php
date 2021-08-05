<?php


namespace Enhavo\Bundle\BackupBundle\Backup;


use Enhavo\Bundle\BackupBundle\Exception\BackupNotFoundException;
use Enhavo\Bundle\BackupBundle\Source\SourceCollection;
use Enhavo\Bundle\BackupBundle\Storage\StorageCollection;
use Enhavo\Bundle\BackupBundle\Utility\FileHelper;
use Enhavo\Component\Type\FactoryInterface;

class BackupManager
{
    /** @var FactoryInterface */
    private $sourceFactory;

    /** @var FactoryInterface */
    private $normalizerFactory;

    /** @var FactoryInterface */
    private $storageFactory;

    /** @var FactoryInterface */
    private $notificationFactory;

    /** @var FileHelper */
    private $fileHelper;

    /** @var array */
    private $configurations;

    /**
     * BackupManager constructor.
     * @param FactoryInterface $sourceFactory
     * @param FactoryInterface $normalizerFactory
     * @param FactoryInterface $storageFactory
     * @param FactoryInterface $notificationFactory
     * @param FileHelper $fileHelper
     * @param array $configurations
     */
    public function __construct(FactoryInterface $sourceFactory, FactoryInterface $normalizerFactory, FactoryInterface $storageFactory, FactoryInterface $notificationFactory, FileHelper $fileHelper, array $configurations)
    {
        $this->sourceFactory = $sourceFactory;
        $this->normalizerFactory = $normalizerFactory;
        $this->storageFactory = $storageFactory;
        $this->notificationFactory = $notificationFactory;
        $this->fileHelper = $fileHelper;
        $this->configurations = $configurations;
    }


    public function backup($name)
    {
        $backup = $this->getBackup($name);

        $sourceCollections = [];
        $storageCollection = null;
        try {
            $sourceCollections = $backup->collect();
            $storageCollection = $backup->normalize($sourceCollections);
            $storageCollection->setName($backup->getName());
            $backup->store($storageCollection);
            $backup->cleanup();

            $this->cleanupCollections($sourceCollections, $storageCollection);

            $backup->notify(sprintf('Backup "%s" was executed successfully', $name), $backup);

        } catch (\Exception $ex) {
            $this->cleanupCollections($sourceCollections, $storageCollection);

            $backup->notify(sprintf('Error during Backup "%s": "', $name) . $ex->getMessage() . '"', $backup);

            throw $ex;
        }
    }

    public function cleanup($name)
    {
        $backup = $this->getBackup($name);

        try {
            $backup->cleanup();

        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    /**
     * @param array $sourceCollections
     * @param StorageCollection|null $storageCollection
     */
    private function cleanupCollections(array $sourceCollections, ?StorageCollection $storageCollection)
    {
        foreach ($sourceCollections as $collection) {
            $this->fileHelper->remove($collection->getCleanupFiles());
        }
        if ($storageCollection) {
            $this->fileHelper->remove($storageCollection->getCleanupFiles());
        }
    }

    public function getBackup(string $name): Backup
    {
        if (!isset($this->configurations[$name])) {
            throw new BackupNotFoundException(sprintf('Configuration named "%s" not found', $name));
        }

        $config = $this->configurations[$name];

        $notifiation = isset($config['notification']) ? $this->createNotification($config['notification']) : null;

        return new Backup($name, $this->createSources($config['sources']), $this->createNormalizer($config['normalizer']), $this->createStorages($config['storages']), $notifiation);
    }

    private function createSources($config)
    {
        $sources = [];

        foreach ($config as $key => $options) {
            $sources[$key] = $this->createSource($options);
        }

        return $sources;
    }

    private function createSource($options)
    {
        return $this->sourceFactory->create($options);
    }

    private function createStorages($config)
    {
        $storages = [];

        foreach ($config as $key => $options) {
            $storages[$key] = $this->createStorage($options);
        }

        return $storages;
    }

    private function createStorage($options)
    {
        return $this->storageFactory->create($options);
    }

    private function createNormalizer($options)
    {
        return $this->normalizerFactory->create($options);
    }

    private function createNotification($options)
    {
        return $this->notificationFactory->create($options);
    }
}
