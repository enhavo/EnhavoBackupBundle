<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Source\Type;


use Enhavo\Bundle\BackupBundle\Source\SourceCollection;
use Enhavo\Bundle\BackupBundle\Source\SourceTypeInterface;
use Enhavo\Component\Type\AbstractType;

class SourceType extends AbstractType implements SourceTypeInterface
{
    public function collect(array $options): SourceCollection
    {
        return new SourceCollection();
    }
}
