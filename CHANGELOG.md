CHANGELOG
=========

v2.1.0
------
* Add a filesystem decorator that encrypts backups on-the-fly ([#1])

v2.0.0
------
* Changed the directory for local backups form /var/sql to /backups
* Ignore new tl_cron_job table
* Read the database config from the `DATABASE_URL` env variable

v1.0.0
------
* Initial release

[#1]: https://github.com/richardhj/contao-backup-manager/pull/1
