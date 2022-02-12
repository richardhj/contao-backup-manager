# contao-backup-manager

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE)
[![Dependency Status][ico-dependencies]][link-dependencies]

This is a wrapper extension for <https://github.com/backup-manager/symfony>.

> ⚠️ With the new backup command introduced in Contao 4.13, this bundle will not be maintained anymore. Please refer to <https://docs.contao.org/manual/en/cli/db-backups>.  
> 
> **Upgrade path:**
>  Run `php vendor/bin/contao-console contao:backup:create` instead of `php vendor/bin/contao-console backup-manager:backup contao local -c gzip --filename backup.sql`.
> 
> **Upgrade path for using external storage:**
> 1. Install the Flysytem adapter of interest, e.g., `composer require league/flysystem-sftp:^3.0`.
> 2. Replace the backups storage in Contao as follows:
>     1. Create a DI extension that implements the `ConfigureFilesystemInterface`
>     2. Configure your extenal stoarge, e.g., SFTP storage:
> ```php
>    public function configureFilesystem(FilesystemConfiguration $config): void
>    {
>        $config
>            ->mountAdapter('sftp', [
>                'host' => '%env(DB_STORAGE_HOST)%',
>                'port' => 22,
>                'username' => '%env(DB_STORAGE_USERNAME)%',
>                'password' => '%env(DB_STORAGE_PASSWORD)%',
>                'root' => '/db',
>                'timeout' => 10,
>            ], 'backups', 'backups')
>        ;
>    }
>  ```
>    3. If you don't have an bundle nor extension, refer to <https://docs.contao.org/dev/guides/modify-container-at-compile-time> and replace the filesystem with `(new FilesystemConfig($container))->...`.
>    
> **Upgrade path for using encrypted external storage:**
> - Configure an additional middleware flysystem adapter similar to [alextartan/flysystem-libsodium-adapter](https://github.com/alextartan/flysystem-libsodium-adapter) and use this filesystem as backup storage.

## Install

Via Composer

``` bash
$ composer require richardhj/contao-backup-manager
```

## Usage

### Commands 
#### Database backup

Run `php vendor/bin/contao-console backup-manager:backup contao local -c gzip --filename backup.sql` to create a backup

The dump will be saved within the `/backups` folder in the website root.

#### Database restore

Run `php vendor/bin/contao-console backup-manager:restore contao local backup.sql.gz -c gzip` to restore from a backup.

The database dump will be searched within the `/backups` folder in the website root.

### External storage

You can define external storage for the database dump to upload to. An external SFTP storage, AWS, or many more storages
are available. Please consult the documentation of [backup-manager](https://github.com/backup-manager/symfony) or see 
the example in the section below.

### File encryption

You can encrypt the database dump before it is uploaded on external storage. The encrypted file will be decrypted 
on-the-fly on restore.

**To use file encryption:**
- **Install `alextartan/flysystem-libsodium-adapter`**
- **Configure a `kernel.secret`**

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
encrypt the files on-the-fly. The implementation is adopted from the [official documentation](https://libsodium.gitbook.io/doc/secret-key_cryptography/secretstream). To check the implementation, check the [source code](https://github.com/alextartan/flysystem-libsodium-adapter/blob/master/src/ChunkEncryption/Libsodium.php). To encrypt the files, we use a "password" (`'%env(DB_ENCRYPTION_KEY)%'`) and "salt" (`'%env(kernel.secret)%'`) to [derive a 32-byte encryption key from](https://github.com/richardhj/contao-backup-manager/blob/main/src/Filesystem/EncryptedFilesystem.php#L45). The encryption key must not change in order to be able to decrypt the files. As we use the kernel secret for salting the encryption key, please make sure you have the kernel.secret defined in your parameters.yml.

Note: The files can only be encrypted with the same secret, but the kernel secret should be rotated from time to time,
so this feature is not recommended for long data retention.

### Data Retention

You can configure the data retention:

```yaml
# /config/config.yml

contao_backup_manager:
  purge:
    max_days: 14
    max_files: 4
```

With this config, older files will be deleted automatically on the backup process.

**Important:** There must not be any other files in the configured backup folder, because the files get purged regardless of their file type.


[ico-version]: https://img.shields.io/packagist/v/richardhj/contao-backup-manager.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-LGPL-brightgreen.svg?style=flat-square
[ico-dependencies]: https://www.versioneye.com/php/richardhj:contao-backup-manager/badge.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/richardhj/contao-backup-manager
[link-dependencies]: https://www.versioneye.com/php/richardhj:contao-backup-manager
