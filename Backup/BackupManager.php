<?php


namespace Enhavo\Bundle\BackupBundle\Backup;

use Enhavo\Bundle\BackupBundle\Exception\BackupNotFoundException;
use Enhavo\Bundle\BackupBundle\Storage\StorageCollection;
use Enhavo\Bundle\BackupBundle\Utility\FileHelper;
use Enhavo\Bundle\MediaBundle\Factory\FileFactory;
use Enhavo\Component\Type\FactoryInterface;

class BackupManager
{
    public function __construct(
        private FactoryInterface $sourceFactory,
        private FactoryInterface $normalizerFactory,
        private FactoryInterface $storageFactory,
        private FactoryInterface $notificationFactory,
        private FileHelper $fileHelper,
        private array $configurations,
        private FileFactory $fileFactory,
    ) {
    }

    public function backup($name): BackupOutput
    {
        $output = new BackupOutput();

        $backup = $this->getBackup($name);

        $sourceCollections = [];
        $storageCollection = null;
        try {
            $sourceCollections = $backup->collect();
            $storageCollection = $backup->normalize($sourceCollections);
            $storageCollection->setName($backup->getName());

            foreach ($storageCollection->getFiles() as $file) {
                $output->addFile($this->fileFactory->createFromPath($file));
            }

            $backup->store($storageCollection);
            $backup->cleanup();

            $this->cleanupCollections($sourceCollections, $storageCollection);

            $backup->notify(sprintf('Backup "%s" was executed successfully', $name), $backup);

        } catch (\Exception $ex) {
            $this->cleanupCollections($sourceCollections, $storageCollection);

            $backup->notify(sprintf('Error during Backup "%s": "', $name) . $ex->getMessage() . '"', $backup);

            throw $ex;
        }

        return $output;
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
