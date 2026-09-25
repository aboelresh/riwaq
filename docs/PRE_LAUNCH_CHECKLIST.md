\# Pre-Launch Security Checklist — Code Master



\## Run Automated Audit

```bash

php artisan app:security-audit

```

All items must pass or be consciously accepted as warnings.



\## Environment

\- \[ ] `APP\_ENV=production`

\- \[ ] `APP\_DEBUG=false`

\- \[ ] `APP\_KEY` generated (`php artisan key:generate`)

\- \[ ] `JWT\_SECRET` generated (`php artisan jwt:secret`)

\- \[ ] `DB\_PASSWORD` is strong (16+ chars, mixed)

\- \[ ] `REDIS\_PASSWORD` is set

\- \[ ] `MAIL\_MAILER=smtp` with real credentials

\- \[ ] `CORS\_ALLOWED\_ORIGINS` restricted to your domains



\## Database

\- \[ ] MySQL 8.4 running

\- \[ ] `php artisan migrate --force` ran successfully

\- \[ ] `php artisan db:seed --class=ProductionSeeder` ran once

\- \[ ] Admin password changed from default



\## Security

\- \[ ] HTTPS configured (SSL certificate)

\- \[ ] Security headers verified (check https://securityheaders.com)

\- \[ ] Rate limiting tested (5 login attempts = 429)

\- \[ ] JWT tokens expire correctly (60 min default)

\- \[ ] No sensitive data in logs (verified `storage/logs/`)



\## Performance

\- \[ ] `php artisan config:cache`

\- \[ ] `php artisan route:cache`

\- \[ ] `php artisan view:cache`

\- \[ ] Queue worker running (Supervisor configured)

\- \[ ] Redis connected for cache + queue



\## Final Verification

\- \[ ] `GET /api/v1/health` returns `healthy: true`

\- \[ ] Login works with correct credentials

\- \[ ] Login fails with wrong credentials (401)

\- \[ ] Admin route rejects learner token (403)

\- \[ ] Unauth request returns JSON 401 (not HTML)

\- \[ ] Swagger UI accessible at `/api/documentation`

\- \[ ] PHPUnit: `php artisan test` — 86 tests pass

