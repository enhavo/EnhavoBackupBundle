<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Storage;


use Enhavo\Bundle\BackupBundle\Storage\Type\StorageType;
use Enhavo\Component\Type\AbstractType;

class AbstractStorageType extends AbstractType implements StorageTypeInterface
{
    /** @var StorageTypeInterface */
    protected $parent;

    public function store(StorageCollection $storageCollection, array $options): void
    {
        $this->parent->store($storageCollection, $options);
    }

    public function cleanup(array $options): void
    {
        $this->parent->cleanup($options);
    }


    public static function getParentType(): ?string
    {
        return StorageType::class;
    }
}
