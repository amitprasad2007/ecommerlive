<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
class MigrateSingle extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:single {migration}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a single migration file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $migration = $this->argument('migration');
        $migrationPath = database_path('migrations/' . $migration . '.php');

        if (!file_exists($migrationPath)) {
            $this->error("Migration file not found: {$migrationPath}");
            return 1;
        }

        // Run the migration
        Artisan::call('migrate', [
            '--path' => 'database/migrations/' . $migration . '.php'
        ]);

        $this->info("Migration {$migration} run successfully.");
        return 0;
    }
}
