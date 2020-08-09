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

namespace Richardhj\ContaoBackupManager\Procedure;

use BackupManager\Filesystems\Destination;
use BackupManager\Filesystems\FilesystemProvider;
use BackupManager\Procedures\Sequence;
use BackupManager\Tasks\Storage\DeleteFile;

class PurgeProcedure
{
    private FilesystemProvider $filesystems;

    private ?int $maxDays;
    private ?int $maxFiles;

    public function __construct(FilesystemProvider $filesystems, ?int $maxDays, ?int $maxFiles)
    {
        $this->filesystems = $filesystems;
        $this->maxDays     = $maxDays;
        $this->maxFiles    = $maxFiles;
    }

    /**
     * @param Destination[] $destinations
     */
    public function run(array $destinations)
    {
        $this->removeMaxDays($destinations);
        $this->removeMaxCountOldest($destinations);
    }

    private function removeMaxCountOldest(array $destinations): void
    {
        if (!$this->maxFiles) {
            return;
        }

        $sequence = new Sequence();

        foreach ($destinations as $destination) {
            $filesystem = $this->filesystems->get($destination->destinationFilesystem());

            $contents = $filesystem->listContents();
            usort($contents, static fn (array $obj1, array $obj2) => $obj2['timestamp'] - $obj1['timestamp']);

            foreach (\array_slice($contents, $this->maxFiles) as $object) {
                $sequence->add(new DeleteFile($filesystem, $object['path']));
            }
        }

        $sequence->execute();
    }

    private function removeMaxDays(array $destinations): void
    {
        if (!$this->maxDays) {
            return;
        }

        $sequence = new Sequence();

        $time = time();
        foreach ($destinations as $destination) {
            $filesystem = $this->filesystems->get($destination->destinationFilesystem());

            $contents = $filesystem->listContents();
            foreach ($contents as $object) {
                if (($time - $object['timestamp']) / (60 * 60 * 24) > $this->maxDays) {
                    $sequence->add(new DeleteFile($filesystem, $object['path']));
                }
            }
        }

        $sequence->execute();
    }
}
