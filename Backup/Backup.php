<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Backup;


use Enhavo\Bundle\BackupBundle\Source\SourceCollection;
use Enhavo\Bundle\BackupBundle\Storage\StorageCollection;
use Enhavo\Bundle\BackupBundle\Normalizer\Normalizer;
use Enhavo\Bundle\BackupBundle\Source\Source;
use Enhavo\Bundle\BackupBundle\Storage\Storage;

class Backup
{
    /** @var Source[] */
    private $sources;
    /** @var Normalizer */
    private $normalizer;
    /** @var Storage[] */
    private $storages;

    /**
     * Strategy constructor.
     * @param Source[] $sources
     * @param Normalizer $normalizer
     * @param Storage[] $storages
     */
    public function __construct(array $sources, Normalizer $normalizer, array $storages)
    {
        $this->sources = $sources;
        $this->normalizer = $normalizer;
        $this->storages = $storages;
    }


    /**
     * @return SourceCollection[]
     */
    public function collect(): array
    {
        $collections = [];
        // iterate over sources and return source tmp directories
        foreach ($this->sources as $source) {
            $collections[] = $source->collect();
        }

        return $collections;
    }

    public function normalize(array $sourceCollections): StorageCollection
    {
        // archive->execute store in strategie's very own bundler-tmp-dir
        return $this->normalizer->normalize($sourceCollections);
    }

    public function store(StorageCollection $storageCollection): void
    {
        // iterate over storages->store upload into storages / cleanup
        foreach ($this->storages as $storage) {
            $storage->store($storageCollection);
        }
    }

    public function cleanup(): void
    {
        foreach ($this->storages as $storage) {
            $storage->cleanup();
        }
    }
}
