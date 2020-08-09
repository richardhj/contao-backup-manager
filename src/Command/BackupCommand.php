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

namespace Richardhj\ContaoBackupManager\Command;

use BackupManager\Filesystems\Destination;
use BackupManager\Manager;
use BM\BackupManagerBundle\Command\BackupCommand as BaseBackupCommand;
use Richardhj\ContaoBackupManager\Procedure\PurgeProcedure;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class BackupCommand extends BaseBackupCommand
{
    private Manager $manager;
    private PurgeProcedure $purgeProcedure;

    public function __construct(Manager $manager, PurgeProcedure $purgeProcedure)
    {
        $this->manager        = $manager;
        $this->purgeProcedure = $purgeProcedure;

        parent::__construct($manager, '');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (null === $filename = $input->getOption('filename')) {
            $filename = (new \DateTime())->format('Y-m-d_H-i-s');
        }

        $destinations = [];
        foreach ($input->getArgument('destinations') as $name) {
            $destinations[] = new Destination($name, $filename);
        }

        // Backup
        $this->manager->makeBackup()->run($input->getArgument('database'), $destinations, $input->getOption('compression'));

        // Purge
        $this->purgeProcedure->run($destinations);

        return 0;
    }
}
