<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\Storage\Type;


use Doctrine\ORM\EntityManagerInterface;
use Enhavo\Bundle\BackupBundle\Entity\Backup;
use Enhavo\Bundle\BackupBundle\Entity\BackupFile;
use Enhavo\Bundle\BackupBundle\Repository\BackupRepository;
use Enhavo\Bundle\BackupBundle\Storage\AbstractStorageType;
use Enhavo\Bundle\BackupBundle\Storage\StorageCollection;
use Enhavo\Bundle\BackupBundle\Utility\FileHelper;
use Enhavo\Bundle\MediaBundle\Factory\FileFactory;
use Gedmo\Sluggable\Util\Urlizer;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EnhavoStorageType extends AbstractStorageType
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var BackupRepository */
    private $repository;

    /** @var FactoryInterface */
    private $backupFactory;

    /** @var FactoryInterface */
    private $fileFactory;

    /** @var FileHelper */
    private $fileHelper;

    /**
     * EnhavoStorageType constructor.
     * @param EntityManagerInterface $entityManager
     * @param BackupRepository $repository
     * @param FactoryInterface $backupFactory
     * @param FactoryInterface $fileFactory
     * @param FileHelper $fileHelper
     */
    public function __construct(EntityManagerInterface $entityManager, BackupRepository $repository, FactoryInterface $backupFactory, FactoryInterface $fileFactory, FileHelper $fileHelper)
    {
        $this->entityManager = $entityManager;
        $this->repository = $repository;
        $this->backupFactory = $backupFactory;
        $this->fileFactory = $fileFactory;
        $this->fileHelper = $fileHelper;
    }

    public function store(StorageCollection $storageCollection, array $options): void
    {
        /** @var Backup $backup */
        $backup = $this->backupFactory->createNew();
        $backup->setBackup($storageCollection->getName());
        $backup->setDate(new \DateTime());
        $backup->setName($backup->getDate()->format($options['name_format']));
        
        $dir = $this->fileHelper->mkdir($options['target']);
        $slug = $this->slugify($backup->getName());
        $dir = $this->fileHelper->mkdir($dir . '/' . $slug);

        foreach ($storageCollection->getFiles() as $path) {
            /** @var BackupFile $file */
            $file = $this->fileFactory->createNew();
            $fileName = basename($path);
            
            $this->fileHelper->copy($path, $dir . '/' . $fileName);
            
            $file->setPath($options['target'] . '/' . basename($dir));
            $file->setName($fileName);
            $backup->addFile($file);
        }

        $this->entityManager->persist($backup);
        $this->entityManager->flush();
    }

    public function cleanup(array $options): void
    {
        if ($options['max_age'] !== null) {
            $date = new \DateTime();
            $dateInterval = \DateInterval::createFromDateString($options['max_age']);
            $date->sub($dateInterval);
            $backups = $this->repository->findBetween('date', new \DateTime('0001-01-01'), $date);

            foreach ($backups as $backup) {
                $this->entityManager->remove($backup);
            }

            $this->entityManager->flush();
        }
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'max_age' => null,
            'name_format' => 'Y-m-d H:m:s',
            'target' => 'var/media/backups'
        ]);
    }

    public static function getName(): ?string
    {
        return 'enhavo';
    }

    private function slugify($content, $separator = '-')
    {
        $urlizer = new Urlizer();
        $content = $urlizer->urlize($content, $separator);
        $content  = $urlizer->transliterate($content, $separator);
        return $content;
    }
}
