<?php
declare(strict_types=1);

namespace App\Commands;

use App\Services\BestModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use RuntimeException;
use Throwable;

class BestModelCommand extends Command
{
    public const HEADER = ['#', 'Model', 'Quantity', 'Buyer ID', 'Name', 'Surname'];

    /**
     * @inheritdoc
     */
    protected $signature = 'best:model
    {buyer_id? : Buyer Id for which the model are requested}';
    /**
     * @inheritdoc
     */
    protected $description = 'Best selling model per client';

    public function handle(): int
    {
        $this->ensureEnvironment();

        $this->title("Best selling model per customer");

        $models = (new BestModel())->execute($this->argument('buyer_id'));

        if (count($models) === 0){
            $this->comment("No suitable records found.");

            return 1;
        }

        $this->table(self::HEADER, $models, 'box');

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
