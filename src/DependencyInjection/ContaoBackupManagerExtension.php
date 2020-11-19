<?php

declare(strict_types=1);

/*
 * This file is part of richardhj/contao-backup-manager.
 *
 * Copyright (c) 2018-2020 Richard Henkenjohann
 *
 * @package   richardhj/contao-backup-manager
 * @author    Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright 2018-2020 Richard Henkenjohann
 * @license   https://github.com/richardhj/contao-backup-manager/blob/master/LICENSE LGPL-3.0
 */

namespace Richardhj\ContaoBackupManager\DependencyInjection;

use AlexTartan\Flysystem\Adapter\EncryptionAdapterDecorator;
use Richardhj\ContaoBackupManager\Procedure\PurgeProcedure;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ContaoBackupManagerExtension extends Extension
{
    public function getAlias()
    {
        return 'contao_backup_manager';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();

        $config = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $this->validateStorage($config['storage'] ?? []);

        $container->getDefinition(PurgeProcedure::class)
            ->replaceArgument(1, $config['purge']['max_days'] ?? null)
            ->replaceArgument(2, $config['purge']['max_files'] ?? null);
    }

    /**
     * We want to make sure the correct dependencies are installed for a storage.
     */
    private function validateStorage(array $config)
    {
        $requirements = [
            'Encrypted' => [
                'package' => 'alextartan/flysystem-libsodium-adapter:^1.0',
                'test'    => EncryptionAdapterDecorator::class,
            ],
        ];

        foreach ($config as $key => $storageConfig) {
            $type = $storageConfig['type'];

            if (!\array_key_exists($type, $requirements)) {
                continue;
            }

            if (!class_exists($requirements[$type]['test'])) {
                throw new \LogicException(sprintf('To use the configuration key "%s" in "contao_backup_manager.storage.%s.type" you need to install "%s"', $type, $key, $requirements[$type]['package']));
            }
        }
    }
}
