CHANGELOG
=========

v2.2.0
------

* Made the `alextartan/flysystem-libsodium-adapter` package optional, so install if needed

v2.1.3
------

* Excldue `tl_search_term` table ([#3])
* Do not exclude `tl_version` table

v2.1.2
------
* Derive a 32-bytes key from the encryption password

v2.1.1
------
* Fix a warning in the Extension configuration processign

v2.1.0
------
* Add a filesystem decorator that encrypts backups on-the-fly ([#1])
* Add config to automatically purge old backups ([#2])

v2.0.0
------
* Changed the directory for local backups form /var/sql to /backups
* Ignore new tl_cron_job table
* Read the database config from the `DATABASE_URL` env variable

v1.0.0
------
* Initial release

[#1]: https://github.com/richardhj/contao-backup-manager/pull/1
[#2]: https://github.com/richardhj/contao-backup-manager/pull/2
[#2]: https://github.com/richardhj/contao-backup-manager/pull/3
