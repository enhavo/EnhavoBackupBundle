<?php
/*
 * @author blutze-media
 * @since 2020-09-24
 */

namespace Enhavo\Bundle\BackupBundle\Storage;


class StorageCollection
{
    /** @var string */
    private $name;
    /** @var \DateTime */
    private $date;
    /** @var string|null */
    private $directory;
    /** @var array */
    private $files = [];
    /** @var array */
    private $cleanupFiles = [];

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \DateTime
     */
    public function getDate(): \DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate(\DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return string|null
     */
    public function getDirectory(): ?string
    {
        return $this->directory;
    }

    /**
     * @param string|null $directory
     */
    public function setDirectory(?string $directory): void
    {
        $this->directory = $directory;
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param array $files
     */
    public function setFiles(array $files): void
    {
        $this->files = $files;
    }

    /**
     * @return array
     */
    public function getCleanupFiles(): array
    {
        return $this->cleanupFiles;
    }

    /**
     * @param array $cleanupFiles
     */
    public function setCleanupFiles(array $cleanupFiles): void
    {
        $this->cleanupFiles = $cleanupFiles;
    }

    /**
     * @param mixed $file
     */
    public function addFile($file)
    {
        $this->files[] = $file;
    }

    /**
     * @param mixed $file
     */
    public function removeFile($file)
    {
        if (false !== $key = array_search($file, $this->files, true)) {
            array_splice($this->files, $key, 1);
        }
    }

    /**
     * @param mixed $cleanupFile
     */
    public function addCleanupFile($cleanupFile)
    {
        $this->cleanupFiles[] = $cleanupFile;
    }

    /**
     * @param mixed $cleanupFile
     */
    public function removeCleanupFile($cleanupFile)
    {
        if (false !== $key = array_search($cleanupFile, $this->cleanupFiles, true)) {
            array_splice($this->cleanupFiles, $key, 1);
        }
    }
}
