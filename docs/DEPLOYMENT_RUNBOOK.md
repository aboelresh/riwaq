\# Deployment Runbook — Code Master



\## Pre-flight Checklist (قبل أي deployment)



\- \[ ] `php artisan test` كلهم pass

\- \[ ] `APP\_DEBUG=false` في .env.production

\- \[ ] JWT\_SECRET مش الـ default

\- \[ ] DB backup عمل بنجاح

\- \[ ] CORS\_ALLOWED\_ORIGINS محدد (مش \*)

\- \[ ] Postman Full Test Suite اشتغلت على staging



\## First Deployment (أول مرة)



```bash

\# 1. Clone repo

git clone https://github.com/your-org/code-master.git /var/www/codemaster

cd /var/www/codemaster



\# 2. Setup environment

cp .env.production.example .env

nano .env  # Fill in all values



\# 3. Install dependencies

composer install --no-dev --optimize-autoloader



\# 4. Generate keys

php artisan key:generate

php artisan jwt:secret



\# 5. Database setup

php artisan migrate --force

\# NOTE: Do NOT run db:seed on production — seeder is for development only



\# 6. Storage setup

php artisan storage:link



\# 7. Cache for performance

php artisan config:cache

php artisan route:cache

php artisan view:cache



\# 8. Set permissions

chown -R www-data:www-data /var/www/codemaster

chmod -R 755 /var/www/codemaster/storage

chmod -R 755 /var/www/codemaster/bootstrap/cache



\# 9. Setup queue worker (Supervisor)

sudo nano /etc/supervisor/conf.d/codemaster-worker.conf

```



Supervisor config:

```ini

\[program:codemaster-worker]

process\_name=%(program\_name)s\_%(process\_num)02d

command=php /var/www/codemaster/artisan queue:work redis --tries=3 --backoff=60

autostart=true

autorestart=true

user=www-data

numprocs=2

redirect\_stderr=true

stdout\_logfile=/var/www/codemaster/storage/logs/worker.log

```



```bash

sudo supervisorctl reread

sudo supervisorctl update

sudo supervisorctl start codemaster-worker:\*

```



\## Rollback Procedure



```bash

\# 1. Revert to previous release

git log --oneline -5  # Find previous commit hash

git checkout <previous-hash>



\# 2. Rollback database (if migration ran)

php artisan migrate:rollback



\# 3. Restart services

php artisan config:cache

sudo systemctl reload php8.2-fpm

sudo supervisorctl restart codemaster-worker:\*

```



\## Health Check



```bash

curl https://api.codemaster.com/api/v1/health

```



Expected: `{"healthy": true, "checks": {"database": {"status": "ok"}, ...}}`



\## Emergency: Take Down \& Restore



```bash

\# Maintenance mode (returns 503 to all API clients)

php artisan down



\# Restore

php artisan up

```

