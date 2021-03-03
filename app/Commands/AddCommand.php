<?php
declare(strict_types=1);

namespace App\Commands;

use App\Services\Recorder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use LaravelZero\Framework\Commands\Command;
use RuntimeException;
use Throwable;

class AddCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'add
    {VehicleId : Vehicle Id (integer)}
    {InhouseSellerId : Seller Id (integer)}
    {BuyerId : Buyer Id (integer)}
    {ModelId : Model Id (integer)}
    {SaleDate : Sale Date (YYYY-MM-DD)}
    {BuyDate : Buy Date (YYYY-MM-DD)}
    {--nobatch : Add record in interactive mode (WIP)}';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Add record to database';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $this->title("Add records to test database");

        $this->ensureEnvironment();

        try {
            $recorder = new Recorder($this->output);
            $recorder->add(Arr::except($this->arguments(), ['command']));
        } catch (Throwable $throwable) {
            $this->error($throwable->getMessage());

            return 1;
        }

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
