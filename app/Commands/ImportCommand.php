<?php
declare(strict_types=1);

namespace App\Commands;

use App\Services\Importer;
use App\Services\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use RuntimeException;
use Throwable;

final class ImportCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'import
    {--fromCache : Use cached data}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Import test data from provided URL or from cache (see .env).';

    public function handle(): int
    {
        $this->title("Import (with parse) data to DB");

        $this->ensureEnvironment();

        try {

            $dataProvider = new Importer($this->output);
            $dataProvider->import($this->option('fromCache'));

            $dbSeeder = new Seeder($dataProvider, $this->output);

            $dbSeeder->prepareDatabase()
                ->seedDatabase();

        } catch (Throwable $throwable) {
            $this->error("Error importing data: " . $throwable->getMessage());

            return 1;
        }

        return 0;
    }

    private function ensureEnvironment(): void
    {
        $error = 'Please check you environment settings (.env)';

        if ((DB::getDefaultConnection() === 'sqlite') && (!File::exists(config('database.connections.sqlite.database')))){
            File::makeDirectory('./database');
            File::put(config('database.connections.sqlite.database'), '');

            return;
        }

        try {
            $d = DB::getPdo();
        } catch (Throwable $throwable) {
            throw new RuntimeException("No PDO connection. " . $error);
        }

    }
}
