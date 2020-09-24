<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Normalizer;


use Enhavo\Bundle\BackupBundle\Storage\StorageCollection;
use Enhavo\Component\Type\AbstractContainerType;

class Normalizer extends AbstractContainerType
{
    /** @var NormalizerTypeInterface */
    protected $type;

    public function normalize(array $sourceCollections): StorageCollection
    {
        return $this->type->normalize($sourceCollections, $this->options);
    }
}
