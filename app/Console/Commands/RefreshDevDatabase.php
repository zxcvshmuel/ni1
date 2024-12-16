<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RefreshDevDatabase extends Command
{
    protected $signature = 'dev:refresh {--force : Force the operation without confirmation}';
    protected $description = 'Refresh the development database with test data';

    public function handle(): int
    {
        if (!app()->environment('local', 'development')) {
            $this->error('This command can only be run in the local/development environment!');
            return 1;
        }

        if (!$this->option('force') && !$this->confirm('This will DELETE ALL DATA from your database. Are you sure?')) {
            $this->info('Operation cancelled.');
            return 0;
        }

        $this->info('Starting database refresh...');

        // Drop all tables
        $this->info('Dropping all tables...');
        Schema::disableForeignKeyConstraints();
        foreach(DB::select('SHOW TABLES') as $table) {
            $table_array = get_object_vars($table);
            Schema::drop($table_array[key($table_array)]);
        }
        Schema::enableForeignKeyConstraints();

        // Run migrations
        $this->info('Running migrations...');
        $this->call('migrate');

        // Clear cache
        $this->info('Clearing cache...');
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('view:clear');

        // Seed the database
        $this->info('Seeding database with test data...');
        $this->call('db:seed');

        $this->info('Development database has been refreshed!');
        $this->info('Default admin user: admin@example.com / password');
        $this->info('Default test user: test@example.com / password');

        return 0;
    }
}