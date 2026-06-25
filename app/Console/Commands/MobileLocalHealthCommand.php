<?php

namespace App\Console\Commands;

use App\Services\MobileLocal\MobileLocalStorageHealthCheck;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('mobile:local-health')]
#[Description('Verify NativePHP local SQLite read and write access.')]
class MobileLocalHealthCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(MobileLocalStorageHealthCheck $healthCheck): int
    {
        $report = $healthCheck->run();

        $this->line("Connection: {$report->connection}");
        $this->line("Database: {$report->databasePath}");
        $this->line("Migrations: {$report->migrationPath}");

        if (! $report->ok) {
            $this->error($report->message);

            return self::FAILURE;
        }

        $this->info($report->message);

        return self::SUCCESS;
    }
}
