<?php
declare(strict_types=1);

namespace App\Commands;

use App\Services\BestMonths;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use RuntimeException;
use Throwable;

class BestMonthsCommand extends Command
{
    public const HEADER = ['Model', 'Quantity', 'Month', 'Year'];

    /**
     * @inheritdoc
     */
    protected $signature = 'best:months
    {model_id : Model Id for which the best three months are requested}
    {months=3 : Period in month for search}';

    /**
     * @inheritdoc
     */
    protected $description = 'Best months for specified model';

    public function handle()
    {
        $this->ensureEnvironment();

        $modelId = $this->argument('model_id');
        $months = $this->argument('months');

        $this->title("Best {$months} months for model {$modelId}");

        $months = (new BestMonths())->execute( $modelId, $months );

        if (count($months) === 0){
            $this->comment("No suitable records found.");

            return 1;
        }

        $this->table(self::HEADER, $months, 'box');

        return 0;
    }

    private function ensureEnvironment()
    {
        $error = 'Please check you environment settings (.env)';

        if ((DB::getDefaultConnection() === 'sqlite') && (!File::exists(config('database.connections.sqlite.database')))){
            File::makeDirectory('./database');
            File::put(config('database.connections.sqlite.database'), '');

            try {
                $this->call('import', ['--fromCache' => true]);
            } catch (Throwable $throwable) {
                throw new RuntimeException($throwable->getMessage());
            }

            return;
        }

        try {
            $d = DB::getPdo();
        } catch (Throwable $throwable) {
            throw new RuntimeException("No PDO connection. " . $error);
        }

    }
}
