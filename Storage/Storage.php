<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Storage;


use Enhavo\Component\Type\AbstractContainerType;

class Storage extends AbstractContainerType
{
    /** @var StorageTypeInterface */
    protected $type;

    public function store(StorageCollection $storageCollection): void
    {
        $this->type->store($storageCollection, $this->options);
    }

    public function cleanup(): void
    {
        $this->type->cleanup($this->options);
    }
}
