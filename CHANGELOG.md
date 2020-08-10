CHANGELOG
=========

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
