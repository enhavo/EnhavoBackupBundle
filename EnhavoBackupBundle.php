<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle;

use Enhavo\Bundle\BackupBundle\Normalizer\Normalizer;
use Enhavo\Bundle\BackupBundle\Notification\Notification;
use Enhavo\Bundle\BackupBundle\Source\Source;
use Enhavo\Bundle\BackupBundle\Storage\Storage;
use Enhavo\Component\Type\TypeCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EnhavoBackupBundle extends Bundle
{
    public static function getSupportedDrivers()
    {
        return ['doctrine/orm'];
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new TypeCompilerPass('BackupSource', 'enhavo_backup.source', Source::class));
        $container->addCompilerPass(new TypeCompilerPass('BackupNormalizer', 'enhavo_backup.normalizer', Normalizer::class));
        $container->addCompilerPass(new TypeCompilerPass('BackupStorage', 'enhavo_backup.storage', Storage::class));
        $container->addCompilerPass(new TypeCompilerPass('BackupNotification', 'enhavo_backup.notification', Notification::class));
    }
}
