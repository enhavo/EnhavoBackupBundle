<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Normalizer\Type;

use Enhavo\Bundle\BackupBundle\Normalizer\AbstractNormalizerType;
use Enhavo\Bundle\BackupBundle\Source\SourceCollection;
use Enhavo\Bundle\BackupBundle\Storage\StorageCollection;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ZipCliNormalizerType extends AbstractNormalizerType
{
    public function normalize(array $sourceCollections, array $options): StorageCollection
    {
        $collection = new StorageCollection();
        $fileFormat = date($options['file_format']);
        $tmpDir = $this->fileHelper->mkTmpDir(uniqid());
        $target = $tmpDir . '/' . $fileFormat . '.' . $options['file_extension'];

        $this->zip($sourceCollections, $target);
        $collection->addFile($target);
        $collection->addCleanupFile($tmpDir);

        return $collection;
    }

    /**
     * @param SourceCollection[] $sourceCollections
     * @param $target
     * @param string $addPattern
     */
    private function zip(array $sourceCollections, $target)
    {
        $files = [];
        $projectDir = $this->fileHelper->normalizeDirectory('');
        foreach ($sourceCollections as $sourceCollection) {
            foreach ($sourceCollection->getFiles() as $source) {
                if ($sourceCollection->getDirectory()) {
                    $file = substr($source, strpos($source, $sourceCollection->getDirectory()));
                } else {
                    $file = substr($source, strlen($projectDir));
                }
                $files[] = $file;
            }
        }

        $command = sprintf('zip %s %s', $target, implode(' ', $files));

        $result = '';
        passthru($command, $result);
        echo $result;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'file_format' => 'Y-m-d_H-i-s',
            'file_extension' => 'zip',
            'recursive' => true,
        ]);
    }

    public static function getName(): ?string
    {
        return 'zip_cli';
    }
}
