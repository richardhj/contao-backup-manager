<?php

/**
 * This file is part of richardhj/contao-backup-manager.
 *
 * Copyright (c) 2018-2018 Richard Henkenjohann
 *
 * @package   richardhj/contao-backup-manager
 * @author    Richard Henkenjohann <richardhenkenjohann@googlemail.com>
 * @copyright 2018-2018 Richard Henkenjohann
 * @license   https://github.com/richardhj/contao-backup-manager/blob/master/LICENSE LGPL-3.0
 */

namespace Richardhj\ContaoBackupManager\ContaoManager;

use BM\BackupManagerBundle\BMBackupManagerBundle;
use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Config\ConfigInterface;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Contao\ManagerPlugin\Config\ConfigPluginInterface;
use Contao\ManagerPlugin\Dependency\DependentPluginInterface;
use Richardhj\ContaoBackupManagerBundle\RichardhjContaoBackupManagerBundle;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Contao Manager plugin.
 */
class Plugin implements BundlePluginInterface, ConfigPluginInterface, DependentPluginInterface
{

    /**
     * Gets a list of autoload configurations for this bundle.
     *
     * @param ParserInterface $parser
     *
     * @return ConfigInterface[]
     */
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(BMBackupManagerBundle::class)
                ->setLoadAfter([ContaoCoreBundle::class]),
            BundleConfig::create(RichardhjContaoBackupManagerBundle::class)
                ->setLoadAfter([BMBackupManagerBundle::class]),
        ];
    }

    public function registerContainerConfiguration(LoaderInterface $loader, array $managerConfig)
    {
        $loader->load(__DIR__ . '/../Resources/config/backup_manager.yml');
    }

    public function getPackageDependencies()
    {
        return [
            'backup-manager/symfony',
        ];
    }
}
