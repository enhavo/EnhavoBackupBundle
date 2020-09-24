<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Normalizer;

use Enhavo\Bundle\BackupBundle\Storage\StorageCollection;

interface NormalizerTypeInterface
{
    public function normalize(array $sourceCollections, array $options): StorageCollection;
}
