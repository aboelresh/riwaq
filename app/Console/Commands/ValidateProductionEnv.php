<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ValidateProductionEnv extends Command
{
    protected $signature   = 'app:validate-production-env';
    protected $description = 'Validate that all required production environment variables are set correctly';

    public function handle(): int
    {
        $this->info('Validating production environment...');
        $failures = [];
        $warnings = [];

        // Critical — blocks deployment
        if (config('app.debug') === true) {
            $failures[] = 'APP_DEBUG must be false in production';
        }

        if (empty(config('app.key'))) {
            $failures[] = 'APP_KEY is not set — run: php artisan key:generate';
        }

        $jwtSecret = config('jwt.secret', '');
        if (empty($jwtSecret)) {
            $failures[] = 'JWT_SECRET is not set — run: php artisan jwt:secret';
        } elseif (strlen($jwtSecret) < 32) {
            $failures[] = 'JWT_SECRET is too short (minimum 32 characters)';
        }

        if (config('app.env') !== 'production') {
            $failures[] = 'APP_ENV must be "production"';
        }

        // Warnings — should be reviewed
        $allowedOrigins = config('cors.allowed_origins', ['*']);
        if (in_array('*', $allowedOrigins)) {
            $warnings[] = 'CORS_ALLOWED_ORIGINS is set to wildcard (*) — restrict to specific domains';
        }

        if (config('queue.default') === 'sync') {
            $warnings[] = 'QUEUE_CONNECTION is "sync" — use "redis" or "database" in production';
        }

        if (in_array(config('mail.default'), ['log', 'array'])) {
            $warnings[] = 'MAIL_MAILER is "' . config('mail.default') . '" — configure real SMTP for production';
        }

        if (config('database.default') === 'sqlite') {
            $warnings[] = 'DB_CONNECTION is "sqlite" — use MySQL in production';
        }

        // Results
        if (!empty($failures)) {
            $this->error('CRITICAL FAILURES — deployment blocked:');
            foreach ($failures as $f) {
                $this->error("  ✗ {$f}");
            }
        }

        if (!empty($warnings)) {
            $this->warn('WARNINGS — review before deployment:');
            foreach ($warnings as $w) {
                $this->warn("  ⚠ {$w}");
            }
        }

        if (empty($failures) && empty($warnings)) {
            $this->info('All production environment checks passed.');
            return Command::SUCCESS;
        }

        if (empty($failures)) {
            $this->info('No critical failures — warnings above should be reviewed.');
            return Command::SUCCESS;
        }

        return Command::FAILURE; // Exit 1 — blocks CI
    }
}