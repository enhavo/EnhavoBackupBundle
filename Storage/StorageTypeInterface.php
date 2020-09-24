<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Storage;


interface StorageTypeInterface
{
    public function store(StorageCollection $storageCollection, array $options): void;

    public function cleanup(array $options): void;
}
