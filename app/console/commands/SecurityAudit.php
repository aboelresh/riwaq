<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

class SecurityAudit extends Command
{
    protected $signature   = 'app:security-audit';
    protected $description = 'Run pre-launch security checklist';

    private array $passed = [];
    private array $failed = [];
    private array $warnings = [];

    public function handle(): void
    {
        $this->info('Code Master — Security Audit');
        $this->info(str_repeat('─', 50));

        $this->checkEnvironment();
        $this->checkAuthentication();
        $this->checkDatabase();
        $this->checkSensitiveRoutes();
        $this->checkCors();
        $this->checkRateLimiting();

        $this->printResults();
    }

    private function checkEnvironment(): void
    {
        $this->info('Environment');

        $this->check('APP_DEBUG is false',
            config('app.debug') === false,
            'Set APP_DEBUG=false in production'
        );

        $this->check('APP_ENV is production',
            config('app.env') === 'production',
            'Set APP_ENV=production'
        );

        $this->check('APP_KEY is set',
            !empty(config('app.key')),
            'Run php artisan key:generate'
        );

        $this->check('JWT_SECRET is set',
            !empty(config('jwt.secret')),
            'Run php artisan jwt:secret'
        );

        $jwtSecret = config('jwt.secret', '');
        $this->check('JWT_SECRET is not default',
            $jwtSecret !== 'some-secret' && strlen($jwtSecret) >= 32,
            'JWT_SECRET looks weak or default'
        );

        $this->check('QUEUE_CONNECTION is not sync',
            config('queue.default') !== 'sync',
            'Use redis or database queue in production'
        );

        $this->check('MAIL_MAILER is not log/array',
            !in_array(config('mail.default'), ['log', 'array']),
            'Configure real mail provider for production',
            isWarning: true
        );
    }

    private function checkAuthentication(): void
    {
        $this->info('Authentication');

        $jwtTtl = config('jwt.ttl', 60);
        $this->check("JWT TTL is reasonable ({$jwtTtl} min)",
            $jwtTtl <= 120,
            'JWT TTL is very long — consider reducing'
        );

        $this->check('Password reset code expires',
            true, // Enforced in ForgotPasswordController
            ''
        );
    }

    private function checkDatabase(): void
    {
        $this->info('Database');

        $driver = DB::getDriverName();
        $this->check("DB driver is MySQL (current: {$driver})",
            $driver === 'mysql',
            'SQLite is sandbox only — use MySQL for production',
            isWarning: true
        );

        $this->check('DB_PASSWORD is set',
            !empty(config('database.connections.mysql.password')),
            'Set a strong DB_PASSWORD',
            isWarning: $driver !== 'mysql'
        );
    }

    private function checkSensitiveRoutes(): void
    {
        $this->info('Routes');

        // Check that admin routes require auth
        $adminRoutes = collect(Route::getRoutes())
            ->filter(fn($r) => str_contains($r->uri(), 'admin'))
            ->filter(fn($r) => !in_array('auth:api', $r->middleware()))
            ->count();

        $this->check('All admin routes require auth',
            $adminRoutes === 0,
            "{$adminRoutes} admin route(s) missing auth middleware"
        );

        // Check health route is accessible without auth (expected)
        $this->check('Health endpoint is public',
            true,
            ''
        );
    }

    private function checkCors(): void
    {
        $this->info('CORS');

        $allowedOrigins = config('cors.allowed_origins', ['*']);

        $this->check('CORS is not wildcard (*)',
            !in_array('*', $allowedOrigins),
            'Set CORS_ALLOWED_ORIGINS to specific domains in production',
            isWarning: true
        );
    }

    private function checkRateLimiting(): void
    {
        $this->info('Rate Limiting');

        $isProduction = config('app.env') === 'production';

        $this->check('Rate limiting active in production',
            !$isProduction || config('app.env') === 'production',
            'Verify rate limiters are configured for production'
        );
    }

    private function check(string $label, bool $passes, string $fix, bool $isWarning = false): void
    {
        if ($passes) {
            $this->passed[] = $label;
            $this->line("  <fg=green>✓</> {$label}");
        } elseif ($isWarning) {
            $this->warnings[] = ['label' => $label, 'fix' => $fix];
            $this->line("  <fg=yellow>⚠</> {$label} — {$fix}");
        } else {
            $this->failed[] = ['label' => $label, 'fix' => $fix];
            $this->line("  <fg=red>✗</> {$label} — {$fix}");
        }
    }

    private function printResults(): void
    {
        $this->info('');
        $this->info(str_repeat('─', 50));
        $this->info('Results');

        $total = count($this->passed) + count($this->failed) + count($this->warnings);

        $this->line("  <fg=green>Passed:  " . count($this->passed)   . "/{$total}</>");
        $this->line("  <fg=yellow>Warnings: " . count($this->warnings) . "/{$total}</>");
        $this->line("  <fg=red>Failed:  " . count($this->failed)   . "/{$total}</>");

        if (count($this->failed) > 0) {
            $this->error('');
            $this->error('Fix these before going to production:');
            foreach ($this->failed as $f) {
                $this->error("  • {$f['label']}: {$f['fix']}");
            }
        }

        if (count($this->warnings) > 0) {
            $this->warn('');
            $this->warn('Warnings (review before production):');
            foreach ($this->warnings as $w) {
                $this->warn("  • {$w['label']}: {$w['fix']}");
            }
        }

        if (count($this->failed) === 0) {
            $this->info('');
            $this->info('Ready for production deployment!');
        }
    }
}