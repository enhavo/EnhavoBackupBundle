<?php

namespace Enhavo\Bundle\BackupBundle\Backup;

use Enhavo\Bundle\MediaBundle\Model\FileInterface;

class BackupOutput
{
    /** @var FileInterface[] */
    private array $files = [];

    public function addFile(FileInterface $file)
    {
        $this->files[] = $file;
    }

    /** @return FileInterface[] */
    public function getFiles(): array
    {
        return $this->files;
    }
}
