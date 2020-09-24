<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Storage\Type;


use Enhavo\Bundle\BackupBundle\Storage\StorageCollection;
use Enhavo\Bundle\BackupBundle\Storage\StorageTypeInterface;
use Enhavo\Component\Type\AbstractType;

class StorageType extends AbstractType implements StorageTypeInterface
{
    public function store(StorageCollection $storageCollection, array $options): void
    {
    }

    public function cleanup(array $options): void
    {
    }

}
