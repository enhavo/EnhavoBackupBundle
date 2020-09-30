<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Storage\Type;


use Enhavo\Bundle\BackupBundle\Storage\AbstractStorageType;
use Enhavo\Bundle\BackupBundle\Storage\StorageCollection;
use Enhavo\Bundle\BackupBundle\Utility\FileHelper;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalStorageType extends AbstractStorageType
{
    /** @var FileHelper */
    private $fileHelper;

    /**
     * LocalStorageType constructor.
     * @param FileHelper $fileHelper
     */
    public function __construct(FileHelper $fileHelper)
    {
        $this->fileHelper = $fileHelper;
    }

    public function store(StorageCollection $storageCollection, array $options): void
    {
        $target = $this->getTarget($options);
        $this->copyFiles($storageCollection->getFiles(), $target);
    }

    public function cleanup(array $options): void
    {
        if ($options['max_age'] !== null) {
            $dateInterval = \DateInterval::createFromDateString($options['max_age']);
            $now = new \DateTime();
            $target = $this->getTarget($options);
            $files = $this->fileHelper->listFiles($target);
            foreach ($files as $file) {
                if ($file->isFile()) {
                    $mtime = new \DateTime();
                    $mtime->setTimestamp($file->getMTime());
                    $mtime->add($dateInterval);
                    if ($mtime < $now) {
                        unlink($file->getRealPath());
                    }
                }
            }
        }
    }

    private function getTarget(array $options)
    {
        return $this->fileHelper->mkdir($this->fileHelper->normalizeDirectory($options['target']));
    }

    private function copyFiles($files, $target)
    {
        foreach ($files as $file) {
            $filename = basename($file);
            if (is_file($file)) {
                $this->fileHelper->copy($file, $target . '/' . $filename);
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'max_age' => null,
        ]);

        $resolver->setRequired([
            'target',
        ]);
    }

    public static function getName(): ?string
    {
        return 'local';
    }
}
