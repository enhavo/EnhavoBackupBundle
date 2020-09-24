<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Source;


use Enhavo\Component\Type\AbstractContainerType;

class Source extends AbstractContainerType
{
    /** @var SourceTypeInterface */
    protected $type;

    public function collect(): SourceCollection
    {
        return $this->type->collect($this->options);
    }
}
