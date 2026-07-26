<?php

namespace App\Console\Commands;

use App\Services\LegacyJsonImporter;
use Illuminate\Console\Command;
use Throwable;

class ImportLegacyJson extends Command
{
    protected $signature = 'legacy:import-json
        {path : Absolute path or backend-relative path to the versioned JSON export}
        {--dry-run : Validate and execute inside a rolled-back transaction}';

    protected $description = 'Import a versioned legacy data export into the Laravel database';

    public function handle(LegacyJsonImporter $importer): int
    {
        try {
            $result = $importer->import(
                (string) $this->argument('path'),
                (bool) $this->option('dry-run')
            );
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        foreach ($result['imported'] as $table => $count) {
            $this->line("Imported {$table}: {$count}");
        }

        foreach ($result['skipped'] as $table => $count) {
            $this->warn("Skipped ephemeral {$table}: {$count}");
        }

        if ($result['dry_run']) {
            $this->components->info('Dry run complete; all database writes were rolled back.');
        } else {
            $this->components->info('Legacy import committed successfully.');
        }

        return self::SUCCESS;
    }
}
