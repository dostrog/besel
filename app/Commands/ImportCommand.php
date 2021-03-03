<?php
declare(strict_types=1);

namespace App\Commands;

use App\Services\Importer;
use App\Services\Seeder;
use LaravelZero\Framework\Commands\Command;
use Throwable;

final class ImportCommand extends Command
{
    use EnsureEnvironment;

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

        $this->ensureEnvironment( false );

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
}
