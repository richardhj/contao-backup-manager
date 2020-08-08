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

namespace Richardhj\ContaoBackupManager;

use Richardhj\ContaoBackupManager\DependencyInjection\CompilerPass\ProvideFilesystemsPass;
use Richardhj\ContaoBackupManager\DependencyInjection\ContaoBackupManagerExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RichardhjContaoBackupManagerBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ProvideFilesystemsPass());
    }

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new ContaoBackupManagerExtension();
        }

        return $this->extension;
    }
}
