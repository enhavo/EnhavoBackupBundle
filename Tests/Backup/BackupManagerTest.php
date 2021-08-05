<?php
/*
 * @author blutze-media
 * @since 2020-09-28
 */

namespace Enhavo\Bundle\BackupBundle;


use Enhavo\Bundle\BackupBundle\Backup\Backup;
use Enhavo\Bundle\BackupBundle\Backup\BackupManager;
use Enhavo\Bundle\BackupBundle\Exception\BackupNotFoundException;
use Enhavo\Bundle\BackupBundle\Normalizer\Normalizer;
use Enhavo\Bundle\BackupBundle\Normalizer\Type\ZipNormalizerType;
use Enhavo\Bundle\BackupBundle\Notification\Notification;
use Enhavo\Bundle\BackupBundle\Source\Source;
use Enhavo\Bundle\BackupBundle\Source\SourceCollection;
use Enhavo\Bundle\BackupBundle\Storage\Storage;
use Enhavo\Bundle\BackupBundle\Storage\StorageCollection;
use Enhavo\Bundle\BackupBundle\Utility\FileHelper;
use Enhavo\Component\Type\FactoryInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BackupManagerTest extends TestCase
{
    private function createDependencies()
    {
        $dependencies = new BackupManagerTestDependencies();
        $dependencies->notificationFactory = $this->getMockBuilder(FactoryInterface::class)->getMock();
        $dependencies->sourceFactory = $this->getMockBuilder(FactoryInterface::class)->getMock();
        $dependencies->storageFactory = $this->getMockBuilder(FactoryInterface::class)->getMock();
        $dependencies->normalizerFactory = $this->getMockBuilder(FactoryInterface::class)->getMock();
        $dependencies->fileHelper = $this->getMockBuilder(FileHelper::class)->disableOriginalConstructor()->getMock();
        return $dependencies;
    }

    private function createInstance($dependencies, $config)
    {
        return new BackupManager(
            $dependencies->sourceFactory,
            $dependencies->normalizerFactory,
            $dependencies->storageFactory,
            $dependencies->notificationFactory,
            $dependencies->fileHelper,
            $config
        );
    }


    public function testBackup()
    {
        $config = $this->getConfig();

        $dependencies = $this->createDependencies();
        $storageCollection = new StorageCollection();
        $sourceCollection = new SourceCollection();
        $manager = $this->createInstance($dependencies, $config);

        $dependencies->normalizerFactory->method('create')->willReturnCallback(function () use ($storageCollection) {
            $normalizer = $this->getMockBuilder(Normalizer::class)->disableOriginalConstructor()->getMock();
            $normalizer->expects($this->once())->method('normalize')->willReturn($storageCollection);
            return $normalizer;
        });
        $dependencies->sourceFactory->method('create')->willReturnCallback(function () use ($sourceCollection) {
            $source = $this->getMockBuilder(Source::class)->disableOriginalConstructor()->getMock();
            $source->expects($this->once())->method('collect')->willReturn($sourceCollection);
            return $source;
        });
        $dependencies->storageFactory->method('create')->willReturnCallback(function () {
            $storage = $this->getMockBuilder(Storage::class)->disableOriginalConstructor()->getMock();
            $storage->expects($this->once())->method('store');
            $storage->expects($this->once())->method('cleanup');
            return $storage;
        });
        $dependencies->notificationFactory->method('create')->willReturnCallback(function () {
            $storage = $this->getMockBuilder(Notification::class)->disableOriginalConstructor()->getMock();
            $storage->expects($this->once())->method('notify');
            return $storage;
        });

        $manager->backup('default');

        $this->assertEquals('default', $storageCollection->getName());
    }

    public function testCleanup()
    {
        $config = $this->getConfig();

        $dependencies = $this->createDependencies();
        $manager = $this->createInstance($dependencies, $config);
        $dependencies->normalizerFactory->method('create')->willReturnCallback(function () {
            $normalizer = $this->getMockBuilder(Normalizer::class)->disableOriginalConstructor()->getMock();
            return $normalizer;
        });
        $dependencies->sourceFactory->method('create')->willReturnCallback(function () {
            $source = $this->getMockBuilder(Source::class)->disableOriginalConstructor()->getMock();
            return $source;
        });

        $dependencies->storageFactory->method('create')->willReturnCallback(function () {
            $storage = $this->getMockBuilder(Storage::class)->disableOriginalConstructor()->getMock();
            $storage->expects($this->once())->method('cleanup');
            return $storage;
        });

        $manager->cleanup('default');
    }


    public function testBackupEx()
    {
        $config = $this->getConfig();

        $dependencies = $this->createDependencies();
        $manager = $this->createInstance($dependencies, $config);
        $dependencies->normalizerFactory->method('create')->willReturnCallback(function () {
            $normalizer = $this->getMockBuilder(Normalizer::class)->disableOriginalConstructor()->getMock();
            $normalizer->expects($this->never())->method('normalize');
            return $normalizer;
        });
        $dependencies->sourceFactory->method('create')->willReturnCallback(function () {
            $source = $this->getMockBuilder(Source::class)->disableOriginalConstructor()->getMock();
            $source->expects($this->once())->method('collect')->willThrowException(new \Exception('fail'));
            return $source;
        });

        $dependencies->storageFactory->method('create')->willReturnCallback(function () {
            $storage = $this->getMockBuilder(Storage::class)->disableOriginalConstructor()->getMock();
            $storage->expects($this->never())->method('store');
            return $storage;
        });

        $this->expectExceptionMessage('fail');
        $manager->backup('default');
    }

    public function testCleanupEx()
    {
        $config = $this->getConfig();

        $dependencies = $this->createDependencies();
        $manager = $this->createInstance($dependencies, $config);
        $dependencies->normalizerFactory->method('create')->willReturnCallback(function () {
            $normalizer = $this->getMockBuilder(Normalizer::class)->disableOriginalConstructor()->getMock();
            return $normalizer;
        });
        $dependencies->sourceFactory->method('create')->willReturnCallback(function () {
            $source = $this->getMockBuilder(Source::class)->disableOriginalConstructor()->getMock();
            return $source;
        });

        $dependencies->storageFactory->method('create')->willReturnCallback(function () {
            $storage = $this->getMockBuilder(Storage::class)->disableOriginalConstructor()->getMock();
            $storage->expects($this->once())->method('cleanup')->willThrowException(new \Exception('fail'));
            return $storage;
        });

        $this->expectExceptionMessage('fail');
        $manager->cleanup('default');
    }


    public function testConfig()
    {
        $config = $this->getConfig();

        $dependencies = $this->createDependencies();

        $manager = $this->createInstance($dependencies, $config);


        $this->expectException(BackupNotFoundException::class);
        $manager->backup('missing');
    }

    private function getConfig(): array
    {
        return [
            'default' => [
                'sources' => [
                    'mysql' => [
                        'type' => 'mysql',
                        'url' => 'mysql://root:root@127.0.0.1:3306/enhavo_extensions',
                    ]
                ],
                'normalizer' => [
                    'type' => 'zip'
                ],
                'storages' => [
                    'local' => [
                        'type' => 'local',
                        'target' => 'var/backups/default'
                    ]
                ],
                'notification' => [
                    'type' => 'email',
                    'from' => 'from@enhavo.com',
                    'to' => 'to@enhavo.com'
                ]
            ]
        ];
    }
}

class BackupManagerTestDependencies
{
    /** @var FactoryInterface|MockObject */
    public $sourceFactory;
    /** @var FactoryInterface|MockObject */
    public $normalizerFactory;
    /** @var FactoryInterface|MockObject */
    public $storageFactory;
    /** @var FactoryInterface|MockObject */
    public $notificationFactory;
    /** @var FileHelper|MockObject */
    public $fileHelper;
}
