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

namespace Richardhj\ContaoBackupManager\Filesystem;

use AlexTartan\Flysystem\Adapter\ChunkEncryption\Libsodium;
use AlexTartan\Flysystem\Adapter\EncryptionAdapterDecorator;
use BackupManager\Filesystems\Filesystem;
use BackupManager\Filesystems\FilesystemProvider;
use League\Flysystem\Filesystem as Flysystem;

class EncryptedFilesystem implements Filesystem
{
    private FilesystemProvider $filesystems;

    public function __construct(FilesystemProvider $filesystems)
    {
        $this->filesystems = $filesystems;
    }

    public function handles($type)
    {
        return 'encrypted' === strtolower($type);
    }

    public function get(array $config)
    {
        $adapter = $this->filesystems->get($config['storage'])->getAdapter();

        $encryption = Libsodium::factory($config['encryption_key'], 4096);

        $adapterDecorator = new EncryptionAdapterDecorator($adapter, $encryption);

        return new Flysystem($adapterDecorator);
    }
}
