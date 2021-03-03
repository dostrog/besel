<?php
declare(strict_types=1);

namespace App\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;
use Throwable;

trait EnsureEnvironment
{
    public function ensureEnvironment(bool $forceImport = true): void
    {
        $error = 'Please check you environment settings (.env)';

        if ((DB::getDefaultConnection() === 'sqlite') && (!File::exists(config('database.connections.sqlite.database')))){
            File::makeDirectory('./database');
            File::put(config('database.connections.sqlite.database'), '');

            if ($forceImport) {
                try {
                    $this->call('import', ['--fromCache' => true]);
                } catch (Throwable $throwable) {
                    throw new RuntimeException($throwable->getMessage());
                }
            }

            return;
        }

        try {
            DB::getPdo();
        } catch (Throwable $throwable) {
            throw new RuntimeException("No PDO connection. " . $error);
        }
    }
}
