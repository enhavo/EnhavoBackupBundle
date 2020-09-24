<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Storage\Type;


use Enhavo\Bundle\BackupBundle\Exception\StorageException;
use Enhavo\Bundle\BackupBundle\Storage\AbstractStorageType;
use Enhavo\Bundle\BackupBundle\Storage\StorageCollection;
use League\Flysystem\Adapter\Ftp;
use League\Flysystem\Filesystem;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FtpStorageType extends AbstractStorageType
{
    /** @var Filesystem */
    private $filesystem;

    public function store(StorageCollection $storageCollection, array $options): void
    {
        $root = $options['root'];
        $options['root'] = null;
        $this->setup($options);
        if ($root !== null) {
            $this->filesystem->createDir($root);
            $options['root'] = $root;
        }
        $this->setup($options);

        foreach ($storageCollection->getFiles() as $file) {
            $resource = fopen($file, 'r');
            if ($resource !== false) {
                $result = $this->filesystem->writeStream(basename($file), $resource);
                fclose($resource);

                if ($result == false) {
                    throw new StorageException(sprintf('File "%s/%s" is not writable!', $options['root'], $file));
                }
            } else {
                throw new StorageException(sprintf('File "%s" is not readable!', $file));
            }
        }
    }

    public function cleanup(array $options): void
    {
        if ($options['max_age'] !== null) {
            $this->setup($options);

            $dateInterval = \DateInterval::createFromDateString($options['max_age']);
            $now = new \DateTime();

            $files = $this->filesystem->listContents();
            foreach ($files as $metadata) {
                $file = $metadata['path'];
                if ($metadata['type'] == 'file') {
                    $mtime = new \DateTime();
                    $mtime->setTimestamp($this->filesystem->getTimestamp($file));
                    $mtime->add($dateInterval);
                    if ($mtime < $now) {
                        $this->filesystem->delete($file);
                    }
                }
            }
        }
    }

    private function setup(array $options)
    {
        $this->filesystem = new Filesystem(new Ftp($options));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'ssl' => false,
            'timeout' => 90,
            'root' => null,
            'port' => 21,
            'passive' => true,
            'transferMode' => FTP_BINARY,
            'utf8' => false,
            'max_age' => null,
            'username' => null,
            'password' => null,
        ]);
        $resolver->setRequired([
            'host',
        ]);
    }

    public static function getName(): ?string
    {
        return 'ftp';
    }
}
