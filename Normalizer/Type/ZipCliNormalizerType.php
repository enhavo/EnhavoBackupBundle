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
use Symfony\Component\Process\Process;

class ZipCliNormalizerType extends AbstractNormalizerType
{
    public function normalize(array $sourceCollections, array $options): StorageCollection
    {
        $collection = new StorageCollection();
        $fileFormat = date($options['file_format']);
        $tmpDir = $this->fileHelper->mkTmpDir(uniqid());
        $target = $tmpDir . '/' . $fileFormat . '.' . $options['file_extension'];

        $this->zip($sourceCollections, $target, $options['command'], $options['arguments']);
        $collection->addFile($target);
        $collection->addCleanupFile($tmpDir);

        return $collection;
    }

    /**
     * @param array $sourceCollections
     * @param $target
     * @param string $application
     * @param array $arguments
     * @return void
     */
    private function zip(array $sourceCollections, $target, string $application = 'zip', array $arguments = []): void
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

        $command = array_merge([$application], $arguments, [$target], $files);

        $process = new Process($command);
        $process->run();
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'file_format' => 'Y-m-d_H-i-s',
            'file_extension' => 'zip',
            'command' => 'zip',
            'arguments' => [],
            'recursive' => true,
        ]);
    }

    public static function getName(): ?string
    {
        return 'zip_cli';
    }
}
