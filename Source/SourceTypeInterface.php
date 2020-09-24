<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Source;


interface SourceTypeInterface
{
    public function collect(array $options): SourceCollection;
}
