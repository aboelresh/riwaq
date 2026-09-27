<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupProduction extends Command
{
    protected $signature   = 'app:setup-production';
    protected $description = 'Run all production setup steps in correct order';

    public function handle(): void
    {
        $this->warn('Starting production setup...');

        // 1. Run migrations
        $this->info('Running migrations...');
        $this->call('migrate', ['--force' => true]);

        // 2. Seed production data
        $this->info('Seeding production data...');
        $this->call('db:seed', [
            '--class' => 'ProductionSeeder',
            '--force' => true,
        ]);

        // 3. Cache config + routes
        $this->info('Caching config and routes...');
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');

        // 4. Storage link
        $this->info('Creating storage link...');
        $this->call('storage:link');

       // 5. Remind about cron
       $this->info('Adding cron job reminder...');
       $this->warn('IMPORTANT: Add this cron entry on the server:');
       $this->warn('* * * * * cd /var/www/codemaster && php artisan schedule:run >> /dev/null 2>&1');

        $this->info('');
        $this->info('Production setup complete!');
        $this->table(
            ['Check', 'Status'],
            [
                ['Migrations',    'Done'],
                ['Seeded',        'Done'],
                ['Config cached', 'Done'],
                ['Route cached',  'Done'],
                ['Storage link',  'Done'],
            ]
        );
    }
}