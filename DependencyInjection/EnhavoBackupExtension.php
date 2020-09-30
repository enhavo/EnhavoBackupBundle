<?php
/*
 * @author blutze-media
 * @since 2020-09-23
 */

namespace Enhavo\Bundle\BackupBundle\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class EnhavoBackupExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $config);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $this->registerResources('enhavo_backup', $config['driver'], $config['resources'], $container);

        $container->setParameter('enhavo_backup.tmp_dir', $config['tmp_dir']);
        $container->setParameter('enhavo_backup.backups', $config['backups']);

        $configFiles = [
            'services/services.yaml',
            'services/source.yaml',
            'services/normalizer.yaml',
            'services/storage.yaml',
            'services/notification.yaml',
        ];

        foreach ($configFiles as $configFile) {
            $loader->load($configFile);
        }
    }

    public function prepend(ContainerBuilder $container)
    {
        $configs = Yaml::parse(file_get_contents(__DIR__.'/../Resources/config/app/config.yaml'));
        foreach($configs as $name => $config) {
            $container->prependExtensionConfig($name, $config);
        }
    }
}
