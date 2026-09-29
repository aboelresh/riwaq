\# Backup Strategy — Riwaq Backend



\## Production Database

\- Engine: MySQL 8.4

\- Database name: Riwaq\_production



\## Backup Schedule



\### Daily (incremental)

```bash

mysqldump -u root -p Riwaq\_production \\

&#x20; --single-transaction \\

&#x20; --routines \\

&#x20; --triggers \\

&#x20; | gzip > /backups/daily/Riwaq\_$(date +%Y%m%d).sql.gz

```



\### Weekly (full)

```bash

mysqldump -u root -p Riwaq\_production \\

&#x20; --single-transaction \\

&#x20; --all-databases \\

&#x20; | gzip > /backups/weekly/Riwaq\_full\_$(date +%Y%m%d).sql.gz

```



\## Retention Policy

\- Daily backups: keep 7 days

\- Weekly backups: keep 4 weeks

\- Monthly backups: keep 6 months



\## Restore Procedure

```bash

\# 1. Stop the application

php artisan down



\# 2. Drop and recreate the database

mysql -u root -p -e "DROP DATABASE IF EXISTS Riwaq\_production; CREATE DATABASE Riwaq\_production CHARACTER SET utf8mb4 COLLATE utf8mb4\_unicode\_ci;"



\# 3. Restore from backup

gunzip -c /backups/daily/Riwaq\_YYYYMMDD.sql.gz | mysql -u root -p Riwaq\_production



\# 4. Verify

mysql -u root -p Riwaq\_production -e "SHOW TABLES;"



\# 5. Restart

php artisan up

```



\## Storage Location

\- Primary: same server /backups/ (RAID)

\- Secondary: S3 bucket s3://Riwaq-backups/ (auto-sync daily)



\## Soft Delete Retention

Teams are soft-deleted (deleted\_at set, data preserved).

Run this weekly to permanently delete teams older than 30 days:

```bash

php artisan schedule:run

\# Scheduler calls: Team::onlyTrashed()->where('deleted\_at', '<', now()->subDays(30))->forceDelete()

```



\## Production DB Config (.env.production)



DB\_CONNECTION=mysql

DB\_HOST=127.0.0.1

DB\_PORT=3306

DB\_DATABASE=Riwaq\_production

DB\_USERNAME=Riwaq\_user

DB\_PASSWORD=STRONG\_PASSWORD\_HERE

DB\_CHARSET=utf8mb4

DB\_COLLATION=utf8mb4\_unicode\_ci





Note: utf8mb4\_unicode\_ci collation makes email lookups case-insensitive on MySQL,

which means the LOWER(email) workaround in LoginController works correctly in

both SQLite (sandbox) and MySQL (production).

