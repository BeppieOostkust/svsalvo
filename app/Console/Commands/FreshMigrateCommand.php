<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class FreshMigrateCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'migrate:fresh-safe {--seed : Seed the database after migration}';

    /**
     * The console command description.
     */
    protected $description = 'Drop all tables safely (handling foreign keys) and re-run all migrations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (!$this->confirm('This will drop all tables and re-run migrations. Are you sure?')) {
            $this->info('Migration cancelled.');
            return;
        }

        $this->info('Disabling foreign key checks...');
        
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        try {
            // Get all table names
            $tables = collect(DB::select('SHOW TABLES'))
                ->map(function ($table) {
                    $key = 'Tables_in_' . config('database.connections.mysql.database');
                    return $table->$key;
                });

            $this->info('Dropping tables...');
            
            // Drop each table
            foreach ($tables as $tableName) {
                DB::statement("DROP TABLE IF EXISTS `{$tableName}`");
                $this->line("Dropped table: {$tableName}");
            }

        } finally {
            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->info('Re-enabled foreign key checks.');
        }

        $this->info('Running migrations...');
        
        // Run migrations
        $this->call('migrate');

        if ($this->option('seed')) {
            $this->info('Seeding database...');
            $this->call('db:seed');
        }

        $this->info('Fresh migration completed successfully!');
    }
}
