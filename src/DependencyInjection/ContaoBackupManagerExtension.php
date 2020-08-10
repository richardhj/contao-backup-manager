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
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        $container->getDefinition(PurgeProcedure::class)
            ->replaceArgument(1, $config['purge']['max_days'] ?? null)
            ->replaceArgument(2, $config['purge']['max_files'] ?? null)
        ;
    }
}
