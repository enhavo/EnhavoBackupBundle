<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Normalizer;


use Enhavo\Bundle\BackupBundle\Normalizer\Type\NormalizerType;
use Enhavo\Bundle\BackupBundle\Storage\StorageCollection;
use Enhavo\Bundle\BackupBundle\Utility\FileHelper;
use Enhavo\Component\Type\AbstractType;

class AbstractNormalizerType extends AbstractType implements NormalizerTypeInterface
{
    /** @var NormalizerTypeInterface */
    protected $parent;

    /** @var FileHelper */
    protected $fileHelper;

    /**
     * AbstractNormalizerType constructor.
     * @param FileHelper $fileHelper
     */
    public function __construct(FileHelper $fileHelper)
    {
        $this->fileHelper = $fileHelper;
    }


    public function normalize(array $sourceCollections, array $options): StorageCollection
    {
        return $this->parent->normalize($sourceCollections, $options);
    }

    public static function getParentType(): ?string
    {
        return NormalizerType::class;
    }
}
