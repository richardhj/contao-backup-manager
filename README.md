# contao-backup-manager

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Dependency Status][ico-dependencies]][link-dependencies]

This is a wrapper extension for <https://github.com/backup-manager/symfony>.

## Install

Via Composer

``` bash
$ composer require richardhj/contao-backup-manager
```

## Usage

### Database backup

Run `php vendor/bin/contao-console backup-manager:backup contao local -c gzip --filename backup.sql` to create a backup

The dump will saved within the `/backups` folder in the website root.

### Database restore

Run `php vendor/bin/contao-console backup-manager:restore contao local backup.sql.gz -c gzip` to restore from a backup.

The database dump will be searched within the `/backups` folder in the website root.

[ico-version]: https://img.shields.io/packagist/v/richardhj/contao-backup-manager.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-LGPL-brightgreen.svg?style=flat-square
[ico-dependencies]: https://www.versioneye.com/php/richardhj:contao-backup-manager/badge.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/richardhj/contao-backup-manager
[link-dependencies]: https://www.versioneye.com/php/richardhj:contao-backup-manager
