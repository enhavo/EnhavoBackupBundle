<?php
/*
 * @author blutze-media
 * @since 2020-09-25
 */

namespace Enhavo\Bundle\BackupBundle\Entity;


use Sylius\Component\Resource\Model\ResourceInterface;

class BackupFile implements ResourceInterface
{
    /** @var int|null */
    private $id;

    /** @var string|null */
    private $path;

    /** @var string|null */
    private $name;

    /** @var Backup|null */
    private $backup;

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
     * @return Backup|null
     */
    public function getBackup(): ?Backup
    {
        return $this->backup;
    }

    /**
     * @param Backup|null $backup
     */
    public function setBackup(?Backup $backup): void
    {
        $this->backup = $backup;
    }

    /**
     * @return string|null
     */
    public function getPath(): ?string
    {
        return $this->path;
    }

    /**
     * @param string|null $path
     */
    public function setPath(?string $path): void
    {
        $this->path = $path;
    }


}
