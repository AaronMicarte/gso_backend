<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportGsoDb extends Command
{
    protected $signature = 'gso:import';
    protected $description = 'Import database/import/gso_db.sql into the current database';

    public function handle()
    {
        $path = database_path('import/gso_db.sql');

        if (!file_exists($path)) {
            $this->error("File not found: {$path}");
            return 1;
        }

        $sql = file_get_contents($path);

        // Strip CREATE DATABASE / USE statements — Aiven already provides defaultdb
        $sql = preg_replace('/^\s*(CREATE DATABASE|USE)\s+.*?;\s*$/mi', '', $sql);

        try {
            DB::unprepared($sql);
            $this->info('Import complete.');
            return 0;
        } catch (\Throwable $e) {
            $this->error('Import failed: ' . $e->getMessage());
            return 1;
        }
    }
}