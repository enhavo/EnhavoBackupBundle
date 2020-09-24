<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Source;


use Enhavo\Bundle\BackupBundle\Source\Type\SourceType;
use Enhavo\Bundle\BackupBundle\Utility\FileHelper;
use Enhavo\Component\Type\AbstractType;

class AbstractSourceType extends AbstractType implements SourceTypeInterface
{
    /** @var SourceTypeInterface */
    protected $parent;

    /** @var FileHelper */
    protected $fileHelper;

    /**
     * AbstractSourceType constructor.
     * @param FileHelper $fileHelper
     */
    public function __construct(FileHelper $fileHelper)
    {
        $this->fileHelper = $fileHelper;
    }


    public function collect(array $options): SourceCollection
    {
        return $this->parent->collect($options);
    }


    public static function getParentType(): ?string
    {
        return SourceType::class;
    }
}
