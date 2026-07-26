<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use InvalidArgumentException;
use JsonException;
use RuntimeException;
use Throwable;

class LegacyJsonImporter
{
    /**
     * @return array{imported: array<string, int>, skipped: array<string, int>, dry_run: bool}
     */
    public function import(string $path, bool $dryRun = false): array
    {
        $payload = $this->readPayload($path);
        $configuredTables = config('legacy_import.tables', []);
        $skipTables = array_fill_keys(config('legacy_import.skip_tables', []), true);
        $tables = $payload['tables'];

        foreach (array_keys($tables) as $table) {
            if (! array_key_exists($table, $configuredTables) && ! isset($skipTables[$table])) {
                throw new InvalidArgumentException("Unknown import table [{$table}].");
            }
        }

        $result = ['imported' => [], 'skipped' => [], 'dry_run' => $dryRun];

        DB::beginTransaction();

        try {
            foreach ($configuredTables as $table => $rules) {
                if (! array_key_exists($table, $tables)) {
                    continue;
                }

                $result['imported'][$table] = $this->importRows($table, $tables[$table], $rules);
            }

            foreach ($skipTables as $table => $_) {
                if (array_key_exists($table, $tables)) {
                    $result['skipped'][$table] = count($tables[$table]);
                }
            }

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (Throwable $exception) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            throw $exception;
        }

        return $result;
    }

    /**
     * @return array{format: string, exported_at: string, tables: array<string, list<array<string, mixed>>>}
     */
    private function readPayload(string $path): array
    {
        $resolvedPath = is_file($path) ? $path : base_path($path);

        if (! is_file($resolvedPath) || ! is_readable($resolvedPath)) {
            throw new InvalidArgumentException("Import file is not readable: {$path}");
        }

        $contents = file_get_contents($resolvedPath);

        if ($contents === false) {
            throw new RuntimeException("Unable to read import file: {$path}");
        }

        try {
            $payload = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new InvalidArgumentException('Import file contains invalid JSON.', previous: $exception);
        }

        if (! is_array($payload)
            || ($payload['format'] ?? null) !== config('legacy_import.format')
            || ! is_string($payload['exported_at'] ?? null)
            || ! is_array($payload['tables'] ?? null)) {
            throw new InvalidArgumentException('Import file does not match the required versioned envelope.');
        }

        foreach ($payload['tables'] as $table => $rows) {
            if (! is_string($table) || ! is_array($rows) || ! array_is_list($rows)) {
                throw new InvalidArgumentException('Every import table must contain a list of rows.');
            }
        }

        return $payload;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @param  array{unique: list<string>, json?: list<string>, redact?: list<string>}  $rules
     */
    private function importRows(string $table, array $rows, array $rules): int
    {
        if (! Schema::hasTable($table)) {
            throw new RuntimeException("Destination table [{$table}] does not exist.");
        }

        $columns = array_fill_keys(Schema::getColumnListing($table), true);
        $uniqueColumns = $rules['unique'];
        $jsonColumns = array_fill_keys($rules['json'] ?? [], true);
        $redactedColumns = $rules['redact'] ?? [];

        foreach ($rows as $index => $row) {
            if (! is_array($row) || array_is_list($row)) {
                throw new InvalidArgumentException("Row {$index} in [{$table}] must be an object.");
            }

            $unknownColumns = array_diff(array_keys($row), array_keys($columns));

            if ($unknownColumns !== []) {
                throw new InvalidArgumentException(
                    "Unknown column(s) in [{$table}]: ".implode(', ', $unknownColumns)
                );
            }

            foreach ($uniqueColumns as $column) {
                if (! array_key_exists($column, $row) || $row[$column] === null || $row[$column] === '') {
                    throw new InvalidArgumentException(
                        "Row {$index} in [{$table}] is missing stable key [{$column}]."
                    );
                }
            }

            foreach ($redactedColumns as $column) {
                if (array_key_exists($column, $columns)) {
                    $row[$column] = null;
                }
            }

            foreach ($jsonColumns as $column => $_) {
                if (array_key_exists($column, $row) && $row[$column] !== null) {
                    $row[$column] = json_encode(
                        $row[$column],
                        JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
                    );
                }
            }

            $identity = array_intersect_key($row, array_fill_keys($uniqueColumns, true));
            DB::table($table)->updateOrInsert($identity, $row);
        }

        return count($rows);
    }
}
