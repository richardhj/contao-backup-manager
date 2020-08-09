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

### Commands 
#### Database backup

Run `php vendor/bin/contao-console backup-manager:backup contao local -c gzip --filename backup.sql` to create a backup

The dump will saved within the `/backups` folder in the website root.

#### Database restore

Run `php vendor/bin/contao-console backup-manager:restore contao local backup.sql.gz -c gzip` to restore from a backup.

The database dump will be searched within the `/backups` folder in the website root.

### External storage

You can define external storage for the database dump to upload to. An external SFTP storage, AWS, or many more storages
are available. Please consult the documentation of [backup-manager](https://github.com/backup-manager/symfony) or see 
the example in the section below.

### File encryption

You can encrypt the database dump before it will be uploaded on external storage. The encrypted file will be decrypted on-the-fly on restore.

Example configuration to write encrypted files on an external SFTP storage:

```yaml
# /config/config.yml

contao_backup_manager:
  storage:
    hetzner_enc:
      type: Encrypted
      storage: hetzner
      encryption_key: '%env(DB_ENCRYPTION_KEY)%'
    hetzner:
      type: Sftp
      host: '%env(DB_STORAGE_HOST)%'
      username: '%env(DB_STORAGE_USERNAME)%'
      password: '%env(DB_STORAGE_PASSWORD)%'
      port: 22
      root: '/db'
      timeout: 10
```

```dotenv
# /.env.local

DB_STORAGE_HOST=storage.beispiel.de
DB_STORAGE_USERNAME=user
DB_STORAGE_PASSWORD=pass
DB_ENCRYPTION_KEY=aaabbbcccddd
```

```shell script
php vendor/bin/contao-console backup-manager:backup contao hetzner_enc -c gzip
```

The filesystem utilizes [libsodium's Poly1305](https://libsodium.gitbook.io/doc/advanced/poly1305) algorithm to 
encrypt the files on-the-fly.

To check the implementation, please consult 
https://github.com/alextartan/flysystem-libsodium-adapter/blob/master/src/ChunkEncryption/Libsodium.php

Note: The encryption algorithm might change in the future (only in major version releases), so this feature is 
not recommended for long data retention.

[ico-version]: https://img.shields.io/packagist/v/richardhj/contao-backup-manager.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-LGPL-brightgreen.svg?style=flat-square
[ico-dependencies]: https://www.versioneye.com/php/richardhj:contao-backup-manager/badge.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/richardhj/contao-backup-manager
[link-dependencies]: https://www.versioneye.com/php/richardhj:contao-backup-manager
