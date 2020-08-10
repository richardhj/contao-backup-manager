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
    private string $secret;

    public function __construct(FilesystemProvider $filesystems, string $secret)
    {
        $this->filesystems = $filesystems;
        $this->secret      = $secret;
    }

    public function handles($type)
    {
        return 'encrypted' === strtolower($type);
    }

    public function get(array $config)
    {
        $adapter = $this->filesystems->get($config['storage'])->getAdapter();

        // Derive key from password using the same salt (kernel.secret) for each message
        $key = sodium_crypto_pwhash(
            SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_KEYBYTES,
            $config['encryption_key'],
            substr($this->secret, 0, SODIUM_CRYPTO_PWHASH_SALTBYTES),
            SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE,
            SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE
        );

        $adapterDecorator = new EncryptionAdapterDecorator($adapter, Libsodium::factory($key, 4096));

        return new Flysystem($adapterDecorator);
    }
}
