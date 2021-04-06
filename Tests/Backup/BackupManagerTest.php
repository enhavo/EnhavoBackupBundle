<?php
/*
 * @author blutze-media
 * @since 2020-09-28
 */

namespace Enhavo\Bundle\BackupBundle;


use Enhavo\Bundle\BackupBundle\Backup\BackupManager;
use Enhavo\Bundle\BackupBundle\Exception\BackupNotFoundException;
use Enhavo\Bundle\BackupBundle\Utility\FileHelper;
use Enhavo\Component\Type\FactoryInterface;
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

    public function testConfig()
    {
        $config = [
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

        $dependencies = $this->createDependencies();

        $manager = $this->createInstance($dependencies, $config);


        $this->expectException(BackupNotFoundException::class);
        $manager->backup('missing');
    }
}

class BackupManagerTestDependencies
{
    public $sourceFactory;
    public $normalizerFactory;
    public $storageFactory;
    public $notificationFactory;
    public $fileHelper;
}
