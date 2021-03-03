<?php
declare(strict_types=1);

namespace App\Services;

use Faker\Factory;
use Illuminate\Console\OutputStyle;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class Recorder
{
    /**
     * @var OutputStyle
     */
    private OutputStyle $output;

    public function __construct(OutputStyle $output)
    {
        $this->output = $output;
    }

    private function addRecordToTrades(array $record): void
    {
        $data = [];

        $this->output->write("Adding record to " . Seeder::TRADES_TABLE_NAME . " with Id " . $record['VehicleId'] .  ": ");

        try {
            $data = [
                // Casting protect from SQL injection as well, well a little bit..., I hope...
                // TODO: Add additional check against injection
                'vehicle_id' => (int)$record['VehicleId'],
                'inhouse_seller_id' => (int)$record['InhouseSellerId'],
                'buyer_id' => (int)$record['BuyerId'],
                'model_id' => (int)$record['ModelId'],
                'sale_date' => Carbon::parse($record['SaleDate'])->toDateString(),
                'buy_date' => Carbon::parse($record['BuyDate'])->toDateString()
            ];
        } catch (Throwable $throwable) {
            $error = 'Error parsing data: ' . $throwable->getMessage();
            Log::error($error, ['row' => $data]);
            throw new RuntimeException($error . $throwable->getMessage());
        }

        try {
            DB::beginTransaction();
            $lastId = DB::table(Seeder::TRADES_TABLE_NAME)->insertGetId($data);
        } catch (Throwable $throwable) {
            DB::rollBack();
            $error = 'Error insert record: ' . $throwable->getMessage();
            Log::error($error, ['row' => $data]);
            throw new RuntimeException($error . $throwable->getMessage());
        }

        DB::commit();
        $this->output->writeln(' added with ID <comment>' . $lastId . '</comment>');
    }

    private function createBuyer($buyerId): void
    {
        $faker = Factory::create(config('besel.faker_locale'));
        $fakeName = explode(' ', $faker->name());
        $buyer['name'] = $fakeName[count($fakeName)-2];
        $buyer['surname'] = $fakeName[count($fakeName)-1];
        $buyer['id'] = (int)$buyerId;

        $this->output->write("Adding record to " . Seeder::BUYERS_TABLE_NAME . " with Id " . $buyer['id'] . " : ");

        try {
            DB::beginTransaction();
            DB::table(Seeder::BUYERS_TABLE_NAME)->insert( $buyer );
        } catch (Throwable $throwable) {
            DB::rollBack();
            $error = 'Error insert record: ' . $throwable->getMessage();
            Log::error($error, ['row' => $buyer]);
            throw new RuntimeException($error . $throwable->getMessage());
        }

        DB::commit();
        $this->output->writeln(sprintf("done (<comment>%s %s</comment>).", $buyer['name'], $buyer['surname']));

    }

    public function add(array $record): void
    {
        $this->addRecordToTrades($record);
        $this->createBuyer($record['BuyerId']);
    }
}
