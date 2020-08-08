<?php

namespace Richardhj\ContaoBackupManager\DependencyInjection\CompilerPass;

use Richardhj\ContaoBackupManager\Filesystem\EncryptedFilesystem;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ProvideFilesystemsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $config = $container->getExtensionConfig('contao_backup_manager');
        $config = array_merge(...$config);

        if (!isset($config['storage'])) {
            return;
        }

        $configStorageDef =  $container->getDefinition('backup_manager.config_storage');
        $configStorage = $configStorageDef->getArgument(0);

        $filesystemDef = $container->getDefinition('backup_manager.filesystems');
        foreach ($config['storage'] as $storageKey => $storageConfig) {
            if ('Encrypted' === $storageConfig['type']) {
                $filesystemDef->addMethodCall('add', [new Reference(EncryptedFilesystem::class)]);

                $configStorage += [
                    $storageKey => $storageConfig
                ];
            }
        }

        $configStorageDef->replaceArgument(0, $configStorage);
    }
}
