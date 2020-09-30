<?php
/*
 * @author blutze-media
 * @since 2020-09-25
 */

namespace Enhavo\Bundle\BackupBundle\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Enhavo\Bundle\MediaBundle\Model\FileInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

class Backup implements ResourceInterface
{
    /** @var int|null */
    private $id;

    /** @var string|null */
    private $name;
    
    /** @var string|null */
    private $backup;

    /** @var Collection */
    private $files;

    /** @var \DateTime|null */
    private $date;

    /**
     * EnhavoTypeBackup constructor.
     */
    public function __construct()
    {
        $this->files = new ArrayCollection();
    }


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return \DateTime|null
     */
    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    /**
     * @param \DateTime|null $date
     */
    public function setDate(?\DateTime $date): void
    {
        $this->date = $date;
    }

    /**
     * @return Collection
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    /**
     * @param BackupFile $file
     */
    public function addFile(BackupFile $file): void
    {
        $this->files[] = $file;
        $file->setBackup($this);
    }

    /**
     * @param BackupFile $file
     */
    public function removeFile(BackupFile $file): void
    {
        $this->files->removeElement($file);
        $file->setBackup(null);
    }

    /**
     * @return string|null
     */
    public function getBackup(): ?string
    {
        return $this->backup;
    }

    /**
     * @param string|null $backup
     */
    public function setBackup(?string $backup): void
    {
        $this->backup = $backup;
    }

    
}
