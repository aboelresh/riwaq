# Pre-Launch Deployment Checklist — Code Master v1.0.0

## 1. Server Requirements
- [ ] Ubuntu 24.04 LTS
- [ ] PHP 8.2-fpm + extensions (mysql, redis, mbstring, xml, curl, zip, gd, bcmath, intl)
- [ ] MySQL 8.4 running
- [ ] Redis 7 running
- [ ] Nginx running
- [ ] Supervisor running
- [ ] Composer installed globally
- [ ] SSL certificate (Let's Encrypt via certbot)

## 2. Application Setup
```bash
cd /var/www/codemaster
git clone https://github.com/your-org/code-master.git .
cp .env.production.example .env
nano .env  # Fill all CHANGE_ME values
php artisan app:setup-production
```

- [ ] APP_KEY generated
- [ ] JWT_SECRET generated
- [ ] All CHANGE_ME values replaced
- [ ] Storage link created
- [ ] Config/route/view cached

## 3. Environment Verification
```bash
php artisan app:security-audit
```

- [ ] APP_DEBUG=false 
- [ ] APP_ENV=production 
- [ ] APP_KEY set 
- [ ] JWT_SECRET strong 
- [ ] DB_PASSWORD strong 
- [ ] CORS_ALLOWED_ORIGINS specific domains 
- [ ] MAIL_MAILER=smtp with real credentials 

## 4. Database
- [ ] MySQL database created with utf8mb4_unicode_ci
- [ ] Migrations ran successfully (no errors)
- [ ] ProductionSeeder ran (plans + admin + default org)
- [ ] Admin password changed from default
- [ ] Verify: `GET /api/v1/health` → database.status = "ok"

## 5. Queue Workers
```bash
# Copy supervisor config
cp docker/supervisor/codemaster.conf /etc/supervisor/conf.d/
supervisorctl reread
supervisorctl update
supervisorctl start codemaster:*
supervisorctl status
```

- [ ] Workers running (codemaster-worker-default x2)
- [ ] Email worker running (codemaster-worker-emails x1)
- [ ] Logs writing to storage/logs/worker-*.log

## 6. Scheduler (Cron)
```bash
crontab -e -u www-data
# Add:
* * * * * cd /var/www/codemaster && php artisan schedule:run >> /dev/null 2>&1
```

- [ ] Cron entry added
- [ ] Test: `php artisan schedule:run` runs without errors

## 7. Nginx + SSL
```bash
cp docker/nginx/production.conf /etc/nginx/sites-available/codemaster
ln -s /etc/nginx/sites-available/codemaster /etc/nginx/sites-enabled/
certbot --nginx -d api.codemaster.com
nginx -t && systemctl reload nginx
```

- [ ] HTTP → HTTPS redirect working
- [ ] SSL certificate valid
- [ ] Security headers present (check https://securityheaders.com)
- [ ] HSTS header set

## 8. Final Smoke Tests (on production)
```bash
# Health check
curl https://api.codemaster.com/api/v1/health

# Auth
curl -X POST https://api.codemaster.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@codemaster.com","password":"your_password"}'
```

- [ ] `GET /api/v1/health` → healthy: true
- [ ] `POST /api/v1/auth/login` → token returned
- [ ] `POST /api/v1/auth/login` (wrong password) → 401
- [ ] `GET /api/v1/admin/tracks` (learner token) → 403
- [ ] `GET /api/v1/profile` (no token) → 401 JSON (not HTML)
- [ ] `GET /api/v1/documentation` → Swagger UI loads

## 9. PHPUnit on Production Server
```bash
php artisan test --env=testing
```

- [ ] 127 tests / 398 assertions — all pass

## 10. Monitoring
- [ ] Logs rotating (`storage/logs/laravel.log`, `errors.log`, `audit.log`)
- [ ] Server monitoring set up (uptime, memory, disk)
- [ ] Alert on `/api/v1/health` returning 503

##  Go/No-Go Decision
**GO** if: Security audit 9+/15, all smoke tests pass, queue workers running, SSL valid.
**NO-GO** if: Any smoke test fails, health check fails, or APP_DEBUG=true.