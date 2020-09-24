<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Normalizer\Type;


use Enhavo\Bundle\BackupBundle\Normalizer\NormalizerTypeInterface;
use Enhavo\Bundle\BackupBundle\Storage\StorageCollection;
use Enhavo\Component\Type\AbstractType;

class NormalizerType extends AbstractType implements NormalizerTypeInterface
{
    public function normalize(array $sourceCollections, array $options): StorageCollection
    {
        return new StorageCollection();
    }
}
