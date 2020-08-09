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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('contao_backup_manager');
        $rootNode    = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->variableNode('storage')
                    ->validate()
                        ->always(function ($storageConfig) {
                            foreach ($storageConfig as $name => $config) {
                                if (!isset($config['type'])) {
                                    throw new InvalidConfigurationException(sprintf('You must define a "type" for storage "%s"', $name));
                                }

                                if ('Encrypted' === $config['type']) {
                                    $this->validateAuthenticationType(['storage', 'encryption_key'], $config, 'Encrypted');
                                }
                            }

                            return $storageConfig;
                        })
                    ->end()

                ->end() // End storage

            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * Validate that the configuration fragment has the specified keys and none other.
     *
     * @param mixed $typeName
     */
    private function validateAuthenticationType(array $expected, array $actual, $typeName)
    {
        unset($actual['type']);
        $actual = array_keys($actual);

        if (empty(array_diff($actual, $expected))) {
            return;
        }

        throw new InvalidConfigurationException(sprintf('Storage type "%s" received invalid key "%s". Please choose one of "%s".', $typeName, implode(', ', $expected), implode(', ', $actual)));
    }
}
