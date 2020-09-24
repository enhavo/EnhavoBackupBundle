<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Source\Type;


use Enhavo\Bundle\BackupBundle\Source\SourceCollection;
use Enhavo\Bundle\BackupBundle\Source\AbstractSourceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DirectorySourceType extends AbstractSourceType
{
    public function collect(array $options): SourceCollection
    {
        $collection = new SourceCollection();
        $collection->setDate(new \DateTime());
        $collection->setDirectory($options['source']);
        $collection->setRoot($this->fileHelper->normalizeDirectory($collection->getDirectory()));
        $this->addFiles($options['source'], $collection, $options['recursive']);

        return $collection;
    }

    private function addFiles($source, SourceCollection $collection, $recursive = false)
    {
        $root = $this->fileHelper->normalizeDirectory($source);
        $files = $recursive ? $this->fileHelper->listRecursive($root) : $this->fileHelper->listFiles($root);

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $collection->addFile($file->getRealPath());
            }
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'recursive' => false,
        ]);
        $resolver->setRequired([
            'source',
        ]);
    }

    public static function getName(): ?string
    {
        return 'directory';
    }
}
